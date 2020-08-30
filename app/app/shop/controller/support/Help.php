<?php

namespace app\shop\controller\support;

use app\shop\controller\Controller;
use app\shop\model\support\Help as HelpModel;
use app\shop\model\product\Category as CategoryModel;

class Help extends Controller
{
    /**
     * 列表
     */
    public function lists($type = 'all')
    {
        // 分类
        $model = new CategoryModel;
        $category = $model->getCacheTree();
        $category = array_combine(array_column($category, 'category_id'), $category);

        // 订单列表
        $model = new HelpModel();
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
        $detail = HelpModel::detail($id);
        return $this->renderSuccess('', compact('detail'));
    }
}
