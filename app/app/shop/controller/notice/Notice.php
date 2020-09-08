<?php

namespace app\shop\controller\notice;

use app\shop\controller\Controller;
use app\shop\model\notice\Notice as NoticeModel;

class Notice extends Controller
{
    public function lists()
    {
        // 列表
        $model = new NoticeModel();
        $role = $this->store['user']['role'];
        if ($role == 1 || $role == 2) {
            $list = $model->getList($this->postData(), $role);
        } else {
            $list = $model->getList($this->postData());
        }
        if ($list) {
            return $this->renderSuccess('', compact('list'));
        }
        return $this->renderError('参数错误');
    }

    public function detail()
    {
        $id = input('id', 0, 'intval');
        if ($id > 0) {
            $data = (new NoticeModel)->info([
                'id' => $id,
            ]);
            if (!is_string($data)) {
                // 用户未读消息置0
                NoticeModel::find($id)->isAutoWriteTimestamp(false)->save([
                    'admin_unread' => 0
                ]);
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
        $id = input('id', 0, 'intval');
        $text = input('text', '', 'htmlspecialchars');
        if ($id > 0) {
            if ($text == '') {
                return $this->renderError('请输入文字');
            }
            $data = (new NoticeModel)->send([
                'uid' => 0,
                'nid' => $id,
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
