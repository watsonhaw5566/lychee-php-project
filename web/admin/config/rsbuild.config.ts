import { defineConfig } from '@rsbuild/core';
import { pluginBabel } from '@rsbuild/plugin-babel';
import { pluginVue } from '@rsbuild/plugin-vue';
import { pluginVueJsx } from '@rsbuild/plugin-vue-jsx';
import { pluginLess } from '@rsbuild/plugin-less';
import { pluginSvgr } from '@rsbuild/plugin-svgr';
import { resolve } from 'path';
import { pluginImageCompress } from '@rsbuild/plugin-image-compress';
import configArcoResolverPlugin from './plugin/arcoResolver';

export default defineConfig({
  html: {
    template: './index.html',
    favicon: './src/assets/logo.png',
  },
  source: {
    entry: {
      index: './src/main.ts',
    },
    // 样式按需引入
    transformImport: [
      {
        libraryName: '@arco-design/web-vue',
        libraryDirectory: 'es',
        style: 'css',
      },
    ],
  },
  resolve: {
    // 别名
    alias: {
      '@': resolve(__dirname, '../src'),
      assets: resolve(__dirname, '../src/assets'),
      vue: 'vue/dist/vue.esm-bundler.js', // compile template
      'vue-i18n': 'vue-i18n/dist/vue-i18n.cjs.js', // Resolve the i18n warning issue
    },
  },
  // 构建产物直接输出到后端 public/admin，由 PHP 站点以 /admin 子目录提供
  output: {
    // 构建产物输出到项目根目录的 public/admin 子目录
    // 整个 admin 目录均为前端生成，可安全整体清理重建
    distPath: {
      root: resolve(__dirname, '../../../public/admin'),
    },
    // 静态资源 URL 前缀，与 Vue Router base 保持一致
    assetPrefix: '/admin/',
  },
  // 生产优化
  performance: {
    removeConsole: true,
  },
  // Rsbuild v2：split-by-experience 策略迁移为 splitChunks.preset: 'default'
  splitChunks: {
    preset: 'default',
  },
  tools: {
    rspack: {
      plugins: [configArcoResolverPlugin()],
    },
  },
  plugins: [
    pluginSvgr(),
    // 处理 Vue jsx
    pluginBabel({
      include: /\.(?:jsx|tsx)$/,
    }),
    pluginVueJsx(),
    pluginVue(),
    pluginImageCompress(),
    pluginLess({
      lessLoaderOptions: {
        lessOptions: {
          modifyVars: {
            hack: `true; @import (reference) "${resolve('src/assets/style/breakpoint.less')}";`,
          },
        },
      },
    }),
  ],
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        secure: false,
      },
      '/storage': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        secure: false,
      },
    },
  },
});
