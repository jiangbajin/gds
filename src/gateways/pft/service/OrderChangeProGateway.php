<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 13:37
 */

namespace cncn\gds\gateways\pft\service;

use cncn\gds\gateways\pft\Pft;

/**
 * 修改/取消订单网关
 * Class OrderChangeProGateway
 * @package cncn\gds\gateways\pft\OrderChangeProGateway
 */
class OrderChangeProGateway extends Pft
{

    public static $changeMode = [
        0, //取消订单
        -1, //不做修改，只要修改订单取票人手机号
    ];
    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'Order_Change_Pro';
    }

    /**
     * 应用并返回参数
     * @param array $options
     * @return string
     */
    public function request(array $options = [])
    {
        $requestParams = $this->checkParams($options);
        return (parent::request($requestParams))[0];

        //其他业务动作
    }

    private function checkParams(array $options = [])
    {
        $data = [];
        //校验参数空值与否
        $checkKeys = [
            'ordern', //票付通订单号
            'num', //0 为取消订单 -1 不做修改，指要修改订单取票人手机
            'ordertel', //取票人手机
            'm', //预留参数可为空
        ];
        foreach ($checkKeys as $key) {
            if (isset($options[$key])) {
                $data[$key] = $options[$key];
            } else {
                throw new \RuntimeException("缺少{$key}参数");
            }
        }

        if(empty($data['ordern'])){
            throw new \RuntimeException("票付通订单号为空");
        }

//        if(!in_array($data['num'], self::$changeMode)){
//            throw new \RuntimeException("修改模式必须是【".implode(',',  self::$changeMode)."】之一");
//        }

        if(!empty($data['ordertel']) && !$this->isMobileNo($data['ordertel'])){
            throw new \RuntimeException("手机号码不正确");
        }

        return $data;
    }
}