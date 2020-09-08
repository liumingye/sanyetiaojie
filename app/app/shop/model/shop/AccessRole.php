<?php

namespace app\shop\model\shop;

use app\shop\model\shop\Access as AccessModel;
use app\common\model\shop\AccessRole as AccessRoleModel;

class AccessRole extends AccessRoleModel
{
    /**
     * 获取权限列表
     */
    public function getList($role)
    {
        $all = static::withoutGlobalScope()
            ->field('aid')
            ->where('rid', $role)
            ->select()
            ->toArray();
        $all = array_column($all, 'aid');
        $res = (new AccessModel)
            ->withoutGlobalScope()->where('access_id', 'IN', $all)
            ->hidden(['url', 'create_time', 'update_time', 'is_show'])
            ->where(['is_show' => 1])
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->select()
            ->toArray();
        $res = (new AccessModel)->recursiveMenuArray($res, 0);
        return array_values((new AccessModel)->foo($res));
    }
}
