<?php

namespace app\shop\controller\auth;

use app\shop\controller\Controller;
use app\shop\model\auth\Group as GroupModel;

class Group extends Controller
{

    public function index()
    {
        $model = new GroupModel();
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    public function add()
    {
        $model = new GroupModel;
        // 新增记录
        if ($model->add($this->request->post())) {
            return $this->renderSuccess('添加成功');
        }
        return $this->renderError('添加失败');
    }

    public function edit($id)
    {
        // 详情
        $model = GroupModel::detail($id);
        // 更新记录
        if ($model->edit($this->request->post())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    public function delete($id)
    {
        $model = GroupModel::find($id);
        if (!$model->remove($id)) {
            return $this->renderError('删除失败');
        }
        return $this->renderSuccess('删除成功');
    }
}
