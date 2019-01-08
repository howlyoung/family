<?php

use core\request\request;
use core\request\Respone;
use core\Db\Db;
/**
 * Created by PhpStorm.
 * ������
 */
class main
{
    private static $obj;

    protected static $config;      //��������

    protected static $map;         //����Ĳ��ֱ���

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

        //�����������
        ob_start();
        $controller->beforeAction($action);
        $res = $controller->$action();
        $controller->afterAction($action);
        $controller->respone($res);
        //������ݣ���������
        ob_end_flush();
    }

    /**
     * ��ȡ���ݿ����ӣ����û�У�������
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
     * ��ȡ����    $key = 'key.key1.key2'
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