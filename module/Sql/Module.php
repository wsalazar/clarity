<?php
/**
 * Created by PhpStorm.
 * User: willsalazar
 * Date: 7/12/14
 * Time: 8:15 AM
 */

namespace Sql;

use Sql\Resolver\SqlWrapper;
use Zend\Db\Sql\Sql;


class Module {
//    public function __construct(Adapter $adapter, $table, $columns, $where = array()){
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'SqlWrapper' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $mapper = new SqlWrapper(new Sql($dbAdapter));
                        return $mapper;
                    }
            ),
        );
    }

} 