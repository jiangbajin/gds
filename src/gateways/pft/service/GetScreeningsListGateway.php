<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 13:40
 */

namespace cncn\gds\gateways\pft\service;

use cncn\gds\gateways\pft\Pft;
/**
 * 获取场次信息网关接口
 * Class GetScreeningsListGateway
 * @package cncn\gds\gateways\pft\GetScreeningsListGateway
 */
class GetScreeningsListGateway extends Pft
{

    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'Get_Screenings_List';
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
            'aid', //供应商id
            'tid', //门票id
            'date', //查询日期
        ];
        foreach ($checkKeys as $key) {
            if (isset($options[$key])) {
                $data[$key] = $options[$key];
            } else {
                throw new \RuntimeException("缺少{$key}参数");
            }
        }

        if(empty($data['aid'])){
            throw new \RuntimeException("供应商id不能为空");
        }

        if(empty($data['tid'])){
            throw new \RuntimeException("门票id不能为空");
        }

        if(!$this->checkDate($data['date'])){
            throw new \RuntimeException('开始日期' . $data['date'] . '不是正确的日期格式, 正确格式为10位"2019-01-01"');
        }

        return $data;
    }
}