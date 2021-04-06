<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/18
 * Time: 14:14
 */

include '../init.php';
// 加载配置参数
$config = require(__DIR__ . '/config.php');

// 景区列表获取参数
$options = [
    'tid'       => '5715',
    'tnum'      => '10',
    'playtime'  => '2019-05-18',
    'ordertel'  => '13666036274',
    'ordername' => '刁以松,刁以松',
    'm'         => '113',
    'paymode'   => '0',
    'personid'  => '230227198302151067',
];

$service = new cncn\gds\Gds($config);

try {
    $result = $service->driver('pft')->gateway('OrderPreCheck')->request($options);
    echo '<pre>';
    var_export($result);
} catch (Exception $e) {
    echo $e->getMessage();
}