/// <reference types="@rsbuild/core/types" />

declare module '@arco-design/color' {
  export function generate(color: string, options?: { list?: boolean; dark?: boolean }): string[];
  export function getRgbStr(color: string): string;
}

declare module '*.vue' {
  import { DefineComponent } from 'vue';
  // eslint-disable-next-line @typescript-eslint/no-explicit-any, @typescript-eslint/ban-types
  const component: DefineComponent<{}, {}, any>;
  export default component;
}
interface ImportMetaEnv {
  readonly VITE_API_BASE_URL: string;
}
