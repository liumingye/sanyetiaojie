<?php

namespace app\api\validate\order;

use think\Validate;
use app\common\model\file\UploadFile as UploadFileModel;
use app\common\model\order\Mediate as MediateModel;

class AddImage extends Validate
{
    protected $rule = [
        'id'  => 'require|hasId',
        'uid'  => 'require',
        'iFile' => 'require|checkFile'
    ];

    protected $message  =   [
        'id.require' => '参数错误',
        'id.hasId' => '无此案件记录',
        'uid.require' => '请先登录',
        'iFile.require' => '请选择添加的图片',
        'iFile.checkFile' => '上传图片记录不存在',
    ];

    public function hasId($value, $rule, $data)
    {
        $res = MediateModel::where(['id' => $value, 'uid' => $data['uid']])->find();
        if ($res) {
            return true;
        }
        return false;
    }

    public function checkFile($value)
    {
        $arr = explode(',', $value);
        foreach ($arr as $vo) {
            $res = UploadFileModel::where('file_id', $vo)->find();
            if (!$res) {
                return false;
            }
        }
        return true;
    }
}
