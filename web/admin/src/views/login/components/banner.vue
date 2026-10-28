<template>
  <div class="banner">
    <div class="banner-inner">
      <img class="banner-logo" :src="logoUrl" alt="logo" />
      <div class="banner-title">{{ setting.title }}</div>
      <div class="banner-sub-title">{{ setting.bannerSubTitle }}</div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import logoUrl from '@/assets/logo.png';
import setting from '../../../config/settings.json';
</script>

<style lang="less" scoped>
.banner {
  display: flex;
  align-items: center;
  justify-content: center;

  &-inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0 40px;
    text-align: center;
  }

  &-logo {
    width: 168px;
    height: 168px;
    object-fit: contain;
  }

  &-title {
    margin-top: 28px;
    color: #fff;
    font-weight: 600;
    font-size: 30px;
    line-height: 42px;
  }

  &-sub-title {
    margin-top: 12px;
    color: rgb(255 255 255 / 75%);
    font-size: 15px;
    line-height: 24px;
  }
}

@media (max-height: 640px) {
  .banner-logo {
    width: 120px;
    height: 120px;
  }
}
</style>
