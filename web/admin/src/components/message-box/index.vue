<template>
  <div class="message-box">
    <div class="message-box__header">
      <span class="message-box__title">{{ $t('messageBox.tab.title.message') }}</span>
      <a-link
        v-if="messageStore.unreadCount"
        :size="'small'"
        class="message-box__all-read"
        @click="handleAllRead"
      >
        {{ $t('messageBox.allRead') }}
      </a-link>
    </div>
    <a-spin class="message-box__body" :loading="messageStore.loading">
      <a-empty
        v-if="!renderList.length"
        :description="$t('messageBox.noContent')"
        class="message-box__empty"
      />
      <List v-else :render-list="renderList" @item-click="handleItemClick" />
    </a-spin>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { useMessageStore } from '@/store';
import { MessageListType } from '@/api/message';
import List from './list.vue';

const showMax = 5;
const messageStore = useMessageStore();

const renderList = computed(() => messageStore.list.slice(0, showMax));
const handleItemClick = (items: MessageListType) => {
  if (renderList.value.length) messageStore.readMessages([...items]);
};
const handleAllRead = () => {
  const unread = renderList.value.filter((item) => !item.status);
  if (unread.length) messageStore.readMessages(unread);
};
</script>

<style scoped lang="less">
.message-box {
  width: 320px;
}

.message-box__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  border-bottom: 1px solid var(--color-border-2);
}

.message-box__title {
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-1);
}

.message-box__body {
  display: block;
  max-height: 360px;
  overflow-y: auto;
}

.message-box__empty {
  padding: 24px 0 28px;
}
</style>
