<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 13:41
 */

namespace cncn\gds\gateways\pft\service;

use cncn\gds\gateways\pft\Pft;
/**
 * 资金余额查看
 * Class PFTMemberFundGateway
 * @package cncn\gds\gateways\pft\PFTMemberFundGateway
 */
class PFTMemberFundGateway extends Pft
{

    public static $dType = [
        0, //自己的账户余额
        1, //可用供应商余额
        2, //供应商开放额度
    ];
    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'PFT_Member_Fund';
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
            'dtype',  //查询类型 0 自己的账户余额 1可用供应商余额 2.供应商开放额度
            'aid', //供应商id
        ];

        foreach ($checkKeys as $key) {
            if (isset($options[$key])) {
                $data[$key] = $options[$key];
            } else {
                throw new \RuntimeException("缺少{$key}参数");
            }
        }

        if(!in_array($data['dtype'], self::$dType)){
            throw new \RuntimeException("查询类型必须是【".implode(',',  self::$dType)."】之一");
        }

        if(empty($data['aid'])){
            throw new \RuntimeException("供应商id不能为空");
        }

        return $data;
    }
}