<?php

namespace app\shop\controller\settings;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;


class Xcx extends Controller
{

    public function index()
    {
        $model = new SettingModel;
        $data = $this->request->param();

        $values = [
            'aboutus' => $data['aboutus'],
        ];
        $key = 'xcx';
        if ($model->edit($key, $values)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 商城设置进来请求的接口
     */
    public function fetchData()
    {
        $key = 'xcx';
        $vars['values'] = SettingModel::getItem($key);
        return $this->renderSuccess('', compact('vars'));
    }
}
