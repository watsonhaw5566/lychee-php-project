import { computed, reactive, ref } from 'vue';
import { Pagination } from '@/types/global';
import type { TableRowSelection } from '@arco-design/web-vue/es/table/interface';

/**
 * 表格管理钩子
 * @param T 选中行的键类型
 * @param D 表格数据项类型
 * @param defaultPageSize 默认分页大小
 */
export default function useTable<T = number, D = any>(defaultPageSize = 20) {
  // 基础分页配置
  const basePagination: Pagination = {
    current: 1,
    pageSize: defaultPageSize,
    showPageSize: true,
    showTotal: true,
    pageSizeOptions: [20, 50, 100, 200],
  };

  // 响应式状态
  const pagination = reactive<Pagination>({
    ...basePagination,
  });

  const rowSelection = reactive<TableRowSelection>({
    type: 'checkbox',
    showCheckedAll: true,
    onlyCurrent: false,
  });

  const selectedRowKeys = ref<T[]>([]);
  const dataSource = ref<D[]>([]);
  const loading = ref(false);
  const total = ref(0);
  const sorter = ref<any>({});
  const filters = ref<any>({});
  const searchKeyword = ref('');

  // 计算属性
  const getQueryParams = computed(() => ({
    current: pagination.current,
    pageSize: pagination.pageSize,
    keyword: searchKeyword.value,
    sorter: sorter.value,
    filters: filters.value,
  }));

  const isAllSelected = computed(() => {
    return selectedRowKeys.value.length === dataSource.value.length && dataSource.value.length > 0;
  });

  const isPartialSelected = computed(() => {
    const hasSelection = selectedRowKeys.value.length > 0;
    return hasSelection && !isAllSelected.value;
  });

  const getSelectedRows = computed(() => {
    if (!dataSource.value.length || !selectedRowKeys.value.length) return [];
    return dataSource.value.filter((item: any) =>
      selectedRowKeys.value.includes(item.id || item.key || item),
    );
  });

  // 方法
  const resetPagination = () => {
    Object.assign(pagination, basePagination);
  };

  const resetSelection = () => {
    selectedRowKeys.value = [];
  };

  const resetFilters = () => {
    filters.value = {};
    sorter.value = {};
    searchKeyword.value = '';
  };

  const reset = () => {
    resetPagination();
    resetSelection();
    resetFilters();
  };

  const fetchData = async (apiFn: Function, params?: any) => {
    try {
      loading.value = true;
      const response = await apiFn({
        ...getQueryParams.value,
        ...params,
      });
      dataSource.value = response.list || [];
      total.value = response.total || 0;
      return response;
    } catch (error) {
      console.error('Failed to fetch table data:', error);
      throw error;
    } finally {
      loading.value = false;
    }
  };

  const refresh = () => {
    // 此处可以提供一个默认的刷新实现
    // 或者由用户在使用时自行实现
  };

  const handlePageChange = (current: number, pageSize: number) => {
    pagination.current = current;
    pagination.pageSize = pageSize;
    resetSelection(); // 切换页码时清空选择
  };

  const toggleSelectionMode = (type: 'checkbox' | 'radio') => {
    rowSelection.type = type;
    resetSelection(); // 切换模式时清空选择
  };

  return {
    // 响应式状态
    selectedRowKeys,
    rowSelection,
    pagination,
    basePagination,
    dataSource,
    loading,
    total,
    sorter,
    filters,
    searchKeyword,

    // 计算属性
    getQueryParams,
    isAllSelected,
    isPartialSelected,
    getSelectedRows,

    // 方法
    fetchData,
    refresh,
    handlePageChange,
    resetPagination,
    resetSelection,
    resetFilters,
    reset,
    toggleSelectionMode,
  };
}
