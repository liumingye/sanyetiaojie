<?php

namespace app\api\validate\user;

use think\Validate;
use app\api\model\user\FeedbackCat as CategoryModel;
use app\common\model\file\UploadFile as UploadFileModel;

class Feedback extends Validate
{
    protected $rule =   [
        'cid'  => 'require|hasCid',
        'text'  => 'require|max:1000',
    ];

    protected $message  =   [
        'cid.require' => '请选择问题类型',
        'cid.hasCid' => '该分类不存在',
        'text.require' => '请填写留言内容',
        'text.max' => '留言内容最多不能超过1000个字符',
        'iFile' => 'checkFile'
    ];

    public function hasCid($value)
    {
        $res = CategoryModel::where('category_id', $value)->find();
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
