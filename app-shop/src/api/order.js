import request from '@/utils/request'
let OrderApi = {
  /*订单列表*/
  orderlist(data, errorback) {
    return request._post('order.mediate/lists', data, errorback);
  },
  /*订单详情*/
  orderdetail(data, errorback) {
    return request._post('order.mediate/detail', data, errorback);
  },
  setway(data, errorback) {
    return request._post('order.mediate/setway', data, errorback);
  },
  setComplete(data, errorback) {
    return request._post('order.mediate/setcomplete', data, errorback);
  },
  searchLawyer(data, errorback) {
    return request._post('order.mediate/searchlawyer', data, errorback);
  },
  searchCommittee(data, errorback) {
    return request._post('order.mediate/searchcommittee', data, errorback);
  },
  setRole(data, errorback) {
    return request._post('order.mediate/setrole', data, errorback);
  },
  getWord() {
    return request.getbaseURL() + 'order.mediate/getword';
  },
  // /*售后管理*/
  // orderrefund(data, errorback) {
  //     return request._post('order.refund/index', data, errorback);
  // },
  // /*去发货*/
  // delivery(data, errorback) {
  //     return request._post('order.mediate/delivery', data, errorback);
  // },
  // /*确认审核*/
  // confirm(data, errorback) {
  //     return request._post('order.Operate/confirmCancel', data, errorback);
  // },
  // /*售后详情*/
  // refundDetail(data, errorback) {
  //     return request._get('order.refund/detail', data, errorback);
  // },
  // /*售后审核*/
  // Approval(data, errorback) {
  //     return request._get('order.refund/audit', data, errorback);
  // },
  // /*确认收货并退款*/
  // receipt(data, errorback) {
  //     return request._post('order.refund/receipt', data, errorback);
  // },
  // /*修改价格*/
  // updatePrice(data, errorback) {
  //     return request._post('order.mediate/updatePrice', data, errorback);
  // },
}

export default OrderApi;
