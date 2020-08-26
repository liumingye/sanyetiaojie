<?php

namespace app\api\model\support;

use app\common\model\support\Cases as CasesModel;

class Cases extends CasesModel
{
    protected $type = [
        'date'    =>  'timestamp:Y-m-d',
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
