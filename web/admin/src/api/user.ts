import axios from 'axios';
import type { RouteRecordNormalized } from 'vue-router';
import { UserState } from '@/store/modules/user/types';
import { RequestParams } from '@/types/global';

export interface LoginData {
  email: string;
  password: string;
}

export interface LoginRes {
  token: string;
}
export function login(data: LoginData) {
  return axios.post<LoginRes>('/api/user/login', data);
}

export function logout() {
  return axios.post<LoginRes>('/api/user/logout');
}

export interface UserRecord {
  id: number | null;
  username: string;
  email: string;
  role: string;
  role_id: number | null;
  status: number;
  password?: string;
  last_login_time?: string;
  create_time?: string;
}

export interface UserListRes {
  list: UserRecord[];
  total: number;
}
export function queryUserList(params: RequestParams) {
  return axios.get<UserListRes>('/api/user', {
    params,
  });
}

export function postUser(data: UserRecord) {
  return axios.post<UserListRes>('/api/user', data);
}

export function putUser(data: UserRecord) {
  return axios.put<UserListRes>(`/api/user/${data.id}`, data);
}
export function deleteUser(id: number) {
  return axios.delete<UserListRes>(`/api/user/${id}`);
}
export function getUserInfo() {
  return axios.post<UserState>('/api/user/info');
}

export function getMenuList() {
  return axios.post<RouteRecordNormalized[]>('/api/user/get_menu');
}

export function postChangeInfo(data: { email: string; nickname: string }) {
  return axios.post('/api/user/change_info', data);
}

export interface UpdateUserPassword {
  password: string;
  confirm_password: string;
}
export function postChangePassword(data: UpdateUserPassword) {
  return axios.post('/api/user/change_password', data);
}
