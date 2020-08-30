<?php

namespace app\shop\controller\user;

use app\shop\controller\Controller;
use app\shop\model\user\Feedback as FeedbackModel;
use app\common\model\user\FeedbackCat as CategoryModel;


class Feedback extends Controller
{
    /**
     * 列表
     */
    public function lists($type = 'all')
    {
        // 分类
        $model = new CategoryModel;
        $category = $model->getCacheTreeSimple();
        $category = array_combine(array_column($category, 'cid'), $category);

        // 订单列表
        $model = new FeedbackModel();
        $list = $model->getList($this->postData());

        foreach ($list as &$vo) {
            if (isset($category[$vo['cid']])) {
                $vo['category_name'] = $category[$vo['cid']]['name'];
            } else {
                $vo['category_name'] = '未分类';
            }
        }

        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 详情
     */
    public function detail($id)
    {
        $detail = FeedbackModel::detail($id);
        return $this->renderSuccess('', compact('detail'));
    }
}
