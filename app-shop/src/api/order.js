import request from '@/utils/request'
let OrderApi = {
  /*案件列表*/
  orderlist(data, errorback) {
    return request._post('order.mediate/lists', data, errorback);
  },
  /*案件详情*/
  orderdetail(data, errorback) {
    return request._post('order.mediate/detail', data, errorback);
  },
  /*获取人员列表*/
  userList(data, errorback) {
    return request._post('auth.user/index', data, errorback);
  },
  /*设置步骤*/
  setWay(data, errorback) {
    return request._post('order.mediate/setway', data, errorback);
  },
  /*调解完成*/
  setComplete(data, errorback) {
    return request._post('order.mediate/setcomplete', data, errorback);
  },
  /*调解失败*/
  setFail(data, errorback) {
    return request._post('order.mediate/setfail', data, errorback);
  },
  /*设置人员*/
  setRole(data, errorback) {
    return request._post('order.mediate/setrole', data, errorback);
  },
  /*获取文档*/
  getWord() {
    return request.getbaseURL() + 'order.mediate/getword';
  },
  /*添加进度*/
  addSchedule(data, errorback) {
    return request._post('order.mediate/addschedule', data, errorback);
  },
  /*删除进度*/
  delSchedule(data, errorback) {
    return request._post('order.mediate/delschedule', data, errorback);
  },
  /*获取通知ID*/
  getNotice(data, errorback) {
    return request._post('order.mediate/getnotice', data, errorback);
  },
  /*编辑详情*/
  editInfo(data, errorback) {
    return request._post('order.mediate/editInfo', data, errorback);
  },
}

export default OrderApi;
