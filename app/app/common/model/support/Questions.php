<?php

namespace app\common\model\support;

use app\common\model\BaseModel;

class Questions extends BaseModel
{
    //表名
    protected $name = 'questions';
    //主键字段名
    protected $pk = 'id';

    public function getList($param)
    {
        $model = $this;
        // 默认搜索条件
        $params = array_merge([
            'text' => '',
            'list_rows' => 15,
            'field' => '*'
        ], $param);

        $list = $model
            ->field($params['field'])
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
        // 整理数据并返回
        return $model;
    }

    public static function detail($id)
    {
        return self::find($id);
    }
    
}
