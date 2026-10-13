import axios from 'axios';

export interface DashboardStats {
  user_total: number;
  admin_total: number;
  active_total: number;
  disabled_total: number;
}

export function queryDashboardStats() {
  return axios.get<DashboardStats>('/api/dashboard/stats');
}
