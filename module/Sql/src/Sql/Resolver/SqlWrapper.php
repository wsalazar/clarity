<?php
/**
 * Created by PhpStorm.
 * User: willsalazar
 * Date: 7/12/14
 * Time: 8:18 AM
 */

namespace Sql\src\Sql\Resolver;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\AbstractSql;

class SqlWrapper {

    protected $adapter;

    protected $sql;

    protected $table;

    protected $columns = array();

    protected $where;

    public function __construct(Adapter $adapter) {//}, $table =  null, $columns = array(), $where = array()){
        $this->adapter = $adapter;
//        $this->table = $table;
        $this->sql = new Sql($this->adapter);
//        $this->columns = $columns;
//        $this->where = $where;
    }

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function insert()
    {
        $insert = $this->sql->insert($this->getTable());
    }

    public function select()
    {
        $select = $this->sql->select($this->getTable());
//        $this->where($select, $this->where);

    }

    public function where(AbstractSql $sql, $where){
        $sql->where($where);
    }

} 