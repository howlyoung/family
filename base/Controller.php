<?php
namespace base;
/**
 */
class Controller
{
    /** @var  \core\request\request $request */
    protected $request; //请求对象
    /** @var  \core\request\Respone $respone */
    protected $respone; //应答对象

    protected $templeteLoader; //模板加载器


    protected $layout;  //布局对象

    protected function setLayout($layout) {
        $this->layout = $layout;
    }

    /**
     * 执行方法前调用
     * @param $action
     */
    public function beforeAction($action) {

    }

    /**
     * 执行方法后调用
     * @param $action
     */
    public function afterAction($action) {

    }

}