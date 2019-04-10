<?php

use core\request\request;
use core\request\Respone;
use core\Db\Db;
use component\Module\User;
/**
 * Created by PhpStorm.
 * 主程序
 */
class main
{
    private static $obj;

    protected static $config;      //配置数组

    protected static $map;         //缓存的部分变量

    private function  __construct() {

    }

    public static function getMain($config) {
        if(empty(self::$obj)) {
            self::$obj = new self();
            self::$obj->initConfig($config);
        }
        return self::$obj;
    }

    /**
     * 获得容器
     * @return core\DI\DI
     */
    public static function getContainer() {
        return isset(self::$map['container'])?self::$map['container']:null;
    }

    protected static function setContainer($container) {
        self::$map['container'] = $container;
    }

    protected function initConfig($config) {
        self::$config = $config;
        if(!self::getContainer()) {    //初始化DI容器
            self::setContainer(new \core\DI\DI());
        }

        if(!isset(self::$config['exception'])) {
            $exceptionHandle = self::getContainer()->get('component\Exception\BaseException');
            $exceptionHandle->register();
        }
    }

    protected function getRequest() {
        $request = self::getContainer()->get('core\request\request');
        self::getContainer()->set('core\request\request','',$request);  //注册请求对象为单例
        return $request;
    }

    public function run() {
        $request = $this->getRequest();
        session_start();    //开启session，优化!
        $controllerName = $request->getController();
        $objName = '\\Controller\\'.$controllerName.'Controller';   //优化!

        $controller = self::getContainer()->get($objName);
        $respone = $controller->run($request->getAction());
        $respone->send();

    }

    /**
     * 获取数据库连接，如果没有，则连接
     * @param string $dbName
     * @return object
     * @throws Exception
     */
    public static function getDb($dbName='db') {
        if(isset(self::$map['db'][$dbName])) {
            return self::$map['db'][$dbName];
        } else {
            $config = self::$config['db'][$dbName];
            $db = self::getContainer()->get($config['class']);
            if(empty($db)||!($db instanceof \core\Db\DbInterface)) {
                throw new Exception($dbName.'指定的数据处理类不正确!');
            }

            $d = $db->getDb($config);

            if(empty($d)) {
                throw new Exception($dbName.'数据库配置不正确');
            }

            self::$map['db'][$dbName] = $d;
            return $db;
        }
    }



    /**
     * 获取配置    $key = 'key.key1.key2'
     * @param $key
     * @return mixed
     */
    public static function getConfig($key) {
        $keyArr = explode('.',$key);
        $tmp = self::$config;
        foreach($keyArr as $v) {
            if(!isset($tmp[$v])) {
                return null;
            } else {
                $tmp = $tmp[$v];
            }
        }
        return $tmp;
    }

    /**
     * 获取当前登录的用户id
     * @return int
     */
    public static function getUserId() {
        return isset($_SESSION['uid'])?$_SESSION['uid']:0;
    }

    /**
     * @return User|null
     */
    public static function getUser() {
        $uid = self::getUserId();
        return User::loadById($uid);
    }
}