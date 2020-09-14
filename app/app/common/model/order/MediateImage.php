<?php

namespace app\common\model\order;

use app\common\model\BaseModel;
use app\common\library\storage\Driver as StorageDriver;
use app\api\model\settings\Setting as SettingModel;

class MediateImage extends BaseModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'mid',
        'app_id',
        'create_time',
        'file_name',
        'file_url'
    ];
    // 关闭更新时间
    protected $updateTime = false;

    /**
     * 关联文件库
     */
    public function file()
    {
        return $this->belongsTo('app\\common\\model\\file\\UploadFile', 'image_id', 'file_id')
            ->bind(['file_path', 'file_type', 'save_name', 'real_name']);
    }

}
