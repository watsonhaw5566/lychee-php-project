<template>
  <a-form
    ref="formRef"
    :model="formData"
    class="form"
    :label-col-props="{ span: 8 }"
    :wrapper-col-props="{ span: 16 }"
  >
    <a-form-item
      field="password"
      label="新密码"
      :rules="[
        { required: true, message: '请输入新密码' },
        { minLength: 6, message: '密码长度不能少于 6 位' },
      ]"
    >
      <a-input-password
        v-model="formData.password"
        placeholder="请输入新密码，至少 6 位"
        :max-length="32"
      />
    </a-form-item>
    <a-form-item
      field="confirm_password"
      label="确认新密码"
      :rules="[{ required: true, message: '请再次输入新密码' }, { validator: validateConfirm }]"
    >
      <a-input-password
        v-model="formData.confirm_password"
        placeholder="请再输入一次新密码"
        :max-length="32"
      />
    </a-form-item>
    <a-form-item>
      <a-space>
        <a-button type="primary" :loading="saving" @click="handleSubmit">
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
import type { FormInstance } from '@arco-design/web-vue/es/form';
import { Message } from '@arco-design/web-vue';
import { postChangePassword, UpdateUserPassword } from '@/api/user';

const formRef = ref<FormInstance>();
const saving = ref(false);

const formData = ref<UpdateUserPassword>({
  password: '',
  confirm_password: '',
});

const validateConfirm = (value: string, callback: (error?: string) => void) => {
  if (value !== formData.value.password) {
    callback('两次输入的密码不一致');
  } else {
    callback();
  }
};

const handleSubmit = async () => {
  const errors = await formRef.value?.validate();
  if (errors) return;

  saving.value = true;
  try {
    await postChangePassword(formData.value);
    Message.success('修改成功，下次登录请使用新密码');
    reset();
  } finally {
    saving.value = false;
  }
};

const reset = () => {
  formData.value = {
    password: '',
    confirm_password: '',
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
