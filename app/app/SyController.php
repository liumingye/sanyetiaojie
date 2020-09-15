<?php
/*
 * @Created by: VSCode
 * @Author: LiuMingye
 * @Date: 2020-08-29 19:56:57
 * @LastEditTime: 2020-09-14 15:48:34
 * @LastEditors: your name
 * @FilePath: \app\app\SyController.php
 */

declare (strict_types=1);

namespace app;

/**
 * 控制器基础类
 */
abstract class SyController extends BaseController
{
    /**
     * 返回封装后的 API 数据到客户端
     */
    protected function renderJson($code = 1, $msg = '', $data = [])
    {
        return compact('code', 'msg', 'data');
    }

    /**
     * 返回操作成功json
     */
    protected function renderSuccess($msg = 'success', $data = [])
    {
        return json($this->renderJson(1, $msg, $data));
    }

    /**
     * 返回操作失败json
     */
    protected function renderError($msg = 'error', $data = [], $code = 0)
    {
        return json($this->renderJson($code, $msg, $data));
    }

    /**
     * 获取post数据 (数组)
     */
    protected function postData($key = null)
    {
        return $this->request->param(is_null($key) ? '' : $key . '/a');
    }

    /**
     * 获取post数据 (数组)
     */
    protected function getData($key = null)
    {
        return $this->request->get(is_null($key) ? '' : $key);
    }
}
