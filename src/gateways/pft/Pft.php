<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 10:13
 */

namespace cncn\gds\gateways\pft;

use cncn\foundation\traits\ObjectLoggingTrait;
use cncn\gds\contracts\Config;
use cncn\gds\contracts\PftGatewayInterface;
use cncn\gds\exceptions\Exception;
use cncn\gds\exceptions\GatewayException;
use cncn\gds\exceptions\InvalidArgumentException;

/**
 * 票付通基础类
 * Class Pft
 * @package cncn\gds\gateways\Pft
 */
abstract class Pft extends PftGatewayInterface
{
    use ObjectLoggingTrait;
    /**
     * 票付通全局参数
     * @var array
     */
    protected $config;

    /**
     * 是否开启日志
     */
    protected $log = true;

    /**
     * 日志地址
     */
    protected $logPath = 'pft';

    /**
     * 通知日志地址
     */
    protected $notifyLogPath = 'pft_notify';

    /**
     * 用户定义配置
     * @var Config
     */
    protected $userConfig;

    protected $client;

    /**
     * 明确需要登记所有请求和返回的日志方法
     * @var array
     */
    private $logMethod = [
        'OrderPreCheck',
        'PFT_Order_Submit',
        'Order_Change_Pro',
//        'Get_ScenicSpot_List'
    ];

    /**
     * 票付通正式网关地址
     * @var string
     */
    protected $gateway = 'http://open.12301.cc/openService/MXSE.wsdl';


    public function __construct(array $config)
    {
        date_default_timezone_set('Asia/Shanghai');

        $this->userConfig = new Config($config);

        //回调不需要验证账号密码，但是验签需要，如果没有，验签也会有错误
        if($this->getMethod() != 'Notify') {
            if (empty($this->userConfig->get('ac'))) {
                throw new InvalidArgumentException('Missing Config -- 缺少票付通接口账号');
            }
            if (empty($this->userConfig->get('pw'))) {
                throw new InvalidArgumentException('Missing Config -- 缺少票付通接口密码');
            }
        }

        $this->config = [
            'ac'      => $this->userConfig->get('ac'),
            'pw'      => $this->userConfig->get('pw'),
        ];

        // 沙箱模式
        if (!empty($config['debug'])) {
            $this->gateway = 'http://open.12301dev.com/openService/MXSE_beta.wsdl';
            $this->config = [
                'ac'      => '100019',
                'pw'      => '82d28136a4cd5936754ebc376691613c',
            ];
        }

        //sdk增加票付通测试账号处沙盒处理
        if($this->config['ac'] == '100019'){
            $this->gateway = 'http://open.12301dev.com/openService/MXSE_beta.wsdl';
        }

        //是否关闭日志
        if(isset($config['log']) && $config['log'] == false){
            $this->log = false;
        }

        //日志地址
        if(isset($config['logPath']) && !empty($config['logPath'])){
            $this->logPath = $config['logPath'];
        }

        //票付通通知日志地址
        if(isset($config['notifyLogPath']) && !empty($config['notifyLogPath'])){
            $this->notifyLogPath = $config['notifyLogPath'];
        }

        libxml_disable_entity_loader(false);
        $this->client = new \SoapClient($this->gateway, array("trace" => true));

        $this->setLogger(\cncn\foundation\util\Logger::getInstance($this->logPath, '..'));
    }

    /**
     * xml转换成数组
     * @param $xml
     * @return mixed
     */
    public function xmlToArray($xml)
    {
        if (!$xml) {
            throw new InvalidArgumentException('convert to array error !invalid xml');
        }
        libxml_disable_entity_loader(true);

        //转换方式一、遍历节点，读取出Rec结果，避免Rec覆盖问题；
        $xmlReader = new \XMLReader();
        $xmlReader->XML($xml);
        $data = [];
        while($xmlReader->read()) {
            if ($xmlReader->name == 'Rec' and $xmlReader->nodeType == \XMLReader::ELEMENT) { // 进入 object
                $inXml      = $xmlReader->readOuterXML(); // 获取当前整个 object 内容（字符串）
                $inXml      = simplexml_load_string($inXml,'SimpleXMLElement', LIBXML_NOCDATA); // 转换成 SimpleXMLElement 对象
                $jsonStr    = json_encode($inXml, JSON_UNESCAPED_UNICODE);
                $json2Array = json_decode($jsonStr, true);
                if (isset($json2Array['@attributes'])) {
                    //UUID好像没什么用，这里就不加到主要数据里面了
//                    foreach ($json2Array['@attributes'] as $key => $value) {
//                        $json2Array[$key] = $value;
//                    }
                    unset($json2Array['@attributes']);
                }
                $data[] = $json2Array;
            }
        }

        //转换方式二、可能有数据问题，暂时不用该方法，先注释
//        $result = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE), true);
////        根据特性，筛选掉不需要的数据
//        if(isset($result['Rec'])){
//            $filterResult = $result['Rec'];
//            foreach($filterResult as $key=>&$_value){
//                if(isset($_value['@attributes'])){
//                    $_value['ID'] = $_value['@attributes']['ID'];
//                    unset($_value['@attributes']);
//                }
//            }
//            return $filterResult;
//        }else{
//            return $result;
//        }

        return $data;
    }

