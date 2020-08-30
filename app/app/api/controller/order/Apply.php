<?php

namespace app\api\controller\order;

use app\api\controller\Controller;
use app\api\model\order\Mediate as MediateModel;
use app\api\model\order\MediateInfo as MediateInfoModel;
use app\api\validate\order\Apply as ApplyValidate;
use app\common\service\order\OrderService;
use think\exception\ValidateException;

class Apply extends Controller
{
    public function index()
    {
        if (!request()->isPost()) {
            return $this->renderError('请求错误');
        }
        $user = $this->getUser(false);
        if (!$user['user_id']) {
            return $this->renderError('请先登录');
        }
        $data = [
            'cid' => request()->param('cid', '', 'intval'),
            'no' => $this->orderNo(),
            'uid' => $user['user_id'],
            'name' => request()->param('name', '', 'htmlspecialchars'),
            'mobile' => request()->param('mobile', '', 'htmlspecialchars'),
            'idcard' => request()->param('idcard', '', 'htmlspecialchars'),
            'appeal' => request()->param('appeal', '', 'htmlspecialchars'),
            'other_name' => request()->param('other_name', '', 'htmlspecialchars'),
            'other_phone' => request()->param('other_phone', '', 'htmlspecialchars'),
            'text' => request()->param('text', '', 'htmlspecialchars'),
            'area' => request()->param('area', '', 'htmlspecialchars'),
            'address' => request()->param('address', '', 'htmlspecialchars'),
            'iFile' => request()->param('iFile', '', 'htmlspecialchars'),
        ];
        try {
            validate(ApplyValidate::class)->check($data);
            $data = (new MediateModel)->add($data);
            if ($data) {
                (new MediateInfoModel)->add([
                    'mid' => $data->id,
                    'text' => '待调解',
                    'status' => 1,

                ]);
                return $this->renderSuccess('', '提交成功');
            } else {
                return $this->renderError('提交失败');
            }
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return $this->renderError($e->getError());
        }
        return $this->renderError('未知错误');
    }

    /**
     * 生成案件码
     */
    public function orderNo()
    {
        return OrderService::createOrderNo();
    }
}
