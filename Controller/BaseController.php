<?php
namespace Controller;

class BaseController extends \base\Controller
{

    public function __construct($request,$respone) {
        /** @var \core\request\request $request */
        $this->request = $request;
        /** @var \core\request\Respone $respone */
        $this->respone = $respone;

        $controllerName = str_replace('Controller','',basename(get_class($this)));
        //加载模板引擎
        \Twig_Autoloader::register();
        $this->templeteLoader = new \Twig_Loader_Filesystem('../View/'.$controllerName);
    }

    public function respone($res) {
        $this->respone->setContent($res);
        $this->respone->addContentToBuffer();
    }

    public function render($templete,$params) {
        $tmp = new \Twig_Environment($this->templeteLoader,[]);

        $content =  $tmp->render($templete,$params);

        if(!empty($this->layout)) {
            //增加布局文件的路径
            $layoutPath = \main::getConfig('layout.path');
            $layoutAlias = \main::getConfig('layout.alias');
            try {
                if(!$layoutAlias||!$layoutPath) {
                    throw new \Exception('找不到布局配置');
                }
                if(!file_exists($layoutPath.'/'.$this->layout)) {
                    throw new \Exception('找不到布局文件');
                }
            } catch(\Exception $e) {
                echo $e->getMessage();
                exit;
            }
            $this->templeteLoader->addPath($layoutPath,$layoutAlias);

            $alias = '@'.$layoutAlias;
            return $tmp->render($alias.'/'.$this->layout,['content'=>$content]);
        } else {
            return $content;
        }
    }

    /**
     * @param $name
     * @return null
     */
    public function getParam($name) {
        return $this->request->getParams($name);
    }

    /**
     * @param $name
     * @return null
     */
    public function postParam($name) {
        return $this->request->postParams($name);
    }
}