import { generate, getRgbStr } from '@arco-design/color';

/** 主题色在 localStorage 中的键名 */
export const THEME_COLOR_STORAGE_KEY = 'app-theme-color';

/** 读取用户在页面配置中选择并持久化的主题色，未设置时返回 null */
export function getStoredThemeColor(): string | null {
  return localStorage.getItem(THEME_COLOR_STORAGE_KEY);
}

/** 持久化主题色 */
export function storeThemeColor(color: string) {
  localStorage.setItem(THEME_COLOR_STORAGE_KEY, color);
}

/**
 * 根据主题色生成 10 级色阶，覆盖 Arco 的 arcoblue CSS 变量。
 * Arco 组件内部以 rgb(var(--arcoblue-6)) 引用主色，
 * 且 --primary-6 等业务变量均映射自 arcoblue 色板。
 * 同时处理亮色与暗色（[arco-theme="dark"]）两套色板。
 */
export function applyThemeColor(color: string) {
  const lightColors = generate(color, { list: true }) as string[];
  const darkColors = generate(color, { list: true, dark: true }) as string[];

  const buildRules = (selector: string, colors: string[]) =>
    `${selector}{${colors.map((c, index) => `--arcoblue-${index + 1}:${getRgbStr(c)}`).join(';')}}`;

  // Arco 的色板变量声明在 body / body[arco-theme='dark'] 上，
  // 自定义属性在元素自身声明会屏蔽从 :root(html) 继承的值，
  // 因此必须用更高特异性的选择器直接覆盖 body 层级。
  const css = [
    buildRules('html body', lightColors),
    buildRules("html body[arco-theme='dark']", darkColors),
  ].join('');

  let style = document.getElementById('app-theme-color') as HTMLStyleElement | null;
  if (!style) {
    style = document.createElement('style');
    style.id = 'app-theme-color';
    document.head.appendChild(style);
  }
  style.innerHTML = css;
}
