<?php

namespace app\api\model\support;

use app\common\model\support\News as NewsModel;

class News extends NewsModel
{
    protected $type = [
        'create_time'    =>  'timestamp:Y-m-d',
    ];
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'status',
        'update_time'
    ];
}
