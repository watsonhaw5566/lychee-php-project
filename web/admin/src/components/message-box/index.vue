<template>
  <div class="message-box">
    <div class="message-box__header">
      <span class="message-box__title">{{ $t('messageBox.tab.title.message') }}</span>
      <a-link
        v-if="unreadCount"
        :size="'small'"
        class="message-box__all-read"
        @click="handleAllRead"
      >
        {{ $t('messageBox.allRead') }}
      </a-link>
    </div>
    <a-spin class="message-box__body" :loading="loading">
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
import { ref, computed } from 'vue';
import { queryMessageList, setMessageStatus, MessageRecord, MessageListType } from '@/api/message';
import useLoading from '@/hooks/loading';
import List from './list.vue';

const showMax = 5;
const { loading, setLoading } = useLoading(true);
const messageList = ref<MessageRecord[]>([]);

async function fetchSourceData() {
  setLoading(true);
  try {
    const { data } = await queryMessageList();
    messageList.value = data;
  } catch (err) {
    // you can report use errorHandler or other
  } finally {
    setLoading(false);
  }
}
async function readMessage(data: MessageListType) {
  const ids = data.map((item) => item.id);
  await setMessageStatus({ ids });
  fetchSourceData();
}
// 开箱即用阶段不区分消息/通知/待办，统一展示最近的若干条
const renderList = computed(() => messageList.value.slice(0, showMax));
const unreadCount = computed(() => renderList.value.filter((item) => !item.status).length);
const handleItemClick = (items: MessageListType) => {
  if (renderList.value.length) readMessage([...items]);
};
const handleAllRead = () => {
  const unread = renderList.value.filter((item) => !item.status);
  if (unread.length) readMessage(unread);
};
fetchSourceData();
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
