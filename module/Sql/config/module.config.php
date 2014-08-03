<?php
/**
 * Created by PhpStorm.
 * User: willsalazar
 * Date: 7/31/14
 * Time: 10:13 PM
 */

return array(

    'db' => array(
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname='.DB.';host='.HOST,
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    )
);
