<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 11:50
 */

namespace cncn\gds\gateways\pft\service;

use cncn\gds\gateways\pft\Pft;

/**
 * 查询门票列表网关
 * Class GetTicketList
 * @package cncn\gds\gateways\pft\GetTicketList
 */
class GetTicketListGateway extends Pft
{

    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'Get_Ticket_List';
    }

    /**
     * 应用并返回参数
     * @param array $options
     * @return string
     */
    public function request(array $options = [])
    {
        $requestParams = $this->checkParams($options);
        return parent::request($requestParams);

        //其他业务动作
    }

    private function checkParams(array $options = [])
    {
        $data = [];
        //校验参数空值与否
        $checkKeys = [
            'n', //景区ID
            'm'  //门票ID
        ];
        foreach ($checkKeys as $key) {
            if (isset($options[$key])) {
                $data[$key] = $options[$key];
            } else {
                throw new \RuntimeException("缺少{$key}参数");
            }
        }

        //n,m必须是正整数
        array_map(function($v) use($data){
            if(preg_match("/^[0-9]|[1-9][0-9]*$/",$v)){
                return $v;
            }else{
                throw new \RuntimeException('参数' . (array_flip($data))[$v] . '必须是非负整数');
            }
        },$data);

        //景区id和门票id不能同时为空
        if(empty($data['n']) && empty($data['m'])){
            throw new \RuntimeException('景区id和门票id不能同时为空');
        }

        return $data;
    }
}