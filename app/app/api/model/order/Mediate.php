<?php

namespace app\api\model\order;

use app\common\model\order\Mediate as MediateModel;

class Mediate extends MediateModel
{
    
    protected $type = [
        'create_time'    =>  'timestamp:Y-m-d',
    ];

    /**
     * 获取总数
     */
    public function getCount($user_id, $type = 'all')
    {
        // 筛选条件
        $filter = [];
        $filter['uid'] = $user_id;
        // 订单数据类型
        switch ($type) {
            case 'accepting';
                $filter['status'] = 1;
                break;
            case 'adjusting';
                $filter['status'] = 2;
                break;
            case 'adjusted';
                $filter['status'] = 3;
                break;
        }
        return $this->where($filter)->count();
    }
}
