<template>
  <a-card :bordered="false">
    <a-space :size="54" wrap>
      <a-upload
        :custom-request="customRequest"
        list-type="picture-card"
        :file-list="fileList"
        :show-upload-button="true"
        :show-file-list="false"
        accept="image/*"
        @change="uploadChange"
      >
        <template #upload-button>
          <a-avatar :size="100" class="info-avatar">
            <template #trigger-icon>
              <icon-camera />
            </template>
            <img
              v-if="fileList.length && fileList[0].url"
              :src="fileList[0].url"
              alt="user avatar"
            />
            <span v-else class="avatar-text">{{ avatarText }}</span>
          </a-avatar>
        </template>
      </a-upload>
      <a-descriptions
        :data="renderData"
        :column="1"
        layout="inline-horizontal"
        :label-style="{
          width: '96px',
          fontWeight: 'normal',
          color: 'var(--color-text-3)',
        }"
        :value-style="{
          paddingLeft: '8px',
        }"
      >
        <template #label="{ label }">{{ $t(label) }} :</template>
      </a-descriptions>
    </a-space>
  </a-card>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue';
import type { FileItem, RequestOption } from '@arco-design/web-vue/es/upload/interfaces';
import { useUserStore } from '@/store';
import { userUploadApi } from '@/api/user-center';
import type { DescData } from '@arco-design/web-vue/es/descriptions/interface';

const userStore = useUserStore();
const fileList = ref<FileItem[]>(
  userStore.avatar ? [{ uid: '-1', name: 'avatar.png', url: userStore.avatar }] : [],
);

// 未上传头像时，用用户名首字符作为头像
const avatarText = computed(() => (userStore.name || '?').charAt(0).toUpperCase());

const renderData = computed<DescData[]>(() => [
  {
    label: 'userSetting.label.name',
    value: userStore.name || '-',
  },
  {
    label: 'userSetting.label.accountId',
    value: userStore.accountId || '-',
  },
  {
    label: 'userSetting.label.registrationDate',
    value: userStore.registrationDate || '-',
  },
]);

const uploadChange = (_fileItemList: FileItem[], fileItem: FileItem) => {
  fileList.value = [fileItem];
};

const customRequest = (options: RequestOption) => {
  // docs: https://axios-http.com/docs/cancellation
  const controller = new AbortController();

  (async function requestWrap() {
    const { onProgress, onError, onSuccess, fileItem, name = 'file' } = options;
    onProgress(20);
    const formData = new FormData();
    formData.append(name as string, fileItem.file as Blob);
    const onUploadProgress = (event: ProgressEvent) => {
      let percent;
      if (event.total > 0) {
        percent = (event.loaded / event.total) * 100;
      }
      onProgress(parseInt(String(percent), 10), event);
    };

    try {
      const res = await userUploadApi(formData, {
        controller,
        onUploadProgress,
      });
      onSuccess(res);
      fileList.value = [{ uid: fileItem.uid, name: fileItem.name, url: res.data.url }];
      userStore.setInfo({
        avatar: res.data.url,
      });
    } catch (error) {
      onError(error);
    }
  })();

  return {
    abort() {
      controller.abort();
    },
  };
};
</script>

<style scoped lang="less">
.arco-card {
  border-radius: 4px;
}

.info-avatar {
  font-size: 40px;
  color: #fff;
  background-color: rgb(var(--arcoblue-6));
}

:deep(.arco-avatar-trigger-icon-button) {
  width: 32px;
  height: 32px;
  line-height: 32px;
  background-color: rgb(var(--arcoblue-1));

  .arco-icon-camera {
    margin-top: 8px;
    color: rgb(var(--arcoblue-6));
    font-size: 14px;
  }
}
</style>
