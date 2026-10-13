import { App } from 'vue';
import permission from './permission';
import access from './access';

export default {
  install(Vue: App) {
    Vue.directive('permission', permission);
    Vue.directive('access', access);
  },
};
