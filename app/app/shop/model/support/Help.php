<?php

namespace app\shop\model\support;

use app\common\model\support\Help as HelpModel;

class Help extends HelpModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'update_time',
    ];
    
    /**
     * 关联人员表
     */
    public function staff()
    {
        return $this->hasMany('app\\shop\\model\\support\\HelpRelation', 'hid', 'id');
    }

    public function getList($type, $params = null, $role = 0)
    {
        $model = $this;
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
            case 'fail';
                $filter['status'] = 4;
                break;
        }
        // 获取数据列表
        if ($role == 1 || $role == 2) {
            $model = $model->rightJoin('help_relation', 'hid=id');
        }
        return $model
            ->with(['user' => function ($query) {
                $query->field('user_id,nickName');
            }])
            ->order(['id desc'])
            ->where($filter)
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);
    }

    /* 获取详情 */
    public static function detail($where, $with = [])
    {
        is_array($where) ? $filter = $where : $filter['id'] = (int) $where;
        return self::with($with)->find($where);
    }

    /**
     * 总数
     */
    public function getHelpTotal($where = [])
    {
        return $this->where($where)->count();
    }

    /* 编辑详情 */
    public function editInfo($id, $name, $value)
    {
        // 开启事务
        $this->startTrans();
        try {
            $this->where('id', $id)->save([
                $name => $value
            ]);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
}
