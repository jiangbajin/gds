<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 13:38
 */

namespace cncn\gds\gateways\pft\service;

use cncn\gds\gateways\pft\Pft;

/**
 * 订单短信重发网关
 * Class ReSendSMSGlobalPLGateway
 * @package cncn\gds\gateways\pft\ReSendSMSGlobalPLGateway
 */
class ReSendSMSGlobalPLGateway extends Pft
{

    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'reSend_SMS_Global_PL';
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
            throw new \RuntimeException("票付通订单号不能为空");
        }

        return $data;
    }
}