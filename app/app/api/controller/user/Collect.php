<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\support\Lawyer as LawyerModel;
use app\api\model\support\LawyerCat as CategoryModel;

class Collect extends Controller
{

    public function lawyer()
    {
        $user = $this->getUser(false);

        $cid = input('cid', 0, 'intval');
        $page = input('page', 1, 'intval');

        $category1[0] = [
            "cid" => 0,
            "name" => "全部"
        ];

        $category2 = (array) CategoryModel::getCacheTreeSimple();
        $category = array_merge($category1, $category2);

        if ($user['user_id'] > 0) {
            $list = (new LawyerModel)->getCollect([
                'user_id' => $user['user_id'],
                'category_id' => $cid,
                'page' => $page,
                'field' => 'id,name,image_id',
                'status' => 1,
            ]);
            return $this->renderSuccess('', compact('category', 'list'));
        }
        return $this->renderError('请先登录');
    }
}
