<?php
/*
 * @Created by: VSCode
 * @Author: LiuMingye
 * @Date: 2020-09-10 13:43:44
 * @LastEditTime: 2020-09-14 15:59:28
 * @LastEditors: your name
 * @FilePath: \app\app\common\extend\Image.php
 */

namespace app\common\extend;

use think\image\Exception as ImageException;
use think\Image as TpImage;

/**
 * @name: 图片缩略图类
 */
class Image extends TpImage
{

    /**
     * 图像资源对象
     *
     * @var resource
     */
    public $im;

    /**
     * 打开一个图片文件
     * @param \SplFileInfo|string $file
     * @return Image
     */
    public static function open($file)
    {
        if (is_string($file)) {
            $file = new \SplFileInfo($file);
        }
        if (!$file->isFile()) {
            throw new ImageException('image file not exist');
        }
        return new self($file);
    }
}
