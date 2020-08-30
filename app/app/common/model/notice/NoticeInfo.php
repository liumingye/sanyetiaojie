<?php

namespace app\common\model\notice;

use app\common\model\BaseModel;

class NoticeInfo extends BaseModel
{

    //表名
    protected $name = 'notice_info';
    //主键字段名
    protected $pk = 'id';
    
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id'
    ];

    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->belongsTo("app\\common\\model\\user\\User", 'uid', 'user_id');
    }


}
