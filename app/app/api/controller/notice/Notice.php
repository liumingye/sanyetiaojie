<?php

namespace app\api\controller\notice;

use app\api\controller\Controller;
use app\api\model\notice\Notice as NoticeModel;

class Notice extends Controller
{
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
                return $this->renderSuccess('', compact('data'));
            }
            return $this->renderError($data);
        }
        return $this->renderError('参数错误');
    }

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
        if ($id > 0) {
            if ($text == '') {
                return $this->renderError('请输入文字');
            }
            $data = (new NoticeModel)->send([
                'uid' => $user['user_id'],
                'id' => $id,
                'text' => $text,
            ]);
            if (!is_string($data)) {
                return $this->renderSuccess('', $data);
            }
            return $this->renderError($data);
        }
        return $this->renderError('参数错误');
    }
}
