<?php

namespace app\api\controller\index;

use app\api\controller\Controller;
use app\api\model\order\Mediate as MediateModel;

class User extends Controller
{
    public function index()
    {
        // 获取我的 待受理 已调解 正在调解 个数
        $user = $this->getUser(false);
        $user_id = $user['user_id'];
        $mediate_count = [
            'accepting' => 0,
            'adjusting' => 0,
            'adjusted' => 0
        ];
        if ($user_id) {
            $model = new MediateModel();
            $mediate_count = [
                'accepting' => $model->getCount($user_id, 'accepting'),
                'adjusting' => $model->getCount($user_id, 'adjusting'),
                'adjusted' => $model->getCount($user_id, 'adjusted')
            ];
        }
        return $this->renderSuccess('', compact('mediate_count'));
    }
}
