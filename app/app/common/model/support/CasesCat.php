<?php

namespace app\common\model\support;

use app\common\model\BaseModel;
use think\facade\Cache;

class CasesCat extends BaseModel
{
    //表名
    protected $name = 'cases_cat';
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
    public static function getCacheTreeSimple()
    {
        $model = new static;
        if (!Cache::get('casescat_' . $model::$app_id)) {
            $data = $model->field('category_id as cid,name,sort,create_time')->order(['sort' => 'asc', 'create_time' => 'desc'])->select();
            $all = !empty($data) ? $data->toArray() : [];
            Cache::tag('cache')->set('casescat_' . $model::$app_id, compact('all'));
        }
        return Cache::get('casescat_' . $model::$app_id)['all'];
    }
}
