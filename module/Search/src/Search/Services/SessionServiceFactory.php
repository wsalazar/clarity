<?php
/**
 * Created by PhpStorm.
 * User: willsalazar
 * Date: 8/18/14
 * Time: 12:33 AM
 */

namespace Search\Services;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SessionServiceFactory implements FactoryInterface{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sessionService = $serviceLocator->get('sessionService');
    }

} 