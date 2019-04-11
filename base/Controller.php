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

    public function __construct() {

    }

    /**\
     * 获取request对象
     * @return \core\request\request|object
     */
    public function getRequest() {
        if($this->request == null) {
            $this->request = \main::getContainer()->get('core\request\request');
        }
        return $this->request;
    }

    /**
     * 获取view所在文件夹,view的位置应该使用配置,优化!
     * @return string
     */
    public function getViewPath() {
        $controllerName = str_replace('Controller','',basename(get_class($this)));
        return VIEW_PATH.$controllerName.'/';
    }

    /**
     * 获取view文件，应该在配置中约定好view文件后缀名,优化!
     * @param $viewName
     * @return string
     */
    public function getViewFile($viewName) {
        $path = $this->getViewPath();
        $file = $path.$viewName.'.php';
        if(!file_exists($file)) {
            $file = $path.$viewName.'.html';
        }
        return $file;
    }

    /**
     * 访问方法前执行，主要判断是否可以访问
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
     * 获取view对象
     * @return object
     */
    public function getView() {
        return \main::getContainer()->get('base\view');
    }

    /**
     * 渲染页面
     * @param $name
     * @param $params
     */
    public function render($name,$params) {
        $viewPath = $this->getViewFile($name);
        return $this->getView()->render($viewPath,$params);
    }

}