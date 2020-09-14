<?php

namespace app\common\model\order;

use app\common\model\BaseModel;
use app\common\service\order\OrderService;

/**
 * 订单模型模型
 */
class Mediate extends BaseModel
{
    //表名
    protected $name = 'mediate';
    //主键字段名
    protected $pk = 'id';

    //追加字段
    protected $append = [
        'state_text', // 状态文字描述
    ];

    protected $type = [
        'court_time'    =>  'timestamp:Y-m-d H:i:s',
        'closing_time'    =>  'timestamp:Y-m-d H:i:s',
    ];

    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->belongsTo("app\\common\\model\\user\\User", 'uid', 'user_id');
    }

    /**
     * 关联进度表
     */
    public function info()
    {
        return $this->hasMany("app\\common\\model\\order\\MediateInfo", 'mid', 'id');
    }

    /**
     * 关联分类表
     */
    public function category()
    {
        return $this->belongsTo('app\\common\\model\\product\\Category', 'cid', 'category_id');
    }

    /**
     * 关联图片表
     */
    public function image()
    {
        return $this->hasMany('app\\common\\model\\order\\MediateImage', 'mid', 'id')->order(['id' => 'asc']);
    }

    /**
     * 状态文字描述
     */
    public function getStateTextAttr($value, $data)
    {
        if ($data['status'] == 1) {
            return '待调解';
        } else if ($data['status'] == 2) {
            return '调解中';
        } else if ($data['status'] == 3) {
            return '已调解';
        } else if ($data['status'] == 4) {
            return '调解失败';
        }
        return $value;
    }

    /**
     * 获取列表
     */
    public function getList($uid, $status = 0)
    {
        $model = $this;
        $params = [
            'list_rows' => 20,
        ];
        //搜索手机号
        $model = $model->where('uid', '=', trim($uid));
        $filter = [];
        switch ($status) {
            case 1:
                $filter['status'] = 1;
                break;
            case 2:
                $filter['status'] = 2;
                break;
            case 3:
                $filter['status'] = 3;
                break;
            case 4:
                $filter['status'] = 4;
                break;
        }
        // 获取数据列表
        return $model
            ->field('id,cid,no,name,mobile,idcard,my_address,appeal,other_name,other_phone,other_address,text,area,address,times,create_time,way,status')
            ->with(['category' => function ($query) {
                $query->field('category_id,name');
            }])
            ->where($filter)
            ->order(['create_time' => 'desc'])
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);
    }

    public function allowEdit($id, $val)
    {
        // 开启事务
        $this->startTrans();
        try {
            $this->where('id', $id)->save([
                'allow_edit' => $val
            ]);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 生成订单号
     */
    public function orderNo()
    {
        return OrderService::createOrderNo();
    }
}
