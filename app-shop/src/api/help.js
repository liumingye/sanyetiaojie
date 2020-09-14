import request from '@/utils/request'
let HelpApi = {
  /*援助列表*/
  getlist(data, errorback) {
    return request._post('support.help/lists', data, errorback);
  },
  getNotice(data, errorback) {
    return request._post('support.help/getnotice', data, errorback);
  },
  /*援助详情*/
  helpdetail(data, errorback) {
    return request._post('support.help/detail', data, errorback);
  },
  /*设置人员*/
  setRole(data, errorback) {
    return request._post('support.help/setrole', data, errorback);
  },
  /*获取人员列表*/
  userList(data, errorback) {
    return request._post('auth.user/index', data, errorback);
  },
  /*设置步骤*/
  setWay(data, errorback) {
    return request._post('support.help/setway', data, errorback);
  },
  /*调解完成*/
  setComplete(data, errorback) {
    return request._post('support.help/setcomplete', data, errorback);
  },
  /*编辑详情*/
  editInfo(data, errorback) {
    return request._post('support.help/editInfo', data, errorback);
  },
  /*删除附件*/
  delImage(data, errorback) {
    return request._post('support.help/delImage', data, errorback);
  },
}

export default HelpApi;
