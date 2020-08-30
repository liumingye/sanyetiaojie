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

    public function getCollect($param)
    {
        $model = $this;
        // 默认搜索条件
        $params = array_merge([
            'category_id' => '0',
            'list_rows' => 10,
            'field' => '*',
            'user_id' => '0',
        ], $param);
        // 筛选条件
        $filter = [];
        if ($params['category_id'] > 0) {
            $model = $model->where('category_id', $params['category_id']);
        }
        $withCollect = [
            'hasCollect' => function ($query) use ($params) {
                $query->where('uid', $params['user_id']);
            },
        ];
        $withImage = [
            'image' => function ($query) {
                $query->field(['file_id,file_url,file_name,storage,save_name'])->hidden(['file_id', 'file_url', 'file_name', 'storage', 'save_name']);
            },
        ];
        $list = $model
            ->field($params['field'])
            ->withJoin($withCollect)
            ->with($withImage)
            ->where($filter)
            ->order('id desc')
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);

        // 整理列表数据并返回
        return $this->setListData($list);
    }

    public function getList($param)
    {
        $model = $this;
        // 默认搜索条件
        $params = array_merge([
            'category_id' => '0',
            'text' => '',
            'list_rows' => 10,
            'field' => '*',
            'user_id' => '0'
        ], $param);

        // 筛选条件
        $filter = [];
        if ($params['category_id'] > 0) {
            $model = $model->where('category_id', $params['category_id']);
        }
        if ($params['text'] != '') {
            $params['text'] = "%{$params['text']}%";
            $model = $model
                ->where('name', 'like', $params['text']);
        }
        $with = [];
        if ($params['user_id'] > 0) {
            $withCollect = [
                'hasCollect' => function ($query) use ($params) {
                    $query->where('uid', $params['user_id']);
                },
            ];
            $with = array_merge($with, $withCollect);
        }

        $withImage = [
            'image' => function ($query) {
                $query->field(['file_id,file_url,file_name,storage,save_name'])->hidden(['file_id', 'file_url', 'file_name', 'storage', 'save_name']);
            },
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

    public function getInfo($id, $with = [])
    {
        $with = array_merge(['image' => function ($query) {
            $query->field(['file_id,file_url,file_name,storage,save_name'])->hidden(['storage', 'file_url', 'file_name', 'save_name']);
        }], $with);
        $model = $this
            ->where('id', '=', $id)
            ->with($with)
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
                'lid' => $id,
            ])->find();
            if ($res) {
                return true;
            } else {
                return (new LawyerCollectModel)->save([
                    'uid' => $user_id,
                    'lid' => $id,
                    'app_id' => self::$app_id,
                ]);
            }
        } else {
            return (new LawyerCollectModel)->where(['uid' => $user_id, 'lid' => $id])->delete();
        }
    }

    public function shopUser()
    {
        return $this->hasOne('app\\common\\model\\shop\\User', 'shop_user_id', 'admin_id');
    }

    public static function detail($id)
    {
        return self::find($id);
    }
}
