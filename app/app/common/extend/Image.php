<?php

namespace app\common\extend;

use think\image\Exception as ImageException;
use think\Image as TpImage;

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
