<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 13:35
 */

namespace cncn\gds\gateways\pft\service;

use cncn\gds\gateways\pft\Pft;

/**
 * 查询订单网关
 * Class OrderQueryGateway
 * @package cncn\gds\gateways\pft\OrderQueryGateway
 */
class OrderQueryGateway extends Pft
{

    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'OrderQuery';
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
            'pftOrdernum', //票付通订单号
            'remoteOrdernum', //远端订单号，外部订单号
        ];
        foreach ($checkKeys as $key) {
            if (isset($options[$key])) {
                $data[$key] = $options[$key];
            } else {
                throw new \RuntimeException("缺少{$key}参数");
            }
        }

        if(empty($data['pftOrdernum']) && empty($data['remoteOrdernum'])){
            throw new \RuntimeException("票付通订单号和远端订单号不能同时为空");
        }

        return $data;
    }
}