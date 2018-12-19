<?php

use core\request\request;
use core\request\Respone;
/**
 * Created by PhpStorm.
 * ������
 */
class main
{
    private static $obj;

    private function  __construct() {

    }

    public static function getMain() {
        if(empty(self::$obj)) {
            self::$obj = new self();
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
}