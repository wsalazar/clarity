<?php
/**
 * Created by PhpStorm.
 * User: willsalazar
 * Date: 7/12/14
 * Time: 8:15 AM
 */

namespace Sql;

use Sql\src\Sql\Resolver\SqlWrapper;


class Module {
//    public function __construct(Adapter $adapter, $table, $columns, $where = array()){
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'SqlWrapper' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $mapper = new SqlWrapper($dbAdapter);
                        return $mapper;
                    }
            ),
        );
    }

} 