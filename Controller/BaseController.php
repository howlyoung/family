<?php
namespace Controller;

class BaseController extends \base\Controller
{

    public function __construct($request,$respone) {
        $this->request = $request;
        $this->respone = $respone;

        $controllerName = str_replace('Controller','',basename(get_class($this)));
        //加载模板引擎
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem('../View/'.$controllerName);
        $this->templete = new \Twig_Environment($loader,[]);
    }

    public function respone($res) {
        $this->respone->setContent($res);
        $this->respone->addContentToBuffer();
    }

    public function render($templete,$params) {
        return $this->templete->render($templete,$params);
    }
}