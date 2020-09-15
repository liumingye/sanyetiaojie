import request from '@/utils/request'

let ProductApi = {
  /*分类管理*/
  catList(data, errorback) {
    return request._post('product.category/index', data, errorback);
  },
  /*分类添加*/
  catAdd(data, errorback) {
    return request._post('product.category/add', data, errorback);
  },
  /*分类删除*/
  catDel(data, errorback) {
    return request._post('product.category/delete', data, errorback);
  },
  /*分类修改*/
  catEdit(data, errorback) {
    return request._post('product.category/edit', data, errorback);
  },
}

export default ProductApi;
