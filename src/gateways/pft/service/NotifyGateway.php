<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/18
 * Time: 16:25
 */

namespace cncn\gds\gateways\pft\service;

use cncn\gds\Exceptions\Exception;
use cncn\gds\gateways\pft\Pft;

/**
 * 票付通通知接收
 * Class CheckPersonIdGateway
 * @package cncn\gds\gateways\pft\CheckPersonIdGateway
 */
class NotifyGateway extends Pft
{

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->setLogger(\cncn\foundation\util\Logger::getInstance($this->notifyLogPath, '..'));
    }

    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'Notify';
    }

    public function request(array $options)
    {
        throw new \RuntimeException('通知接口不能访问request');
    }

    /**
     * @param null $handle
     * @param bool $isInitFirst
     * @return mixed|void
     */
    public function notify($handle = NULL, $isInitFirst = false)
    {
        $postStr = file_get_contents('php://input');
        //报文登记
        $this->logInfo('票付通通知报文:' . $postStr);

        if($isInitFirst){
            $this->responseSuccess($isInitFirst);
            exit;
        }

        $postArr = json_decode($postStr, true);
        //报文参数判定
        if(!$isInitFirst && !isset($postArr['VerifyCode'])){
            $this->logInfo('报文不合法，缺失VerifyCode参数');
            $this->responseFail();
        }

        //验签失败就不执行了
        if(!$isInitFirst && isset($postArr['VerifyCode']) && !$this->verify($postArr['VerifyCode'])){
            $this->responseFail();
        };

        //执行业务
        if(method_exists($handle,'notifyCallbackHandler')) {
            try {
                $handle->notifyCallbackHandler($postArr);
                $this->responseSuccess($isInitFirst);
            }catch (\Exception $e){
                $this->logInfo($e->getMessage());
                $this->responseFail();
            }
        }else{
            $this->responseFail();
        }
    }

    /**
     * 验签模块
     * @param string $verifyCode
     * @return bool
     */
    private function verify($verifyCode = '')
    {
        if(($localSign = md5($this->config['ac'] . $this->config['pw'])) == $verifyCode){
            $this->logInfo('票付通通知验签成功:票付通签名->' . $verifyCode . ';本地签名->' . $localSign);
            return true;
        }else{
            $this->logInfo('票付通通知验签失败:票付通签名->' . $verifyCode . ';本地签名->' . $localSign);
            return false;
        }
    }

    /**
     * 成功回复
     * @param bool $isInitFirst
     */
    private function responseSuccess($isInitFirst = false)
    {
        if($isInitFirst){
            $responseCode =  'success';
        }else{
            $responseCode =  '200';
        }
        $this->logInfo('回复票付通通知:' . $responseCode);
        @ob_clean();
        echo $responseCode;
    }

    /**
     * 失败回复
     */
    private function responseFail()
    {
        $this->logInfo('回复票付通通知:' . 'fail');
        @ob_clean();
        echo 'fail';
    }
}