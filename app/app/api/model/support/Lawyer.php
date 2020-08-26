<?php

namespace app\api\model\support;

use app\common\model\support\Lawyer as LawModel;

class Lawyer extends LawModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'image_id',
        'app_id',
        'status',
        'update_time'
    ];
}
