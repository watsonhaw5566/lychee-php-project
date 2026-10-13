<template>
  <div class="container">
    <Breadcrumb :items="['menu.user', 'menu.user.info']" />
    <a-space direction="vertical" :size="16" fill>
      <UserInfoHeader />
      <a-card class="general-card">
        <template #title>
          {{ $t('userInfo.title.baseInfo') }}
        </template>
        <template #extra>
          <a-button type="primary" @click="$router.push({ name: 'Setting' })">
            <template #icon>
              <icon-edit />
            </template>
            {{ $t('userInfo.editUserInfo') }}
          </a-button>
        </template>
        <a-descriptions
          :data="renderData"
          :column="2"
          layout="inline-horizontal"
          :label-style="{ width: '100px', color: 'var(--color-text-3)', paddingRight: '12px' }"
        >
          <template #label="{ label }">{{ $t(label) }}</template>
          <template #value="{ value, data }">
            <a-tag v-if="data.label === 'userInfo.label.role'" color="arcoblue" size="small">
              {{ value }}
            </a-tag>
            <span v-else>{{ value || '-' }}</span>
          </template>
        </a-descriptions>
      </a-card>
    </a-space>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useUserStore } from '@/store';
import UserInfoHeader from './components/user-info-header.vue';

const userStore = useUserStore();
const userInfo = storeToRefs(userStore);

const roleText = computed(() => (userInfo.role.value === 'admin' ? '管理员' : '职员'));

const renderData = computed(() => [
  {
    label: 'userSetting.label.accountId',
    value: userInfo.accountId.value,
    dataIndex: 'accountId',
  },
  {
    label: 'userSetting.label.name',
    value: userInfo.name.value,
    dataIndex: 'name',
  },
  {
    label: 'userSetting.basicInfo.form.label.email',
    value: userInfo.email.value,
    dataIndex: 'email',
  },
  {
    label: 'userInfo.label.role',
    value: roleText.value,
    dataIndex: 'role',
  },
  {
    label: 'userSetting.label.registrationDate',
    value: userInfo.registrationDate.value,
    dataIndex: 'registrationDate',
  },
  {
    label: 'userInfo.label.lastLoginTime',
    value: userInfo.lastLoginTime.value,
    dataIndex: 'lastLoginTime',
  },
]);
</script>

<script lang="ts">
export default {
  name: 'Info',
};
</script>

<style scoped lang="less">
.container {
  padding: 0 20px 20px;
}

:deep(.arco-descriptions) {
  max-width: 720px;
}

@media (max-width: 768px) {
  :deep(.arco-descriptions) {
    grid-template-columns: 1fr !important;
  }
}
</style>
