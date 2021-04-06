### 命名空间
- cncn\gds
- 比如票付通，OTA，仁义旅投，智游宝等对接SDK

### composer.json
```$xslt
{
    "name": "cncn/gds",
    "description": "全球支援中心",
    "keywords": [
        "票付通"
    ],
    "homepage": "http://erp.cncn.net",
    "authors": [
        {
            "name": "jyqin"
        }
    ],
    "require": {
        "php": "^5.6 || ^7.0",
        "cncn/foundation": "dev-master"
    },
    "autoload": {
        "psr-4": {
            "cncn\\gds\\": "src"
        }
    },
    "repositories": [
        {
            "type": "git",
            "url": "http://172.18.3.16:3000/cncn/foundation.git"
        },
        {
            "type": "composer",
            "url": "https://packagist.phpcomposer.com"
        },
        {"packagist": false}
    ],
    "config":{
        "secure-http":false,
        "preferred-install": "dist"
    }
}
```
### auth.json
- 需要注意auth.json，用于http://172.18.3.16:3000git仓库认证

```$xslt
{
  "http-basic": {
    "172.18.3.16:3000": {
      "username": "dev",
      "password": "a123456"
    }
  }
}
```


## 安装
```shell
// 方法一、 使用composer安装
composer require cncn/gds

// 方法二、 直接加载gds SDK
include 'init.php'
```

####  SDK 中对应的 driver 和 gateway 如下表所示：

|driver|gateway|描述|
|:-----  |:-----|-----                           |
| pft | getScenicSpotList | 查询景区列表：Get_ScenicSpot_List   |
| pft | getScenicSpotInfo |  查询景区详情信息：Get_ScenicSpot_Info  |
| pft | getTicketList     | 查询门票列表：Get_Ticket_List  |
| pft | getRealTimeStorage|  动态价格，实时库存上限获取：GetRealTimeStorage    |
| pft | checkPersonID     |  身份证校验接口：Check_PersonID    |
| pft | orderPreCheck     | 预判下单：OrderPreCheck    |
| pft | pFTOrderSubmit    |  提交订单：PFT_Order_Submit   |
| pft | orderQuery        |   查询订单：OrderQuery |
| pft | orderChangePro    |  修改/取消订单：Order_Change_Pro  |
| pft | reSendSMSGlobalPL |  订单短信重发接口：reSend_SMS_Global_PL  |
| pft | terminalCodeVerify|  查看订单凭证码：Terminal_Code_Verify  |
| pft | getScreeningsList |  获取场次信息接口：Get_Screenings_List |
| pft | pFTMemberFund     |  资金余额查看：PFT_Member_Fund |
| pft | pFTMemberRelationship    |  会员关系查看:PFT_Member_Relationship  |
| pft | notify    |  票付通处理通知模块  |


### Quick Start

##### 票付通请求使用示例
```$xslt
//第一种方法根据自己阅读文档，加载驱动使用网关

include '../init.php';

// 加载配置参数
$config = require(__DIR__ . '/config.php');

// 景区列表获取参数
$options = [
    'n' => '0',
    'm' => '10000',
];


$service = new cncn\gds\Gds($config);

try {
    $result = $service->driver('pft')->gateway('GetScenicSpotList')->request($options);
    echo '<pre>';
    var_export($result);
} catch (Exception $e) {
    echo $e->getMessage();
}


//第一种方法根据自己阅读文档，加载驱动使用网关

include '../init.php';

// 加载配置参数
$config = require(__DIR__ . '/config.php');

// 景区列表获取参数
$options = [
    'n' => '0',
    'm' => '10000',
];


//第二种方法访问网关宝里面提供的DriverType和GatewayServiceType
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
```


##### 票付通接收通知示例
```$xslt
//票付通接收通知模块，并且自动登记通知日志，自动回复示例
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

```