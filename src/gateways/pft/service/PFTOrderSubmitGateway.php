<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 13:33
 */

namespace cncn\gds\gateways\pft\service;

use cncn\gds\gateways\pft\Pft;

/**
 * 提交订单网关
 * Class OrderSubmitGateway
 * @package cncn\gds\gateways\pft\OrderSubmitGateway
 */
class PFTOrderSubmitGateway extends Pft
{

    /**
     * 是否发送短信
     * @var array
     */
    public static $smsSend = [
        0, //发送
        1, //不发送
    ];

    /**
     * 下单模式
     * @var array
     */
    public static $orderMode = [
        0, //正常下单
    ];

    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'PFT_Order_Submit';
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
            'lid', //景区id
            'tid', //门票id
            'remotenum', //远端订单号
            'tprice', //供应商给的计算价，单位分
            'tnum', //数量
            'playtime', //游玩日期
            'ordername', //取票人姓名，多个用英文逗号隔开
            'ordertel', //取票人手机号
            'contactTEL', //联系人手机号
            'smsSend', //是否需要发送短信，0，发送， 不发送：发短信知会返回双方订单号，不发短信才会将凭证信息返回
            'paymode', //扣款方式
            'ordermode', //0正常下单
            'assembly', //线路的时候需要，可为空
            'series', //线路的时候需要，可为空
            'concatId', //联票ID，未开放，请填写0
            'pCode', //套票ID，未开放，请填写0
            'm', //供应商id，查询门票列表的UUaid
            'personId', //身份证号，多个请用英文逗号隔开，与ordername配合使用
            'memo', //备注，可为空
        ];
        foreach ($checkKeys as $key) {
            if (isset($options[$key])) {
                $data[$key] = $options[$key];
            } else {
                throw new \RuntimeException("缺少{$key}参数");
            }
        }

        if(empty($data['lid'])){
            throw new \RuntimeException("景区id不能为空");
        }

        if(empty($data['tid'])){
            throw new \RuntimeException("门票id不能为空");
        }

        if(empty($data['remotenum'])){
            throw new \RuntimeException("remotenum订单号不能为空");
        }

        if(empty($data['tprice'])){
            throw new \RuntimeException("供应商结算价不能为空");
        }

        if(!preg_match("/^[1-9][0-9]*$/", $data['tprice'])){
            throw new \RuntimeException("供应商结算价必须是正整数");
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

        if(empty($data['ordername'])){
            throw new \RuntimeException("游客姓名为空");
        }

        if(!$this->isMobileNo($data['ordertel'])){
            throw new \RuntimeException("取票人手机号码不正确");
        }

        if(!$this->isMobileNo($data['contactTEL'])){
            throw new \RuntimeException("联系人手机号码不正确");
        }

        if(!in_array($data['smsSend'], self::$smsSend)){
            throw new \RuntimeException("是否发送短信参数必须在【".implode(',', self::$smsSend)."】之一");
        }

        if(!in_array($data['paymode'], OrderPreCheckGateway::$payMode)){
            throw new \RuntimeException("支付方式必须是【".implode(',',  OrderPreCheckGateway::$payMode)."】之一");
        }

        if(!in_array($data['ordermode'], self::$orderMode)){
            throw new \RuntimeException("下单方式必须是【".implode(',', self::$orderMode)."】之一");
        }

        $data['concatID'] = 0;
        $data['pCode']    = 0;

        if(empty($data['m'])){
            throw new \RuntimeException("供应商参数不能为空");
        }

        if(!empty($data['personID'])){
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