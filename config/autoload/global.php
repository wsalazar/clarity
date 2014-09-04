<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

use Zend\ServiceManager\ServiceLocatorInterface;

return array(
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Dashboard',
                'route' => 'home',
                'resource' => 'home',
            ),
            array(
                'label' => 'Manage Content',
                'route' => 'home',
                'resource' => 'home',
                'pages' => array(
                    array(
                        'label' => 'Search',
                        'route' => 'search',
                        'resource' => 'search',
                    ),
                    array(
                        'label' => 'Attribute Management',
                        'route' => 'manageattributes',
                        'resource' => 'manageattributes',
                    ),
                    array(
                        'label' => 'Website Assignment',
                        'route' => 'webassignment',
                        'resource' => 'webassignment',
                    ),
                ),
            ),
            array(
                'label' => 'API Calls',
                'route' => 'apis',
                'resource' => 'apis',
            ),
            array(
                'label' => 'History',
                'route' => 'logging',
                'resource' => 'logging',
            ),
              
             
        )
    ),
    'service_manager' => array(
        'factories' => array(
//            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'Zend\Db\Adapter\Adapter' =>'Zend\Db\Adapter\AdapterServiceFactory',
            'sessionService'    =>  function (ServiceLocatorInterface $serviceLocator){
                    $sessionNames  =  array(
                        'intranet',
                        'login',
                        'dirty_skus',
                    );
                    foreach($sessionNames as $sessions){
                        $sessionContainer = new \Zend\Session\Container($sessions);
                        $sessionService = new SessionService();
                        $sessionService->setSessionContainer($sessionContainer);
                    }
                    return $sessionService;

                }
        ),
    )
     
 
);
