<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 14:41
 */

$root = dirname(__DIR__) . '/gds';
require_once $root.'/vendor/autoload.php';
require_once 'Autoloader.php';
$autoloader = cncn\gds\Autoloader::getInstance();
$autoloader->registerNamespaces('cncn\gds', $root."/src");
