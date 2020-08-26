<?php

namespace app\shop\model\support;

use app\common\model\support\Cases as CasesModel;
use app\shop\model\support\CasesCat as CategoryModel;

class Cases extends CasesModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'status',
        'update_time',
    ];

    public function getBaseData()
    {
        $category = (new CategoryModel)->getCacheTreeSimple();
        return compact('category');

    }

    /**
     * 删除商品分类
     */
    public function remove()
    {
        return $this->delete();
    }

    /**
     * 添加法律
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
     * 编辑法律
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
