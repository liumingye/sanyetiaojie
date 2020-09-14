<?php

namespace app\api\controller\order;

use app\api\controller\Controller;
use app\api\model\order\Mediate as MediateModel;
use app\api\model\order\MediateInfo as MediateInfoModel;
use app\api\validate\order\Apply as ApplyValidate;
use app\common\service\order\OrderService;
use think\exception\ValidateException;
use app\api\model\notice\Notice as NoticeModel;

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
            'my_area' => request()->param('my_area', '', 'htmlspecialchars'),
            'my_address' => request()->param('my_address', '', 'htmlspecialchars'),
            'appeal' => request()->param('appeal', '', 'htmlspecialchars'),
            'other_name' => request()->param('other_name', '', 'htmlspecialchars'),
            'other_phone' => request()->param('other_phone', '', 'htmlspecialchars'),
            'other_area' => request()->param('other_area', '', 'htmlspecialchars'),
            'other_address' => request()->param('other_address', '', 'htmlspecialchars'),
            'text' => request()->param('text', '', 'htmlspecialchars'),
            'area' => request()->param('area', '', 'htmlspecialchars'),
            'address' => request()->param('address', '', 'htmlspecialchars'),
            'iFile' => request()->param('iFile', '', 'htmlspecialchars'),
        ];
        try {
            validate(ApplyValidate::class)->check($data);
            $model = new MediateModel;
            if ($model->add($data)) {
                // 加进度
                (new MediateInfoModel)->add([
                    'mid' => $model->id,
                    'text' => '待调解中，请耐心等待！',
                    'status' => 1,
                ]);
                // 发消息
                $notice =  (new NoticeModel)->add([
                    'mid' => $model->id,
                    'hid' => 0,
                    'type' => 3,
                    'uid' => $data['uid'],
                    'name' => mb_substr($data['text'], 0, 20)
                ]);
                if ($notice) {
                    (new NoticeModel)->send([
                        'nid' => $notice->id,
                        'uid' => 0,
                        'text' => '您的申请我们已经收到，请耐心等待。',
                    ]);
                }
                // 发系统消息
                (new NoticeModel)->sendSystemNotice($data['uid'], '您有一个纠纷待调解');
                return $this->renderSuccess('', '提交成功');
            }
            return $this->renderError('提交失败');
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
