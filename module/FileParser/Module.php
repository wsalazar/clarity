<?php
/**
 * Created by PhpStorm.
 * User: willsalazar
 * Date: 9/21/14
 * Time: 2:04 AM
 */

namespace FileParser;


class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
} 