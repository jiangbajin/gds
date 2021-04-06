<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/18
 * Time: 12:32
 */

include '../init.php';
// 加载配置参数
$config = require(__DIR__ . '/config.php');

// 景区列表获取参数
$options = [
    'aid' => '113',
    'pid' => '2803',
    'start_date' => '2019-04-17',
    'end_date' => '2019-05-17',
];

$service = new cncn\gds\Gds($config);

try {
    $result = $service->driver('pft')->gateway('getRealTimeStorage')->request($options);
    echo '<pre>';
    var_export($result);
} catch (Exception $e) {
    echo $e->getMessage();
}