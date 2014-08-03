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
use Zend\Db\Adapter\Adapter;


class Module {

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(

                'db' => function($sm) {
//                        echo PHP_EOL . "SM db-adapter executed." . PHP_EOL;
                        $config = $sm->get('config');
                        $config = $config['db'];
                        //print_r($config);
                        //exit();
                        $dbAdapter = new Adapter($config);
                        return $dbAdapter;
                    },
//                'Album\Model\AlbumTable' =>  function($sm) {
//                        $tableGateway = $sm->get('AlbumTableGateway');
//                        $table = new AlbumTable($tableGateway);
//                        return $table;
//                    },
                'SqlWrapper' => function ($sm) {
                        $dbAdapter = $sm->get('db');
                        //print_r($dbAdapter);
                        //exit();
//                        $resultSetPrototype = new ResultSet();
//                        $resultSetPrototype->setArrayObjectPrototype(new Form());
//                        return new SqlWrapper('album', $dbAdapter, null, $resultSetPrototype);
                        return new SqlWrapper(new Sql($dbAdapter));
                    },
            ),
        );
//        return array(
//            'factories' => array(
//                'SqlWrapper' => function ($sm) {
//                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
//                        $mapper = new SqlWrapper(new Sql($dbAdapter));
//                        return $mapper;
//                    }
//            ),
//        );
    }

} 