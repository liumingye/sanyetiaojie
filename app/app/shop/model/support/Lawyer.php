<?php

namespace app\shop\model\support;

use app\common\model\support\Lawyer as LawyerModel;
use app\shop\model\support\LawyerCat as CategoryModel;
use app\shop\model\auth\User as UserModel;

class Lawyer extends LawyerModel
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

    public function getBaseData()
    {
        $category = (new CategoryModel)->getCacheTreeSimple(false);
        return compact('category');
    }

    /**
     * 删除律师
     */
    public function remove()
    {
        return $this->delete();
    }

    /**
     * 添加律师
     */
    public function add(array $data)
    {
        $data['app_id'] = self::$app_id;
        // 开启事务
        $this->startTrans();
        try {
            $this->save($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 编辑律师
     */
    public function edit($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->transaction(function () use ($data) {
            // 保存商品
            $this->save($data);
            return true;
        });
    }
}
