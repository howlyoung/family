<?php
namespace core\request;
/**
 * 处理请求
 * 请求格式  controller/action&param1=1&param2=2
 */
class request
{
    protected $post;
    protected $get;
    protected $method;

    protected $controller;
    protected $action;

    public function __construct() {
        $queryString = $_SERVER['QUERY_STRING'];
        $queryArr = explode('&', $queryString);

        if (!is_array($queryArr)) {
            throw new \Exception('');
        }

        $this->formatControllerAction($queryArr);

        $this->setMethod($_SERVER['REQUEST_METHOD']);
        $this->setRequestParams();
    }

    public function createController() {
        $objName = '\\Controller\\'.$this->controller.'Controller';
        /** @var \Controller\BaseController $controller */
        $controller = new $objName($this,new Respone());
    }

    public function getController() {
        return $this->controller;
    }

    public function getAction() {
        return $this->action;
    }

    protected function setMethod($type) {
        $this->method = $type;
    }

    protected function setGetParams($params) {
        $tmp = [];
        foreach($params as $k=>$v) {
            $tmp[$k] = \core\Filter\Filter::filterRequest($v);
        }
        $this->get = $tmp;
    }

    protected function setPostParams($params) {
        $tmp = [];
        foreach($params as $k=>$v) {
            $tmp[$k] = \core\Filter\Filter::filterRequest($v);
        }
        $this->post = $tmp;
    }

    protected function setController($controller) {
        $this->controller = $controller;
    }

    protected function setAction($action) {
        $this->action = $action;
    }

    public function formatControllerAction($arr) {
        if(!is_array($arr)) {
            throw new \Exception('解析控制器失败');
        }

        $controller = '';
        $action = '';

        foreach($arr as $k=>$v) {
            $requestParamArr = explode('=',$v);
            if(count($requestParamArr) > 1) {
                $param = array_shift($requestParamArr);
                if('r'==$param) {
                    $valArr = explode('/',implode('=',$requestParamArr));
                    $controller = array_shift($valArr);
                    $action = implode('/',$valArr);
                }
            }
        }

        if(empty($controller)||empty($action)) {
            throw new \Exception('无效的请求');
        }

        $this->setController($controller);
        $this->setAction($action);
    }

    public function setRequestParams() {
        switch($this->method) {
            case 'GET':
                $this->setGetParams($_GET);
                break;
            case 'POST':
                $this->setPostParams($_POST);
                break;
            default;
                throw new \Exception('不支持的请求类型');
                break;
        }
    }

    /**
     * 获取get参数
     * @param $name
     * @return null
     */
    public function getParams($name) {
        return isset($this->get[$name])?$this->get[$name]:null;
    }

    /**
     * 获取post参数
     * @param $name
     * @return null
     */
    public function postParams($name) {
        return isset($this->post[$name])?$this->post[$name]:null;
    }
}