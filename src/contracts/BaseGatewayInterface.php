<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 10:32
 */

namespace cncn\gds\contracts;

/**
 * gds所有产品网关接口
 * Interface GatewayInterface
 * @package cncn\gds\contracts
 */
abstract class BaseGatewayInterface
{
    /**
     * 发起请求
     * @param $options
     * @return mixed
     */
    abstract public function request(array $options);

    /**
     * 票付通通知接收模块
     * @param $handle
     * @param bool $isInitFirst
     * @return mixed
     */
    abstract public function notify($handle, $isInitFirst = false);
}