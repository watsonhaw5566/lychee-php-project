module.exports = {
  root: true,
  extends: ['stylelint-config-standard', 'stylelint-config-prettier'],
  rules: {
    // less 语法
    'at-rule-no-unknown': null,
    'no-descending-specificity': null,
    // 允许 BEM 之外的类名写法
    'selector-class-pattern': null,
    // Vue scoped 伪类
    'selector-pseudo-class-no-unknown': [true, { ignorePseudoClasses: ['deep', 'global'] }],
  },
  overrides: [
    {
      // 直接用 postcss-html 解析 .vue 中的 <style>，不依赖额外配置包
      files: ['**/*.vue'],
      customSyntax: 'postcss-html',
      rules: {
        // 允许空的 <style> 占位块
        'no-empty-source': null,
        // Vue 单文件中常见写法
        'selector-pseudo-class-no-unknown': [true, { ignorePseudoClasses: ['deep', 'global'] }],
      },
    },
    {
      files: ['**/*.less'],
      customSyntax: 'postcss-less',
    },
  ],
  ignoreFiles: ['node_modules', 'dist'],
};
