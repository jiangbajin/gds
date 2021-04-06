<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/18
 * Time: 16:48
 */

include '../init.php';
// 加载配置参数
$config = require(__DIR__ . '/config.php');

// 景区列表获取参数
$handle = new Handle();

$service = new cncn\gds\Gds($config);

try {
    $result = $service->driver('pft')->gateway('notify')->notify($handle, true);
} catch (Exception $e) {
    echo $e->getMessage();
}

class Handle
{
    public function notifyCallbackHandler($notifyStr)
    {
        echo '<pre>';
        var_export($notifyStr);
    }
}