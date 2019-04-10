<?php
namespace component\Exception;
/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/4/1
 * Time: 9:50
 */
class BaseException extends \core\Exception\ExceptionHandle
{

    public function handleException($exception) {
//        echo $exception->getMessage();
        $view = \main::getContainer()->get('base\view');
        echo $view->render('../View/Error/common.php',['message'=>$exception->getMessage()]);
    }

    public function handleError($exception) {

    }
}