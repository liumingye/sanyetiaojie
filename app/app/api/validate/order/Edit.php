<?php

namespace app\api\validate\order;

use think\Validate;
use app\common\model\file\UploadFile as UploadFileModel;
use app\common\model\order\Mediate as MediateModel;

class Edit extends Validate
{
    protected $rule =   [
        'id'  => 'require|hasId',
        'no'  => 'require',
        'uid'  => 'require',
        'iFile' => 'checkFile'
    ];

    protected $message  =   [
        'id.require' => '缺少参数ID',
        'id.hasId' => '未找到此案件',
        'uid.require' => '请先登录',
        'iFile.checkFile' => '上传图片记录不存在',
    ];

    public function hasId($value, $rule, $data)
    {
        $res = MediateModel::where(['id' => $value, 'no' => $data['no'], 'uid' => $data['uid']])->find();
        if (!$res) {
            return false;
        }
        return true;
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
