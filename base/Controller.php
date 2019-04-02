<?php
namespace base;
use core\request\Respone;

/**
 */
class Controller
{
    /** @var  \core\request\request $request */
    protected $request; //请求对象
    /** @var  \core\request\Respone $respone */
    protected $respone; //应答对象

    protected $templeteLoader; //模板对象

    protected $layout;  //布局对象

    protected function setLayout($layout) {
        $this->layout = $layout;
    }

    public static function createController($controllerId) {
        $controllerClassName = '\\Controller\\'.$controllerId.'Controller';


    }

    public function __construct() {

    }

    public function getRequest() {
        return \main::getContainer()->get('core\request\request');
    }

    /**
     *
     * @param $action
     * @return bool
     */
    public function beforeAction($action) {
        return true;
    }

    /**
     * ִ
     * @param $action
     * @param $result
     */
    public function afterAction($action,$result) {
        return $result;
    }


    public function run($action) {
        if($this->beforeAction($action)) {
            $res = $this->$action();
            $res = $this->afterAction($action,$res);
        } else {
            throw new \Exception('未获得访问该方法的权限!');
        }
        if($res instanceof Respone) {
            return $res;
        } else {
            $respone = new Respone();
            $respone->setContent($res);
            return $respone;
        }
    }

    /**
     * 渲染页面
     * @param $path
     * @param $params
     */
    public function render($path,$params) {

    }

}