<?php

namespace app\common\model\user;

use app\common\model\BaseModel;

class Feedback extends BaseModel
{
    //表名
    protected $name = 'feedback';
    //主键字段名
    protected $pk = 'id';

    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->belongsTo("app\\common\\model\\user\\User", 'uid', 'user_id');
    }

    /**
     * 添加
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        $data['status'] = 1;
        $data['create_time'] = time();
        $data['update_time'] = time();
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
}
