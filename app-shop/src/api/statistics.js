import request from '@/utils/request'

let StatisticsApi = {

  /*数据统计*/
  getStatistics(data, errorback) {
    return request._post('statistics.data/index', data, errorback);
  },
  /*时间段统计*/
  getSurvey(data, errorback) {
    return request._post('statistics.data/survey', data, errorback);
  },
}

export default StatisticsApi;
