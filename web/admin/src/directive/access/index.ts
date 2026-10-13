import { useUserStore } from '@/store';
import type { DirectiveBinding } from 'vue';

/**
 * 检查用户行为权限的函数
 * @param el 元素
 * @param binding 指令绑定值
 */
function checkAccess(el: HTMLElement, binding: DirectiveBinding) {
  const { value } = binding;
  const userStore = useUserStore();
  const { access, role } = userStore;

  // 管理员角色直接通过，不执行隐藏
  if (role === 'admin') {
    return;
  }
  if (Array.isArray(value)) {
    if (value.length > 0) {
      const hasAccess = access.some((item) => value.includes(item));
      if (!hasAccess && el.parentNode) {
        el.parentNode.removeChild(el);
      }
    }
  } else {
    throw new Error(`need access! Like v-access="['customer','customer.add']"`);
  }
}

// action 指令
export default {
  mounted(el: HTMLElement, binding: DirectiveBinding) {
    checkAccess(el, binding);
  },
  updated(el: HTMLElement, binding: DirectiveBinding) {
    checkAccess(el, binding);
  },
};
