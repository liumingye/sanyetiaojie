<?php

namespace app\common\model\notice;

use app\common\model\BaseModel;
use app\common\model\notice\Notice as NoticeModel;

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

    public function add($param)
    {
        $params = array_merge([], $param);
        $notice = (new NoticeModel)->field('user_unread')->where('id', $params['nid'])->find();
        if (!$notice) {
            return '未找到此消息';
        }
        try {
            $model = $this;
            $data = $model->save([
                'nid' => $param['nid'],
                'uid' => $param['uid'],
                'text' => $param['text'],
                'app_id' => self::$app_id
            ]);
            if ($data) {
                // 增加用户未读消息数
                $notice->user_unread += 1;
                $notice->save();
            }
            return $data;
        } catch (\Exception $e) {
            return (string) $e->getMessage();
        }
    }
}
