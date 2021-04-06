<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 14:46
 */
include '../init.php';

// 加载配置参数
$config = require(__DIR__ . '/config.php');

// 订单查询参数
$options = [
    'pftOrdernum' => '16429619',
    'remoteOrdernum' => 'T201904161522305123',
];


$service = new cncn\gds\Gds($config);

try {
    $result = $service->driver(cncn\gds\gateways\DriverType::PTF)
        ->gateway(cncn\gds\gateways\GatewayServiceType::PFT_ORDER_QUERY)
        ->request($options);
    echo '<pre>';
    var_export($result);
} catch (Exception $e) {
    echo $e->getMessage();
}