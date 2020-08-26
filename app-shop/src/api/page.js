import request from '@/utils/request'

let PageApi = {
    /*编辑页面*/
    editPage(data, errorback) {
        return request._get('page.page/edit', data, errorback);
    },

    /*保存编辑页面*/
    SavePage(data, errorback) {
        return request._post('page.page/edit', data, errorback);
    },

    /*获取分类*/
    getCategory(data, errorback) {
        return request._get('page.page/category', data, errorback);
    },

    /*提交分类*/
    postCategory(data, errorback) {
        return request._post('page.page/category', data, errorback);
    },


}

export default PageApi;
