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
        } else {
            //保存
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
        static::table()->execSql($sql);
    }
}