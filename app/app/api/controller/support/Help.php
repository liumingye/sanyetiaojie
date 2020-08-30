<?php

namespace app\api\controller\support;

use app\api\controller\Controller;
use think\exception\ValidateException;
use app\api\validate\support\Help as HelpValidate;
use app\api\model\support\Help as HelpModel;
use app\api\model\product\Category as CategoryModel;

class Help extends Controller
{

    public function category()
    {
        $list = (array) CategoryModel::getCacheTreeSimple();
        return $this->renderSuccess('', compact('list'));
    }

    public function add()
    {
        if (!request()->isPost()) {
            return $this->renderError('请求错误');
        }
        $user = $this->getUser(false);
        if (!$user['user_id']) {
            return $this->renderError('请先登录');
        }
        $data = $this->postData();
        $data['uid'] = $user['user_id'];
        try {
            validate(HelpValidate::class)->check($data);
            $model = new HelpModel;
            if ($model->add($data)) {
                return $this->renderSuccess('提交成功');
            }
            return $this->renderError($model->getError() ?: '提交失败');
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return $this->renderError($e->getError());
        }
    }
}
