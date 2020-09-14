<?php

namespace app\api\model\notice;

use app\common\model\notice\Notice as NoticeModel;
use app\common\model\notice\NoticeInfo as NoticeInfoModel;

class Notice extends NoticeModel
{
    public function add(array $data)
    {
        $data['app_id'] = self::$app_id;
        // 开启事务
        $this->startTrans();
        try {
            $this->save($data);
            $this->commit();
            return $this;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    function list($param)
    {
        $params = array_merge([
            'list_rows' => 20,
        ], $param);
        $model = $this;
        $list = $model
            ->field('id,uid,type,name,user_unread as unread,create_time')
            ->with(['msg' => function ($query) {
                $query->field('nid,text,create_time')->order('create_time desc,id desc')->withLimit(1);
            }])
            ->where(['uid' => $params['uid'], 'is_delete' => 0])
            ->order('sort desc,update_time desc,id desc')
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);
        // 整理列表数据并返回
        foreach ($list as &$vo) {
            if (count($vo['msg']) >= 1) {
                $vo['text'] = $vo['msg'][0]['text'];
                $vo['time'] = time_tran($vo['msg'][0]['create_time']);
            } else {
                $vo['text'] = '';
                $vo['time'] = time_tran($vo['create_time']);
            }
            unset($vo['uid']);
            unset($vo['msg']);
            unset($vo['create_time']);
        }
        return $list;
    }

    public function info($param)
    {
        $params = array_merge([
            'list_rows' => 20,
        ], $param);
        $notice = $this->field('uid')->where('id', $params['id'])->find();
        if (!$notice) {
            return '未找到此消息';
        }
        if ($notice->uid != $params['uid']) {
            return '无权访问此消息';
        }
        $model = new NoticeInfoModel;
        $list = $model
            ->field('id,uid,text,create_time')
            ->with(['user' => function ($query) {
                $query->field('user_id,nickName,avatarUrl');
            }])
            ->where(['nid' => $params['id'], 'is_delete' => 0])
            ->order('update_time desc,id desc')
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);
        foreach ($list as &$vo) {
            if ($vo['uid'] == $params['uid']) {
                $vo['is_user'] = 1;
            } else {
                $vo['is_user'] = 0;
                if ($vo['uid'] == 0) {
                    $vo['user'] = [
                        'nickName' => '系统',
                    ];
                }
            }
            if (isset($vo['user']['user_id'])) {
                unset($vo['user']['user_id']);
            }
            list($vo['date'], $vo['time']) = explode(' ', $vo['create_time']);
            unset($vo['id']);
            unset($vo['uid']);
            unset($vo['create_time']);
        }
        // 整理列表数据并返回
        return $list;
    }
    
    /* 用户发送函数 */
    public function send($param)
    {
        $params = array_merge([], $param);
        $notice = $this->field('user_unread,admin_unread')->where('id', $params['nid'])->find();
        if (!$notice) {
            return '未找到此消息';
        }
        try {
            $model = new NoticeInfoModel;
            $data = $model->save([
                'nid' => $param['nid'],
                'uid' => $param['uid'],
                'text' => $param['text'],
                'app_id' => self::$app_id
            ]);
            if ($data) {
                // 增加未读消息数
                if($param['uid'] == 0){
                    $notice->user_unread += 1;
                }else{
                    $notice->admin_unread += 1;
                }
                $notice->save();
            }
            return $data;
        } catch (\Exception $e) {
            return (string) $e->getMessage();
        }
    }
}
