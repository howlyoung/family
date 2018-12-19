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

    public static $obj = null;

    private function __construct() {

    }

    /**
     * @return request|null
     * @throws \Exception
     */
    public static function analysisRequest()
    {
        if(empty(self::$obj)) {
            $queryString = $_SERVER['QUERY_STRING'];
            $queryArr = explode('&', $queryString);

            if (!is_array($queryArr)) {
                throw new \Exception('');
            }
            $r = new self();
            self::$obj = $r;
            //设置控制器和方法变量
            $caArr = explode('/',$queryArr[0]);
            $r->formatControllerAction($caArr);

            $r->setMethod($_SERVER['REQUEST_METHOD']);
            $r->setRequestParams();
            return $r;
        } else {
            return self::$obj;
        }

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
        $this->get = $params;
    }

    protected function setPostParams($params) {
        $this->post = $params;
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
        if(count($arr) > 2) {
            throw new \Exception('不支持多模块');
        }
        $carr = explode('=',$arr[0]);
        if(empty($carr[1])) {
            throw new \Exception('URL格式不正确');
        }
        $this->setController($carr[1]);
        $action = empty($arr[1])?'index':$arr[1];
        $this->setAction($action);
    }

    public function setRequestParams() {
        switch($this->method) {
            case 'GET':
                $this->setGetParams($_GET);
                break;
            case 'POST':
                $this->setGetParams($_POST);
                break;
            default;
                throw new \Exception('不支持的请求类型');
                break;
        }
    }
}