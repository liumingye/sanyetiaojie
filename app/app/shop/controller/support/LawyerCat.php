<?php

namespace app\shop\controller\support;

use app\shop\model\support\LawyerCat as CategoryModel;
use app\shop\controller\Controller;

class LawyerCat extends Controller
{

    /**
     * 分类列表
     */
    public function index()
    {
        $model = new CategoryModel;
        $list = $model->getCacheTreeSimple(false);
        return $this->renderSuccess('', $list);
    }

    /**
     * 删除分类
     */
    public function delete($cid)
    {
        $model = CategoryModel::find($cid);
        if (!$model->remove($cid)) {
            return $this->renderError('删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

    /**
     * 添加分类
     */
    public function add()
    {
        $model = new CategoryModel;
        // 新增记录
        if ($model->add($this->request->post())) {
            return $this->renderSuccess('添加成功');
        }
        return $this->renderError('添加失败');
    }

    /**
     * 编辑分类
     */
    public function edit($cid)
    {
        // 模板详情
        $model = CategoryModel::detail($cid);
        // 更新记录
        if ($model->edit($this->request->post())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

}
