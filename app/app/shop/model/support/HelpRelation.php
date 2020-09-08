<?php

namespace app\shop\model\support;

use app\common\model\BaseModel;

class HelpRelation extends BaseModel
{
    //表名
    protected $name = 'help_relation';

    public function addAll($id, $data)
    {
        $list = [];
        foreach ($data as $vo) {
            $list[] = [
                'hid' => $id,
                'uid' => $vo,
                'app_id' => static::$app_id
            ];
        }
        return $this->saveAll($list);
    }
}
