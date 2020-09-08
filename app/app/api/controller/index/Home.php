<?php

namespace app\api\controller\index;

use app\api\controller\Controller;
use app\common\model\support\News as NewsModel;

class Home extends Controller
{
    public function index()
    {
        // 5条新闻
        $news = (new NewsModel)->getList([
            'list_rows' => 5,
            'field' => 'id,title'
        ]);
        return $this->renderSuccess('', compact('news'));
    }

}
