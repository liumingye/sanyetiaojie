<?php

namespace app\shop\controller\support;

use app\shop\model\support\News as NewsModel;
use app\shop\controller\Controller;

class News extends Controller
{
    public function list()
    {
        $cid = input('cid', 0, 'intval');
        $page = input('page', 1, 'intval');
        $text = input('text', '', 'htmlspecialchars');

        $list = (new NewsModel)->getList([
            'category_id' => $cid,
            'text' => $text,
            'page' => $page,
            'list_rows' => 10,
            'field' => 'id,title,author,source,create_time'
        ]);

        return $this->renderSuccess('', $list);
    }

    /**
     * 添加新闻
     */
    public function add()
    {
        $data = $this->postData();

        $model = new NewsModel;

        if ($model->add($data)) {
            return $this->renderSuccess('添加成功');
        }
        return $this->renderError($model->getError() ?: '添加失败');
    }

    /**
     * 编辑新闻
     */
    public function edit($id)
    {
        // 商品详情
        $model = NewsModel::detail($id);
        // 更新记录
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    /**
     * 删除新闻
     */
    public function delete($id)
    {
        $model = NewsModel::detail($id);
        if (!$model->remove()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

    /**
     * 新闻详情
     */
    public function info()
    {
        $id = input('id', 0, 'intval');
        $data = (new NewsModel)->getInfo([
            'id' => $id
        ]);
        if ($data) {
            return $this->renderSuccess('', $data);
        } else {
            return $this->renderError('参数错误');
        }
    }

    public function getBaseData()
    {
        $data = (new NewsModel)->getBaseData();
        return $this->renderSuccess('', $data);
    }
}
