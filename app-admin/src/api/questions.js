import request from '@/utils/request'

let QuestionsApi = {
    /*问题列表*/
    supportLists(data, errorback) {
        return request._post('support.questions/list', data, errorback);
    },
    /*新增问题*/
    addQuestions(data, errorback) {
        return request._post('support.questions/add', data, errorback);
    },
    /*编辑问题*/
    editQuestions(data, errorback) {
        return request._post('support.questions/edit', data, errorback);
    },
    /*删除问题*/
    delQuestions(data, errorback) {
        return request._post('support.questions/delete', data, errorback);
    },
    /*问题详情*/
    getQuestions(data, errorback) {
        return request._post('support.questions/info', data, errorback);
    }
}
export default QuestionsApi;
