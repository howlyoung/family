<?php

namespace core\Db;
/**
*pdo
 */
class Db
{
    protected $host;    //主机名称
    protected $dbname;  //数据库名称
    protected $user;    //用户名
    protected $password;    //连接密码
    protected $type;    //数据库类型

    protected $conn;        //链接


    protected $mapPdoType = [
        'boolean' => \PDO::PARAM_BOOL,
        'integer' => \PDO::PARAM_INT,
        'string' => \PDO::PARAM_STR,
        'double' => \PDO::PARAM_STR,
    ];

    public function __construct() {

    }

    public static function getDb($config) {
        $obj = new self();
        if($obj->checkParms($config)) {
            $obj->host = $config['host'];
//            $this->dbname = $config['dbname'];
            $obj->user = $config['user'];
            $obj->password = $config['password'];
            $obj->connect();
            return $obj;
        } else {
            return null;
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
     * 执行查询
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
                    throw new \Exception('不支持的参数类型');
                }
                $sth->bindValue($k,$v,$type);
            }
            return $sth->execute()?$sth:null;
        } catch(\Exception $e) {
            echo $e->getMessage();
        }
    }


    /**
     * 获取所有的查询结果
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
            throw new \Exception('数据库配置的参数不正确');
        }

        $fieldArr = $this->getConfigField();
        //检查配置项是否齐全
        foreach($config as $k=>$v) {
            if(array_key_exists($k,$fieldArr)) {
                unset($fieldArr[$k]);
            }
        }
        if(empty($fieldArr)) {
            return true;
        } else {
            $msg = implode('_',array_flip($fieldArr));
            throw new \Exception('数据库配置缺少'.$msg);
        }
    }

    /**
     * 配置项应有的字段
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