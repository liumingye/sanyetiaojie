<?php

namespace app\common\model\notice;

use app\common\model\BaseModel;

class Notice extends BaseModel
{

    //表名
    protected $name = 'notice';
    //主键字段名
    protected $pk = 'id';

    public function msg()
    {
        return $this->hasMany("app\\common\\model\\notice\\NoticeInfo", 'nid', 'id');
    }

}
