<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 11:47
 */

namespace cncn\gds\gateways\pft\service;

use cncn\gds\gateways\pft\Pft;

/**
 * 查询景区详情信息网关
 * Class GetScenicSpotInfoGateway
 * @package cncn\gds\gateways\pft\GetScenicSpotInfoGateway
 */
class GetScenicSpotInfoGateway extends Pft
{

    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'Get_ScenicSpot_Info';
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
            'n' // 景区ID
        ];
        foreach ($checkKeys as $key) {
            if (isset($options[$key])) {
                $data[$key] = $options[$key];
            } else {
                throw new \RuntimeException("缺少{$key}参数");
            }
        }
        array_map(function($v) use($data){
            if(preg_match("/^[0-9]|[1-9][0-9]*$/",$v)){
                return $v;
            }else{
                throw new \RuntimeException('景区ID参数' . (array_flip($data))[$v] . '必须是非负整数');
            }
        },$data);

        if(empty($data['n'])){
            throw new \RuntimeException('缺少景区id');
        }

        return $data;
    }
}