<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/18
 * Time: 15:52
 */

include '../init.php';
// 加载配置参数
$config = require(__DIR__ . '/config.php');

// 景区列表获取参数
$options = [
    'ordern' => '16429994', //票付通订单号
    'num' => '-1', //0 为取消订单 -1 不做修改，指要修改订单取票人手机
    'ordertel' => '', //取票人手机
    'm' => '', //预留参数可为空
];

$service = new cncn\gds\Gds($config);

try {
    $result = $service->driver('pft')->gateway('OrderChangePro')->request($options);
    echo '<pre>';
    var_export($result);
} catch (Exception $e) {
    echo $e->getMessage();
}