    public function notify($handle, $isInitFirst = false)
    {
        throw new \RuntimeException('请求接口不能访问notify');
    }

    /**
     * 所有的票付通接口统一请求入口
     * @param array $options
     * @return mixed
     */
    public function request(array $options)
    {
        //把账号密码组装到请求参数里面
        $options = array_merge($this->config, $options);

        //下单预判，下单，取消订单等重要业务都要明确登记所有request和response日志
        if($this->log && in_array($this->getMethod(), $this->logMethod)){
            $this->logInfo('=====================' . $this->getMethod() .'开始=========================');
            $this->logInfo($this->getMethod() . '—请求报文:' . json_encode($options, JSON_UNESCAPED_UNICODE));
        }

        try {
            $xml = $this->client->__soapCall($this->getMethod(), $options);
            $responseToArray =  $this->xmlToArray($xml);

            //下单预判，下单，取消订单等重要业务都要明确登记所有request和response日志
            if($this->log && in_array($this->getMethod(), $this->logMethod)){
                $this->logInfo($this->getMethod() . '—响应报文:' . json_encode($responseToArray, JSON_UNESCAPED_UNICODE));
            }

            //判断返回报文里面是否含有错误编码
            if(isset($responseToArray[0]) && isset($responseToArray[0]['UUerrorcode'])){
//                //一、直接返回错误
//                return $responseToArray;
//                //二、抛出错误
                throw new \RuntimeException('接口错误：UUerrorcode:' .
                    $responseToArray[0]['UUerrorcode'] . '|' .
                    'UUerrorinfo:' . $responseToArray[0]['UUerrorinfo']);
            }else{
                if($this->log && in_array($this->getMethod(), $this->logMethod)){
                    $this->logInfo('=====================' . $this->getMethod() .'结束=========================');
                }
                return $responseToArray;
            }
        }catch (\SOAPFault $e) {
            throw new \RuntimeException($e->getMessage());
        }catch (\Exception $e){
            if($this->log) {
                $this->logInfo('票付通报错请求参数：' . $this->getMethod() . var_export($options, true));
                $this->logInfo($e->getMessage());
                if(in_array($this->getMethod(), $this->logMethod)){
                    $this->logInfo('=====================' . $this->getMethod() . '结束=========================');
                }
            }
            //最后还要抛出错误给最外层
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * @return string
     */
    abstract protected function getMethod();


    /**
     * 判断日期字符串是否合法
     * 票付通规定日期必须是10位用两个"-"隔开的字符串
     * @param string $dateStr
     * @return string
     */
    protected function checkDate($dateStr = '2019-01-01')
    {
        if(strlen($dateStr) != 10 || count(explode('-', $dateStr))!=3 || strtotime($dateStr) === false){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 截止至201807已公布最新号段
     * 移动号段：134 135 136 137 138 139 147(上网卡) 148 150 151 152 157 158 159 172 178 182 183 184 187 188 198
     * 联通号段：130 131 132 145(上网卡) 146(4G) 155 156 166 171 175 176 185 186
     * 电信号段：133 149 153 173 174 177(4G) 180 181 189 199
     * 卫星通信：1349
     * 虚拟运营商：170
     * 验证是否是手机号
     * @param $mobile
     * @return bool
     */
    public function isMobileNo($mobile)
    {
        if (!is_numeric($mobile)) {
            return false;
        }
        return preg_match('#^13[\d]{9}$|^14[5,6,7,8,9]{1}\d{8}$|^15[^4]{1}\d{8}$|^16[6]{1}\d{8}$|^17[^9]{1}\d{8}$|^18[\d]{9}$|^19[8,9]{1}\d{8}$#', $mobile) ? true : false;

    }
}