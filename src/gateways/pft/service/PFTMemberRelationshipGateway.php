<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 13:42
 */

namespace cncn\gds\gateways\pft\service;

use cncn\gds\gateways\pft\Pft;

/**
 * 会员关系查看
 * Class PFTMemberRelationshipGateway
 * @package cncn\gds\gateways\pft\PFTMemberRelationshipGateway
 */
class PFTMemberRelationshipGateway extends Pft
{

    public static $queryType = [
        1, //查看我的分销商
        2, //查看我的供应商
        3, //查看我的员工
    ];
    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'PFT_Member_Relationship';
    }

    /**
     * 应用并返回参数
     * @param array $options
     * @return string
     */
    public function request(array $options = [])
    {
        $requestParams = $this->checkParams($options);
        return parent::request($requestParams);

        //其他业务动作
    }

    private function checkParams(array $options = [])
    {
        $data = [];
        //校验参数空值与否
        $checkKeys = [
            'n',  //查询类型 0 查看我的分销商 1查看我的供应商 2.查看我的员工
            'm', //预留参数可为空
        ];

        foreach ($checkKeys as $key) {
            if (isset($options[$key])) {
                $data[$key] = $options[$key];
            } else {
                throw new \RuntimeException("缺少{$key}参数");
            }
        }

        if(!in_array($data['n'], self::$queryType)){
            throw new \RuntimeException("查询类型必须是【".implode(',',  self::$queryType)."】之一");
        }

        return $data;
    }
}