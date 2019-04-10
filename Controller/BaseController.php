<?php
namespace Controller;

class BaseController extends \base\Controller
{

    protected $user;    //登录用户对象

    protected $loginVerifyExceptionAction; //不需要登录验证的方法


    public function __construct() {
        $this->user = \main::getUser();
    }



    public function beforeAction($action) {
        if(parent::beforeAction($action)) {
            if(!$this->checkIsNeedLogin($action)) {
                throw new \Exception('需要登录!');
            }
            return true;
        } else {
            return false;
        }
    }


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

    /**
     * 是否需要登录，true不用，false用
     * @param $action
     * @return bool
     */
    public function checkIsNeedLogin($action) {
        if(!property_exists(get_called_class(),'loginVerifyProperty')) {
            if(!is_array($this->loginVerifyExceptionAction)||!in_array($action,$this->loginVerifyExceptionAction)) {
                if(is_null($this->user)) {
                    return false;
                }
            }
        }
        return true;
    }
}