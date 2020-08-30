<?php

namespace app\shop\model\support;

use app\common\model\support\Lawyer as LawyerModel;
use app\shop\model\support\LawyerCat as CategoryModel;
use app\shop\model\auth\User as UserModel;

class Lawyer extends LawyerModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'image_id',
        'app_id',
        'status',
        'update_time'
    ];

    public function getBaseData()
    {
        $category = (new CategoryModel)->getCacheTreeSimple(false);
        return compact('category');
    }

    /**
     * 删除律师
     */
    public function remove()
    {
        $adminId = $this->admin_id;
        if ($adminId > 0) {
            (new UserModel)->del(['shop_user_id' => $adminId]);
        }
        return $this->delete();
    }

    private function genUserNumber()
    {
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $username = "";
        for ($i = 0; $i < 6; $i++) {
            $username .= $chars[mt_rand(0, strlen($chars))];
        }
        return strtoupper(base_convert(time() - 1420070400, 10, 36)) . $username;
    }

    /**
     * 添加律师
     */
    public function add(array $data)
    {
        $data['app_id'] = self::$app_id;
        // 开启事务
        $this->startTrans();
        try {
            if (isset($data['user_name'])) {
                $data['user_name'] = trim($data['user_name']);
                if ($data['user_name'] == '') {
                    $data['user_name'] = $this->genUserNumber();
                }
            } else {
                $data['user_name'] = $this->genUserNumber();
            }
            if (isset($data['password'])) {
                $data['password'] = trim($data['password']);
                if ($data['password'] == '') {
                    $data['password'] = $this->genUserNumber();
                }
            } else {
                $data['password'] = $this->genUserNumber();
            }
            $adminUser = (new UserModel)->addUser([
                'user_name' => $data['user_name'],
                'password' => $data['password'],
                'real_name' => $data['user_name'],
                'role' => 2
            ], true);
            $data['admin_id'] = $adminUser->shop_user_id;
            $this->save($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 编辑律师
     */
    public function edit($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->transaction(function () use ($data) {
            // 保存商品
            $this->save($data);
            return true;
        });
    }
}
