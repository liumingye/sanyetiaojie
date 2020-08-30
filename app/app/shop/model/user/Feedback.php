<?php

namespace app\shop\model\user;

use app\common\model\user\Feedback as FeedbackModel;

class Feedback extends FeedbackModel
{

    public function getList($params = null)
    {
        $model = $this;
        //搜索时间段
        if (isset($params['create_time']) && $params['create_time'] != '') {
            $sta_time = array_shift($params['create_time']);
            $end_time = array_pop($params['create_time']);
            $model = $model->whereBetweenTime('create_time', $sta_time, $end_time);
        }
        // 获取数据列表
        return $model
            ->with(['user'=>function($query)
            {
                $query->field('user_id,nickName');
            }])
            ->order(['create_time' => 'desc'])
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);
    }

    public function getFeedbackTotal($where = [])
    {
        return $this->where($where)->count();
    }

}
