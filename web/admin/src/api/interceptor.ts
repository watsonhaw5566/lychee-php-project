import type { AxiosRequestConfig, AxiosResponse } from 'axios';
import axios from 'axios';
import { Message, Modal } from '@arco-design/web-vue';
import { getToken } from '@/utils/auth';
import { useUserStore } from '@/store';

axios.interceptors.request.use(
  (config: AxiosRequestConfig) => {
    // let each request carry token
    // this example using the JWT token
    // Authorization is a custom headers key
    // please modify it according to the actual situation
    const token = getToken();
    if (token) {
      if (!config.headers) {
        config.headers = {};
      }
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    // do something
    return Promise.reject(error);
  },
);
// add response interceptors
axios.interceptors.response.use(
  (response: AxiosResponse) => {
    const res = response.data;
    // if the custom code is not 20000, it is judged as an error.
    if (res.code !== 200) {
      Message.error({
        content: res.msg || 'Error',
        duration: 5 * 1000,
      });
      // 50008: Illegal token; 50012: Other clients logged in; 50014: Token expired;
      if ([401, 403].includes(res.code) && response.config.url !== '/api/user/info') {
        Modal.error({
          title: 'Confirm logout',
          content: 'You have been logged out, you can cancel to stay on this page, or log in again',
          okText: 'Re-Login',
          async onOk() {
            const userStore = useUserStore();
            await userStore.logout();
            window.location.reload();
          },
        });
      }
      return Promise.reject(new Error(res.msg || 'Error'));
    }
    return res;
  },
  (error) => {
    if (error.response.status === 400) {
      Message.error({
        content: error.response.data.msg || 'Request Error',
        duration: 5 * 1000,
      });
    }
    if (error.response.status === 401) {
      Message.error({
        content: error.response.data.msg || 'Request Error',
        duration: 5 * 1000,
      });
    }
    if (error.response.status === 403) {
      // 尝试重新登录
      Message.error({
        content: error.response.data.msg || 'Request Error',
        duration: 5 * 1000,
      });
    }
    if (error.response.status === 500) {
      Message.error({
        content: '服务器迷路了',
        duration: 5 * 1000,
      });
    }
    return Promise.reject(error);
  },
);
