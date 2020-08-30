<?php

namespace app\shop\model\auth;

use app\common\model\shop\User as UserModel;

/**
 * 角色模型
 */
class User extends UserModel
{

    public function getList($limit = 20)
    {
        return $this->where('is_delete', '=', 0)->order(['create_time' => 'desc'])
            ->paginate($limit, false, [
                'query' => \request()->request(),
            ]);
    }

    /**
     * 获取所有角色列表
     */
    public function getList1()
    {
        $all = $this->getAll();
        return $this->formatTreeData($all);
    }

    public function getMenu()
    {
        $res = $this->where(['parent_id' => 0])->select();
        $role_id = array_column($res->toArray(), 'role_id');

        $where['parent_id'] = $role_id;
        $arr = $this->where($where)->select();
        return array_merge($role_id, array_column($arr->toArray(), 'role_id'));
    }

    public function getInfo($where)
    {
        // return $this->with(['access'])->where($where)->find();
        return $this->where($where)->find();
    }

    /**
     * 获取所有上级id集
     */
    public function getTopRoleIds($role_id, &$all = null)
    {
        static $ids = [];
        is_null($all) && $all = $this->getAll();
        foreach ($all as $item) {
            if ($item['role_id'] == $role_id && $item['parent_id'] > 0) {
                $ids[] = $item['parent_id'];
                $this->getTopRoleIds($item['parent_id'], $all);
            }
        }
        return $ids;
    }

    /**
     * 获取所有角色
     */
    private function getAll()
    {
        $data = $this->order(['sort' => 'asc', 'create_time' => 'asc'])->select();
        return $data ? $data->toArray() : [];
    }

    /**
     * 获取权限列表
     */
    private function formatTreeData(&$all, $parent_id = 0, $deep = 1)
    {
        static $tempTreeArr = [];
        foreach ($all as $key => $val) {
            if ($val['parent_id'] == $parent_id) {
                // 记录深度
                $val['deep'] = $deep;
                // 根据角色深度处理名称前缀
                $val['role_name_h1'] = $this->htmlPrefix($deep) . $val['role_name'];
                $tempTreeArr[] = $val;
                $this->formatTreeData($all, $val['role_id'], $deep + 1);
            }
        }
        return $tempTreeArr;
    }

    /**
     * 角色名称 html格式前缀
     */
    private function htmlPrefix($deep)
    {
        // 根据角色深度处理名称前缀
        $prefix = '';
        if ($deep > 1) {
            for ($i = 1; $i <= $deep - 1; $i++) {
                //                $prefix .= '&nbsp;&nbsp;&nbsp;├ ';
                $prefix .= '   ├ ';
            }
            //            $prefix .= '&nbsp;';
            $prefix .= ' ';
        }
        return $prefix;
    }

    public function addUser($data, $force = false)
    {
        $this->startTrans();
        try {
            $res = $this->where(['user_name' => trim($data['user_name'])])->find();
            // 是否已有此用户
            if ($res) {
                if (!$force) {
                    return false;
                } else {
                    // 强制添加 随机后缀
                    $data['user_name'] .= mt_rand(1000, 9999);
                }
            }
            // 是否为超级管理员
            $data['role'] = intval($data['role']);
            if ($data['role'] == 0) {
                $data['is_super'] = 1;
            } else {
                $data['is_super'] = 0;
            }
            // 生成数据
            $arr = [
                'user_name' => trim($data['user_name']),
                'password' => salt_hash($data['password']),
                'real_name' => trim($data['real_name']),
                'role' => $data['role'],
                'is_super' => $data['is_super'],
                'app_id' => self::$app_id,
            ];
            $res = self::create($arr);
            // 事务提交
            $this->commit();
            return $res;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    public function getUserName($where, $shop_user_id = 0)
    {
        if ($shop_user_id > 0) {
            return $this->where($where)->where('shop_user_id', '<>', $shop_user_id)->count();
        }
        return $this->where($where)->count();
    }

    public function editUser($data)
    {
        $this->startTrans();
        try {
            $res = $this->where(['user_name' => trim($data['user_name'])])->where('shop_user_id', '<>', $data['shop_user_id'])->find();
            // 是否已有此用户
            if ($res) {
                return false;
            }
            // 是否为超级管理员
            $data['role'] = intval($data['role']);
            if ($data['role'] == 0) {
                $data['is_super'] = 1;
            } else {
                $data['is_super'] = 0;
            }
            // 生成数据
            $arr = [
                'user_name' => $data['user_name'],
                'password' => salt_hash($data['password']),
                'real_name' => $data['real_name'],
                'role' => $data['role'],
                'is_super' => $data['is_super'],
                'app_id' => self::$app_id,
            ];
            if (empty($data['password'])) {
                unset($arr['password']);
            }
            $where['shop_user_id'] = $data['shop_user_id'];
            self::update($arr, $where);
            // 事务提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    public function getChild($where)
    {
        return $this->where($where)->count();
    }

    public function del($where)
    {
        self::update(['is_delete' => 1], $where);
        return UserRole::destroy($where);
    }
}
