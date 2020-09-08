<?php

namespace app\common\model\user;

use app\common\model\BaseModel;
use app\common\model\user\FeedbackImage as FeedbackImageModel;

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
     * 关联图片表
     */
    public function image()
    {
        return $this->hasMany('app\\common\\model\\user\\FeedbackImage', 'fid', 'id')->order(['id' => 'asc']);
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
                !empty($data) && (new FeedbackImageModel)->saveAll($data);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
}
