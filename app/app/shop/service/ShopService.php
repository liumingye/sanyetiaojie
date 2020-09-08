<?php

namespace app\shop\service;

use app\shop\model\user\User;
use app\shop\model\order\Mediate;
use app\shop\model\user\Feedback;
use app\shop\model\support\Help;

/**
 * 三叶模型
 */
class ShopService
{
    private $MediateModel;
    private $HelpModel;
    private $FeedbackModel;
    private $UserModel;

    /**
     * 构造方法
     */
    public function __construct()
    {
        /* 初始化模型 */
        $this->MediateModel = new Mediate();
        $this->HelpModel = new Help();
        $this->FeedbackModel = new Feedback();
        $this->UserModel = new User();
    }

    /**
     * 后台首页数据
     */
    public function getHomeData()
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $data = [
            'widget_card' => [
                'mediate_total' => $this->getMediateTotal(),
                'help_total' => $this->getHelpTotal(),
                'feedback_total' => $this->getFeedbackTotal(),
                'user_total' => $this->getUserTotal()
            ],
            'widget_outline' => [
                // 新增用户数
                'new_user_total' => [
                    'tday' => $this->getUserTotal($today),
                    'ytd' => $this->getUserTotal($yesterday)
                ],

            ],
            'right_away' => []
        ];
        return $data;
    }

    private function getMediateTotal()
    {
        return number_format($this->MediateModel->getMediateTotal());
    }

    private function getHelpTotal()
    {
        return number_format($this->HelpModel->getHelpTotal());
    }

    private function getFeedbackTotal()
    {
        return number_format($this->FeedbackModel->getFeedbackTotal());
    }

    private function getUserTotal($day = null)
    {
        return number_format($this->UserModel->getUserTotal($day));
    }

    /**
     * 获取商品库存告急总量
     */
    /*private function getProductStockTotal()
    {
        return number_format($this->ProductModel->getProductStockTotal());
    }*/

    /**
     * 获取订单总量
     */
    /*private function getOrderTotal($day = null)
    {
        return number_format($this->OrderModel->getPayOrderTotal($day));
    }*/

    /**
     * 获取待处理订单总量
     */
    /*private function getReviewOrderTotal()
    {
        return number_format($this->OrderModel->getReviewOrderTotal());
    }*/

    /**
     * 获取售后订单总量
     */
    /*private function getRefundOrderTotal()
    {
        return number_format($this->OrderRefund->getRefundOrderTotal());
    }*/

    /**
     * 获取评价总量
     */
    /*private function getCommentTotal()
    {
        $model = new Comment;
        return number_format($model->getCommentTotal());
    }*/

    /**
     * 获取某天的总销售额
     */
    /*private function getOrderTotalPrice($day)
    {
        return sprintf('%.2f', $this->OrderModel->getOrderTotalPrice($day));
    }*/

    /**
     * 获取某天的下单用户数
     */
    /*private function getPayOrderUserTotal($day)
    {
        return number_format($this->OrderModel->getPayOrderUserTotal($day));
    }*/
}
