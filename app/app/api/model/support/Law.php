<?php

namespace app\api\model\support;

use app\common\model\support\Law as LawModel;

class Law extends LawModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'status',
        'update_time',
    ];
}
