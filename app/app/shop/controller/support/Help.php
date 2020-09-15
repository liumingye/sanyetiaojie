<?php

namespace app\shop\controller\support;

use app\shop\controller\Controller;
use app\shop\model\support\Help as HelpModel;
use app\shop\model\product\Category as CategoryModel;
use app\shop\model\notice\Notice as NoticeModel;
use app\shop\model\support\HelpRelation as HelpRelationModel;
use app\common\model\support\HelpImage as HelpImageModel;
use app\shop\model\auth\User as UserModel;
use app\shop\model\file\UploadFile as UploadFileModel;

class Help extends Controller
{

    public function getNotice($id)
    {
        $data = (new NoticeModel)->field('id')->where('hid', $id)->find();
        if ($data) {
            return $this->renderSuccess('', $data);
        }
        return $this->renderError('未找到消息');
    }

    /**
     * 列表
     */
    public function lists($type = 'all')
    {
        // 分类
        $model = new CategoryModel;
        $category = $model->getCacheTree();
        $category = array_combine(array_column($category, 'category_id'), $category);

        // 订单列表
        $model = new HelpModel();
        $role = $this->store['user']['role'];
        if ($role == 1 || $role == 2) {
            $list = $model->getList($type, $this->postData(), $role);
            /*$order_count = [
                'order_count' => [
                    'accepting' => $model->getCountByRole('accepting', $role),
                    'adjusting' => $model->getCountByRole('adjusting', $role),
                ],
            ];*/
        } else {
            $list = $model->getList($type, $this->postData());
            /*$order_count = [
                'order_count' => [
                    'accepting' => $model->getCount('accepting'),
                    'adjusting' => $model->getCount('adjusting'),
                ],
            ];*/
        }

        foreach ($list as &$vo) {
            if (isset($category[$vo['cid']])) {
                $vo['category_name'] = $category[$vo['cid']]['name'];
            } else {
                $vo['category_name'] = '未分类';
            }
        }

        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 详情
     */
    public function detail($id)
    {
        $detail = HelpModel::detail($id, ['user' => function ($query) {
            $query->field('user_id,nickName');
        }, 'image.file', 'staff' =>  function ($query) {
            $query->field('hid,uid');
        }]);
        foreach ($detail->image as $vo) {
            if (isset($vo->save_name)) {
                if ($vo->file_type == 'image') {
                    $vo->thumb_path =  base_url() . "shop.php/image.thumb?src=" . $vo->save_name;
                }
                unset($vo->save_name);
            }
        }
        $staff = array_column($detail->staff->toArray(), 'uid');
        unset($detail->staff);
        $detail->__set('staff', $staff);
        return $this->renderSuccess('', compact('detail'));
    }

    /* 设置人员 */
    public function setRole()
    {
        $data = $this->postData();
        $id = $data['id'];
        $userlist = isset($data['userlist']) ? $data['userlist'] : [];

        $model = new HelpRelationModel;
        $model->where('hid', $id)->delete();
        $role1 = []; // 委员会
        $role2 = []; // 律师
        if (count($userlist) > 0) {
            $res = (new UserModel)->field('real_name,role')->where('shop_user_id', 'IN', $userlist)->select();
            if ($res) {
                foreach ($res as $vo) {
                    if ($vo['role'] == 1) {
                        $role1[] = $vo['real_name'];
                    } elseif ($vo['role'] == 2) {
                        $role2[] = $vo['real_name'];
                    }
                }
            }
            $model->addAll($id, $userlist);
        } else {
            return $this->renderError('请设置人员');
        }
        if (count($role1) > 0 || count($role2) > 0) {
            // 发送消息
            $text = '';
            if (count($role1) > 0) {
                $text .= '调解员：' . implode(",", $role1);
            }
            if (count($role2) > 0) {
                if ($text != '') {
                    $text .= ' ';
                }
                $text .= '律师：' . implode(",", $role2);
            }
            $text =  '已分配 ' . $text . ' 负责此问题处理';
            (new NoticeModel)->sendHelpNotice($id,  $text);
        }
        $this->setWay($id, 3);
        return $this->renderSuccess('设置成功');
    }

    /* 调解完成 */
    public function setComplete($id)
    {
        if (!request()->isPost()) {
            return $this->renderError('请求错误');
        }
        $model = HelpModel::find($id);
        if ($model) {
            $model->status = 3;
            if ($model->save()) {
                (new NoticeModel)->sendSystemNotice($model->uid, '您有一个问题已处理成功');
                (new NoticeModel)->sendHelpNotice($id,  '问题已处理成功');
                return $this->renderSuccess('提交成功');
            }
            return $this->renderError($model->getError() ?: '提交失败');
        }
        return $this->renderError('提交失败');
    }

    /**
     * 编辑详情
     */
    public function editInfo($id, $name, $value = '')
    {
        if ((new HelpModel)->editInfo($id, $name, $value)) {
            return $this->renderSuccess('保存成功');
        }
        return $this->renderError('保存失败');
    }

    /**
     * 设置进度 
     * 0 未选择调解方式
     * 1 电话调解
     * 2 律师调解
     * 3 律师调解确认人员
     */
    public function setWay($id, $way)
    {
        if (!request()->isPost()) {
            return $this->renderError('请求错误');
        }
        $model = HelpModel::find($id);
        if ($model) {
            $model->way = $way;
            if ($way == 0) {
                $model->status = 1;
            } else {
                $model->status = 2;
                (new NoticeModel)->sendSystemNotice($model->uid, '您有一个问题正在处理中');
            }
            if ($model->save()) {
                return $this->renderSuccess('提交成功');
            }
            return $this->renderError($model->getError() ?: '提交失败');
        } else {
            return $this->renderError('提交失败');
        }
    }

    /* 删除附件 */
    public function delImage($id)
    {
        $helpImage = HelpImageModel::where('id', $id)->find();
        // 验证权限
        $user = $this->store['user'];
        if ($user['role'] == 1 || $user['role'] == 2) {
            $helpRelation = HelpRelationModel::where('hid', $helpImage->hid)->count();
            if ($helpRelation == 0) {
                return $this->renderError('删除失败');
            }
        }
        // 删除本地文件
        (new UploadFileModel)->remove([$helpImage->image_id]);
        // 删除数据库记录
        if (!$helpImage->delete()) {
            return $this->renderError($helpImage->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }
}
