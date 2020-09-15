<?php

namespace app\api\validate\notice;

use think\Validate;
use app\api\model\notice\Notice as NoticeModel;

class Send extends Validate
{
    protected $rule = [
        'nid'  => 'require|hasId',
        'uid'  => 'require',
        'text' => 'require'
    ];

    protected $message  =   [
        'nid.require' => '参数错误',
        'nid.hasId' => '发送失败',
        'uid.require' => '请先登录',
        'text.require' => '不能发送空白消息',
    ];

    public function hasId($value, $rule, $data)
    {
        $res = NoticeModel::where(['id' => $value, 'uid' => $data['uid']])->find();
        if ($res) {
            return true;
        }
        return false;
    }
}
