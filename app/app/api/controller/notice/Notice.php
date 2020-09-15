<?php

namespace app\api\controller\notice;

use app\api\controller\Controller;
use app\api\model\notice\Notice as NoticeModel;
use app\api\validate\notice\Send as SendValidate;
use think\exception\ValidateException;

class Notice extends Controller
{
    /* 发送列表 */
    function list()
    {
        $user = $this->getUser(false);
        if (!$user['user_id']) {
            return $this->renderError('请先登录');
        }
        $data = (new NoticeModel)->list([
            'uid' => $user['user_id'],
        ]);
        return $this->renderSuccess('', compact('data'));
    }

    /* 发送详情 */
    public function info()
    {
        $user = $this->getUser(false);
        if (!$user['user_id']) {
            return $this->renderError('请先登录');
        }
        $id = input('id', 0, 'intval');
        if ($id > 0) {
            $data = (new NoticeModel)->info([
                'uid' => $user['user_id'],
                'id' => $id,
            ]);
            if (!is_string($data)) {
                // 用户未读消息置0
                NoticeModel::find($id)->isAutoWriteTimestamp(false)->save([
                    'user_unread' => 0
                ]);
                $detail = (new NoticeModel)->field('type,name')->where('id', $id)->find();
                return $this->renderSuccess('', compact(['detail', 'data']));
            }
            return $this->renderError($data);
        }
        return $this->renderError('参数错误');
    }

    /* 发送消息 */
    public function send()
    {
        if (!request()->isPost()) {
            return $this->renderError('请求错误');
        }
        $user = $this->getUser(false);
        if (!$user['user_id']) {
            return $this->renderError('请先登录');
        }
        $id = input('id', 0, 'intval');
        $text = input('text', '', 'htmlspecialchars');
        $data = [
            'nid' => $id,
            'uid' => $user['user_id'],
            'aid' => 0,
            'text' => $text,
        ];
        try {
            validate(SendValidate::class)->check($data);
            $model = new NoticeModel;
            $res = $model->send($data);
            if ($res) {
                return $this->renderSuccess('', $res);
            }
            return $this->renderError($model->getError() ?: '发送失败');
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return $this->renderError($e->getError());
        }
        return $this->renderError('未知错误');
    }
}
