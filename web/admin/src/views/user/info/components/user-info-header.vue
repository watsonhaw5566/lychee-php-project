<template>
  <a-card class="user-header" :bordered="false">
    <div class="banner" />
    <div class="user-body">
      <a-avatar :size="80" class="user-avatar">
        <img v-if="userInfo.avatar" :src="userInfo.avatar" alt="avatar" />
        <span v-else>{{ avatarText }}</span>
      </a-avatar>
      <div class="user-meta">
        <a-space :size="8">
          <a-typography-title :heading="5" style="margin: 0">
            {{ userInfo.name || '-' }}
          </a-typography-title>
          <a-tag color="arcoblue" size="small">
            {{ roleText }}
          </a-tag>
        </a-space>
        <a-typography-text type="secondary" class="user-sub">
          <icon-email />
          {{ userInfo.email || '-' }}
        </a-typography-text>
      </div>
    </div>
  </a-card>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { useUserStore } from '@/store';

const userInfo = useUserStore();

// 未上传头像时，用用户名首字符作为头像
const avatarText = computed(() => (userInfo.name || '?').charAt(0).toUpperCase());

const roleText = computed(() => (userInfo.role === 'admin' ? '管理员' : '职员'));
</script>

<style scoped lang="less">
.user-header {
  overflow: hidden;
  border-radius: 4px;

  :deep(.arco-card-body) {
    padding: 0;
  }
}

.banner {
  height: 120px;
  background: linear-gradient(135deg, rgb(var(--arcoblue-5)) 0%, rgb(var(--arcoblue-7)) 100%);
}

.user-body {
  display: flex;
  align-items: flex-end;
  padding: 0 24px 20px;
}

.user-avatar {
  margin-top: -40px;
  overflow: hidden;
  font-size: 28px;
  color: #fff;
  background-color: rgb(var(--arcoblue-6));
  border: 3px solid var(--color-bg-2);
  border-radius: 50%;
}

.user-meta {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 6px;
  margin-left: 16px;
}

.user-sub {
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
</style>
