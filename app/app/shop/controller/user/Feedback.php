<?php

namespace app\shop\controller\user;

use app\shop\controller\Controller;
use app\shop\model\user\Feedback as FeedbackModel;
use app\common\model\user\FeedbackCat as CategoryModel;


class Feedback extends Controller
{
    /**
     * 列表
     */
    public function lists($type = 'all')
    {
        // 分类
        $model = new CategoryModel;
        $category = $model->getCacheTreeSimple();
        $category = array_combine(array_column($category, 'cid'), $category);

        // 列表
        $model = new FeedbackModel();
        $list = $model->getList($this->postData());

        foreach ($list as &$vo) {
            if (isset($category[$vo['cid']])) {
                $vo['category_name'] = $category[$vo['cid']]['name'];
            } else {
                $vo['category_name'] = '未分类';
            }
            // 获取摘要
            $content = trim($vo['text']);
            $content = preg_replace("@<script(.*?)</script>@is", "", $content);
            $content = preg_replace("@<iframe(.*?)</iframe>@is", "", $content);
            $content = preg_replace("@<style(.*?)</style>@is", "", $content);
            $content = preg_replace("@<(.*?)>@is", "", $content);
            $content = str_replace(PHP_EOL, '', $content);
            $space = array(" ", "　", "  ", " ", " ");
            $go_away = array("", "", "", "", "");
            $content = str_replace($space, $go_away, $content);
            $count = 75;
            $res = mb_substr($content, 0, $count, 'UTF-8');
            if (mb_strlen($content, 'UTF-8') > $count) {
                $res = $res . "...";
            }
            $vo['text'] = $res;
        }

        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 详情
     */
    public function detail($id)
    {
        $detail = FeedbackModel::detail($id, ['user' => function ($query) {
            $query->field('user_id,nickName');
        }, 'image.file']);
        foreach ($detail->image as $vo) {
            if (isset($vo->save_name)) {
                if ($vo->file_type == 'image') {
                    $vo->thumb_path =  base_url() . "shop.php/image.thumb?src=" . $vo->save_name;
                }
                unset($vo->save_name);
            }
        }
        return $this->renderSuccess('', compact('detail'));
    }
}
