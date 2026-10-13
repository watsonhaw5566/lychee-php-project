<template>
  <div class="container">
    <div class="welcome-title">你好，{{ userStore.name }}，欢迎回来</div>
    <div class="welcome-desc">{{ today }} · Lychee Admin 管理后台</div>

    <a-card class="overview-card" :bordered="false">
      <div class="overview-title">数据概况</div>
      <a-row :gutter="20" class="stat-row">
        <a-col
          v-for="item in statCards"
          :key="item.key"
          :xs="{ span: 24 }"
          :sm="{ span: 12 }"
          :xl="{ span: 6 }"
        >
          <div class="stat-item" @click="goUserList">
            <div class="stat-icon" :style="{ backgroundColor: item.color }">
              <component :is="item.icon" />
            </div>
            <div class="stat-meta">
              <div class="stat-label">{{ item.title }}</div>
              <a-spin v-if="loading" :size="24" />
              <div v-else class="stat-value">{{ item.value }}</div>
            </div>
          </div>
        </a-col>
      </a-row>
    </a-card>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import dayjs from 'dayjs';
import { queryDashboardStats, DashboardStats } from '@/api/dashboard';
import useLoading from '@/hooks/loading';
import { useUserStore } from '@/store';

const router = useRouter();
const userStore = useUserStore();
const { loading, setLoading } = useLoading();

const weekDays = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
const today = `${dayjs().format('YYYY年MM月DD日')} ${weekDays[dayjs().day()]}`;
const stats = ref<DashboardStats>({
  user_total: 0,
  admin_total: 0,
  active_total: 0,
  disabled_total: 0,
});

const statCards = computed(() => [
  {
    key: 'user_total',
    title: '用户总数',
    value: stats.value.user_total,
    icon: 'icon-user-group',
    color: 'rgb(var(--arcoblue-6))',
  },
  {
    key: 'admin_total',
    title: '管理员',
    value: stats.value.admin_total,
    icon: 'icon-safe',
    color: '#ff9f40',
  },
  {
    key: 'active_total',
    title: '正常用户',
    value: stats.value.active_total,
    icon: 'icon-check-circle',
    color: '#0fb42e',
  },
  {
    key: 'disabled_total',
    title: '禁用用户',
    value: stats.value.disabled_total,
    icon: 'icon-close-circle',
    color: '#165dff',
  },
]);

const fetchStats = async () => {
  setLoading(true);
  try {
    const { data } = await queryDashboardStats();
    stats.value = data;
  } finally {
    setLoading(false);
  }
};

const goUserList = () => {
  router.push({ name: 'AccountList' });
};

fetchStats();
</script>

<script lang="ts">
export default {
  name: 'Dashboard',
};
</script>

<style lang="less" scoped>
.container {
  padding: 20px;
  background-color: var(--color-fill-2);
  min-height: 100%;
}

.welcome-title {
  font-size: 20px;
  font-weight: 500;
  color: var(--color-text-1);
}

.welcome-desc {
  margin: 4px 0 16px;
  font-size: 13px;
  color: var(--color-text-3);
}

.overview-card {
  border-radius: 4px;

  :deep(.arco-card-body) {
    padding: 20px 24px 24px;
  }
}

.overview-title {
  margin-bottom: 16px;
  font-size: 16px;
  font-weight: 500;
  color: var(--color-text-1);
}

.stat-row {
  row-gap: 20px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 16px;
  height: 120px;
  padding: 0 24px;
  background-color: var(--color-fill-2);
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.2s;

  &:hover {
    background-color: var(--color-fill-3);
  }
}

.stat-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 56px;
  height: 56px;
  border-radius: 12px;
  font-size: 28px;
  color: #fff;
  flex-shrink: 0;
}

.stat-meta {
  min-width: 0;
}

.stat-label {
  font-size: 14px;
  color: var(--color-text-3);
}

.stat-value {
  margin-top: 8px;
  font-size: 30px;
  font-weight: 600;
  line-height: 1.2;
  color: var(--color-text-1);
}
</style>
