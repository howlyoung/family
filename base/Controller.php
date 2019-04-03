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


    public function getViewPath($path) {
        $controllerName = str_replace('Controller','',basename(get_class($this)));
        return '../View/'.$controllerName.'/'.$path.'.php';
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

    public function getView() {
        return \main::getContainer()->get('base\view');
    }

    /**
     * 渲染页面
     * @param $path
     * @param $params
     */
    public function render($path,$params) {
        $viewPath = $this->getViewPath($path);
        return $this->getView()->render($viewPath,$params);
    }

}