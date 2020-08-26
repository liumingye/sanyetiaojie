import request from '@/utils/request'

let LawApi = {

    /*分类管理*/
    catList(data, errorback) {
        return request._post('support.lawcat/index', data, errorback);
    },
    /*分类添加*/
    catAdd(data, errorback) {
        return request._post('support.lawcat/add', data, errorback);
    },
    /*分类删除*/
    catDel(data, errorback) {
        return request._post('support.lawcat/delete', data, errorback);
    },
    /*分类修改*/
    catEdit(data, errorback) {
        return request._post('support.lawcat/edit', data, errorback);
    },
    /*法律法规列表*/
    supportLists(data, errorback) {
        return request._post('support.law/list', data, errorback);
    },
    /*基础数据*/
    getBaseData(data, errorback) {
        return request._post('support.law/getBaseData', data, errorback);
    },
    /*新增法律*/
    addLaw(data, errorback) {
        return request._post('support.law/add', data, errorback);
    },
    /*编辑法律*/
    editLaw(data, errorback) {
        return request._post('support.law/edit', data, errorback);
    },
    /*删除法律*/
    delLaw(data, errorback) {
        return request._post('support.law/delete', data, errorback);
    },
    /*法律详情*/
    getLaw(data, errorback) {
        return request._post('support.law/info', data, errorback);
    }
}

export default LawApi;
