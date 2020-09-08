<?php

namespace app\shop\model\auth;

use app\common\model\BaseModel;

class Loginlog extends BaseModel
{
    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->belongsTo("app\\shop\\model\\auth\\User", 'uid', 'shop_user_id');
    }
    public function empty()
    {
        $this->query("truncate table `" . config('database.connections.mysql.prefix') . "loginlog`");
    }
    public function add($param = [])
    {
        $data = $this->save([
            'username' => $param['username'],
            'result' => $param['result'],
            'ip' => $param['ip']
        ]);
        return $data;
    }
    public function getList($param = [])
    {
        $model = $this;
        // 默认搜索条件
        $params = array_merge([
            'username' => '',
            'list_rows' => 20,
            'field' => '*'
        ], $param);
        $model = $model->withoutGlobalScope();
        if ($params['username'] != '') {
            $params['username'] = "%{$params['username']}%";
            $model = $model->where('username', 'like', $params['username']);
        }
        // 筛选条件
        $res = $model
            ->field('id,username,ip,result,create_time')
            ->order(['create_time' => 'desc'])
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);
        foreach ($res as &$vo) {
            $vo['ip'] = long2ip($vo['ip']);
        }
        return $res;
    }
}
