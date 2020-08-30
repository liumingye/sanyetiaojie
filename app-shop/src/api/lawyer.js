import request from '@/utils/request'

let LawyerApi = {

    /*分类管理*/
    catList(data, errorback) {
        return request._post('support.lawyercat/index', data, errorback);
    },
    /*分类添加*/
    catAdd(data, errorback) {
        return request._post('support.lawyercat/add', data, errorback);
    },
    /*分类删除*/
    catDel(data, errorback) {
        return request._post('support.lawyercat/delete', data, errorback);
    },
    /*分类修改*/
    catEdit(data, errorback) {
        return request._post('support.lawyercat/edit', data, errorback);
    },
    /*律师法规列表*/
    supportLists(data, errorback) {
        return request._post('support.lawyer/list', data, errorback);
    },
    /*基础数据*/
    getBaseData(data, errorback) {
        return request._post('support.lawyer/getBaseData', data, errorback);
    },
    /*新增律师*/
    addLawyer(data, errorback) {
        return request._post('support.lawyer/add', data, errorback);
    },
    /*编辑律师*/
    editLawyer(data, errorback) {
        return request._post('support.lawyer/edit', data, errorback);
    },
    /*删除律师*/
    delLawyer(data, errorback) {
        return request._post('support.lawyer/delete', data, errorback);
    },
    /*律师详情*/
    getLawyer(data, errorback) {
        return request._post('support.lawyer/info', data, errorback);
    }
}

export default LawyerApi;
