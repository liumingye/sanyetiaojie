<?php

namespace app\shop\controller\support;

use app\shop\model\support\Cases as CasesModel;
use app\shop\model\support\CasesCat as CategoryModel;
use app\shop\controller\Controller;

class Cases extends Controller
{
    public function list()
    {
        $cid = input('cid', 0, 'intval');
        $page = input('page', 1, 'intval');
        $text = input('text', '', 'htmlspecialchars');

        $category = CategoryModel::getCacheTreeSimple();
        $category = array_combine(array_column($category, 'cid'), $category);

        $list = (new CasesModel)->getList([
            'category_id' => $cid,
            'text' => $text,
            'page' => $page,
            'list_rows' => 10,
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
     * 添加案例
     */
    public function add()
    {
        $data = $this->postData();

        $model = new CasesModel;
        if (!isset($data['category_id']) || (isset($data['category_id']) && $data['category_id'] == '')) {
            return $this->renderError('请选择分类');
        }

        if ($model->add($data)) {
            return $this->renderSuccess('添加成功');
        }
        return $this->renderError($model->getError() ?: '添加失败');
    }

    /**
     * 编辑案例
     */
    public function edit($id)
    {
        // 商品详情
        $model = CasesModel::detail($id);
        // 更新记录
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    /**
     * 删除案例
     */
    public function delete($id)
    {
        $model = CasesModel::detail($id);
        if (!$model->remove()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }
    
    /**
     * 案例详情
     */
    public function info(){
        $id = input('id', 0, 'intval');
        $data = (new CasesModel)->getInfo($id);
        if($data){
            return $this->renderSuccess('', $data);
        }else{
            return $this->renderError('参数错误');
        }
    }
    
    public function getBaseData(){
        $data = (new CasesModel)->getBaseData();
        return $this->renderSuccess('', $data);

    }

}
