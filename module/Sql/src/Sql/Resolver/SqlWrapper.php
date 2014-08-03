<?php
/**
 * Created by PhpStorm.
 * User: willsalazar
 * Date: 7/12/14
 * Time: 8:18 AM
 */

namespace Sql\Resolver;

use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use LengthException;


class SqlWrapper {

    protected $adapter;

    protected $sql;

    protected $values = array();

    protected $table;

    protected $columns = array();

    protected $where = array();

    protected $select;

    protected $insert;

    protected $update;

    protected $delete;

    public function __construct(Sql $sql) {
        $this->sql = $sql;
    }

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    public function columns($columns = array())
    {
        $this->setColumns($columns);
        return $this;
    }

    public function setColumns($columns = array())
    {
        $this->columns = $columns;
        return $this;

    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function values($values = array())
    {
        $this->setValues($values);
    }

    public function setValues($values = array())
    {
        $this->values = $values;
        return $this;
    }

    public function getValues()
    {
        return $this->values;
    }
    //TODO this may have a where in the future if it had a subselect
    //insert into table() select ..... where ....
    public function insert($table = null)
    {
        if( !is_null($table) ){
            $this->setTable($table);
        }
//        if( !empty($where) ){
//            $this->setWhere($where);
//        }
        $this->insert = $this->sql->insert($this->getTable());
        if(empty($this->getValues())){
            throw new LengthException(sprintf('Class %s must define parameters for values, none given', get_class($this->insert)));
        }
        $this->insert->values($this->getValues());
        return $this;
    }

    /*
     * Think about select will have to add columns
     * */
    public function select($table = null, $where = array())
    {
        if( !is_null($table) ){
            $this->setTable($table);
        }
        if( !empty($where) ){
            $this->setWhere($where);
        }

        $this->select = $this->sql->select($this->getTable());
        if( !empty($this->getColumns()) ){
            $this->select->columns($this->getColumns());
        }
        if( !empty($this->getWhere()) ){
            $this->select->where($this->getWhere());
        }
        return $this;
    }

    public function setWhere($where = array())
    {
        $this->$where = $where;
        return $this;
    }

    public function getWhere()
    {
        return $this->where;
    }

    public function where($where = array())
    {
        $this->setWhere($where);
    }

    public function executeQuery()
    {
        $statement = $this->sql->prepareStatementForSqlObject($this->sql);
        $result = $statement->execute();
//        if(){
//            return $result;
//        }
//        else{
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet;
    }

    public function clearCache()
    {
        unset($this->columns);
        unset($this->where);
        return $this;
    }

    public function clearWhere()
    {
        unset($this->where);
        return $this;
    }

    public function clearColumns()
    {
        unset($this->columns);
        return $this;
    }

    /*
     * Empties property value not drops or empties table as in the queries;
     *
     * */

    public function clearTable()
    {
        unset($this->table);
        return $this;

    }

} 