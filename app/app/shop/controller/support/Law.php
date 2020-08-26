<?php

namespace app\shop\controller\support;

use app\shop\model\support\Law as LawModel;
use app\shop\model\support\LawCat as CategoryModel;
use app\shop\controller\Controller;

class Law extends Controller
{
    public function list()
    {
        $cid = input('cid', 0, 'intval');
        $page = input('page', 1, 'intval');
        $text = input('text', '', 'htmlspecialchars');

        $category = CategoryModel::getCacheTreeSimple();
        $category = array_combine(array_column($category, 'category_id'), $category);

        $list = (new LawModel)->getList([
            'category_id' => $cid,
            'text' => $text,
            'page' => $page,
            'field' => 'id,category_id,title,create_time',
            'status' => 1
        ]);

        foreach($list as &$vo){
            if(isset($category[$vo['category_id']])){
                $vo['category_name'] = $category[$vo['category_id']]['name'];
            }else{
                $vo['category_name'] = '未分类';
            }
        }

        return $this->renderSuccess('', $list);
    }

    /**
     * 添加法律
     */
    public function add()
    {
        $data = $this->postData();

        $model = new LawModel;
        if (!isset($data['category_id']) || (isset($data['category_id']) && $data['category_id'] == '')) {
            return $this->renderError('请选择分类');
        }

        if ($model->add($data)) {
            return $this->renderSuccess('添加成功');
        }
        return $this->renderError($model->getError() ?: '添加失败');
    }

    /**
     * 编辑法律
     */
    public function edit($id)
    {
        // 商品详情
        $model = LawModel::detail($id);
        // 更新记录
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    /**
     * 删除法律
     */
    public function delete($id)
    {
        $model = LawModel::detail($id);
        if (!$model->remove()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }
    
    /**
     * 法律详情
     */
    public function info(){
        $id = input('id', 0, 'intval');
        $data = (new LawModel)->getInfo($id);
        if($data){
            return $this->renderSuccess('', $data);
        }else{
            return $this->renderError('参数错误');
        }
    }
    
    public function getBaseData(){
        $data = (new LawModel)->getBaseData();
        return $this->renderSuccess('', $data);

    }

}
