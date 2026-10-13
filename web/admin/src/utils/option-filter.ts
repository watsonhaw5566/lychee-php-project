import type { SelectOptionData } from '@arco-design/web-vue/es/select/interface';

function filterOption(options: SelectOptionData[], value: string | number | null) {
  let label = '';
  // eslint-disable-next-line no-restricted-syntax
  for (const option of options) {
    if (option.value === value) {
      // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
      label = option.label!;
    }
  }
  return label;
}

function filterOptionList(options: SelectOptionData[], values: string[], text: '-') {
  if (values.length === 0) {
    return text;
  }
  const label: string[] = [];
  // eslint-disable-next-line no-restricted-syntax
  for (const option of options) {
    // eslint-disable-next-line no-restricted-syntax
    for (const val of values) {
      if (option.value === val) {
        label.push(option.label!);
      }
    }
  }
  return label.join(' / ');
}

export { filterOption, filterOptionList };
