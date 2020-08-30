import request from '@/utils/request'
/*权限*/
let AuthApi = {
  /*获取当前角色权限*/
  getRoleList(data, errorback) {
    return request._post('auth.user/getRoleList', data, errorback);
  },
  /*获取角色信息*/
  getUserInfo(data, errorback) {
    return request._post('auth.user/getUserInfo', data, errorback);
  },
  /*角色列表*/
  roleList(data, errorback) {
    return request._post('auth.role/index', data, errorback);
  },
  /*添加角色需要的数据*/
  roleAddInfo(data, errorback) {
    return request._get('auth.role/add', data, errorback);
  },
  /*添加角色*/
  roleAdd(data, errorback) {
    return request._post('auth.role/add', data, errorback);
  },
  /*修改角色需要的数据*/
  roleEditInfo(data, errorback) {
    return request._get('auth.role/edit', data, errorback);
  },
  /*修改角色*/
  roleEdit(data, errorback) {
    return request._post('auth.role/edit', data, errorback);
  },
  /*删除角色*/
  roleDelete(data, errorback) {
    return request._post('auth.role/delete', data, errorback);
  },

  /*管理员列表*/
  userList(data, errorback) {
    return request._post('auth.user/index', data, errorback);
  },
  /*添加管理员需要的数据*/
  userAddInfo(data, errorback) {
    return request._post('auth.user/addInfo', data, errorback);
  },
  /*添加管理员*/
  userAdd(data, errorback) {
    return request._post('auth.user/add', data, errorback);
  },
 /*修改管理员需要的数据*/
  userEditInfo(data, errorback) {
    return request._get('auth.user/edit', data, errorback);
  },
  /*修改管理员*/
  userEdit(data, errorback) {
    return request._post('auth.user/edit', data, errorback);
  },
  /*删除管理员*/
  userDelete(data, errorback) {
    return request._post('auth.user/delete', data, errorback);
  },
  /*登录日志*/
  loginlog(data, errorback) {
    return request._post('auth.loginlog/index', data, errorback);
  },
  /*操作日志*/
  optlog(data, errorback) {
    return request._post('auth.optlog/index', data, errorback);
  }
}
export default AuthApi;
