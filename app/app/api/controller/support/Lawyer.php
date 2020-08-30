<?php

namespace app\api\controller\support;

use app\api\model\support\Lawyer as LawyerModel;
use app\api\model\support\LawyerCat as CategoryModel;
use app\api\controller\Controller;

class Lawyer extends Controller
{

    public function list()
    {
        $user = $this->getUser(false);

        $cid = input('cid', 0, 'intval');
        $page = input('page', 1, 'intval');
        $list_rows = min(input('limit', 10, 'intval'), 10);

        $category1[0] = [
            "cid" => 0,
            "name" => "全部"
        ];

        $category2 = (array) CategoryModel::getCacheTreeSimple();
        $category = array_merge($category1, $category2);

        $list = (new LawyerModel)->getList([
            'user_id' => $user['user_id'],
            'category_id' => $cid,
            'page' => $page,
            'list_rows' => $list_rows,
            'field' => 'id,name,image_id',
            'status' => 1,
        ]);
        return $this->renderSuccess('', compact('category', 'list'));
    }

    public function info()
    {
        $id = input('id', 0, 'intval');
        $data = (new LawyerModel)->getInfo($id);

        $withCat = input('cat', 0, 'intval');
        if ($data) {
            if ($withCat) {
                $category1[0] = [
                    "cid" => 0,
                    "name" => "全部"
                ];
                $category2 = (array) CategoryModel::getCacheTreeSimple();
                $res['category'] = array_merge($category1, $category2);
            }
            $res['data'] = $data;
            return $this->renderSuccess('', $res);
        }
        return $this->renderError('参数错误');
    }

    /* 收藏 && 取消收藏 */
    public function collect()
    {
        $user = $this->getUser(false);
        if (!$user['user_id']) {
            return $this->renderError('请先登录');
        }
        $id = input('id', 0, 'intval');
        $t = input('t', '', 'intval');
        if ($id > 0 && $t !== '') {
            $res = (new LawyerModel)->collect($id, $user['user_id'], $t);
            if ($res) {
                if ($t == 1) {
                    return $this->renderSuccess('收藏成功');
                }
                return $this->renderSuccess('取消成功');
            }
            return $this->renderError('操作失败');
        }
        return $this->renderError('参数错误');
    }
}
