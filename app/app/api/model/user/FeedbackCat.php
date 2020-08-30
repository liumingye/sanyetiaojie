<?php

namespace app\api\model\user;

use app\common\model\user\FeedbackCat as CategoryModel;

class FeedbackCat extends CategoryModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'sort',
        'create_time',
        'update_time'
    ];
}
