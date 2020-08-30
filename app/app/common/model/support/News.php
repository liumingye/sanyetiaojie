<?php

namespace app\common\model\support;

use app\common\model\BaseModel;

class News extends BaseModel
{
    //表名
    protected $name = 'news';
    //主键字段名
    protected $pk = 'id';

    public function getList($param)
    {
        $model = $this;
        // 默认搜索条件
        $params = array_merge([
            'text' => '',
            'list_rows' => 10,
            'field' => '*'
        ], $param);

        // 筛选条件
        $filter = [];
        if ($params['text'] != '') {
            $params['text'] = "%{$params['text']}%";
            $model = $model
                ->where('title', 'like', $params['text']);
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
        $prev = $this->field('id,title')->where('id', '<', $id)->limit(1)->order('id desc')->find();
        $next = $this->field('id,title')->where('id', '>', $id)->limit(1)->find();
        if ($prev) {
            $model->prev = $prev;
        }
        if ($next) {
            $model->next = $next;
        }
        // 整理数据并返回
        return $model;
    }

    public static function detail($id)
    {
        return self::find($id);
    }
    
}
