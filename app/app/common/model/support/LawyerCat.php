<?php

namespace app\common\model\support;

use app\common\model\BaseModel;
use think\facade\Cache;

/**
 * Class Category
 */
class LawyerCat extends BaseModel
{
    //表名
    protected $name = 'lawyer_cat';
    //主键字段名
    protected $pk = 'category_id';

    /**
     * 获取所有分类(树状结构)
     */
    public static function getCacheTreeSimple()
    {
        $model = new static;
        if (!Cache::get('lawyercat_' . $model::$app_id)) {
            $data = $model->order(['sort' => 'asc', 'create_time' => 'desc'])->select();
            $all = !empty($data) ? $data->toArray() : [];
            Cache::tag('cache')->set('lawyercat_' . $model::$app_id, compact('all'));
        }
        return Cache::get('lawyercat_' . $model::$app_id)['all'];
    }
}
