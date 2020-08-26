import request from '@/utils/request'

let IndexApi = {

  /*商品统计*/
  getCount(data, errorback) {
    return request._post('index/index', data, errorback);
  },

}

export default IndexApi;
