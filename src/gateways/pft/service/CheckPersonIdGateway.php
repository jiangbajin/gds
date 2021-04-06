<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 11:55
 */

namespace cncn\gds\gateways\pft\service;

use cncn\gds\gateways\pft\Pft;

/**
 * 身份证校验网关
 * Class CheckPersonIdGateway
 * @package cncn\gds\gateways\pft\CheckPersonIdGateway
 */
class CheckPersonIdGateway extends Pft
{

    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'Check_PersonID';
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
            'personId' //身份证号码
        ];
        foreach ($checkKeys as $key) {
            if (isset($options[$key])) {
                if(empty($options[$key])){
                    throw new \RuntimeException("参数{$key}不能为空");
                }
                $data[$key] = $options[$key];
            } else {
                throw new \RuntimeException("缺少{$key}参数");
            }
        }

        return $data;
    }
}