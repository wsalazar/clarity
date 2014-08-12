<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/11/14
 * Time: 12:55 PM
 */

namespace Listeners;


class Module {

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