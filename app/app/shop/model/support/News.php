<?php

namespace app\shop\model\support;

use app\common\model\support\News as NewsModel;

class News extends NewsModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'status',
        'update_time',
    ];

    /**
     * 删除新闻
     */
    public function remove()
    {
        return $this->delete();
    }

    /**
     * 添加新闻
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
     * 编辑新闻
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
