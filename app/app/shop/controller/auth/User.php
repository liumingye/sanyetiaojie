<?php

namespace app\shop\controller\auth;

use app\shop\controller\Controller;
use app\shop\model\auth\User as UserModel;
use app\shop\model\shop\Access as AccessModel;
use app\shop\model\shop\AccessRole as AccessRoleModel;

/**
 * 管理员
 */
class User extends Controller
{
    public function index()
    {
        $model = new UserModel();
        $list = $model->getList();
        return $this->renderSuccess('', compact('list'));
    }

    public function add()
    {
        $model = new UserModel();
        $res = $model->addUser($this->postData());
        if ($res) {
            return $this->renderSuccess('添加成功');
        }
        return $this->renderError('添加失败');
    }

    public function edit($shop_user_id)
    {
        $model = new UserModel();
        if (request()->isPost()) {
            if ($shop_user_id == 1) {
                return $this->renderError('修改失败');
            }
            $res = $model->editUser($this->postData());
            if ($res) {
                return $this->renderSuccess('修改成功');
            }
            return $this->renderError('修改失败');
        }
        $info = $model->getInfo(['shop_user_id' => $shop_user_id]);
        return $this->renderSuccess('', compact('info'));
    }

    public function delete($shop_user_id)
    {
        $model = new UserModel();
        if ($shop_user_id == 1) {
            return $this->renderError('删除失败');
        }
        $res = $model->del(['shop_user_id' => $shop_user_id]);
        if ($res) {
            return $this->renderSuccess('删除成功');
        }
        return $this->renderError('删除失败');
    }

    /**
     * 获取角色菜单信息
     */
    public function getRoleList()
    {
        if ($this->store['user']['role'] == 0) {
            $model = new AccessModel();
            $menus = $model->getList();
        } else {
            $model = new AccessRoleModel();
            $menus = $model->getList($this->store['user']['role']);
        }
        return $this->renderSuccess('', compact('menus'));
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo()
    {
        $store = session('jjjshop_store');
        $user = [];
        if (!empty($store)) {
            $user = $store['user'];
        }
        return $this->renderSuccess('', compact('user'));
    }
}
