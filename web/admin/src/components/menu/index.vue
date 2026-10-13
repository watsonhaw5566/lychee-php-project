<script lang="ts" setup>
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import type { RouteMeta } from 'vue-router';
import { RouteRecordRaw, useRoute, useRouter } from 'vue-router';
import { useAppStore } from '@/store';
import { listenerRouteChange } from '@/utils/route-listener';
import { openWindow, regexUrl } from '@/utils';
import useMenuTree from './use-menu-tree';
import SubMenu from './submenu/index.vue';

const emit = defineEmits(['collapse']);

const { t } = useI18n();
const appStore = useAppStore();
const router = useRouter();
const route = useRoute();
const { menuTree } = useMenuTree();

const collapsed = computed({
  get() {
    if (appStore.device === 'desktop') return appStore.menuCollapse;
    return false;
  },
  set(value: boolean) {
    appStore.updateSettings({ menuCollapse: value });
  },
});

const topMenu = computed(() => appStore.topMenu);
const openKeys = ref<string[]>([]);
const selectedKey = ref<string[]>([]);

const goto = (item: RouteRecordRaw) => {
  // Open external link
  if (regexUrl.test(item.path)) {
    openWindow(item.path);
    selectedKey.value = [item.name as string];
    return;
  }
  // Eliminate external link side effects
  const { hideInMenu, activeMenu } = item.meta as RouteMeta;
  if (route.name === item.name && !hideInMenu && !activeMenu) {
    selectedKey.value = [item.name as string];
    return;
  }
  // Trigger router change
  router.push({
    name: item.name,
  });
};

const findMenuOpenKeys = (target: string) => {
  const result: string[] = [];
  let isFind = false;
  const backtrack = (item: RouteRecordRaw, keys: string[]) => {
    if (item.name === target) {
      isFind = true;
      result.push(...keys);
      return;
    }
    if (item.children?.length) {
      item.children.forEach((el) => {
        backtrack(el, [...keys, el.name as string]);
      });
    }
  };
  menuTree.value.forEach((el: RouteRecordRaw) => {
    if (isFind) return; // Performance optimization
    backtrack(el, [el.name as string]);
  });
  return result;
};

listenerRouteChange((newRoute) => {
  const { requiresAuth, activeMenu, hideInMenu } = newRoute.meta;
  if (requiresAuth && (!hideInMenu || activeMenu)) {
    const menuOpenKeys = findMenuOpenKeys((activeMenu || newRoute.name) as string);

    const keySet = new Set([...menuOpenKeys, ...openKeys.value]);
    openKeys.value = [...keySet];

    selectedKey.value = [activeMenu || menuOpenKeys[menuOpenKeys.length - 1]];
  }
}, true);

const setCollapse = (val: boolean) => {
  if (appStore.device === 'desktop') appStore.updateSettings({ menuCollapse: val });
};
</script>

<template>
  <a-menu
    v-model:collapsed="collapsed"
    v-model:open-keys="openKeys"
    :auto-open="false"
    :auto-open-selected="true"
    :level-indent="34"
    :mode="topMenu ? 'horizontal' : 'vertical'"
    :selected-keys="selectedKey"
    :show-collapse-button="appStore.device !== 'mobile'"
    style="height: 100%; width: 100%"
    @collapse="setCollapse"
  >
    <template v-for="item in menuTree" :key="item.name">
      <SubMenu :base-path="item.path" :item="item" />
    </template>
  </a-menu>
</template>

<style lang="less" scoped>
:deep(.arco-menu-inner) {
  .arco-menu-inline-header {
    display: flex;
    align-items: center;
  }

  .arco-icon {
    &:not(.arco-icon-down) {
      font-size: 18px;
    }
  }

  .fa {
    font-size: 18px;
    width: 18px;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
}
</style>
