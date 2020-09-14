<?php

namespace app\shop\controller\image;

use app\shop\controller\Controller;
use app\common\extend\Image;

class Thumb extends Controller
{

    public function index($src = '')
    {
        if ($src != '') {
            $image = Image::open('uploads/' . $src);
            $image->thumb(100, 100, 3);
            $png = $image->im;
            imagealphablending($png, false);
            imagesavealpha($png, true);
            header('Content-Type: image/png');
            imagepng($png);
            exit;
        } else {
            return $this->renderError('获取失败');
        }
    }
}
