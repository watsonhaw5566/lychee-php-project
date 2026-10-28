import { defineStore } from 'pinia';
import { Notification } from '@arco-design/web-vue';
import type { RouteRecordNormalized } from 'vue-router';
import defaultSettings from '@/config/settings.json';
import { getMenuList } from '@/api/user';
import { AppState } from './types';

/** localStorage 中持久化设置的键名 */
const SETTINGS_STORAGE_KEY = 'app-settings';

/** 需要持久化的设置字段（排除运行时状态如 serverMenu、device、globalSettings 等） */
const PERSIST_KEYS = [
  'theme',
  'colorWeak',
  'navbar',
  'menu',
  'topMenu',
  'menuCollapse',
  'footer',
  'themeColor',
  'menuWidth',
  'tabBar',
  'menuFromServer',
] as const;

/** 从 localStorage 读取已保存的设置，未设置时返回空对象 */
function loadSavedSettings(): Partial<AppState> {
  try {
    const raw = localStorage.getItem(SETTINGS_STORAGE_KEY);
    return raw ? JSON.parse(raw) : {};
  } catch {
    return {};
  }
}

const useAppStore = defineStore('app', {
  state: (): AppState => ({ ...defaultSettings, ...loadSavedSettings() }),

  getters: {
    appCurrentSetting(state: AppState): AppState {
      return { ...state };
    },
    appDevice(state: AppState) {
      return state.device;
    },
    appAsyncMenus(state: AppState): RouteRecordNormalized[] {
      return state.serverMenu as unknown as RouteRecordNormalized[];
    },
  },

  actions: {
    // Update app settings
    updateSettings(partial: Partial<AppState>) {
      // @ts-expect-error
      this.$patch(partial);
    },

    // Save current settings to localStorage
    saveSettings() {
      const settings: Partial<AppState> = {};
      PERSIST_KEYS.forEach((key) => {
        settings[key] = this[key];
      });
      localStorage.setItem(SETTINGS_STORAGE_KEY, JSON.stringify(settings));
    },

    // Change theme color
    toggleTheme(dark: boolean) {
      if (dark) {
        this.theme = 'dark';
        document.body.setAttribute('arco-theme', 'dark');
      } else {
        this.theme = 'light';
        document.body.removeAttribute('arco-theme');
      }
    },
    toggleDevice(device: string) {
      this.device = device;
    },
    toggleMenu(value: boolean) {
      this.hideMenu = value;
    },
    async fetchServerMenuConfig() {
      try {
        Notification.info({
          id: 'menuNotice', // Keep the instance id the same
          content: 'loading',
          closable: true,
        });
        const { data } = await getMenuList();
        this.serverMenu = data;
        Notification.success({
          id: 'menuNotice',
          content: 'success',
          closable: true,
        });
      } catch (_error) {
        Notification.error({
          id: 'menuNotice',
          content: 'error',
          closable: true,
        });
      }
    },
    clearServerMenu() {
      this.serverMenu = [];
    },
  },
});

export default useAppStore;
