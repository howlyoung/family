<?php
namespace base;
/**
 */
class Controller
{
    /** @var  \core\request\request $request */
    protected $request; //�������
    /** @var  \core\request\Respone $respone */
    protected $respone; //Ӧ�����

    protected $templeteLoader; //ģ�������


    protected $layout;  //���ֶ���

    protected function setLayout($layout) {
        $this->layout = $layout;
    }

    /**
     * ִ�з���ǰ����
     * @param $action
     */
    public function beforeAction($action) {

    }

    /**
     * ִ�з��������
     * @param $action
     */
    public function afterAction($action) {

    }

}