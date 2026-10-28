import { defineStore } from 'pinia';
import { queryMessageList, setMessageStatus, MessageRecord, MessageListType } from '@/api/message';

interface MessageState {
  list: MessageRecord[];
  loading: boolean;
}

const useMessageStore = defineStore('message', {
  state: (): MessageState => ({
    list: [],
    loading: false,
  }),

  getters: {
    unreadCount(state: MessageState): number {
      return state.list.filter((item) => !item.status).length;
    },
    hasUnread(state: MessageState): boolean {
      return state.list.some((item) => !item.status);
    },
  },

  actions: {
    async fetchList() {
      this.loading = true;
      try {
        const { data } = await queryMessageList();
        this.list = data;
      } catch (_err) {
        // ignore
      } finally {
        this.loading = false;
      }
    },
    async readMessages(items: MessageListType) {
      const ids = items.map((item) => item.id);
      await setMessageStatus({ ids });
      this.fetchList();
    },
  },
});

export default useMessageStore;
