<?php
namespace Controller;

class BaseController extends \base\Controller
{

    protected $user;    //登录用户对象

    protected $loginVerifyExceptionAction; //不需要登录验证的方法


    public function __construct() {
        $this->user = \main::getUser();
        $viewPath = $this->getViewPath();
        //加载模板引擎
        if(file_exists($viewPath)) {
            $this->templeteLoader = new \Twig_Loader_Filesystem($viewPath);
        }
    }



    public function beforeAction($action) {
        if(!$this->checkIsNeedLogin($action)) {
            echo '需要登录';
            exit;
        }
        return true;
    }

//    public function render($templete,$params) {
//        $tmp = new \Twig_Environment($this->templeteLoader,[]);
//
//        $content =  $tmp->render($templete,$params);
//
//        if(!empty($this->layout)) {
//            //增加布局文件的路径
//            $layoutPath = \main::getConfig('layout.path');
//            $layoutAlias = \main::getConfig('layout.alias');
//            try {
//                if(!$layoutAlias||!$layoutPath) {
//                    throw new \Exception('找不到布局配置');
//                }
//                if(!file_exists($layoutPath.'/'.$this->layout)) {
//                    throw new \Exception('找不到布局文件');
//                }
//            } catch(\Exception $e) {
//                echo $e->getMessage();
//                exit;
//            }
//            $this->templeteLoader->addPath($layoutPath,$layoutAlias);
//
//            $alias = '@'.$layoutAlias;
//            return $tmp->render($alias.'/'.$this->layout,['content'=>$content]);
//        } else {
//            return $content;
//        }
//    }

    /**
     * @param $name
     * @param $default
     * @return null
     */
    public function getParam($name,$default=null) {
        $res =  $this->getRequest()->getParams($name);
        return is_null($res)?$default:$res;
    }

    /**
     * @param $name
     * @param $default
     * @return null
     */
    public function postParam($name,$default=null) {
        $res =  $this->getRequest()->postParams($name);
        return is_null($res)?$default:$res;
    }

    public function checkIsNeedLogin($action) {
        if(!property_exists(get_called_class(),'loginVerifyProperty')) {
            if(!is_array($this->loginVerifyExceptionAction)||!in_array($action,$this->loginVerifyExceptionAction)) {
                if(is_null(\main::getUser())) {
                    return false;
                }
            }
        }
        return true;
    }
}