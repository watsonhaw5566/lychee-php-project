import axios from 'axios';

export interface BasicInfoModel {
  email: string;
  nickname: string;
}

export function userUploadApi(
  data: FormData,
  config: {
    controller: AbortController;
    onUploadProgress?: (progressEvent: any) => void;
  },
) {
  return axios.post<{ url: string }>('/api/user/upload', data, config);
}
