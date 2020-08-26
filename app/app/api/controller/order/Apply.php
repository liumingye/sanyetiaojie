<?php

namespace app\api\controller\order;

use app\api\controller\Controller;
use think\exception\ValidateException;
use app\api\validate\order\Apply as ApplyValidate;

class Apply extends Controller
{
    public function index()
    {
        $cid = input('cid', '', 'intval');
        $name = input('name', '', 'htmlspecialchars');
        $mobile = input('mobile', '', 'htmlspecialchars');
        $idcard = input('idcard', '', 'htmlspecialchars');
        $appeal = input('appeal', '', 'htmlspecialchars');
        $other_name = input('other_name', '', 'htmlspecialchars');
        $other_phone = input('other_phone', '', 'htmlspecialchars');
        $text = input('text', '', 'htmlspecialchars');
        $area = input('area', '', 'htmlspecialchars');
        $address = input('address', '', 'htmlspecialchars');
        try {
            validate(ApplyValidate::class)->check([
                'cid'  => $cid,
                'name'  => $name,
                'mobile'  => $mobile,
                'idcard'  => $idcard,
                'appeal'  => $appeal,
                'other_name'  => $other_name,
                'other_phone'  => $other_phone,
                'text'  => $text,
                'area'  => $area,
                'address'  => $address
            ]);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return $this->renderError($e->getError());
        }
    }
}
