/**
 * 金额转换 hook，用于将金额转换为以 "w" 为单位的形式
 * @returns 包含金额转换方法的对象
 */
export default function useMoney() {
  /**
   * 将金额转换为以 "w" 为单位的字符串
   * @param number 金额
   * @param decimal 小数位数，默认为 2
   * @returns 转换后的字符串
   */
  const format = (number: number | string, decimal = 2): string => {
    const num = typeof number === 'string' ? parseFloat(number) : number;

    if (Number.isNaN(num)) {
      return '0';
    }

    if (num >= 10000) {
      return `${(num / 10000).toFixed(decimal)}w`;
    }

    return num.toFixed(decimal);
  };

  return {
    format,
  };
}
