<?php

namespace app\shop\model\order;

use app\common\model\BaseModel;

class MediateLawyer extends BaseModel
{
    //表名
    protected $name = 'mediate_lawyer';

    public function addAll($id, $data)
    {
        $list = [];
        foreach ($data as $vo) {
            $list[] = [
                'mid' => $id,
                'uid' => $vo,
                'app_id' => static::$app_id
            ];
        }
        $this->saveAll($list);
    }
}
