<?php

namespace app\shop\model\notice;

use app\common\model\notice\Notice as NoticeModel;
use app\common\model\notice\NoticeInfo as NoticeInfoModel;

class Notice extends NoticeModel
{
    public function getList($params = null, $role = 0)
    {
        $model = $this->alias('a');
        //搜索ID
        if (isset($params['id']) && $params['id'] != '') {
            $model = $model->where('id',  trim($params['id']));
        }
        //用户ID
        if (isset($params['uid']) && $params['uid'] != '') {
            $model = $model->where('a.uid',  trim($params['uid']));
        }
        //搜索类型
        if (!isset($params['type'])) {
            return false;
        }
        switch ($params['type']) {
            case 'mediate':
                $model = $model->where('type', 3);
                if ($role == 1 || $role == 2) {
                    $model = $model->rightJoin('mediate_relation b', 'a.mid=b.mid');
                }
                break;
            case 'help':
                $model = $model->where('type', 2);
                if ($role == 1 || $role == 2) {
                    $model = $model->rightJoin('help_relation b', 'a.hid=b.hid');
                }
                break;
            case 'system';
                $model = $model->where('type', 1);
                break;
            default:
                $model = $model->where('type', trim($params['type']));
        }

        // 获取数据列表
        return $model
            ->field('id,a.uid,type,name,admin_unread as unread,a.create_time,u.user_id,u.nickName')
            ->order(['id desc'])
            ->rightJoin('user u', 'u.user_id=a.uid')
            ->with(['msg' => function ($query) {
                $query->field('nid,text,create_time')->order('create_time desc,id desc')->withLimit(1);
            }])
            ->group('id')
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);
    }

    public function info($param)
    {
        $params = array_merge([
            'list_rows' => 20,
            'aid' => 0
        ], $param);
        $notice = $this->field('uid')->where('id', $params['id'])->find();
        if (!$notice) {
            return '未找到此消息';
        }
        $model = new NoticeInfoModel;
        $list = $model
            ->field('id,uid,aid,text,create_time')
            ->with(['user' => function ($query) {
                $query->field('user_id,nickName,avatarUrl');
            }, 'admin_user' => function ($query) {
                $query->field('shop_user_id,real_name');
            }])
            ->where(['nid' => $params['id'], 'is_delete' => 0])
            ->order('update_time desc,id desc')
            ->paginate($params, false, [
                'query' => \request()->request(),
            ]);
        foreach ($list as &$vo) {
            if ($vo['aid'] == $params['aid']) {
                $vo['is_user'] = 0;
            } else {
                $vo['is_user'] = 1;
            }
            if ($vo['uid'] == 0 && $vo['aid'] == 0) {
                $vo['user'] = [
                    'nickName' => '系统',
                ];
            } elseif ($vo['aid'] != 0) {
                $vo['user'] = [
                    'nickName' => $vo['admin_user']['real_name'],
                ];
            }
            unset($vo->id, $vo->uid, $vo->aid, $vo->admin_user);
        }
        $list = $list->toArray();
        $list['data'] = array_reverse($list['data']);
        // 整理列表数据并返回
        return $list;
    }

    /* 系统发送函数 */
    public function send($param)
    {
        $params = array_merge([], $param);
        $notice = $this->field('user_unread')->where('id', $params['nid'])->find();
        if (!$notice) {
            return '未找到此消息';
        }
        $this->startTrans();
        try {
            $model = new NoticeInfoModel;
            $data = $model->save([
                'nid' => $param['nid'],
                'uid' => $param['uid'],
                'aid' => $param['aid'],
                'text' => $param['text'],
                'app_id' => self::$app_id
            ]);
            if ($data) {
                // 增加用户未读消息数
                $notice->user_unread += 1;
                $notice->save();
            }
            $this->commit();
            return $data;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
}
