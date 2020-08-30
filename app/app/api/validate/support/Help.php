<?php

namespace app\api\validate\support;

use think\Validate;
use app\api\model\product\Category as CategoryModel;

class Help extends Validate
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
    ];

    public function hasCid($value)
    {
        $res = CategoryModel::where('category_id', $value)->find();
        if ($res) {
            return true;
        }
        return false;
    }
}
