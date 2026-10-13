import axios from 'axios';
import { RequestParams } from '@/types/global';

export interface RoleRecord {
  id: number | null;
  title: string;
  description: string;
  data_scope: Record<string, string>;
  access: string[];
  create_time?: string;
}
export interface RoleListRes {
  list: RoleRecord[];
  total: number;
}

export function queryRoleList(params: RequestParams) {
  return axios.get<RoleListRes>('/api/user_role', {
    params,
  });
}
export function getRole(id: number) {
  return axios.get<RoleRecord>(`/api/user_role/${id}`);
}

export function postRole(data: RoleRecord) {
  return axios.post('/api/user_role', data);
}

export function putRole(data: RoleRecord) {
  return axios.put(`/api/user_role/${data.id}`, data);
}

export function deleteRole(id: number) {
  return axios.delete(`/api/user_role/${id}`);
}

export function batchDeleteRole(data: { ids: number[] }) {
  return axios.post('/api/user_role/batch_delete', data);
}
