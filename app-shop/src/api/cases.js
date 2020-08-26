import request from '@/utils/request'

let CasesApi = {

    /*分类管理*/
    catList(data, errorback) {
        return request._post('support.casescat/index', data, errorback);
    },
    /*分类添加*/
    catAdd(data, errorback) {
        return request._post('support.casescat/add', data, errorback);
    },
    /*分类删除*/
    catDel(data, errorback) {
        return request._post('support.casescat/delete', data, errorback);
    },
    /*分类修改*/
    catEdit(data, errorback) {
        return request._post('support.casescat/edit', data, errorback);
    },
    /*法律法规列表*/
    supportLists(data, errorback) {
        return request._post('support.cases/list', data, errorback);
    },
    /*基础数据*/
    getBaseData(data, errorback) {
        return request._post('support.cases/getBaseData', data, errorback);
    },
    /*新增法律*/
    addLaw(data, errorback) {
        return request._post('support.cases/add', data, errorback);
    },
    /*编辑法律*/
    editLaw(data, errorback) {
        return request._post('support.cases/edit', data, errorback);
    },
    /*删除法律*/
    delLaw(data, errorback) {
        return request._post('support.cases/delete', data, errorback);
    },
    /*法律详情*/
    getLaw(data, errorback) {
        return request._post('support.cases/info', data, errorback);
    }
}

export default CasesApi;
