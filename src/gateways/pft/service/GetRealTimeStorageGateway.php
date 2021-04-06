<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 11:52
 */

namespace cncn\gds\gateways\pft\service;

use cncn\gds\gateways\pft\Pft;

use \cncn\gds\util\ParallelSoapClient;

/**
 * 动态价格，实时库存上限获取网关
 * Class GetRealTimeStorageGateway
 * @package cncn\gds\gateways\pft\GetRealTimeStorage
 */
class GetRealTimeStorageGateway extends Pft
{

    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'GetRealTimeStorage';
    }

    /**
     * 应用并返回参数
     * @param array $options
     * @return array
     */
    public function request(array $options = [])
    {
        $requestParams = $this->checkParams($options);
        $response = parent::request($requestParams);
        $storageList =  isset($response[0]['items']) ? $response[0]['items'] : [];
        if(isset($storageList['date'])){
            return [0 => $storageList];
        }else{
            return $storageList;
        }
        //其他业务动作
    }

    public function multiRequest(array $options = [])
    {
        $requestParams = [];
        foreach ($options as $option) {
            $requestParams[] = array_merge($this->config, $this->checkParams($option));
        }

        $parseResultFn = function ($method, $res) {
            if (isset($res->{$method . 'Result'})) {
                return $res->{$method . 'Result'};
            }
            return $res;
        };
        /** @var array $options , array of options for the soap client */
        $options = [
            'connection_timeout' => 40,
            'trace'              => true,
            'exceptions'         => true,
            'soap_version'       => SOAP_1_1,
            'cache_wsdl'         => WSDL_CACHE_BOTH,
            'encoding'           => 'UTF-8',
            'resFn'              => $parseResultFn,
        ];

        $client = new ParallelSoapClient($this->gateway, $options);
        $client->setCurlOptions(
            [CURLOPT_VERBOSE => false]
        );

        $client->setMulti(true);

        $requestIds = [];

        foreach($requestParams as $param){
            $requestIds[] = $client->{$this->getMethod()}($param, $param['pid']);
        }
        /** @var $responses array that hold the response array as array( requestId => responseObject ); */
        $responses = $client->run();
        /** Loop through the responses and get the results */
        foreach ($responses as $id => $response) {
            if ($response instanceof \SoapFault) {
                /** handle the exception here  */
                throw new \RuntimeException('SoapFault: ' . $response->faultcode . ' - ' . $response->getMessage() . "\n");
            } else {
                $response = $this->xmlToArray($response);
                $responseToArr = isset($response[0]['items']) ? $response[0]['items'] : [];
                if(isset($responseToArr['date'])){
                    $responses[$id]  = [0 => $responseToArr];
                }else{
                    $responses[$id] =  $responseToArr;
                }
            }
        }
        return $responses;
    }

    private function checkParams(array $options = [])
    {
        $data = [];
        //校验参数空值与否
        $checkKeys = [
            'aid', //供应商ID
            'pid', //产品ID
            'start_date', //有效开始时间
            'end_date' //有效结束时间
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

        if(!$this->checkDate($data['start_date'])){
            throw new \RuntimeException('开始日期' . $data['start_date'] . '不是正确的日期格式, 正确格式为10位"2019-01-01"');
        }

        if(!$this->checkDate($data['end_date'])){
            throw new \RuntimeException('结束日期' . $data['start_date'] . '不是正确的日期格式, 正确格式为10位"2019-01-01"');
        }

        $dayNum = (strtotime($data['end_date']) - strtotime($data['start_date']))/36400/24;

        if($dayNum < 0){
            throw new \RuntimeException('结束日期不能小于开始日期');
        }

        if($dayNum > 30){
            throw new \RuntimeException('查询日期跨度不能超过30天');
        }

        return $data;
    }
}