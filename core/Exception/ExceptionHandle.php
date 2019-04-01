<?php
namespace core\Exception;
/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/4/1
 * Time: 9:38
 */
abstract class ExceptionHandle
{

    abstract public function handleException($exception);
    abstract public function handleError($error);

    public function register() {
        set_exception_handler([$this,'handleException']);
        set_error_handler([$this,'handleError']);
    }
}