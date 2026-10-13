<script lang="ts" setup>
import { computed, nextTick, onActivated, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import type { TableColumnData } from '@arco-design/web-vue/es/table/interface';
import cloneDeep from 'lodash/cloneDeep';
import Sortable from 'sortablejs';
import { Message, Modal, SelectOption } from '@arco-design/web-vue';
import { Pagination, RequestParams } from '@/types/global';

import useLoading from '@/hooks/loading';
import { filterOption } from '@/utils/option-filter';
import { deleteUser, postUser, putUser, queryUserList, UserRecord } from '@/api/user';
import useTable from '@/hooks/table';

type SizeProps = 'mini' | 'small' | 'medium' | 'large';
type Column = TableColumnData & { checked?: true };

const generateFormModel = () => {
  return {
    username: null,
    email: null,
    role: null,
    status: null,
  };
};
const { loading, setLoading } = useLoading(true);
const { t } = useI18n();
const { pagination } = useTable();

const renderData = ref<UserRecord[]>([]);
const formModel = ref(generateFormModel());
const cloneColumns = ref<Column[]>([]);
const showColumns = ref<Column[]>([]);

const size = ref<SizeProps>('medium');

const basePagination: Pagination = {
  current: 1,
  pageSize: 20,
};

const densityList = computed(() => [
  {
    name: t('searchTable.size.mini'),
    value: 'mini',
  },
  {
    name: t('searchTable.size.small'),
    value: 'small',
  },
  {
    name: t('searchTable.size.medium'),
    value: 'medium',
  },
  {
    name: t('searchTable.size.large'),
    value: 'large',
  },
]);

const columns = computed<TableColumnData[]>(() => [
  {
    title: '#',
    dataIndex: 'id',
  },
  {
    title: '用户名',
    dataIndex: 'username',
    slotName: 'username',
  },
  {
    title: '登录邮箱',
    dataIndex: 'email',
  },
  {
    title: '基础角色',
    dataIndex: 'role',
    slotName: 'role',
  },
  {
    title: '上次登录',
    dataIndex: 'last_login_time',
  },
  {
    title: '状态',
    dataIndex: 'status',
    slotName: 'status',
  },
  {
    title: t('searchTable.columns.createdTime'),
    dataIndex: 'create_time',
  },
  {
    title: t('searchTable.columns.operations'),
    dataIndex: 'operations',
    slotName: 'operations',
    align: 'center',
  },
]);

const fetchData = async (
  params: RequestParams = { current: 1, pageSize: 20, ...formModel.value },
) => {
  setLoading(true);
  try {
    const { data } = await queryUserList(params);
    renderData.value = data.list;
    pagination.total = data.total;
    pagination.current = params.current;
  } catch (err) {
    // you can report use errorHandler or other
  } finally {
    setLoading(false);
  }
};

const search = () => {
  fetchData({
    ...basePagination,
    ...formModel.value,
  } as unknown as RequestParams);
};
const onPageChange = (current: number) => {
  pagination.current = current;
  fetchData({ ...pagination, ...formModel.value });
};

const onPageSizeChange = (pageSize: number) => {
  pagination.pageSize = pageSize;
  fetchData({ ...pagination, ...formModel.value });
};

const reset = () => {
  formModel.value = generateFormModel();
  fetchData();
};

const handleSelectDensity = (val: string | number | Record<string, any> | undefined, e: Event) => {
  size.value = val as SizeProps;
};

const handleChange = (
  checked: boolean | (string | boolean | number)[],
  column: Column,
  index: number,
) => {
  if (!checked) {
    cloneColumns.value = showColumns.value.filter((item) => item.dataIndex !== column.dataIndex);
  } else {
    cloneColumns.value.splice(index, 0, column);
  }
};

const exchangeArray = <T extends Array<any>>(
  array: T,
  beforeIdx: number,
  newIdx: number,
  isDeep = false,
): T => {
  const newArray = isDeep ? cloneDeep(array) : array;
  if (beforeIdx > -1 && newIdx > -1) {
    // 先替换后面的，然后拿到替换的结果替换前面的
    newArray.splice(beforeIdx, 1, newArray.splice(newIdx, 1, newArray[beforeIdx]).pop());
  }
  return newArray;
};

const popupVisibleChange = (val: boolean) => {
  if (val) {
    nextTick(() => {
      const el = document.getElementById('tableSetting') as HTMLElement;
      const sortable = new Sortable(el, {
        onEnd(e: any) {
          const { oldIndex, newIndex } = e;
          exchangeArray(cloneColumns.value, oldIndex, newIndex);
          exchangeArray(showColumns.value, oldIndex, newIndex);
        },
      });
    });
  }
};

watch(
  () => columns.value,
  (val) => {
    cloneColumns.value = cloneDeep(val);
    cloneColumns.value.forEach((item, index) => {
      item.checked = true;
    });
    showColumns.value = cloneDeep(cloneColumns.value);
  },
  { deep: true, immediate: true },
);

function handleSelect(value: string, record: UserRecord) {
  if (value === '修改') {
    formData.value = cloneDeep(record);
    showFormModal.value = true;
  } else if (value === '删除') {
    Modal.confirm({
      title: '删除提示',
      content: `确定删除"${record.username}"用户吗？删除后将无法恢复，请慎重`,
      onOk: async () => {
        const res: any = await deleteUser(record.id!);
        if (res.code === 200) {
          Message.success('删除成功');
          await fetchData();
        } else {
          Message.error(res.msg);
        }
      },
    });
  }
}

const roleOptions: SelectOption[] = [
  {
    label: '管理员',
    value: 'admin',
  },
  {
    label: '职员',
    value: 'staff',
  },
];

const statusOptions: SelectOption[] = [
  {
    label: '正常',
    value: 1,
  },
  {
    label: '禁用',
    value: 0,
  },
];

const showFormModal = ref(false);

const initData: UserRecord = {
  id: null,
  username: '',
  email: '',
  role: 'staff',
  role_id: null,
  password: '123456',
  status: 1,
};
const formRef = ref();
const formData = ref<UserRecord>(initData);

async function handleOk() {
  const errors = await formRef.value?.validate();
  if (!errors) {
    if (formData.value.id) {
      const res: any = await putUser(formData.value);
      if (res.code === 200) {
        Message.success('修改成功');
        showFormModal.value = false;
        await fetchData();
      } else {
        Message.error(res.msg);
      }
    } else {
      const res: any = await postUser(formData.value);
      if (res.code === 200) {
        Message.success('新建成功');
        showFormModal.value = false;
        await fetchData();
      } else {
        Message.error(res.msg);
      }
    }
  } else {
    Message.error('请检查必填选项');
  }
}

function onClose() {
  showFormModal.value = false;
  formData.value = initData;
}

onActivated(async () => {
  await fetchData();
});
</script>

<script lang="ts">
export default {
  name: 'AccountList',
};
</script>

<template>
  <div class="container">
    <Breadcrumb :items="['menu.account', 'menu.account.list']" />
    <a-card class="general-card" title="用户列表">
      <a-row>
        <a-col :flex="1">
          <a-form
            :model="formModel"
            :label-col-props="{ span: 6 }"
            :wrapper-col-props="{ span: 18 }"
            label-align="left"
          >
            <a-row :gutter="16">
              <a-col :span="8">
                <a-form-item field="username" label="用户名">
                  <a-input v-model="formModel.username" placeholder="请输入用户名" />
                </a-form-item>
              </a-col>
              <a-col :span="8">
                <a-form-item field="email" label="登录邮箱">
                  <a-input v-model="formModel.email" placeholder="请输入邮箱" />
                </a-form-item>
              </a-col>
              <a-col :span="8">
                <a-form-item field="role" label="角色">
                  <a-select
                    v-model="formModel.role"
                    placeholder="请选择角色"
                    :options="roleOptions"
                  />
                </a-form-item>
              </a-col>
              <a-col :span="8">
                <a-form-item field="status" label="状态">
                  <a-select
                    v-model="formModel.status"
                    placeholder="请选择状态"
                    :options="statusOptions"
                  />
                </a-form-item>
              </a-col>
            </a-row>
          </a-form>
        </a-col>
        <a-divider style="height: 84px" direction="vertical" />
        <a-col :flex="'86px'" style="text-align: right">
          <a-space direction="vertical" :size="18">
            <a-button type="primary" @click="search">
              <template #icon>
                <icon-search />
              </template>
              {{ t('searchTable.form.search') }}
            </a-button>
            <a-button @click="reset">
              <template #icon>
                <icon-refresh />
              </template>
              {{ t('searchTable.form.reset') }}
            </a-button>
          </a-space>
        </a-col>
      </a-row>
      <a-divider style="margin-top: 0" />
      <a-row style="margin-bottom: 16px">
        <a-col :span="12">
          <a-space>
            <a-col :span="12">
              <a-space>
                <a-button
                  v-access="['account_list.create']"
                  type="primary"
                  @click="
                    () => {
                      formData = initData;
                      showFormModal = true;
                    }
                  "
                >
                  <template #icon>
                    <icon-plus />
                  </template>
                  {{ t('searchTable.operation.create') }}
                </a-button>
              </a-space>
            </a-col>
          </a-space>
        </a-col>
        <a-col :span="12" style="display: flex; align-items: center; justify-content: end">
          <!--          <a-button>-->
          <!--            <template #icon>-->
          <!--              <icon-download />-->
          <!--            </template>-->
          <!--            {{ $t('searchTable.operation.download') }}-->
          <!--          </a-button>-->
          <a-tooltip :content="t('searchTable.actions.refresh')">
            <div class="action-icon" @click="search">
              <icon-refresh size="18" />
            </div>
          </a-tooltip>
          <a-dropdown @select="handleSelectDensity">
            <a-tooltip :content="t('searchTable.actions.density')">
              <div class="action-icon">
                <icon-line-height size="18" />
              </div>
            </a-tooltip>
            <template #content>
              <a-doption
                v-for="item in densityList"
                :key="item.value"
                :value="item.value"
                :class="{ active: item.value === size }"
              >
                <span>{{ item.name }}</span>
              </a-doption>
            </template>
          </a-dropdown>
          <a-tooltip :content="t('searchTable.actions.columnSetting')">
            <a-popover trigger="click" position="bl" @popup-visible-change="popupVisibleChange">
              <div class="action-icon">
                <icon-settings size="18" />
              </div>
              <template #content>
                <div id="tableSetting">
                  <div v-for="(item, index) in showColumns" :key="item.dataIndex" class="setting">
                    <div style="margin-right: 4px; cursor: move">
                      <icon-drag-arrow />
                    </div>
                    <div>
                      <a-checkbox
                        v-model="item.checked"
                        @change="handleChange($event, item as TableColumnData, index)"
                      ></a-checkbox>
                    </div>
                    <div class="title">
                      {{ item.title === '#' ? '序列号' : item.title }}
                    </div>
                  </div>
                </div>
              </template>
            </a-popover>
          </a-tooltip>
        </a-col>
      </a-row>
      <a-table
        row-key="id"
        :loading="loading"
        :pagination="pagination"
        :columns="cloneColumns as TableColumnData[]"
        :data="renderData"
        :bordered="false"
        :size="size"
        @page-change="onPageChange"
        @page-size-change="onPageSizeChange"
      >
        <template #username="{ record }">
          <div style="display: flex; align-items: center">
            <a-image v-if="record.avatar" :src="record.avatar" alt="account logo" :width="25" />
            <div :style="{ marginLeft: '8px' }">{{ record.username }}</div>
          </div>
        </template>
        <template #role="{ record }">
          {{ filterOption(roleOptions, record.role) }}
        </template>
        <template #status="{ record }">
          <span v-if="record.status === 1" class="circle pass"></span>
          <span v-else-if="record.status === 0" class="circle fail"></span>
          {{ filterOption(statusOptions, record.status) }}
        </template>
        <template #operations="{ record }">
          <a-dropdown @select="(value: string) => handleSelect(value, record)">
            <a-button>操作</a-button>
            <template #content>
              <a-doption v-access="['account_list.edit']">修改</a-doption>
              <a-doption v-access="['account_list.delete']">删除</a-doption>
            </template>
          </a-dropdown>
        </template>
      </a-table>
    </a-card>
    <a-modal
      :title="formData.id ? '修改用户' : '新增用户'"
      :visible="showFormModal"
      @before-ok="handleOk"
      @close="onClose"
      @cancel="onClose"
    >
      <a-space direction="vertical" fill>
        <a-form ref="formRef" :model="formData">
          <a-form-item field="username" label="用户名" required>
            <a-input v-model="formData.username" />
          </a-form-item>
          <a-form-item field="email" label="登录邮箱" required>
            <a-input v-model="formData.email" />
          </a-form-item>
          <a-form-item
            field="password"
            label="登录密码"
            :required="!formData.id"
            help="新建用户默认密码为 123456；编辑时留空表示不修改密码"
          >
            <a-input-password
              v-model="formData.password"
              :placeholder="formData.id ? '留空则不修改密码' : '请输入密码'"
            />
          </a-form-item>
          <a-form-item field="role" label="基础身份" required>
            <a-select v-model="formData.role" :options="roleOptions" />
          </a-form-item>
          <a-form-item v-if="formData.id" field="status" label="状态" required>
            <a-select v-model="formData.status" :options="statusOptions" />
          </a-form-item>
        </a-form>
      </a-space>
    </a-modal>
  </div>
</template>

<style scoped lang="less">
.container {
  padding: 0 20px 20px;
}

:deep(.arco-table-th) {
  &:last-child {
    .arco-table-th-item-title {
      margin-left: 16px;
    }
  }
}

.action-icon {
  margin-left: 12px;
  cursor: pointer;
}

.active {
  color: #0960bd;
  background-color: #e3f4fc;
}

.setting {
  display: flex;
  align-items: center;
  width: 200px;

  .title {
    margin-left: 12px;
    cursor: pointer;
  }
}
</style>
