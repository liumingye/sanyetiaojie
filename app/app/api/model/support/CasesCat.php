<?php

namespace app\api\model\support;

use app\common\model\support\CasesCat as CategoryModel;

/**
 * Class Category
 */
class CasesCat extends CategoryModel
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
