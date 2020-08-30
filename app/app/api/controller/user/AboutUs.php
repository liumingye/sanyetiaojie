<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\settings\Setting as SettingModel;

class AboutUs extends Controller
{
    public function index()
    {
        $config = SettingModel::getItem('xcx');
        $res = $config['aboutus'];
        return $this->renderSuccess('', $res);
    }
}
