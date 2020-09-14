<?php

namespace app\common\model\notice;

use app\common\model\BaseModel;
use app\shop\model\notice\Notice as NoticeModel;

class Notice extends BaseModel
{

    //表名
    protected $name = 'notice';
    //主键字段名
    protected $pk = 'id';

    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->belongsTo("app\\common\\model\\user\\User", 'uid', 'user_id');
    }

    public function msg()
    {
        return $this->hasMany("app\\common\\model\\notice\\NoticeInfo", 'nid', 'id');
    }

    public function sendMediateNotice($id, $text)
    {
        $data = $this->field('id')->where(['mid' => $id, 'type' => 3])->find();
        if (!$data) {
            return false;
        } else {
            $nid = $data->id;
        }
        $res = (new NoticeModel)->send([
            'nid' => $nid,
            'uid' => 0,
            'text' => $text
        ]);
        return $res;
    }

    public function sendHelpNotice($id, $text)
    {
        $data = $this->field('id')->where(['hid' => $id, 'type' => 2])->find();
        if (!$data) {
            return false;
        } else {
            $nid = $data->id;
        }
        $res = (new NoticeModel)->send([
            'nid' => $nid,
            'uid' => 0,
            'text' => $text
        ]);
        return $res;
    }

    public function sendSystemNotice($uid, $text)
    {
        $data = $this->field('id')->where(['uid' => $uid, 'type' => 1])->find();
        if (!$data) {
            $data = $this->save([
                'mid' => 0,
                'hid' => 0,
                'uid' => $uid,
                'type' => 1,
                'sort' => 5,
                'name' => '提示消息',
                'app_id' => self::$app_id
            ]);
            $nid = $this->id;
        } else {
            $nid = $data->id;
        }
        $res = (new NoticeModel)->send([
            'nid' => $nid,
            'uid' => 0,
            'text' => $text
        ]);
        return $res;
    }

    /**
     * 获取消息总量
     * type 1 获取系统未读消息数
     */
    public function getNoticeTotal($type)
    {
        if ($type == 1) {
            return $this->sum('admin_unread');
        }
        return 0;
    }
}
