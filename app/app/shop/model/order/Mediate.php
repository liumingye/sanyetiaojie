<?php

namespace app\shop\model\order;

use app\common\model\order\Mediate as MediateModel;

/**
 * 订单模型
 */
class Mediate extends MediateModel
{
    /**
     * 订单列表
     */
    public function getList($type, $params = null)
    {
        $model = $this;
        //搜索订单号
        if (isset($params['no']) && $params['no'] != '') {
            $model = $model->where('no', 'like', '%' . trim($params['no']) . '%');
        }
        //搜索姓名
        if (isset($params['name']) && $params['name'] != '') {
            $model = $model->where('name', 'like', '%' . trim($params['name']) . '%');
        }
        //搜索时间段
        if (isset($params['create_time']) && $params['create_time'] != '') {
            $sta_time = array_shift($params['create_time']);
            $end_time = array_pop($params['create_time']);
            $model = $model->whereBetweenTime('create_time', $sta_time, $end_time);
        }
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all':
                break;
            case 'accepting';
                $filter['status'] = 1;
                break;
            case 'adjusting';
                $filter['status'] = 2;
                break;
            case 'adjusted';
                $filter['status'] = 3;
                break;
        }
        // 获取数据列表
        return $model
            ->order(['id desc'])
            ->where($filter)
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);
    }

    public function getListByRole($type, $params = null, $role)
    {
        $model = $this;
        //搜索订单号
        if (isset($params['no']) && $params['no'] != '') {
            $model = $model->where('no', 'like', '%' . trim($params['no']) . '%');
        }
        //搜索姓名
        if (isset($params['name']) && $params['name'] != '') {
            $model = $model->where('name', 'like', '%' . trim($params['name']) . '%');
        }
        //搜索时间段
        if (isset($params['create_time']) && $params['create_time'] != '') {
            $sta_time = array_shift($params['create_time']);
            $end_time = array_pop($params['create_time']);
            $model = $model->whereBetweenTime('create_time', $sta_time, $end_time);
        }
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all':
                break;
            case 'accepting';
                $filter['status'] = 1;
                break;
            case 'adjusting';
                $filter['status'] = 2;
                break;
            case 'adjusted';
                $filter['status'] = 3;
                break;
        }
        // 获取数据列表
        if ($role == 1) {
            $model = $model->rightJoin('mediate_committee', 'mid=id');
        } else {
            $model = $model->rightJoin('mediate_lawyer', 'mid=id');
        }
        return $model
            ->order(['create_time' => 'desc'])
            ->where($filter)
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);
    }

    /**
     * 获取订单总数
     */
    public function getCount($type = 'all')
    {
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all':
                break;
            case 'accepting';
                $filter['status'] = 1;
                break;
            case 'adjusting';
                $filter['status'] = 2;
                break;
            case 'adjusted';
                $filter['status'] = 3;
                break;
        }
        return $this->where($filter)->count();
    }

    public function getCountByRole($type = 'all', $role)
    {
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all':
                break;
            case 'accepting';
                $filter['status'] = 1;
                break;
            case 'adjusting';
                $filter['status'] = 2;
                break;
            case 'adjusted';
                $filter['status'] = 3;
                break;
        }
        $model = $this;
        if ($role == 1) {
            $model = $model->rightJoin('mediate_committee', 'mid=id');
        } else {
            $model = $model->rightJoin('mediate_lawyer', 'mid=id');
        }
        return $model->where($filter)->count();
    }

    public static function detail($where, $with = [])
    {
        is_array($where) ? $filter = $where : $filter['id'] = (int) $where;
        return self::with($with)->find($where);
    }

    public function getMediateTotal($where = [])
    {
        return $this->where($where)->count();
    }

}
