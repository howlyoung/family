<?php

namespace core\Db;
/**
*pdo
 */
class Db implements DbInterface
{
    protected $host;    //主机名称
    protected $dbname;  //数据库名称
    protected $user;    //用户名
    protected $password;    //连接密码
    protected $type;    //数据库类型


    protected $errMsg;      //执行语句后的错误信息,数组
    protected $conn;        //链接


    protected $mapPdoType = [
        'boolean' => \PDO::PARAM_BOOL,
        'integer' => \PDO::PARAM_INT,
        'string' => \PDO::PARAM_STR,
        'double' => \PDO::PARAM_STR,
    ];

    public function __construct() {

    }

    public function getDb($config) {
        if($this->checkParms($config)) {
            $this->host = $config['host'];
//            $this->dbname = $config['dbname'];
            $this->user = $config['user'];
            $this->password = $config['password'];
            $this->connect();
            return $this;
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
     * 获取最后插入的主键id
     * @return mixed
     */
    public function getLastInsertId() {
        return $this->conn->lastInsertId();
    }

    /**
     * 执行查询 考虑优化! 是否需要开放调用
     * @param $sql
     * @param $params
     * @return null|\PDOStatement
     * @throws \Exception
     */
    public function exec($sql,$params) {
        /** @var \PDOStatement $sth */
        $sth = $this->conn->prepare($sql);
        foreach($params as $k=>$v) {
            $type = isset($this->mapPdoType[gettype($v)])?$this->mapPdoType[gettype($v)]:null;
            if(!$type) {
                throw new \Exception('不支持的参数类型');
            }
            $sth->bindValue($k,$v,$type);
        }

        $res =  $sth->execute()?$sth:null;
        $this->errMsg = $sth->errorInfo();
        return $res;
    }

    /**
     * 获取查询执行的错误信息
     * @return array
     */
    public function getErrMsgArr() {
        return $this->errMsg;
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