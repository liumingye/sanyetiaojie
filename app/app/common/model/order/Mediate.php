<?php

namespace app\common\model\order;

use app\common\model\BaseModel;
use app\common\model\order\MediateImage as MediateImageModel;
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
        'state_text', // 售后单状态文字描述
    ];

    /**
     * 订单模型初始化
     */
    public static function init()
    {
        parent::init();
        $model = new static;
        event('Order', $model);
    }

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

    // public function committee()
    // {
    //     return $this->hasMany('app\\shop\\model\\order\\MediateCommittee', 'mid', 'id');
    // }

    // public function lawyer()
    // {
    //     return $this->hasMany('app\\shop\\model\\order\\MediateLawyer', 'mid', 'id');
    // }

    /**
     * 订单状态文字描述
     */
    public function getStateTextAttr($value, $data)
    {
        // 付款状态
        if ($data['status'] == 1) {
            return '待受理';
        }
        // 订单类型：单独购买
        else if ($data['status'] == 2) {
            return '调解中';
        } else if ($data['status'] == 3) {
            return '已调解';
        }
        return $value;
    }

    /**
     * 添加
     */
    public function add(array $data)
    {
        $data['app_id'] = self::$app_id;
        // 开启事务
        $this->startTrans();
        try {
            $this->save($data);
            if ($data['iFile'] != '') {
                $arr = explode(',', $data['iFile']);
                // 生成图片数据
                $data = [];
                foreach ($arr as $vo) {
                    $data[] = [
                        'mid' => $this->id,
                        'image_id' => $vo,
                        'app_id' => self::$app_id,
                    ];
                }
                !empty($data) && (new MediateImageModel)->saveAll($data);
            }
            $this->commit();
            return $this;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 获取列表
     */
    public function getList($uid)
    {
        $model = $this;
        $params = [
            'list_rows' => 20,
        ];
        //搜索手机号
        $model = $model->where('uid', '=', trim($uid));
        // 获取数据列表
        return $model
            ->field('id,cid,no,name,mobile,idcard,appeal,other_name,other_phone,text,area,address,times,create_time,way,status')
            ->with(['category' => function ($query) {
                $query->field('category_id,name');
            }])
            ->order(['create_time' => 'desc'])
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);
    }

    /**
     * 获取列表 By 电话
     */
    public function getListByPhone($phone)
    {
        $model = $this;
        $params = [
            'list_rows' => 20,
        ];
        //搜索手机号
        $model = $model->where('mobile', '=', trim($phone));
        $model = $model->whereOr('no', '=', trim($phone));
        // 获取数据列表
        return $model
            ->field('id,cid,no,name,mobile,idcard,appeal,other_name,other_phone,text,area,address,times,create_time,status')
            ->with(['category' => function ($query) {
                $query->field('category_id,name');
            }])
            ->order(['create_time' => 'desc'])
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);
    }

    /**
     * 获取详情 By ID&No
     */
    public function getInfoByIdNo($id, $no)
    {
        $model = $this;
        $model = $model->where(['id' => trim($id), 'no' => trim($no)]);
        // 获取数据列表
        $data = $model
            ->field('id,cid,no,name,mobile,idcard,appeal,other_name,other_phone,text,area,address,times,create_time,status')
            ->with(['info' => function ($query) {
                $query->field('mid,text,status,create_time')->order('create_time desc');
            }, 'category' => function ($query) {
                $query->field('category_id,name');
            }])
            ->order(['create_time' => 'desc'])
            ->find();
        if ($data['info']) {
            foreach ($data['info'] as &$vo) {
                $time = strtotime($vo['create_time']);
                $vo['date'] = date('Y-m-d', $time);
                $week = date('N', $time);
                $weekarray = array("日", "一", "二", "三", "四", "五", "六");
                $vo['week'] = '周' . $weekarray[$week];
                $vo['time'] = date('H:i:s', $time);
                $vo['state_text'] = $this->getStateTextAttr($vo['status'], ['status' => $vo['status']]);
                unset($vo['mid']);
                unset($vo['create_time']);
            }
        }
        unset($data['id']);
        return $data;
    }

    /**
     * 生成订单号
     */
    public function orderNo()
    {
        return OrderService::createOrderNo();
    }
}
