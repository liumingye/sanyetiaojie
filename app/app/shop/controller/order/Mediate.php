<?php

namespace app\shop\controller\order;

use app\shop\controller\Controller;
use app\shop\model\order\Mediate as MediateModel;
use app\shop\model\product\Category as CategoryModel;
use app\shop\model\auth\User as UserModel;
use app\shop\model\order\MediateRelation as MediateRelationModel;
use app\api\model\order\MediateInfo as MediateInfoModel;
use app\shop\model\notice\Notice as NoticeModel;
use app\common\model\BaseModel;


class Mediate extends Controller
{

    public function getNotice($id)
    {
        $data = (new NoticeModel)->field('id')->where('mid', $id)->find();
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

        // 列表
        $model = new MediateModel();
        $user = $this->store['user'];
        if ($user['role'] == 1 || $user['role'] == 2) {
            $list = $model->getList($type, $this->postData(), $user);
            $order_count = [
                'order_count' => [
                    'accepting' => $model->getCountByRole('accepting', $user),
                    'adjusting' => $model->getCountByRole('adjusting', $user),
                ],
            ];
        } else {
            $list = $model->getList($type, $this->postData());
            $order_count = [
                'order_count' => [
                    'accepting' => $model->getCount('accepting'),
                    'adjusting' => $model->getCount('adjusting'),
                ],
            ];
        }
        foreach ($list as &$vo) {
            if (isset($category[$vo['cid']])) {
                $vo['category_name'] = $category[$vo['cid']]['name'];
            } else {
                $vo['category_name'] = '未分类';
            }
        }
        return $this->renderSuccess('', compact('list', 'order_count'));
    }

    /**
     * 编辑详情
     */
    public function editInfo($id, $name, $value = '')
    {
        if ((new MediateModel)->editInfo($id, $name, $value)) {
            return $this->renderSuccess('保存成功');
        }
        return $this->renderError('保存失败');
    }

    /**
     * 详情
     */
    public function detail($id)
    {
        $detail = MediateModel::detail($id, ['user' => function ($query) {
            $query->field('user_id,nickName');
        }, 'image.file', 'info' => function ($query) {
            $query->field('id,mid,text,times,status,create_time')->order('create_time desc');
        }]);
        return $this->renderSuccess('', compact('detail'));
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
        $model = MediateModel::find($id);
        if ($model) {
            $model->way = $way;
            if ($way == 0) {
                $model->status = 1;
            } else {
                if ($way == 1) {
                    // 电话调解加进度
                    (new MediateInfoModel)->add([
                        'mid' => $id,
                        'text' => '电话调解中',
                        'status' => 2
                    ]);
                    (new NoticeModel)->sendMediateNotice($id,  '正在进行电话调解中');
                }
                $model->status = 2;
                (new NoticeModel)->sendSystemNotice($model->uid, '您有一个正在纠纷调解中');
            }
            if ($model->save()) {
                return $this->renderSuccess('提交成功');
            }
            return $this->renderError($model->getError() ?: '提交失败');
        } else {
            return $this->renderError('提交失败');
        }
    }

    /* 调解完成 */
    public function setComplete($id)
    {
        if (!request()->isPost()) {
            return $this->renderError('请求错误');
        }
        $model = MediateModel::find($id);
        if ($model) {
            $model->status = 3;
            if ($model->save()) {
                (new MediateInfoModel)->add([
                    'mid' => $id,
                    'text' => '双方和解，调解成功',
                    'status' => 3
                ]);
                (new NoticeModel)->sendSystemNotice($model->uid, '您有一个纠纷已调解成功');
                (new NoticeModel)->sendMediateNotice($id,  '案件已调解成功');
                return $this->renderSuccess('提交成功');
            }
            return $this->renderError($model->getError() ?: '提交失败');
        }
        return $this->renderError('提交失败');
    }

    /* 调解失败 */
    public function setFail($id, $type)
    {
        if ($this->setWay($id, 0)) {
            $text = '';
            if ($type == 1) {
                $text = '电话';
            } elseif ($type == 2) {
                $text = '委员会';
            }
            $text .= '调解失败';
            (new NoticeModel)->sendMediateNotice($id,  $text);
            (new MediateInfoModel)->add([
                'mid' => $id,
                'text' => $text,
                'status' => 4
            ]);
            (new MediateModel)->where('id', $id)->inc('times')->update();
            return $this->renderSuccess('提交成功');
        }
        return $this->renderError('提交失败');
    }

