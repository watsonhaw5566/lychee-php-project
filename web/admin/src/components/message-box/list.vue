<template>
  <ul class="message-list">
    <li
      v-for="item in renderList"
      :key="item.id"
      class="message-item"
      :class="{ 'is-read': item.status }"
      @click="onItemClick(item)"
    >
      <a-avatar :size="36" class="message-item__avatar">
        <img v-if="item.avatar" :src="item.avatar" alt="avatar" />
        <icon-notification v-else />
      </a-avatar>
      <div class="message-item__main">
        <div class="message-item__top">
          <span class="message-item__title">
            {{ item.title }}
            <span v-if="item.subTitle" class="message-item__subtitle">{{ item.subTitle }}</span>
          </span>
          <span class="message-item__time">{{ item.time }}</span>
        </div>
        <p class="message-item__desc">{{ item.content }}</p>
      </div>
      <span v-if="!item.status" class="message-item__dot" />
    </li>
  </ul>
</template>

<script lang="ts" setup>
import { PropType } from 'vue';
import { MessageRecord, MessageListType } from '@/api/message';

const props = defineProps({
  renderList: {
    type: Array as PropType<MessageListType>,
    required: true,
  },
});
const emit = defineEmits(['itemClick']);

const onItemClick = (item: MessageRecord) => {
  if (!item.status) {
    emit('itemClick', [item]);
  }
};
</script>

<style scoped lang="less">
.message-list {
  margin: 0;
  padding: 0 0 4px;
  list-style: none;
}

.message-item {
  position: relative;
  display: flex;
  gap: 10px;
  padding: 10px 16px;
  cursor: pointer;

  &:hover {
    background-color: var(--color-fill-2);
  }

  &.is-read {
    .message-item__title,
    .message-item__desc {
      color: var(--color-text-3);
    }
  }
}

.message-item__avatar {
  flex-shrink: 0;
}

.message-item__main {
  flex: 1;
  min-width: 0;
}

.message-item__top {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  gap: 8px;
}

.message-item__title {
  overflow: hidden;
  font-size: 13px;
  font-weight: 500;
  color: var(--color-text-1);
  text-overflow: ellipsis;
  white-space: nowrap;
}

.message-item__subtitle {
  margin-left: 2px;
  font-weight: 400;
  color: var(--color-text-3);
}

.message-item__time {
  flex-shrink: 0;
  font-size: 12px;
  color: var(--color-text-3);
}

.message-item__desc {
  margin: 2px 0 0;
  overflow: hidden;
  font-size: 12px;
  line-height: 18px;
  color: var(--color-text-2);
  text-overflow: ellipsis;
  white-space: nowrap;
}

.message-item__dot {
  position: absolute;
  top: 8px;
  left: 46px;
  width: 8px;
  height: 8px;
  border: 2px solid var(--color-bg-popup);
  border-radius: 50%;
  background-color: rgb(var(--red-6));
}
</style>
