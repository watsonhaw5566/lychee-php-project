<template>
  <div v-if="!appStore.navbar" class="fixed-settings" @click="setVisible">
    <a-button type="primary">
      <template #icon>
        <icon-settings />
      </template>
    </a-button>
  </div>
  <a-drawer
    :width="300"
    unmount-on-close
    :visible="visible"
    :cancel-text="$t('settings.close')"
    :ok-text="$t('settings.saveSettings')"
    @ok="saveSettings"
    @cancel="cancel"
  >
    <template #title>{{ $t('settings.title') }}</template>
    <div class="block">
      <h5 class="title">{{ $t('settings.themeColor') }}</h5>
      <div class="theme-colors">
        <span
          v-for="item in themeColors"
          :key="item.value"
          class="theme-color-item"
          :class="{ active: isSelected(item.value) }"
          :style="{
            backgroundColor: item.value,
            boxShadow: isSelected(item.value)
              ? `0 0 0 2px var(--color-bg-2), 0 0 0 4px ${item.value}`
              : undefined,
          }"
          :title="item.name"
          @click="handleThemeColor(item.value)"
        >
          <icon-check v-if="isSelected(item.value)" />
        </span>
      </div>
    </div>
    <Block :options="contentOpts" :title="$t('settings.content')" />
    <a-alert>{{ $t('settings.alertContent') }}</a-alert>
  </a-drawer>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue';
import { Message } from '@arco-design/web-vue';
import { useI18n } from 'vue-i18n';
import { useAppStore } from '@/store';
import defaultSettings from '@/config/settings.json';
import { applyThemeColor, getStoredThemeColor, storeThemeColor } from '@/utils/theme-color';
import Block from './block.vue';

const emit = defineEmits(['cancel']);

const appStore = useAppStore();
const { t } = useI18n();
const visible = computed(() => appStore.globalSettings);

// 预设主题色（参考 Ant Design Pro 经典色板，首项为品牌荔枝粉）
const themeColors = [
  { name: '荔枝粉', value: '#F2708E' },
  { name: '拂晓蓝', value: '#165DFF' },
  { name: '极光绿', value: '#00B42A' },
  { name: '活力橙', value: '#FF7D00' },
  { name: '中国红', value: '#F53F3F' },
  { name: '酱样紫', value: '#722ED1' },
];

const selectedTheme = ref(getStoredThemeColor() || defaultSettings.themeColor);

const isSelected = (color: string) => selectedTheme.value.toLowerCase() === color.toLowerCase();

const handleThemeColor = (color: string) => {
  selectedTheme.value = color;
  applyThemeColor(color);
  storeThemeColor(color);
};
const contentOpts = computed(() => [
  { name: 'settings.navbar', key: 'navbar', defaultVal: appStore.navbar },
  {
    name: 'settings.menu',
    key: 'menu',
    defaultVal: appStore.menu,
  },
  {
    name: 'settings.topMenu',
    key: 'topMenu',
    defaultVal: appStore.topMenu,
  },
  { name: 'settings.footer', key: 'footer', defaultVal: appStore.footer },
  { name: 'settings.tabBar', key: 'tabBar', defaultVal: appStore.tabBar },
]);

const cancel = () => {
  appStore.updateSettings({ globalSettings: false });
  emit('cancel');
};
const saveSettings = () => {
  appStore.saveSettings();
  Message.success(t('settings.saveSettings.message'));
};
const setVisible = () => {
  appStore.updateSettings({ globalSettings: true });
};
</script>

<style scoped lang="less">
.block {
  margin-bottom: 24px;
}

.title {
  margin: 10px 0;
  padding: 0;
  font-size: 14px;
}

.theme-colors {
  display: flex;
  gap: 12px;
}

.theme-color-item {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  color: #fff;
  font-size: 14px;
  cursor: pointer;
  transition: transform 0.15s;

  &:hover {
    transform: scale(1.15);
  }
}

.fixed-settings {
  position: fixed;
  top: 280px;
  right: 0;

  svg {
    font-size: 18px;
    vertical-align: -4px;
  }
}
</style>
