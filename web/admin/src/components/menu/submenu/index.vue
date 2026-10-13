<script lang="ts" setup>
import { useI18n } from 'vue-i18n';
import type { RouteRecordRaw } from 'vue-router';
import { useRouter } from 'vue-router';
import { openWindow, regexUrl } from '@/utils';

defineProps<{
  item: RouteRecordRaw;
  basePath?: string;
}>();

const { t } = useI18n();
const router = useRouter();

const goto = (item: RouteRecordRaw) => {
  // Open external link
  if (regexUrl.test(item.path)) {
    openWindow(item.path);
    return;
  }
  // Trigger router change
  router.push({
    name: item.name,
  });
};

const getFullPath = (item: RouteRecordRaw) => {
  return item.path;
};
</script>

<template>
  <a-sub-menu v-if="item.children && item.children.length !== 0" :key="item.name">
    <template #icon>
      <IconWrapper v-if="item?.meta?.icon" :icon="item.meta.icon" />
    </template>
    <template #title>{{ t(item?.meta?.locale || '') }}</template>
    <template v-for="child in item.children" :key="child.name">
      <SubMenu v-if="child.children?.length" :base-path="getFullPath(child)" :item="child" />
      <a-menu-item v-else :key="child.name" @click="goto(child)">
        <template v-if="child?.meta?.icon" #icon>
          <IconWrapper :icon="child.meta.icon" />
        </template>
        {{ t(child?.meta?.locale || '') }}
      </a-menu-item>
    </template>
  </a-sub-menu>
  <a-menu-item v-else :key="item.name" @click="goto(item)">
    <template #icon>
      <IconWrapper v-if="item?.meta?.icon" :icon="item.meta.icon" />
    </template>
    {{ t(item?.meta?.locale || '') }}
  </a-menu-item>
</template>
