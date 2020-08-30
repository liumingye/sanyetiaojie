<?php

namespace app\api\controller\support;

use app\api\model\support\News as NewsModel;
use app\api\controller\Controller;

class News extends Controller
{
    public function list()
    {
        $page = input('page', 1, 'intval');
        $text = input('text', '', 'htmlspecialchars');
        $list_rows = min(input('limit', 10, 'intval'), 10);

        $category1[0] = [
            "name" => "全部"
        ];

        $list = (new NewsModel)->getList([
            'text' => $text,
            'page' => $page,
            'list_rows' => $list_rows,
            'field' => 'id,title,author,source,text,pic,create_time',
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
            // 默认图片
            if(trim($vo['pic']) == ''){
                $list[$key]['pic'] = base_url() . 'image/default/news.jpg';
            }
        }

        return $this->renderSuccess('', compact('list'));
    }

    public function info(){
        $id = input('id', 0, 'intval');
        $data = (new NewsModel)->getInfo($id);
        if($data){
            return $this->renderSuccess('', compact('data'));
        }else{
            return $this->renderError('参数错误');
        }
    }

}
