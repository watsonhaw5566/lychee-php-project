<template>
  <a-form
    ref="formRef"
    :model="formData"
    class="form"
    :label-col-props="{ span: 8 }"
    :wrapper-col-props="{ span: 16 }"
  >
    <a-form-item
      field="nickname"
      :label="$t('userSetting.basicInfo.form.label.nickname')"
      :rules="[{ required: true, message: $t('userSetting.form.error.nickname.required') }]"
    >
      <a-input
        v-model="formData.nickname"
        :placeholder="$t('userSetting.basicInfo.placeholder.nickname')"
        :max-length="50"
      />
    </a-form-item>
    <a-form-item
      field="email"
      :label="$t('userSetting.basicInfo.form.label.email')"
      :rules="[
        { required: true, message: $t('userSetting.form.error.email.required') },
        {
          match: /^[\\w-]+(\\.[\\w-]+)*@[\\w-]+(\\.[\\w-]+)+$/,
          message: $t('userSetting.form.error.email.format'),
        },
      ]"
    >
      <a-input
        v-model="formData.email"
        :placeholder="$t('userSetting.basicInfo.placeholder.email')"
      />
    </a-form-item>
    <a-form-item>
      <a-space>
        <a-button type="primary" :loading="saving" @click="validate">
          {{ $t('userSetting.save') }}
        </a-button>
        <a-button type="secondary" @click="reset">
          {{ $t('userSetting.reset') }}
        </a-button>
      </a-space>
    </a-form-item>
  </a-form>
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import { FormInstance } from '@arco-design/web-vue/es/form';
import { Message } from '@arco-design/web-vue';
import { BasicInfoModel } from '@/api/user-center';
import { useUserStore } from '@/store';
import { postChangeInfo } from '@/api/user';

const userStore = useUserStore();

const formRef = ref<FormInstance>();
const saving = ref(false);
const formData = ref<BasicInfoModel>({
  email: userStore.email || '',
  nickname: userStore.name || '',
});

const validate = async () => {
  const errors = await formRef.value?.validate();
  if (errors) return;

  saving.value = true;
  try {
    await postChangeInfo(formData.value);
    Message.success('保存成功');
    await userStore.info();
  } finally {
    saving.value = false;
  }
};

const reset = () => {
  formData.value = {
    email: userStore.email || '',
    nickname: userStore.name || '',
  };
  formRef.value?.clearValidate();
};
</script>

<style scoped lang="less">
.form {
  width: 540px;
  max-width: 100%;
  margin: 0 auto;
}
</style>
