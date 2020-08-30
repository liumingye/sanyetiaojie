<?php

namespace app\api\model\order;

use app\common\model\order\Apply as ApplyModel;
use app\api\model\order\MediateInfo as MediateInfoModel;

class Apply extends ApplyModel
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
            (new MediateInfoModel)->add([
                'mid' => $this->id,
                'text' => '待调解',
                'status' => 1
            ]);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
}
