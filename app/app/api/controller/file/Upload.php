<?php

namespace app\api\controller\file;

use app\api\controller\Controller;
use app\api\model\file\UploadFile as UploadFileModel;
use app\api\model\settings\Setting as SettingModel;
use app\common\library\storage\Driver as StorageDriver;

/**
 * 文件库管理
 */
class Upload extends Controller
{
    // 存储配置
    private $config;
    // 当前用户
    private $user;

    /**
     * 构造方法
     */
    public function initialize()
    {
        // 存储配置信息
        $this->config = SettingModel::getItem('storage');
        // 验证用户
        $this->user = $this->getUser();
    }

    /**
     * 图片上传接口
     */
    public function image()
    {
        // 参数
        $parmas = $this->postData();
        // 实例化存储驱动
        $StorageDriver = new StorageDriver($this->config);

        // 设置上传文件的信息
        $StorageDriver->setUploadFile('iFile');
        // 上传图片
        $saveName = $StorageDriver->upload();
        if ($saveName == '') {
            return json(['code' => 0, 'msg' => '图片上传失败' . $StorageDriver->getError()]);
        }
        $saveName = str_replace('\\', '/', $saveName);
        // 图片上传路径
        $fileName = $StorageDriver->getFileName();
        // 图片信息
        $fileInfo = request()->file('iFile');
        // 添加文件库记录
        $uploadFile = $this->addUploadFile($fileName, $fileInfo, 'image', $saveName, $parmas);
        $data = [
            'file_id' => $uploadFile['file_id'],
            'file_path' => $uploadFile['file_path'],
        ];
        // 图片上传成功
        return json(['code' => 1, 'msg' => '图片上传成功', 'data' => $data]);
    }

    /**
     * 视频上传接口
     */
    public function video()
    {
        // 参数
        $parmas = $this->postData();
        // 实例化存储驱动
        $StorageDriver = new StorageDriver($this->config);

        // 设置上传文件的信息
        $StorageDriver->setUploadFile('iFile');
        // 上传视频
        $saveName = $StorageDriver->upload();
        if ($saveName == '') {
            return json(['code' => 0, 'msg' => '视频上传失败' . $StorageDriver->getError()]);
        }
        $saveName = str_replace('\\', '/', $saveName);
        // 视频上传路径
        $fileName = $StorageDriver->getFileName();
        // 视频信息
        $fileInfo = request()->file('iFile');
        // 添加文件库记录
        $uploadFile = $this->addUploadFile($fileName, $fileInfo, 'video', $saveName, $parmas);
        $data = [
            'file_id' => $uploadFile['file_id'],
            'file_path' => $uploadFile['file_path'],
        ];
        // 视频上传成功
        return json(['code' => 1, 'msg' => '视频上传成功', 'data' => $data]);
    }

    /**
     * 附件上传接口
     */
    public function file()
    {
        // 参数
        $parmas = $this->postData();
        // 实例化存储驱动
        $StorageDriver = new StorageDriver($this->config);

        // 设置上传文件的信息
        $StorageDriver->setUploadFile('iFile');
        // 上传附件
        $saveName = $StorageDriver->upload();
        if ($saveName == '') {
            return json(['code' => 0, 'msg' => '附件上传失败' . $StorageDriver->getError()]);
        }
        $saveName = str_replace('\\', '/', $saveName);
        // 附件上传路径
        $fileName = $StorageDriver->getFileName();
        // 附件信息
        $fileInfo = request()->file('iFile');
        // 添加文件库记录
        $uploadFile = $this->addUploadFile($fileName, $fileInfo, 'file', $saveName, $parmas);
        $data = [
            'file_id' => $uploadFile['file_id'],
            'file_path' => $uploadFile['file_path'],
        ];
        // 附件上传成功
        return json(['code' => 1, 'msg' => '附件上传成功', 'data' => $data]);
    }

    /**
     * 添加文件库上传记录
     */
    private function addUploadFile($fileName, $fileInfo, $fileType, $savename, $parmas)
    {
        // 存储引擎
        $storage = $this->config['default'];
        // 存储域名
        $fileUrl = isset($this->config['engine'][$storage]['domain'])
            ? $this->config['engine'][$storage]['domain'] : '';
        // 真实名称
        if (isset($parmas['real_name'])) {
            $parmas['real_name'] = htmlspecialchars(trim($parmas['real_name']));
            if ($parmas['real_name'] == '') {
                $parmas['real_name'] = $fileInfo->getOriginalName();
            }
        } else {
            $parmas['real_name'] = $fileInfo->getOriginalName();
        }
        // 添加文件库记录
        $model = new UploadFileModel;
        $model->add([
            'storage' => $storage,
            'file_url' => $fileUrl,
            'file_name' => $fileName,
            'save_name' => $savename,
            'file_size' => $fileInfo->getSize(),
            'file_type' => $fileType,
            'extension' => $fileInfo->getOriginalExtension(),
            'real_name' => $parmas['real_name'],
            'is_user' => 1,
            'app_id' => $parmas['app_id']
        ]);
        return $model;
    }
}
