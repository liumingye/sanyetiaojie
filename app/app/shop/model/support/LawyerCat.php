<?php

namespace app\shop\model\support;

use app\common\model\support\LawyerCat as CategoryModel;

/**
 * Class Category
 */
class LawyerCat extends CategoryModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'update_time'
    ];

}
