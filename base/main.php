<?php

use core\request\request;
use core\request\Respone;
use core\Db\Db;
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
            self::$config = $config;
        }
        return self::$obj;
    }

    public function run() {
        $request = request::analysisRequest();
        $controllerName = $request->getController();
        $objName = '\\Controller\\'.$controllerName.'Controller';
        /** @var \Controller\BaseController $controller */
        $controller = new $objName($request,new Respone());
        $action = $request->getAction();

        //开启输出缓冲
        ob_start();
        $controller->beforeAction($action);
        $res = $controller->$action();
        $controller->afterAction($action);
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
            $db = new Db($config);
            self::$map['db'][$dbName] = $db;
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
}