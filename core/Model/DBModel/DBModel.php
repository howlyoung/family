<?php
namespace core\Model\DBModel;
use core\Model\Model;

/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/4/19
 * Time: 9:28
 */
class DBModel extends Model
{

    protected function getPrefix() {
        return \main::getConfig('db.db.prefix');
    }

    /**
     * @return \core\QueryBuilder\ModelQueryBuilder object
     */
    public static function table() {
        return \main::getContainer()->get('core\QueryBuilder\ModelQueryBuilder',['modelClass' => get_called_class()]);
    }

    public function save() {
        if($this->isNewRecord()) {
            //新记录，插入
            $this->insertRecord();
            $pk = $this->getPrimaryKey();
            $this->_attributes[$pk] = \main::getDb()->getLastInsertId();
            $this->_oldAttributes = $this->_attributes;
        } else {
            //保存
            $this->updateRecord();
        }
    }

    protected function insertRecord() {
        $columns = [];
        $values = [];
        foreach($this->_attributes as $column=>$value) {
            $columns[] = $column;
            $values[] = '"'.$value.'"';
        }

        $sql = 'insert into '.$this->getPrefix().self::getTableName().' ('.implode(',',$columns).') VALUES('.implode(',',$values).')';
        static::table()->exec($sql);
    }

    protected function getPrimaryKey() {
        return array_search('primary',$this->attributes());
    }

    protected function updateRecord() {
        $update = [];
        foreach($this->_attributes as $column=>$value) {
            if($this->_oldAttributes[$column] != $value) {
                //与旧数组值不同，表示需要更新
                $update[] = $column.' = '.'"'.$value.'"';
            }
        }
        $primaryKey = $this->getPrimaryKey();
        $primaryValue = $this->_attributes[$primaryKey];
        $sql = 'update '.$this->getPrefix().self::getTableName().' set '.implode(',',$update).' where '.$primaryKey.'='.$primaryValue;
        static::table()->exec($sql);
    }
}