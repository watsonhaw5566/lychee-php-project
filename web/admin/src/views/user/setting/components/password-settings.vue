<template>
  <a-form
    ref="formRef"
    :model="formData"
    class="form"
    :label-col-props="{ span: 8 }"
    :wrapper-col-props="{ span: 16 }"
    :rules="rules"
  >
    <a-form-item field="password" label="请输入密码">
      <a-input-password v-model="formData.password" placeholder="请输入密码" />
    </a-form-item>
    <a-form-item field="confirm_password" label="请输入密码">
      <a-input-password v-model="formData.confirm_password" placeholder="请再输入一次密码" />
    </a-form-item>
    <a-form-item>
      <a-space>
        <a-button type="primary" @click="validate">
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
import { postChangePassword, UpdateUserPassword } from '@/api/user';
import { Message } from '@arco-design/web-vue';
import { ref } from 'vue';

const formRef = ref();

const formData = ref<UpdateUserPassword>({
  password: '',
  confirm_password: '',
});

const validate = async () => {
  const res = await formRef.value?.validate();
  if (!res) {
    const { data } = await postChangePassword(formData.value as UpdateUserPassword);
    if (data) {
      Message.success('修改成功,下次登录请使用新密码');
    } else {
      Message.error(res.msg);
    }
  }
};
const reset = async () => {
  await formRef.value?.resetFields();
};

const rules = {
  password: [
    {
      required: true,
    },
  ],
  password1: [
    {
      required: true,
    },
    {
      validator: (value: any, cb: any) => {
        if (value !== formData.value.password) {
          cb('两次密码不一致，请检查');
        } else {
          cb();
        }
      },
    },
  ],
};
</script>

<style scoped lang="less">
.form {
  width: 540px;
  margin: 0 auto;
}
</style>
