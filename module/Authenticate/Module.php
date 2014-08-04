<?php
namespace Authenticate;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'services' => array(
                'user_manager'  =>
//                function ($serviceManager) {
//                    return $serviceManager->setService('user_manager',
                        new Entity\User\UserManager( new Entity\User\UserFactory() )
            )
//                }
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
//            'user_manager'  =>  array(

//            ),
        );
    }
}
