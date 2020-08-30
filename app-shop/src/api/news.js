import request from '@/utils/request'

let NewsApi = {
    /*新闻列表*/
    supportLists(data, errorback) {
        return request._post('support.news/list', data, errorback);
    },
    /*新增新闻*/
    addNews(data, errorback) {
        return request._post('support.news/add', data, errorback);
    },
    /*编辑新闻*/
    editNews(data, errorback) {
        return request._post('support.news/edit', data, errorback);
    },
    /*删除新闻*/
    delNews(data, errorback) {
        return request._post('support.news/delete', data, errorback);
    },
    /*新闻详情*/
    getNews(data, errorback) {
        return request._post('support.news/info', data, errorback);
    }
}
export default NewsApi;
