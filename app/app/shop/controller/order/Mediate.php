<?php

namespace app\shop\controller\order;

use app\shop\controller\Controller;
use app\shop\model\order\Mediate as MediateModel;
use app\shop\model\product\Category as CategoryModel;
use app\shop\model\support\Lawyer as LawyerModel;
use app\shop\model\auth\User as UserModel;
use app\shop\model\order\MediateCommittee as MediateCommitteeModel;
use app\shop\model\order\MediateLawyer as MediateLawyerModel;
use app\common\model\BaseModel;


class Mediate extends Controller
{
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
        $role = $this->store['user']['role'];
        if ($role == 1 || $role == 2) {
            $list = $model->getListByRole($type, $this->postData(), $role);
            $order_count = [
                'order_count' => [
                    'accepting' => $model->getCountByRole('accepting', $role),
                    'adjusting' => $model->getCountByRole('adjusting', $role),
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
     * 详情
     */
    public function detail($id)
    {
        $detail = MediateModel::detail($id,['user', 'image.file', 'info'=> function ($query) {
            $query->field('mid,text,status,create_time')->order('create_time desc');
        }]);
        return $this->renderSuccess('', compact('detail'));
    }

    /* 设置进度 */
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
                $model->status = 2;
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
                return $this->renderSuccess('提交成功');
            }
            return $this->renderError($model->getError() ?: '提交失败');
        } else {
            return $this->renderError('提交失败');
        }
    }

    /* 搜索律师 */
    public function searchLawyer($text)
    {
        $lawyer = (new LawyerModel)->getList([
            'text' => $text
        ]);
        return $this->renderSuccess('', $lawyer);
    }

    /* 搜索委员会 */
    public function searchCommittee($text)
    {
        $data['data'] = (new UserModel)->where('real_name', 'LIKE', "%{$text}%")->where('role', 1)->limit(40)->select();
        return $this->renderSuccess('', $data);
    }

    /* 设置人员 */
    public function setRole()
    {
        $data = $this->postData();
        $id = $data['id'];
        $lawyer = isset($data['lawyer']) ? $data['lawyer'] : [];
        $committee = isset($data['committee']) ? $data['committee'] : [];
        if (count($committee) == 0) {
            return $this->renderError('请选择委员会');
        }
        $model1 = new MediateLawyerModel;
        $model1->where('mid', $id)->delete();
        $model2 = new MediateCommitteeModel;
        $model2->where('mid', $id)->delete();
        if (count($lawyer) > 0) {
            $model1->addAll($id, $lawyer);
        }
        if (count($committee) > 0) {
            $model2->addAll($id, $committee);
        }
        if (count($lawyer) > 0 || count($committee) > 0) {
            $this->setWay($id, 3);
            return $this->renderSuccess('设置成功');
        }
        return $this->renderError('设置失败');
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
        $detail = array_merge([
            'create_time' => '',
            'name' => '',
            'idcard' => '',
            'address' => '',
            'mobile' => '',
            'other_name' => '',
            'text' => '',
            'appeal' => ''
        ], MediateModel::detail($id)->toArray());
        // 分类
        $cat = [
            'name' => ''
        ];
        $category = (new CategoryModel)->getCacheTree();
        $category = array_combine(array_column($category, 'category_id'), $category);
        if (isset($category[$detail['cid']])) {
            $cat = $category[$detail['cid']];
        }
        // 委员会
        $com = [
            'real_name' => ''
        ];
        $committee = (new MediateCommitteeModel)
            ->where('mid', $id)
            ->join('shop_user', 'uid = shop_user_id')
            ->find();
        if ($committee) {
            $com = $committee;
        }
        // 打开word
        $dir = str_replace('\\', '/', root_path());
        $source = $dir . "app/upload/word/{$word}";
        $tmp = new \PhpOffice\PhpWord\TemplateProcessor($source);
        // 设置word文字
        if ($type == 1) {
            $tmp->setValue('time', $detail['create_time']);
            $tmp->setValue('name', $detail['name']);
            $tmp->setValue('category', $cat['name']);
            $tmp->setValue('idcard', $detail['idcard']);
            $tmp->setValue('address', $detail['address']);
            $tmp->setValue('mobile', $detail['mobile']);
            $tmp->setValue('other_name', $detail['other_name']);
            $tmp->setValue('other_phone', $detail['other_phone']);
            $tmp->setValue('text', $detail['text']);
            $tmp->setValue('appeal', $detail['appeal']);
            $tmp->setValue('committee', $com['real_name']);
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
            echo '11';
            mkdir($dir, 0777, true);
        }
        // 生成WORD
        $fileName = "{$id}-{$name}.docx";
        $fileUrl = $fileFolder . substr(md5($fileName . "liumingye"), 8, 16) . "-" . $fileName;
        $tmp->saveAs($fileUrl);
        return redirect(base_url() . $fileUrl);
    }

}
