<?php
/*
 * @Created by: VSCode
 * @Author: LiuMingye
 * @Date: 2020-08-29 19:56:57
 * @LastEditTime: 2020-09-14 15:47:45
 * @LastEditors: your name
 * @FilePath: \app\app\api\controller\order\Mediate.php
 */

namespace app\api\controller\order;

use app\api\controller\Controller;
use app\api\model\order\Mediate as MediateModel;
use app\api\validate\order\AddImage as AddImageValidate;
use app\api\validate\order\Edit as EditValidate;
use think\exception\ValidateException;

class Mediate extends Controller
{

    public function addImage()
    {
        if (!request()->isPost()) {
            return $this->renderError('请求错误');
        }
        $user = $this->getUser(false);
        if (!$user['user_id']) {
            return $this->renderError('请先登录');
        }
        $data = [
            'id' => request()->param('id', '', 'intval'),
            'uid' => $user['user_id'],
            'iFile' => request()->param('iFile', '', 'htmlspecialchars'),
        ];
        try {
            validate(AddImageValidate::class)->check($data);
            $model = new MediateModel;
            if ($model->addImage($data)) {
                return $this->renderSuccess('', '提交成功');
            }
            return $this->renderError('提交失败');
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return $this->renderError($e->getError());
        }
        return $this->renderError('未知错误');
    }

    public function list()
    {
        $phone = input('phone', '', 'htmlspecialchars');
        if ($phone != '') {
            $data = (new MediateModel())->getListByPhone($phone);
            return $this->renderSuccess('', compact('data'));
        } else {
            $status = input('status', 0, 'intval');
            $user = $this->getUser(false);
            if ($user['user_id']) {
                $data = (new MediateModel())->getList($user['user_id'], $status);
                return $this->renderSuccess('', compact('data'));
            }
        }
        return $this->renderError('未登录或缺少参数');
    }

    public function info()
    {
        $id = input('id', 0, 'intval');
        $no = input('no', '', 'htmlspecialchars');
        if ($id > 0 && $no  != '') {
            $data = (new MediateModel())->getInfoByIdNo($id, $no);
            if ($data) {
                return $this->renderSuccess('', compact('data'));
            }
            return $this->renderError('未找到此案件');
        }
        return $this->renderError('缺少参数');
    }

    public function edit()
    {
        if (!request()->isPost()) {
            return $this->renderError('请求错误');
        }
        $user = $this->getUser(false);
        if (!$user['user_id']) {
            return $this->renderError('请先登录');
        }
        $id = request()->param('id', '', 'intval');
        $no = request()->param('no', '', 'intval');
        if ($id == '' || $no == '') {
            return $this->renderError('缺少参数');
        }
        $data = [
            'id' => $id,
            'no' => $no,
            'uid' => $user['user_id'],
            'iFile' => request()->param('iFile', '', 'htmlspecialchars'),
        ];
        try {
            validate(EditValidate::class)->check($data);
            $model = new MediateModel;
            unset($data['id'], $data['no'], $data['uid']);
            if ($model->edit(['id' => $id, 'no' => $no], $data)) {
                return $this->renderSuccess('', '提交成功');
            }
            return $this->renderError($model->getError() ?: '删除失败');
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return $this->renderError($e->getError());
        }
        return $this->renderError('未知错误');
    }
}
