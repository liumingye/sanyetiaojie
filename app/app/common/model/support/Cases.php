<?php

namespace app\common\model\support;

use app\common\model\BaseModel;

class Cases extends BaseModel
{
    //表名
    protected $name = 'cases';
    //主键字段名
    protected $pk = 'id';

    public function getList($param)
    {
        $model = $this;
        // 默认搜索条件
        $params = array_merge([
            'category_id' => '0',
            'text' => '',
            'list_rows' => 28,
            'field' => '*'
        ], $param);

        // 筛选条件
        $filter = [];
        if ($params['category_id'] > 0) {
            $model = $model->where('category_id', $params['category_id']);
        }
        if ($params['text'] != '') {
            $params['text'] = "%{$params['text']}%";
            $model = $model
                ->where('title', 'like', $params['text'])
                ->where('text', 'like', $params['text']);
        }

        $list = $model
            ->field($params['field'])
            ->where($filter)
            ->order('id desc')
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);

        // 整理列表数据并返回
        return $list;
    }

    public function getInfo($id)
    {
        $model = $this->where('id', '=', $id)
            ->find();
        if (empty($model)) {
            return $model;
        }
        // 整理商品数据并返回
        return $model;
    }
}
