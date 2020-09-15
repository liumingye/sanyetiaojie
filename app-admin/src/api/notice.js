import request from '@/utils/request'

let NoticeApi = {
  noticeList(data, errorback) {
    return request._post('notice.notice/lists', data, errorback);
  },
  noticeDetail(data, errorback) {
    return request._post('notice.notice/detail', data, errorback);
  },
  noticeSend(data, errorback) {
    return request._post('notice.notice/send', data, errorback);
  },
}

export default NoticeApi;
