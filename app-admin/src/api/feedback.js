import request from '@/utils/request'
let FeedbackApi = {
  /*意见列表*/
  getlist(data, errorback) {
    return request._post('user.feedback/lists', data, errorback);
  },
  /*意见详情*/
  feedbackdetail(data, errorback) {
    return request._post('user.feedback/detail', data, errorback);
  },
}

export default FeedbackApi;
