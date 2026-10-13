import { Message } from '@arco-design/web-vue';
import { FileItem } from '@arco-design/web-vue/es/upload/interfaces';

function uploadFileItemFilter(file: FileItem) {
  if (file.response && (file.response as any).code !== 200) {
    const msg = (file.response as any).msg || '上传失败';
    Message.error(msg);
    return [];
  }
  const url = (file.response && (file.response as any).data?.url) || (file as any).url;
  if (url) {
    return [
      {
        uid: file.uid as string,
        url: url as any,
        status: file.status,
      },
    ];
  }
  return [];
}
export function uploadFileListFilter(fileList: FileItem[]) {
  const result: FileItem[] = [];
  for (const item of fileList) {
    if (item.response && (item.response as any).code !== 200) {
      const msg = (item.response as any).msg || '上传失败';
      Message.error(msg);
      break;
    }
    const url =
      (item.response && item.response.code === 200 ? item.response.data.url : (item as any).url) ||
      undefined;
    result.push({
      uid: item.uid as string,
      url: url as any,
      status: item.status || 'done',
    });
  }
  return result;
}
