<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
  icon: string;
  size?: number | string;
  class?: string;
  style?: Record<string, any>;
}>();

const isFontAwesome = computed(() => {
  const icon = props.icon || '';
  return icon.startsWith('fa-') || icon.startsWith('fa ');
});

const faClass = computed(() => {
  const icon = props.icon || '';
  if (icon.startsWith('fa-')) {
    return `fa ${icon}`;
  }
  return icon;
});

const fontSizeStyle = computed(() => {
  if (props.size !== undefined) {
    const sizeVal = typeof props.size === 'number' ? `${props.size}px` : props.size;
    return { fontSize: sizeVal };
  }
  return {};
});

const arcoComponentName = computed(() => {
  return props.icon;
});
</script>

<template>
  <i
    v-if="isFontAwesome"
    :class="[faClass, props.class]"
    :style="{ ...style, ...fontSizeStyle }"
    aria-hidden="true"
  ></i>
  <component
    v-else
    :is="arcoComponentName"
    :class="props.class"
    :style="{ ...style, ...fontSizeStyle }"
  />
</template>
