<?php

namespace app\api\controller\order;

use app\api\controller\Controller;
use app\api\model\order\Mediate as MediateModel;

class Mediate extends Controller
{
    public function list()
    {
        $phone = input('phone', '', 'htmlspecialchars');
        if ($phone != '') {
            $data = (new MediateModel())->getListByPhone($phone);
            return $this->renderSuccess('', compact('data'));
        } else {
            $user = $this->getUser(false);
            if ($user['user_id']) {
                $data = (new MediateModel())->getList($user['user_id']);
                return $this->renderSuccess('', compact('data'));
            }
        }
        return $this->renderError('未登录或缺少参数');
    }

    public function info()
    {
        $id = input('id', 0, 'intval');
        $no = input('no', '', 'htmlspecialchars');
        if ($id > 0 && $no  != '') {
            $data = (new MediateModel())->getInfoByIdNo($id, $no);
            if ($data) {
                return $this->renderSuccess('', compact('data'));
            }
            return $this->renderError('未找到此案件');
        }
        return $this->renderError('缺少参数');
    }
}
