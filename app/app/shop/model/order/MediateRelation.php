<?php

namespace app\shop\model\order;

use app\common\model\BaseModel;

class MediateRelation extends BaseModel
{
    //表名
    protected $name = 'mediate_relation';

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
        return $this->saveAll($list);
    }
}
