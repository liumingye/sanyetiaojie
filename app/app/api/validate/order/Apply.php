<?php

namespace app\api\validate\order;

use think\Validate;
use app\api\model\product\Category;
use app\common\model\file\UploadFile as UploadFileModel;

class Apply extends Validate
{
    protected $rule =   [
        'cid'  => 'require|hasCid',
        'name'  => 'require|max:30',
        'mobile'  => 'require|mobile',
        'idcard'  => 'require|idCard',
        'appeal'  => 'require|max:255',
        'other_name'  => 'require|max:30',
        'other_phone'  => 'require|mobile',
        'text'  => 'require|max:1000',
        'area'  => 'require|max:255',
        'address'  => 'require|max:255',
        'iFile' => 'checkFile'
    ];

    protected $message  =   [
        'cid.require' => '请选择矛盾类型',
        'cid.hasCid' => '该分类不存在',
        'name.require' => '请填写申请人姓名',
        'name.max' => '申请人姓名最多不能超过30个字符',
        'mobile.require' => '请填写申请人手机号',
        'mobile.mobile' => '申请人手机号格式错误',
        'idcard.require' => '请填写申请人身份证号',
        'idcard.idCard' => '申请人身份证号格式错误',
        'appeal.require' => '请填写诉求',
        'appeal.max' => '诉求最多不能超过255个字符',
        'other_name.require' => '请填写纠纷当事人姓名',
        'other_name.max' => '纠纷当事人姓名最多不能超过30个字符',
        'other_phone.require' => '请填写纠纷当事人手机号',
        'other_phone.max' => '纠纷当事人手机号格式错误',
        'text.require' => '请填写纠纷描述',
        'text.max' => '纠纷描述最多不能超过1000个字符',
        'area.require' => '请填写所在地区',
        'area.max' => '所在地区最多不能超过255个字符',
        'address.require' => '请填写详细地址',
        'address.max' => '详细地址最多不能超过255个字符',
        'iFile.checkFile' => '上传图片记录不存在',
    ];

    public function hasCid($value)
    {
        $res = Category::where('category_id', $value)->find();
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
