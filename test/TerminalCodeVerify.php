<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/18
 * Time: 16:03
 */

include '../init.php';
// 加载配置参数
$config = require(__DIR__ . '/config.php');

// 景区列表获取参数
$options = [
    'ordern' => '16429994',
    'm' => '',
];

$service = new cncn\gds\Gds($config);

try {
    $result = $service->driver('pft')->gateway('TerminalCodeVerify')->request($options);
    echo '<pre>';
    var_export($result);
} catch (Exception $e) {
    echo $e->getMessage();
}