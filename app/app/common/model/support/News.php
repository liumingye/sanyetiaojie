<?php

namespace app\common\model\support;

use app\common\model\BaseModel;

class News extends BaseModel
{
    //表名
    protected $name = 'news';
    //主键字段名
    protected $pk = 'id';

    public function image()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'image_id');
    }

    public function getList($param, $with = [])
    {
        $with = array_merge(['image' => function ($query) {
            $query->field(['file_id,file_url,file_name,storage,save_name'])->hidden(['storage', 'file_url', 'file_name', 'save_name']);
        }], $with);
        $model = $this;
        // 默认搜索条件
        $params = array_merge([
            'text' => '',
            'list_rows' => 10,
            'field' => '*',
            'status' => 0
        ], $param);
        // 筛选条件
        if ($params['text'] != '') {
            $params['text'] = "%{$params['text']}%";
            $model = $model->where('title', 'like', $params['text']);
        }
        if ($params['status'] > 0) {
            $model = $model->where('status', $params['status']);
        }
        // 开始查询
        $list = $model
            ->field($params['field'])
            ->with($with)
            ->order('id desc')
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);
        // 整理列表数据并返回
        return $list;
    }

    public function getInfo($param, $with = [])
    {
        $with = array_merge(['image' => function ($query) {
            $query->field(['file_id,file_url,file_name,storage,save_name'])->hidden(['storage', 'file_url', 'file_name', 'save_name']);
        }], $with);
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
            ->with($with)
            ->find();
        if (empty($model)) {
            return $model;
        }
        $prev = $this->field('id,title')->where('id', '<', $params['id'])->limit(1)->order('id desc')->find();
        $next = $this->field('id,title')->where('id', '>', $params['id'])->limit(1)->find();
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
