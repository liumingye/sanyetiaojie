<?php

namespace app\common\model\order;

use app\common\model\BaseModel;

class MediateInfo extends BaseModel
{
    /**
     * 添加
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
}
