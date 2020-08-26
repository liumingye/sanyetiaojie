<?php

namespace app\api\controller\support;

use app\api\model\support\Law as LawModel;
use app\api\model\support\LawCat as CategoryModel;
use app\api\controller\Controller;

class Law extends Controller
{
    public function list()
    {
        $cid = input('cid', 0, 'intval');
        $page = input('page', 1, 'intval');
        $text = input('text', '', 'htmlspecialchars');

        $category1[0] = [
            "cid" => 0,
            "name" => "全部"
        ];

        $category2 = (array) CategoryModel::getCacheTreeSimple();
        $category = array_merge($category1, $category2);

        $list = (new LawModel)->getList([
            'category_id' => $cid,
            'text' => $text,
            'page' => $page,
            'field' => 'id,title',
            'status' => 1,
        ]);
        return $this->renderSuccess('', compact('category', 'list'));
    }

    public function info(){
        $id = input('id', 0, 'intval');
        $data = (new LawModel)->getInfo($id);
        if($data){
            return $this->renderSuccess('', compact('data'));
        }else{
            return $this->renderError('参数错误');
        }
    }

}
