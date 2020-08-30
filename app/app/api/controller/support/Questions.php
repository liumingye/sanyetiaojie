<?php

namespace app\api\controller\support;

use app\api\model\support\Questions as QuestionsModel;
use app\api\controller\Controller;

class Questions extends Controller
{
    public function list()
    {
        $page = input('page', 1, 'intval');
        $list_rows = min(input('limit', 15, 'intval'), 15);

        $list = (new QuestionsModel)->getList([
            'page' => $page,
            'list_rows' => $list_rows,
            'field' => 'id,title',
            'status' => 1,
        ]);

        return $this->renderSuccess('', compact('list'));
    }

    public function info(){
        $id = input('id', 0, 'intval');
        $data = (new QuestionsModel)->getInfo($id);
        if($data){
            return $this->renderSuccess('', compact('data'));
        }else{
            return $this->renderError('参数错误');
        }
    }

}
