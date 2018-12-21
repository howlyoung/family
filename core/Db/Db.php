<?php

namespace core\Db;
/**
*pdo
 */
class Db
{
    protected $host;    //��������
    protected $dbname;  //���ݿ�����
    protected $user;    //�û���
    protected $password;    //��������
    protected $type;    //���ݿ�����

    protected $conn;        //����

    public function __construct($config) {
        if($this->checkParms($config)) {
            $this->host = $config['host'];
            $this->dbname = $config['dbname'];
            $this->user = $config['user'];
            $this->password = $config['password'];
        }
    }

    public function connect() {
        $this->conn = new \PDO($this->type.':host='.$this->host,$this->user,$this->password,[\PDO::ATTR_PERSISTENT => true]);
    }

    public function query($sql,$params) {

    }

    /**
     * @param $config
     * @return bool
     * @throws \Exception
     */
    protected function checkParms($config) {
        if(!is_array($config)) {
            throw new \Exception('���ݿ����õĲ�������ȷ');
        }

        $fieldArr = $this->getConfigField();
        //����������Ƿ���ȫ
        foreach($config as $k=>$v) {
            if(array_key_exists($k,$fieldArr)) {
                unset($fieldArr[$k]);
            }
        }
        if(empty($fieldArr)) {
            return true;
        } else {
            $msg = implode('_',array_flip($fieldArr));
            throw new \Exception('���ݿ�����ȱ��'.$msg);
        }
    }

    /**
     * ������Ӧ�е��ֶ�
     * @return array
     */
    protected function getConfigField() {
        return [
            'host'=>'1',
            'dbname'=>'2',
            'user'=>'3',
            'password'=>'4',
            'type' => '5',
        ];
    }
}