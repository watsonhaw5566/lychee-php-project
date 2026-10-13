<template>
  <div class="login-form-wrapper">
    <div class="login-form-title">{{ setting.title }}</div>
    <div class="login-form-sub-title">{{ setting.subTitle }}</div>
    <div class="login-form-error-msg">{{ errorMessage }}</div>
    <a-form
      ref="loginForm"
      :model="userInfo"
      class="login-form"
      layout="vertical"
      @submit="handleSubmit"
    >
      <a-form-item
        field="email"
        :rules="[{ required: true, message: $t('login.form.email.errMsg') }]"
        :validate-trigger="['change', 'blur']"
        hide-label
      >
        <a-input v-model="userInfo.email" :placeholder="$t('login.form.email.placeholder')">
          <template #prefix>
            <icon-email />
          </template>
        </a-input>
      </a-form-item>
      <a-form-item
        field="password"
        :rules="[{ required: true, message: $t('login.form.password.errMsg') }]"
        :validate-trigger="['change', 'blur']"
        hide-label
      >
        <a-input-password
          v-model="userInfo.password"
          :placeholder="$t('login.form.password.placeholder')"
          allow-clear
        >
          <template #prefix>
            <icon-lock />
          </template>
        </a-input-password>
      </a-form-item>
      <a-space :size="16" direction="vertical">
        <!--        <div class="login-form-password-actions">-->
        <!--          <a-checkbox-->
        <!--            checked="rememberPassword"-->
        <!--            :model-value="loginConfig.rememberPassword"-->
        <!--            @change="setRememberPassword as any"-->
        <!--          >-->
        <!--            {{ $t('login.form.rememberPassword') }}-->
        <!--          </a-checkbox>-->
        <!--          <a-link>{{ $t('login.form.forgetPassword') }}</a-link>-->
        <!--        </div>-->
        <a-button type="primary" html-type="submit" long :loading="loading">
          {{ $t('login.form.login') }}
        </a-button>
        <!--        <a-button type="text" long class="login-form-register-btn">-->
        <!--          {{ $t('login.form.register') }}-->
        <!--        </a-button>-->
      </a-space>
    </a-form>
  </div>
</template>

<script lang="ts" setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { Message } from '@arco-design/web-vue';
import { ValidatedError } from '@arco-design/web-vue/es/form/interface';
import { useI18n } from 'vue-i18n';
import { useStorage } from '@vueuse/core';
import { useUserStore } from '@/store';
import useLoading from '@/hooks/loading';
import type { LoginData } from '@/api/user';
import setting from '../../../config/settings.json';

const router = useRouter();
const { t } = useI18n();
const errorMessage = ref('');
const { loading, setLoading } = useLoading();
const userStore = useUserStore();

const loginConfig = useStorage('login-config', {
  rememberPassword: true,
  email: 'admin@qq.com', // 开箱即用默认账号
  password: 'admin123', // 开箱即用默认密码
});
const userInfo = reactive({
  email: loginConfig.value.email,
  password: loginConfig.value.password,
});

const handleSubmit = async ({
  errors,
  values,
}: {
  errors: Record<string, ValidatedError> | undefined;
  values: Record<string, any>;
}) => {
  if (loading.value) return;
  if (!errors) {
    setLoading(true);
    try {
      await userStore.login(values as LoginData);
      const { redirect, ...othersQuery } = router.currentRoute.value.query;
      router.push({
        name: (redirect as string) || 'Workplace',
        query: {
          ...othersQuery,
        },
      });
      Message.success(t('login.form.login.success'));
      const { rememberPassword } = loginConfig.value;
      const { email, password } = values;
      // 实际生产环境需要进行加密存储。
      // The actual production environment requires encrypted storage.
      loginConfig.value.email = rememberPassword ? email : '';
      loginConfig.value.password = rememberPassword ? password : '';
    } catch (err) {
      errorMessage.value = (err as Error).message;
    } finally {
      setLoading(false);
    }
  }
};
const setRememberPassword = (value: boolean) => {
  loginConfig.value.rememberPassword = value;
};
</script>

<style lang="less" scoped>
.login-form {
  &-wrapper {
    width: 320px;
    // 登录页保持经典蓝配色（按钮、输入框聚焦态等），与蓝色 banner 呼应；
    // 内页主色仍为荔枝粉，仅在该容器作用域内覆盖。
    --arcoblue-6: 22, 93, 255;
    --arcoblue-7: 64, 128, 255;
    --arcoblue-8: 14, 66, 210;
  }

  &-title {
    color: var(--color-text-1);
    font-weight: 500;
    font-size: 24px;
    line-height: 32px;
  }

  &-sub-title {
    color: var(--color-text-3);
    font-size: 16px;
    line-height: 24px;
  }

  &-error-msg {
    height: 32px;
    color: rgb(var(--red-6));
    line-height: 32px;
  }

  &-password-actions {
    display: flex;
    justify-content: space-between;
  }

  &-register-btn {
    color: var(--color-text-3) !important;
  }
}
</style>
