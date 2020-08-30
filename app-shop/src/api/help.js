import request from '@/utils/request'
let HelpApi = {
    getlist(data, errorback) {
        return request._post('support.help/lists', data, errorback);
    },
    // orderdetail(data, errorback) {
    //     return request._post('order.mediate/detail', data, errorback);
    // },
    // orderrefund(data, errorback) {
    //     return request._post('order.refund/index', data, errorback);
    // },
    // delivery(data, errorback) {
    //     return request._post('order.mediate/delivery', data, errorback);
    // },
    // confirm(data, errorback) {
    //     return request._post('order.Operate/confirmCancel', data, errorback);
    // },
    // refundDetail(data, errorback) {
    //     return request._get('order.refund/detail', data, errorback);
    // },
    // Approval(data, errorback) {
    //     return request._get('order.refund/audit', data, errorback);
    // },
    // receipt(data, errorback) {
    //     return request._post('order.refund/receipt', data, errorback);
    // },
    // updatePrice(data, errorback) {
    //     return request._post('order.mediate/updatePrice', data, errorback);
    // },
}

export default HelpApi;
