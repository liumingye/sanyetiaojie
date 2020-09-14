<?php

namespace app\api\model\order;

use app\common\model\order\Mediate as MediateModel;
use app\common\model\order\MediateImage as MediateImageModel;

class Mediate extends MediateModel
{

    protected $type = [
        'create_time'    =>  'timestamp:Y-m-d',
    ];

    /**
     * 获取总数
     */
    public function getCount($user_id, $type = 'all')
    {
        // 筛选条件
        $filter = [];
        $filter['uid'] = $user_id;
        // 订单数据类型
        switch ($type) {
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
        return $this->where($filter)->count();
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
                $images = [];
                foreach ($arr as $vo) {
                    $images[] = [
                        'mid' => $this->id,
                        'image_id' => $vo,
                        'app_id' => self::$app_id,
                    ];
                }
                !empty($images) && (new MediateImageModel)->saveAll($images);
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
     * 补充附件
     */
    public function addImage($data)
    {
        if ($data['iFile'] != '') {
            $arr = explode(',', $data['iFile']);
            // 生成图片数据
            $data2 = [];
            foreach ($arr as $vo) {
                $data2[] = [
                    'mid' => $data['id'],
                    'image_id' => $vo,
                    'app_id' => self::$app_id,
                ];
            }
            !empty($data2) && (new MediateImageModel)->saveAll($data2);
            return true;
        }
        return false;
    }

    /**
     * 编辑
     */
    public function edit($where, array $data)
    {
        $data['app_id'] = self::$app_id;
        $mediate = $this->field('allow_edit')->where($where)->find();
        if ($mediate['allow_edit'] == 0) {
            $this->error = '无编辑权限';
            return false;
        }
        // 开启事务
        $this->startTrans();
        try {
            $this->allowEdit($where['id'], 0);
            (new MediateImageModel)->where(['mid' => $where['id']])->delete();
            if ($data['iFile'] != '') {
                $arr = explode(',', $data['iFile']);
                // 生成图片数据
                $images = [];
                foreach ($arr as $vo) {
                    $images[] = [
                        'mid' => $where['id'],
                        'image_id' => $vo,
                        'app_id' => self::$app_id,
                    ];
                }
                !empty($images) && (new MediateImageModel)->saveAll($images);
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
            ->field('id,cid,no,name,other_name,appeal,times,create_time,status')
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
            ->field('id,cid,no,name,mobile,idcard,my_area,my_address,appeal,other_name,other_phone,other_area,other_address,text,area,address,times,allow_edit,create_time,status')
            ->with(['info' => function ($query) {
                $query->field('mid,text,times,status,create_time')->order('create_time desc');
            },  'image.file', 'category' => function ($query) {
                $query->field('category_id,name');
            }])
            ->order(['create_time' => 'desc'])
            ->find();
        if ($data->info) {
            foreach ($data->info as &$vo) {
                $time = strtotime($vo->create_time);
                $vo->date = date('Y-m-d', $time);
                $week = date('N', $time);
                $weekarray = array("一", "二", "三", "四", "五", "六", "日");
                $vo->week = '周' . $weekarray[$week - 1];
                $vo->time = date('H:i:s', $time);
                $vo->state_text = $this->getStateTextAttr($vo->status, ['status' => $vo->status]);
                unset($vo->mid);
                unset($vo->create_time);
            }
            foreach ($data->image as $vo) {
                if (isset($vo->id)) {
                    unset($vo->id);
                }
                if (isset($vo->save_name)) {
                    unset($vo->save_name);
                }
            }
        }
        unset($data['id']);
        return $data;
    }
}
