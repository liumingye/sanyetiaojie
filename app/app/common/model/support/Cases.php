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
            'list_rows' => 10,
            'field' => '*',
            'status' => 0
        ], $param);
        // 筛选条件
        if ($params['category_id'] > 0) {
            $model = $model->where('category_id', $params['category_id']);
        }
        if ($params['text'] != '') {
            $params['text'] = "%{$params['text']}%";
            $model = $model
                ->where('title', 'like', $params['text']);
        }
        if ($params['status'] > 0) {
            $model = $model->where('status', $params['status']);
        }
        // 开始查询
        $list = $model
            ->field($params['field'])
            ->order('id desc')
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);

        // 整理列表数据并返回
        return $list;
    }

    public function getInfo($param)
    {
        // 默认搜索条件
        $params = array_merge([
            'field' => '*',
            'status' => 0
        ], $param);
        $model = $this;
        // 筛选条件
        if ($params['status'] > 0) {
            $model = $model->where('status', $params['status']);
        }
        // 开始查询
        $model = $model
            ->where('id', $params['id'])
            ->field($params['field'])
            ->find();
        if (empty($model)) {
            return $model;
        }
        // 整理数据并返回
        return $model;
    }

    public static function detail($id)
    {
        return self::find($id);
    }
}
