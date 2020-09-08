<?php

namespace app\shop\controller\auth;

use app\shop\controller\Controller;
use app\shop\model\auth\Loginlog as LoginlogModel;

/**
 * 登录日志
 */
class Loginlog extends Controller
{
    public function index()
    {
        $model = new LoginlogModel();
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }
    public function empty()
    {
        $model = new LoginlogModel();
        $model->empty();
        return $this->renderSuccess();
    }
}