    /* 设置人员 */
    public function setRole()
    {
        $data = $this->postData();
        $id = $data['id'];
        $userlist = isset($data['userlist']) ? $data['userlist'] : [];

        $model = new MediateRelationModel;
        $model->where('mid', $id)->delete();
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
            // 律师调解加进度
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
            $text =  '已分配 ' . $text . ' 负责此案件调解';
            (new MediateInfoModel)->add([
                'mid' => $id,
                'text' => $text,
                'status' => 2
            ]);
            (new NoticeModel)->sendMediateNotice($id,  $text);
        } else {
            return $this->renderError('人员选择错误');
        }
        $model = MediateModel::find($id);
        $model->save([
            'court_time' => isset($data['court_time']) ? $data['court_time'] : '',
            'court_address' => isset($data['court_address']) ? $data['court_address'] : '',
        ]);
        $this->setWay($id, 3);
        return $this->renderSuccess('设置成功');
    }

    /* 获取WORD */
    public function getWord()
    {
        $data = $this->getData();
        $id = $data['id'];
        $type = $data['type'];

        if ($type == 1) {
            $word = "lianbiao.docx";
            $name = "立案书";
        } elseif ($type == 2) {
            $word = "jieanbiao.docx";
            $name = "结案书";
        }
        // 详情
        $mediate = new MediateModel;
        $detail = array_merge([
            'no' => '',
            'create_time' => '',
            'update_time' => '',
            'name' => '',
            'idcard' => '',
            'my_address' => '',
            'mobile' => '',
            'other_name' => '',
            'other_address' => '',
            'text' => '',
            'appeal' => ''
        ], $mediate->detail($id)->append(['update_time'])->toArray());
        // 分类
        $cat = [
            'name' => ''
        ];
        $category = (new CategoryModel)->getCacheTree();
        $category = array_combine(array_column($category, 'category_id'), $category);
        if (isset($category[$detail['cid']])) {
            $cat = $category[$detail['cid']];
        }
        // 打开word
        $dir = str_replace('\\', '/', root_path());
        $source = $dir . "app/upload/word/{$word}";
        $tmp = new \PhpOffice\PhpWord\TemplateProcessor($source);
        // 设置word文字
        if ($type == 1) {
            $tmp->setValue('no', $detail['no']);
            $tmp->setValue('time', $detail['create_time']);
            $tmp->setValue('name', $detail['name']);
            $tmp->setValue('category', $cat['name']);
            $tmp->setValue('idcard', $detail['idcard']);
            $tmp->setValue('my_address', $detail['my_address']);
            $tmp->setValue('mobile', $detail['mobile']);
            $tmp->setValue('other_name', $detail['other_name']);
            $tmp->setValue('other_phone', $detail['other_phone']);
            $tmp->setValue('other_address', $detail['other_address']);
            $tmp->setValue('text', $detail['text']);
            $tmp->setValue('appeal', $detail['appeal']);
            $tmp->setValue('date', date('Y-m-d'));
        } elseif ($type == 2) {
            $tmp->setValue('no', $detail['no']);
            $tmp->setValue('time', date('Y-m-d', strtotime($detail['update_time'])));
            $tmp->setValue('name', $this->store['user']['user_name']);
        }
        $fileFolder = "temp/" . BaseModel::$app_id . "/";
        // 创建文件夹
        $dir = iconv("UTF-8", "GBK", root_path() . "public/" . $fileFolder);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        // 生成WORD
        $fileName = "{$detail['no']}-{$name}.docx";
        $fileUrl = $fileFolder . $fileName;
        $tmp->saveAs($fileUrl);
        return redirect(base_url() . $fileUrl);
    }

    /* 添加进度 */
    public function addSchedule($id, $text, $status = 2, $time, $sendNotice = false)
    {
        $mediateInfoModel = new MediateInfoModel;
        $mediateInfo = $mediateInfoModel->field('times')->where('mid', $id)->where('create_time', '<', strtotime($time))->limit(1)->order('create_time desc')->find();
        $times = 1;
        if (isset($mediateInfo['times'])) {
            $times = $mediateInfo['times'];
        } else {
            $mediateModel = new MediateModel;
            $mediate = $mediateModel->field('times')->where('id', $id)->find();
            if (isset($mediate['times'])) {
                $times = $mediate['times'];
            }
        }
        $res = $mediateInfoModel->add([
            'mid' => $id,
            'text' => $text,
            'times' => $times,
            'status' => $status,
            'create_time' => strtotime($time)
        ]);
        if ($res) {
            // 自动发送一条通知
            if ($sendNotice == 'true') {
                (new NoticeModel)->sendMediateNotice($id,  $text);
            }
            return $this->renderSuccess('添加成功');
        }
        return $this->renderError('添加失败');
    }

    /* 删除进度 */
    public function delSchedule($id)
    {
        $model = MediateInfoModel::detail($id);
        if (!$model->remove()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }
}
