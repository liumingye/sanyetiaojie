<?php

namespace app\common\model\support;

use app\common\model\BaseModel;
use app\common\model\support\HelpImage as HelpImageModel;

class Help extends BaseModel
{
    //表名
    protected $name = 'help';
    //主键字段名
    protected $pk = 'id';

    //追加字段
    protected $append = [
        'state_text', // 状态文字描述
    ];

    public function getStateTextAttr($value, $data)
    {
        if ($data['status'] == 1) {
            return '待处理';
        } else if ($data['status'] == 2) {
            return '处理中';
        } else if ($data['status'] == 3) {
            return '已处理';
        } else if ($data['status'] == 4) {
            return '处理失败';
        }
        return $value;
    }

    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->belongsTo("app\\common\\model\\user\\User", 'uid', 'user_id');
    }

    /**
     * 关联图片表
     */
    public function image()
    {
        return $this->hasMany('app\\common\\model\\support\\HelpImage', 'hid', 'id')->order(['id' => 'asc']);
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
            if ($data['iFile'] != '') {
                $arr = explode(',', $data['iFile']);
                // 生成图片数据
                $data = [];
                foreach ($arr as $vo) {
                    $data[] = [
                        'mid' => $this->id,
                        'image_id' => $vo,
                        'app_id' => self::$app_id,
                    ];
                }
                !empty($data) && (new HelpImageModel)->saveAll($data);
            }
            $this->commit();
            return $this;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
}
