<?php

namespace app\shop\model\support;

use app\common\model\support\LawCat as CategoryModel;
use think\facade\Cache;

/**
 * Class Category
 */
class LawCat extends CategoryModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'update_time',
    ];

    /**
     * 添加新记录
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        $this->deleteCache();
        return $this->save($data);
    }

    /**
     * 编辑记录
     */
    public function edit($data)
    {
        $this->deleteCache();
        return $this->save($data) !== false;
    }

    /**
     * 删除商品分类
     */
    public function remove()
    {
        $this->deleteCache();
        return $this->delete();
    }

    /**
     * 删除缓存
     */
    private function deleteCache()
    {
        return Cache::delete('lawcat_' . static::$app_id);
    }

}
