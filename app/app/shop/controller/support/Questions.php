<?php

namespace app\shop\controller\support;

use app\shop\model\support\Questions as QuestionsModel;
use app\shop\controller\Controller;

class Questions extends Controller
{
    public function list()
    {
        $cid = input('cid', 0, 'intval');
        $page = input('page', 1, 'intval');
        $text = input('text', '', 'htmlspecialchars');

        $list = (new QuestionsModel)->getList([
            'category_id' => $cid,
            'text' => $text,
            'page' => $page,
            'list_rows' => 10,
            'field' => 'id,title,create_time',
            'status' => 1
        ]);
        
        return $this->renderSuccess('', $list);
    }

    /**
     * 添加问题
     */
    public function add()
    {
        $data = $this->postData();

        $model = new QuestionsModel;

        if ($model->add($data)) {
            return $this->renderSuccess('添加成功');
        }
        return $this->renderError($model->getError() ?: '添加失败');
    }

    /**
     * 编辑问题
     */
    public function edit($id)
    {
        // 商品详情
        $model = QuestionsModel::detail($id);
        // 更新记录
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    /**
     * 删除问题
     */
    public function delete($id)
    {
        $model = QuestionsModel::detail($id);
        if (!$model->remove()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }
    
    /**
     * 问题详情
     */
    public function info(){
        $id = input('id', 0, 'intval');
        $data = (new QuestionsModel)->getInfo($id);
        if($data){
            return $this->renderSuccess('', $data);
        }else{
            return $this->renderError('参数错误');
        }
    }
    
    public function getBaseData(){
        $data = (new QuestionsModel)->getBaseData();
        return $this->renderSuccess('', $data);

    }

}
