// 宽松模式：只保留防错性质的基础规则，不限制代码风格（风格交给 Prettier）。
// 提交时的 eslint --fix 仅做自动修复，剩余问题最多告警，不会阻断提交。
module.exports = {
  root: true,
  parser: 'vue-eslint-parser',
  parserOptions: {
    parser: '@typescript-eslint/parser',
    sourceType: 'module',
    ecmaVersion: 2020,
    ecmaFeatures: {
      jsx: true,
    },
  },
  env: {
    browser: true,
    node: true,
    'vue/setup-compiler-macros': true,
  },
  plugins: ['@typescript-eslint'],
  extends: [
    // 仅 Vue 必要的防错规则（非完整 recommended 风格集）
    'plugin:vue/vue3-essential',
    'plugin:@typescript-eslint/recommended',
    'plugin:prettier/recommended',
  ],
  rules: {
    // 格式完全交给 Prettier
    'prettier/prettier': 'off',

    // —— TypeScript：放开实际开发中高频触发的限制 ——
    '@typescript-eslint/no-explicit-any': 'off',
    '@typescript-eslint/ban-types': 'off',
    '@typescript-eslint/no-non-null-assertion': 'off',
    '@typescript-eslint/ban-ts-comment': 'off',
    '@typescript-eslint/no-empty-function': 'off',
    '@typescript-eslint/no-unused-vars': 'warn',
    '@typescript-eslint/no-var-requires': 'off',
    '@typescript-eslint/consistent-type-imports': 'off',

    // —— Vue：放开模板/命名类风格限制 ——
    'vue/multi-word-component-names': 'off',
    'vue/require-default-prop': 'off',
    'vue/singleline-html-element-content-newline': 'off',
    'vue/max-attributes-per-line': 'off',
    'vue/no-v-text': 'off',
    'vue/require-direct-export': 'off',
    'vue/custom-event-name-casing': 'off',
    'vue/padding-line-between-blocks': 'off',
    'vue/html-self-closing': 'off',
    'vue/attributes-order': 'off',

    // —— 通用风格：全部放开 ——
    'no-console': 'off',
    'no-debugger': 'off',
    'no-param-reassign': 'off',
    'no-restricted-syntax': 'off',
    'no-continue': 'off',
    'no-plusplus': 'off',
    'no-underscore-dangle': 'off',
    'no-unused-expressions': 'off',
    'no-nested-ternary': 'off',
  },
};
