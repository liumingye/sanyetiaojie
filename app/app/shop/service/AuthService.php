<?php

namespace app\shop\service;

use think\facade\Session;
use app\shop\model\auth\User;

use app\shop\model\shop\Access as AccessModel;
use app\shop\model\shop\AccessRole as AccessRoleModel;

/**
 * 商家后台权限业务
 */
class AuthService
{
    // 实例
    static public $instance;

    // 商家登录信息
    private $store;

    // 商家用户信息
    private $user;

    // 权限验证白名单
    protected $allowAllAction = [
        // 用户登录
        'passport/login',
        // 退出登录
        'passport/logout',
        // 修改密码
        'passport/editPass',
        // 获取权限列表
        'auth/user/getRoleList',
        // 获取用户信息
        'auth/user/getUserInfo',
        // 修改当前用户信息
        // 'store.user/renew',
        // 文件库
        // 'upload.library/*',
        // 图片上传
        // 'upload/image',
        // 数据选择
        // 'data/*',
    ];

    /** @var array $accessUrls 商家用户权限url */
    private $accessUrls = [];

    /**
     * 公有化获取实例方法
     * @return Auth
     * @throws \think\exception\DbException
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof AuthService)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 私有化构造方法
     */
    private function __construct()
    {
        // 商家登录信息
        $this->store = Session::get('sy_store');
        // 当前用户信息
        $this->user = User::detail($this->store['user']['shop_user_id']);
    }

    /**
     * 私有化克隆方法
     */
    private function __clone()
    {
    }

    /**
     * 验证指定url是否有访问权限
     */
    public function checkPrivilege($url, $strict = true)
    {
        if (!is_array($url)) :
            return $this->checkAccess($url);
        else :
            foreach ($url as $val) :
                if ($strict && !$this->checkAccess($val)) {
                    return false;
                }
                if (!$strict && $this->checkAccess($val)) {
                    return true;
                }
            endforeach;
        endif;
        return true;
    }

    /**
     * @param string $url
     */
    private function checkAccess($url)
    {

        // 超级管理员无需验证
        if ($this->user['is_super']) {
            return true;
        }

        $find = array('._');
        $replace = array('/');
        $url = str_replace($find, $replace, $url);

        // 验证当前请求是否在白名单
        if (in_array($url, $this->allowAllAction)) {
            return true;
        }

        // 通配符支持
        foreach ($this->allowAllAction as $action) {
            if (
                strpos($action, '*') !== false
                && preg_match('/^' . str_replace('/', '\/', $action) . '/', $url)
            ) {
                return true;
            }
        }

        // 获取当前用户的权限url列表
        if (!in_array($url, $this->getAccessUrls())) {
            return false;
        }
        return true;
    }

    /**
     * 获取当前用户的权限url列表
     */
    private function getAccessUrls()
    {
        if (empty($this->accessUrls)) {
            // 获取当前用户的角色集
            $all = (new AccessRoleModel)->withoutGlobalScope()
                ->field('aid')
                ->where('rid', $this->user['role'])
                ->select()
                ->toArray();
            $all = array_column($all, 'aid');
            $res = (new AccessModel)
                ->withoutGlobalScope()
                ->field('url')
                ->where('access_id', 'IN', $all)
                ->order('sort')
                ->select()
                ->toArray();
            $res = array_filter(array_column($res, 'url'));
            $this->accessUrls = $res;
        }
        return $this->accessUrls;
    }
}
