<?php

namespace app\api\controller\support;

use app\api\model\support\Cases as CasesModel;
use app\api\model\support\CasesCat as CategoryModel;
use app\api\controller\Controller;

class Cases extends Controller
{

    public function list()
    {
        $cid = input('cid', 0, 'intval');
        $page = input('page', 1, 'intval');
        $text = input('text', '', 'htmlspecialchars');
        $list_rows = min(input('limit', 10, 'intval'), 10);

        $category1[0] = [
            "cid" => 0,
            "name" => "全部"
        ];

        $category2 = (array) CategoryModel::getCacheTreeSimple();
        $category = array_merge($category1, $category2);

        $list = (new CasesModel)->getList([
            'category_id' => $cid,
            'text' => $text,
            'page' => $page,
            'list_rows' => $list_rows,
            'field' => "id,title,text,court,date",
            'status' => 1,
        ]);
        foreach ($list as $key => $vo) {
            // 获取文章摘要
            $content = trim($vo['text']);
            $content = preg_replace("@<script(.*?)</script>@is", "", $content);
            $content = preg_replace("@<iframe(.*?)</iframe>@is", "", $content);
            $content = preg_replace("@<style(.*?)</style>@is", "", $content);
            $content = preg_replace("@<(.*?)>@is", "", $content);
            $content = str_replace(PHP_EOL, '', $content);
            $space = array(" ", "　", "  ", " ", " ");
            $go_away = array("", "", "", "", "");
            $content = str_replace($space, $go_away, $content);
            $count = 50;
            $res = mb_substr($content, 0, $count, 'UTF-8');
            if (mb_strlen($content, 'UTF-8') > $count) {
                $res = $res . "...";
            }
            $list[$key]['text'] = $res;
        }
        return $this->renderSuccess('', compact('category', 'list'));
    }

    public function info()
    {
        $id = input('id', 0, 'intval');
        $data = (new CasesModel)->getInfo($id);

        $withCat = input('cat', 0, 'intval');
        if ($data) {
            if ($withCat) {
                $category1[0] = [
                    "cid" => 0,
                    "name" => "全部"
                ];
                $category2 = (array) CategoryModel::getCacheTreeSimple();
                $res['category'] = array_merge($category1, $category2);
            }
            $res['data'] = $data;
            return $this->renderSuccess('', $res);
        }
        return $this->renderError('参数错误');
    }
}
