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
     * 分类详情
     */
    public static function detail($category_id)
    {
        return self::find($category_id);
    }

    /**
     * 获取所有分类(树状结构)
     */
    public static function getCacheTreeSimple($cache = true)
    {
        $model = new static;
        if ($cache) {
            if (!Cache::get('lawyercat_' . $model::$app_id)) {
                $data = $model->field('category_id as cid,name,sort,create_time')->order(['sort' => 'asc', 'create_time' => 'desc'])->select();
                $all = !empty($data) ? $data->toArray() : [];
                Cache::tag('cache')->set('lawyercat_' . $model::$app_id, compact('all'));
            }
            return Cache::get('lawyercat_' . $model::$app_id)['all'];
        }
        $data = $model->field('category_id as cid,name,sort,create_time')->order(['sort' => 'asc', 'create_time' => 'desc'])->select();
        $all = !empty($data) ? $data->toArray() : [];
        return $all;
    }
}
