<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/26
 * Time: 9:44
 */

include '../init.php';
// 加载配置参数
$config = require(__DIR__ . '/config.php');

// 景区列表获取参数
$options = [
    [
        'aid' => '113',
        'pid' => '137802',
        'start_date' => '2019-05-17',
        'end_date' => '2019-05-17',
    ],
    [
        'aid' => '113',
        'pid' => '2803',
        'start_date' => '2019-05-17',
        'end_date' => '2019-05-19',
    ],
];

$service = new cncn\gds\Gds($config);

try {
    $result = $service->driver('pft')->gateway('getRealTimeStorage')->multiRequest($options);
    echo '<pre>';
    var_export($result);
} catch (Exception $e) {
    echo $e->getMessage();
}