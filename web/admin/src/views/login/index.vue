<template>
  <div class="container" :style="{ backgroundColor: isDark ? 'rgb(35, 35, 36)' : '' }">
    <div class="logo">
      <img alt="logo" :src="logoUrl" class="logo-img" />
      <div class="logo-text" :style="{ color: isDark && '#ffff' }">
        {{ setting.title }}
      </div>
    </div>
    <LoginBanner />
    <div class="content" :style="{ backgroundColor: isDark ? 'rgb(35, 35, 36)' : '' }">
      <div class="content-inner">
        <LoginForm />
      </div>
      <div class="footer">
        <Footer />
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { isLogin } from '@/utils/auth';
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import useThemes from '@/hooks/themes';
import LoginForm from './components/login-form.vue';
import LoginBanner from './components/banner.vue';
import setting from '../../config/settings.json';
import logoUrl from '@/assets/logo.png';

const router = useRouter();
const { isDark } = useThemes();

onMounted(() => {
  if (isLogin()) {
    router.push('/dashboard/workplace');
  }
});
</script>

<style lang="less" scoped>
.container {
  display: flex;
  height: 100vh;

  .banner {
    width: 50%;
    background: linear-gradient(163.85deg, #1d2129 0%, #00308f 100%);
  }

  .content {
    position: relative;
    display: flex;
    width: 50%;
    align-items: center;
    justify-content: center;
    padding-bottom: 40px;
  }

  .footer {
    position: absolute;
    right: 0;
    bottom: 0;
    width: 100%;
  }
}

.logo {
  position: fixed;
  top: 24px;
  left: 22px;
  z-index: 1;
  display: inline-flex;
  align-items: center;

  &-img {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    object-fit: cover;
  }

  &-text {
    margin-right: 4px;
    margin-left: 8px;
    color: var(--color-fill-1);
    font-size: 20px;
  }
}
</style>

<style lang="less" scoped>
// responsive
@media (max-width: @screen-lg) {
  .container {
    .banner {
      width: 50%;
    }
  }
}

@media (max-width: @screen-md) {
  .container {
    .banner {
      display: none;
    }

    .content {
      width: 100%;
    }
  }
}
</style>
