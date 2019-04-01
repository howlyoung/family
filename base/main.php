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
     * @return null
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

    public function run() {
        $request = request::analysisRequest();
        session_start();    //开启session
        $controllerName = $request->getController();
        $objName = '\\Controller\\'.$controllerName.'Controller';
        /** @var \Controller\BaseController $controller */
        $controller = new $objName($request,new Respone());
        $action = $request->getAction();

        //开启输出缓冲
        ob_start();
        $controller->beforeAction($action);
        $res = $controller->$action();
//        $controller->afterAction($action);
        $controller->respone($res);
        //输出内容，结束缓冲
        ob_end_flush();
    }

    /**
     * 获取数据库连接，如果没有，则连接
     * @param string $dbName
     * @return Db
     */
    public static function getDb($dbName='db') {
        if(isset(self::$map['db'][$dbName])) {
            return self::$map['db'][$dbName];
        } else {
            $config = self::$config['db'][$dbName];
            try{
                $db = new $config['class']();
                if(empty($db)||!($db instanceof \core\Db\DbInterface)) {
                    throw new Exception($dbName.'指定的数据处理类不正确!');
                }
            } catch(\Exception $e) {
                echo $e->getMessage();
                exit;
            }
            $d = $db->getDb($config);
            try{
                if(empty($d)) {
                    throw new Exception($dbName.'数据库配置不正确');
                }
            } catch(\Exception $e) {
                echo $e->getMessage();
                exit;
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