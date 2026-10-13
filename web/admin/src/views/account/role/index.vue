<script lang="ts" setup>
import { computed, nextTick, onActivated, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import type { TableColumnData } from '@arco-design/web-vue/es/table/interface';
import cloneDeep from 'lodash/cloneDeep';
import Sortable from 'sortablejs';
import { Message, Modal } from '@arco-design/web-vue';
import { Pagination, RequestParams } from '@/types/global';

import useLoading from '@/hooks/loading';
import { deleteRole, postRole, putRole, queryRoleList, RoleRecord } from '@/api/user_role';
import useTable from '@/hooks/table';

type SizeProps = 'mini' | 'small' | 'medium' | 'large';
type Column = TableColumnData & { checked?: true };

const generateFormModel = () => {
  return {
    title: null,
  };
};
const { loading, setLoading } = useLoading(true);
const { t } = useI18n();
const { pagination } = useTable();

const renderData = ref<RoleRecord[]>([]);
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
    title: '角色名称',
    dataIndex: 'title',
  },
  {
    title: '角色描述',
    dataIndex: 'description',
  },
  {
    title: '数据权限',
    dataIndex: 'data_scope',
    slotName: 'data_scope',
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
    const { data } = await queryRoleList(params);
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

function handleSelect(value: string, record: RoleRecord) {
  if (value === '修改') {
    formData.value = cloneDeep(record);
    showFormModal.value = true;
  } else if (value === '删除') {
    Modal.confirm({
      title: '删除提示',
      content: `确定删除"${record.title}"角色吗？删除后将无法恢复，请慎重`,
      onOk: async () => {
        const res: any = await deleteRole(record.id!);
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

const accessOptions = [
  {
    label: '全部',
    value: 'all',
  },
  {
    label: '自己',
    value: 'self',
  },
];

function setAllAccess(value: 'all' | 'self') {
  formData.value.data_scope = {
    account: value,
    account_role: value,
  };
}

const dataScopeLabelMap: Record<string, string> = {
  all: '全部',
  self: '自己',
};

const dataScopeKeyMap: Record<string, string> = {
  account: '账号列表',
  account_role: '角色管理',
};

function formatDataScopeDisplay(dataScope: Record<string, string>) {
  if (!dataScope || typeof dataScope !== 'object') {
    return '-';
  }
  const values = Object.values(dataScope);
  if (values.length === 0) return '-';
  const allSame = values.every((v) => v === values[0]);
  if (allSame) {
    return `全${dataScopeLabelMap[values[0]] || values[0]}`;
  }
  return '自定义';
}

const showFormModal = ref(false);

const initData: RoleRecord = {
  id: null,
  title: '',
  description: '',
  access: [],
  data_scope: {
    account: 'self',
    account_role: 'self',
  },
};
const formRef = ref();
const formData = ref<RoleRecord>(initData);

async function handleOk() {
  const errors = await formRef.value?.validate();
  if (!errors) {
    try {
      if (formData.value.id) {
        const res: any = await putRole(formData.value);
        if (res.code === 200) {
          Message.success('修改成功');
          showFormModal.value = false;
          await fetchData();
        } else {
          Message.error(res.msg);
        }
      } else {
        const res: any = await postRole(formData.value);
        if (res.code === 200) {
          Message.success('新建成功');
          showFormModal.value = false;
          await fetchData();
        } else {
          Message.error(res.msg);
        }
      }
    } finally {
      formData.value = cloneDeep(initData);
    }
  } else {
    Message.error('请检查必填选项');
  }
}

function onCreate() {
  formData.value = cloneDeep(initData);
  showFormModal.value = true;
}

function onClose() {
  showFormModal.value = false;
}

// 菜单权限树形数据
const menuTreeData = [
  {
    title: '账号管理',
    key: 'account',
    children: [
      {
        title: '账号列表',
        key: 'account_list',
        children: [
          { title: '查询', key: 'account_list.list' },
          { title: '新增', key: 'account_list.create' },
          { title: '修改', key: 'account_list.edit' },
          { title: '删除', key: 'account_list.delete' },
        ],
      },
      {
        title: '角色管理',
        key: 'account_role',
        children: [
          { title: '列表', key: 'account_role.list' },
          { title: '新增', key: 'account_role.create' },
          { title: '修改', key: 'account_role.edit' },
          { title: '删除', key: 'account_role.delete' },
        ],
      },
    ],
  },
];

// 递归提取 menuTreeData 中所有的 key 值
function extractAllKeys(tree: any[]): string[] {
  const keys: string[] = [];
  function traverse(nodes: any[]) {
    nodes.forEach((node) => {
      keys.push(node.key);
      if (node.children && node.children.length > 0) {
        traverse(node.children);
      }
    });
  }
  traverse(tree);
  return keys;
}

// 根据 menuTreeData 自动生成 allCheckedKeys（使用 computed）
const allCheckedKeys = computed(() => extractAllKeys(menuTreeData));

const selectedKeys = ref([]);
const allExpandedKeys = ['account_list', 'account_role'];

// 处理菜单权限选择
const handleMenuCheck = (checkedKeys: any) => {
  formData.value.access = checkedKeys;
};

const handleTreeSelect = (selectedKeys: any) => {
  formData.value.access = selectedKeys;
};

const toggleAllChecked = () => {
  if (isAllChecked.value) {
    formData.value.access = [];
  } else {
    formData.value.access = allCheckedKeys.value;
  }
};

const expandedKeys = ref([]);
const toggleExpanded = () => {
  // @ts-ignore
  expandedKeys.value = expandedKeys?.value.length ? [] : allExpandedKeys;
};

// 计算是否全选
const isAllChecked = computed(() => {
  return formData.value.access.length === allCheckedKeys.value.length;
});

onActivated(async () => {
  await fetchData();
});
</script>

<script lang="ts">
export default {
  name: 'RoleList',
};
</script>

<template>
  <div class="container">
    <Breadcrumb :items="['menu.account', 'menu.account.role']" />
    <a-card class="general-card" :title="t('menu.list.searchTable')">
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
                <a-form-item field="title" label="角色名称">
                  <a-input v-model="formModel.title" placeholder="请输入角色名称" />
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
                <a-button v-access="['account_role.create']" type="primary" @click="onCreate">
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
        <template #data_scope="{ record }">
          <a-tooltip v-if="formatDataScopeDisplay(record.data_scope) === '自定义'">
            <template #content>
              <div style="line-height: 1.8">
                <div
                  v-for="(label, key) in dataScopeKeyMap"
                  :key="key"
                  style="display: flex; justify-content: space-between; gap: 16px"
                >
                  <span>{{ label }}:</span>
                  <b>
                    {{
                      dataScopeLabelMap[record.data_scope?.[key]] || record.data_scope?.[key] || '-'
                    }}
                  </b>
                </div>
              </div>
            </template>
            <a-tag :color="'orange'">
              {{ formatDataScopeDisplay(record.data_scope) }}
            </a-tag>
          </a-tooltip>
          <a-tag v-else-if="formatDataScopeDisplay(record.data_scope) === '全全部'" color="green">
            {{ formatDataScopeDisplay(record.data_scope) }}
          </a-tag>
          <a-tag v-else color="gray">
            {{ formatDataScopeDisplay(record.data_scope) }}
          </a-tag>
        </template>
        <template #operations="{ record }">
          <a-dropdown @select="(value: string) => handleSelect(value, record)">
            <a-button>操作</a-button>
            <template #content>
              <a-doption v-access="['account_role.edit']">修改</a-doption>
              <a-doption v-access="['account_role.delete']">删除</a-doption>
            </template>
          </a-dropdown>
        </template>
      </a-table>
    </a-card>
    <a-drawer
      :title="formData.id ? '修改角色' : '新增角色'"
      :visible="showFormModal"
      :width="800"
      @before-ok="handleOk"
      @close="onClose"
      @cancel="onClose"
    >
      <a-form ref="formRef" :model="formData">
        <a-form-item field="title" label="角色名称" required>
          <a-input v-model="formData.title" />
        </a-form-item>
        <a-form-item field="description" label="角色描述">
          <a-input v-model="formData.description" />
        </a-form-item>
        <a-form-item label="数据权限" required>
          <a-space>
            <a-button @click="setAllAccess('all')">全部</a-button>
            <a-button @click="setAllAccess('self')">自己</a-button>
          </a-space>
        </a-form-item>
        <a-form-item label="账号列表" field="data_scope.account" required>
          <a-radio-group v-model="formData.data_scope.account" :options="accessOptions" />
        </a-form-item>
        <a-form-item label="角色管理" field="data_scope.account_role" required>
          <a-radio-group v-model="formData.data_scope.account_role" :options="accessOptions" />
        </a-form-item>
        <a-form-item field="access" label="菜单权限" required>
          <a-space direction="vertical" style="width: 100%">
            <a-button-group style="margin-bottom: 20px">
              <a-button type="primary" @click="toggleAllChecked">
                {{ isAllChecked ? '反选' : '全选' }}
              </a-button>
              <a-button @click="toggleExpanded">
                {{ expandedKeys?.length ? '收起全部' : '展开全部' }}
              </a-button>
            </a-button-group>
            <a-tree
              v-model:selected-keys="selectedKeys"
              v-model:expanded-keys="expandedKeys"
              v-model:checked-keys="formData.access"
              :checkable="true"
              :data="menuTreeData"
              style="max-height: 400px; overflow-y: auto"
              @select="handleTreeSelect"
              @check="handleMenuCheck"
            />
          </a-space>
        </a-form-item>
      </a-form>
    </a-drawer>
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
