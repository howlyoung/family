<?php
namespace component\Exception;
use core\request\Respone;

/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/4/1
 * Time: 9:50
 */
class BaseException extends \core\Exception\ExceptionHandle
{

    /**
     * @param \Exception $exception
     */
    public function handleException($exception) {
        $view = \main::getContainer()->get('base\view');
        $trace = $exception->getTrace();    //获取错误的堆栈信息
        $respone = new Respone();
        $result = $view->render($this->getViewPath(),['message'=>$exception->getMessage(),'trace'=>$trace]);
        $respone->setContent($result);
        $respone->send();
    }

    public function handleError($exception) {

    }

    protected function getViewPath() {
        return VIEW_PATH.'/Error/common.php';
    }


}