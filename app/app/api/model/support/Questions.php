<?php

namespace app\api\model\support;

use app\common\model\support\Questions as QuestionsModel;

class Questions extends QuestionsModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'status',
        'update_time'
    ];
}
