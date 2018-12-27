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


    protected $mapPdoType = [
        'boolean' => \PDO::PARAM_BOOL,
        'integer' => \PDO::PARAM_INT,
        'string' => \PDO::PARAM_STR,
        'double' => \PDO::PARAM_STR,
    ];

    public function __construct($config) {
        if($this->checkParms($config)) {
            $this->host = $config['host'];
//            $this->dbname = $config['dbname'];
            $this->user = $config['user'];
            $this->password = $config['password'];
            $this->connect();
        }
    }

    public function connect() {
        try{
            $this->conn = new \PDO($this->host,$this->user,$this->password,[\PDO::ATTR_PERSISTENT => true]);
        }
        catch(\PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * ִ�в�ѯ
     * @param $sql
     * @param $params
     * @return null|\PDOStatement
     */
    protected function exec($sql,$params) {
        try {
            /** @var \PDOStatement $sth */
            $sth = $this->conn->prepare($sql);
            foreach($params as $k=>$v) {
                $type = isset($this->mapPdoType[gettype($v)])?$this->mapPdoType[gettype($v)]:null;
                if(!$type) {
                    throw new \Exception('��֧�ֵĲ�������');
                }
                $sth->bindValue($k,$v,$type);
            }
            return $sth->execute()?$sth:null;
        } catch(\Exception $e) {
            echo $e->getMessage();
        }
    }


    /**
     * ��ȡ���еĲ�ѯ���
     * @param $sql
     * @param $params
     * @return array|null
     */
    public function queryAll($sql,$params) {
        if($sth = $this->exec($sql,$params)) {
            return $sth->fetchAll();
        } else {
            return null;
        }
    }

    public function queryRow($sql,$params) {
        if($sth = $this->exec($sql,$params)) {
            return $sth->fetch(\PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }

    public function queryObject($className,$sql,$params) {
        if(!class_exists($className)) {
            return false;
        }
        if($sth = $this->exec($sql,$params)) {
            return $sth->fetchObject($className);
        } else {
            return null;
        }
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
//            'dbname'=>'2',
            'user'=>'3',
            'password'=>'4',
//            'type' => '5',
        ];
    }
}