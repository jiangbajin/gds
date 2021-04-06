<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/18
 * Time: 16:14
 */


include '../init.php';
// 加载配置参数
$config = require(__DIR__ . '/config.php');

// 景区列表获取参数
$options = [
    'dtype' => '0',
    'aid'  => '113',
];

$service = new cncn\gds\Gds($config);

try {
    $result = $service->driver('pft')->gateway('PFTMemberFund')->request($options);
    echo '<pre>';
    var_export($result);
} catch (Exception $e) {
    echo $e->getMessage();
}