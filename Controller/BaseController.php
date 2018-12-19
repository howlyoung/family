<?php
namespace Controller;

class BaseController extends \base\Controller
{
    /** @var  \core\request\request $request */
    protected $request; //请求对象
    /** @var  \core\request\Respone $respone */
    protected $respone; //应答对象

    public function __construct($request,$respone) {
        $this->request = $request;
        $this->respone = $respone;
    }

    public function respone($res) {
        $this->respone->setContent($res);
        $this->respone->addContentToBuffer();
    }
}