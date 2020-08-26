<?php

namespace app\common\model\support;

use app\common\model\BaseModel;
use app\common\model\support\LawyerCollect as LawyerCollectModel;

class Lawyer extends BaseModel
{
    //表名
    protected $name = 'lawyer';
    //主键字段名
    protected $pk = 'id';

    public function getList($param)
    {
        $model = $this;
        // 默认搜索条件
        $params = array_merge([
            'category_id' => '0',
            'list_rows' => 30,
            'field' => '*',
            'user_id' => '0'
        ], $param);

        // 筛选条件
        $filter = [];
        if ($params['category_id'] > 0) {
            $model = $model->where('category_id', $params['category_id']);
        }
        $with = [];
        if ($params['user_id'] > 0) {
            $withCollect = [
                'hasCollect' => function ($query) use ($params) {
                    $query->where('uid', $params['user_id']);
                }
            ];
            $with = array_merge($with, $withCollect);
        }

        $withImage = [
            'image' => function ($query) {
                $query->field(['file_id,storage,save_name'])->hidden(['file_id', 'storage', 'save_name']);
            }
        ];
        $with = array_merge($with, $withImage);

        $list = $model
            ->field($params['field'])
            ->with($with)
            ->where($filter)
            ->order('id desc')
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);

        // 整理列表数据并返回
        return $this->setListData($list);
    }

    public function setListData($data)
    {
        $dataSource = $data;
        // 整理商品列表数据
        foreach ($dataSource as &$vo) {
            $collect = $vo['hasCollect'];
            unset($vo['hasCollect']);
            if ($collect != null) {
                $vo['collect'] = true;
            } else {
                $vo['collect'] = false;
            }
        }
        return $data;
    }

    public function getInfo($id)
    {
        $model = $this
            ->where('id', '=', $id)
            ->with(['image' => function ($query) {
                $query->field(['file_id,storage,save_name'])->hidden(['file_id', 'storage', 'save_name']);
            }])
            ->find();
        if (empty($model)) {
            return $model;
        }
        return $model;
    }

    public function image()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'image_id');
    }

    public function hasCollect()
    {
        return $this->hasOne('app\\common\\model\\support\\LawyerCollect', 'lid', 'id');
    }

    public function collect($id, $user_id, $t)
    {
        if ($t) {
            $res = (new LawyerCollectModel)->where([
                'uid' => $user_id,
                'lid' => $id
            ])->find();
            if ($res) {
                return true;
            } else {
                return (new LawyerCollectModel)->save([
                    'uid' => $user_id,
                    'lid' => $id,
                    'app_id' => self::$app_id
                ]);
            }
        } else {
            return (new LawyerCollectModel)->where(['uid' => $user_id, 'lid' => $id])->delete();
        }
    }
}
