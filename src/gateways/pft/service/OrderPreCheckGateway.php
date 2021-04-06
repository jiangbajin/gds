<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 11:57
 */

namespace cncn\gds\gateways\pft\service;

use cncn\gds\gateways\pft\Pft;
use cncn\gds\Gds;

/**
 * 预判下单网关
 * Class OrderPreCheckGateway
 * @package cncn\gds\gateways\pft\OrderPreCheckGateway
 */
class OrderPreCheckGateway extends Pft
{

    public static $payMode = [
        0, //账户余额
        2, //供应商授信额度
        4, //现金支付
    ];
    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'OrderPreCheck';
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
            'tid', //门票ID
            'tnum', //数量
            'playtime', //游玩时间
            'ordertel', //游客手机号
            'ordername', //游客姓名，多个用英文逗号分隔
            'm',  // 供应商id
            'paymode', // 支付方式
            'personid' // 游客身份证，多个用英文逗号分隔
        ];
        foreach ($checkKeys as $key) {
            if (isset($options[$key])) {
                $data[$key] = $options[$key];
            } else {
                throw new \RuntimeException("缺少{$key}参数");
            }
        }

        if(empty($data['tid'])){
            throw new \RuntimeException("门票id不能为空");
        }

        if(empty($data['tnum'])){
            throw new \RuntimeException("票数不能为空");
        }

        if(!preg_match("/^[1-9][0-9]*$/",$data['tnum'])){
            throw new \RuntimeException("票数必须是正整数");
        }

        if(!$this->checkDate($data['playtime'])){
            throw new \RuntimeException('游玩时间' . $data['start_date'] . '不是正确的日期格式, 正确格式为10位"2019-01-01"');
        }

        if(!$this->isMobileNo($data['ordertel'])){
            throw new \RuntimeException("订单手机号码不正确");
        }

        if(empty($data['ordername'])){
            throw new \RuntimeException("游客姓名为空");
        }

        if(empty($data['m'])){
            throw new \RuntimeException("供应商参数不能为空");
        }

        if(!in_array($data['paymode'], self::$payMode)){
            throw new \RuntimeException("支付方式必须是【".implode(',',  self::$payMode)."】之一");
        }

        if(!empty($data['personid'])){
            $personIdArr = explode(',', $data['personid']);
            if(!empty($personIdArr)){
                $service = new \cncn\gds\Gds(['pft' =>$this->config]);
                foreach($personIdArr as $personId){
                    try {
                        $service->driver('pft')->gateway('CheckPersonId')->request(['personId' => $personId]);
                    }catch (\Exception $e){
                        throw new \RuntimeException("$personId:身份证格式错误");
                    }
                }
            }
        }


        return $data;
    }
}