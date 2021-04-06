<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/18
 * Time: 15:34
 */

include '../init.php';
// 加载配置参数
$config = require(__DIR__ . '/config.php');

// 景区列表获取参数
$options = [
    'lid'        => '2633', //景区id
    'tid'        => '5715', //门票id
    'remotenum'  => 'T201904181522305123', //远端订单号
    'tprice'     => '3', //供应商给的计算价，单位分
    'tnum'       => '1', //数量
    'playtime'   => '2019-04-19', //游玩日期
    'ordername'  => '刁以松', //取票人姓名，多个用英文逗号隔开
    'ordertel'   => '13666036274', //取票人手机号
    'contactTEL' => '13666036274', //联系人手机号
    'smsSend'    => '1', //是否需要发送短信，0，发送， 不发送：发短信知会返回双方订单号，不发短信才会将凭证信息返回
    'paymode'    => '0', //扣款方式
    'ordermode'  => '0', //0正常下单
    'assembly'   => '', //线路的时候需要，可为空
    'series'     => '', //线路的时候需要，可为空
    'concatId'   => '0', //联票ID，未开放，请填写0
    'pCode'      => '0', //套票ID，未开放，请填写0
    'm'          => '113', //供应商id，查询门票列表的UUaid
    'personId'   => '230227198302151067', //身份证号，多个请用英文逗号隔开，与ordername配合使用
    'memo'       => '备注', //备注，可为空
];

$service = new cncn\gds\Gds($config);

try {
    $result = $service->driver('pft')->gateway('PFTOrderSubmit')->request($options);
    echo '<pre>';
    var_export($result);
} catch (Exception $e) {
    echo $e->getMessage();
}