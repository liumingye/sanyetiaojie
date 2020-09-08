<?php

namespace app\api\model\user;

use app\common\exception\BaseException;
use app\common\model\user\User as UserModel;
use app\common\library\easywechat\AppWx;

/**
 * 用户模型类
 */
class User extends UserModel
{
    private $token;

    /**
     * 隐藏字段
     */
    protected $hidden = [
        'open_id',
        'is_delete',
        'app_id',
        'create_time',
        'update_time'
    ];

    /**
     * 获取用户信息
     */
    public static function getUser($token)
    {
        return self::where(['open_id' => $token])->find();
    }

    /**
     * 用户登录
     */
    public function login($post)
    {
        // 微信登录 获取session_key
        $app = AppWx::getApp();
        $session = $app->auth->session($post['code']);
        // 自动注册用户
        $userInfo = json_decode(htmlspecialchars_decode($post['user_info']), true);

        $model = $this->register($session['openid'], $userInfo, $session['session_key']);
        $this->token = $session['openid'];
        return $model['user_id'];
    }

    /**
     * 获取手机号
     */
    public function getPhone($post)
    {
        $app = AppWx::getApp();
        $user = $this->getUser($post['token']);
        $decryptedData = $app->encryptor->decryptData($user['session_key'], $post['iv'], $post['encryptedData']);
        try {
            $this->startTrans();
            $this->where('user_id', $user['user_id'])->save([
                'mobile' => $decryptedData['phoneNumber']
            ]);
            $this->commit();
        } catch (\Exception $e) {
        }
        return $decryptedData;
    }

    /**
     * 获取token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * 自动注册用户
     */
    private function register($open_id, $data, $session_key = '')
    {
        $user = self::detail(['open_id' => $open_id]) ?: $this;
        if ($user) {
            $model = $user;
        } else {
            $model = $this;
            $data['reg_source'] = 'wx';
        }
        $this->startTrans();
        try {
            // 保存/更新用户记录
            if (!$model->save(array_merge($data, [
                'open_id' => $open_id,
                'session_key' => $session_key,
                'app_id' => self::$app_id
            ]))) {
                throw new BaseException(['msg' => '用户注册失败']);
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw new BaseException(['msg' => $e->getMessage()]);
        }
        return $model;
    }
}
