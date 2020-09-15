<?php

namespace app\common\service\order;


/**
 * 订单服务类
 */
class OrderService
{
    /**
     * 生成订单号
     */
    public static function createOrderNo()
    {
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}
