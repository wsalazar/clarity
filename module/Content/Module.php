<?php
namespace Content;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\Event;
//use Zend\EventManager\StaticEventManager;
use Zend\Session\Container;
use Zend\Log\Logger;
use Zend\Log\Writer\Db;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Resource\GenericResource;


class Module
{

    use EventManagerAwareTrait;

    protected $_adapter;

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

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

    public function onBootstrap(MvcEvent $event)
    {
//        $this->initAcl($event);
//        $event->getApplication()->getEventManager()->attach('route',[$this, 'checkAcl']);
        $eventManager       = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        $sharedEventManager->attach('*', 'log', function($e){
            $fields = $this->getConfig()['event_listener_construct']['logger'];
            $writer = new Db($e->getParam('dbAdapter'), 'logger', $fields);//$e->getParam('fields')
            $logger = new Logger();
            $logger->addWriter($writer);
            $logger->info(null,$e->getParam('extra'));
        },100);
        $sharedEventManager->attach('*', 'constructLog', function($e){
            $fields = $this->getConfig()['event_listener_construct']['logger'];
//            $makeFields = $e->getParam('makeFields');
            $eventWritables = array('dbAdapter'=>$e->getParam('makeFields')['dbAdapter'], 'fields'=>$fields, 'extra'=>$e->getParam('makeFields')['extra'] );
            $this->getEventManager()->trigger('log', null, $eventWritables);
        },100);
    }

    public function dbRoles(MvcEvent $e)
    {
        $adapter = $e->getApplication()->getServiceManager()->get('Zend\Db\Adapter\Adapter');
        $adapterClass =  get_class($adapter);
        $results = $adapter->query('SELECT * FROM useraccess', $adapterClass::QUERY_MODE_EXECUTE);
        $roles = [];
        foreach ( $results as $result ) {
            $roles[$result['user_role']][] = $result['resource'];
        }
        return $roles;
    }

    public function initAcl(MvcEvent $e)
    {
        $acl = new Acl();
        $roles = $this->dbRoles($e);
//        echo '<pre>';
//        var_dump($roles);
//        die();
//        $roles = include __DIR__ . '/../../config/module.acl.roles.php';
        $allResources = [];
        foreach ( $roles as $role => $resources ) {
//            echo $role . ' ';
            $role = new GenericRole($role);
            $acl->addRole($role);
            foreach ($resources as $key => $resource ) {
                $allResources = array_merge((array)$resource, $allResources);
                if( !$acl->hasResource($resource)) {
                    $acl->addResource(new GenericResource($resource));
                }
//                echo $resource . ' ' ;
            }
//            echo '<pre>';
//            if(is_array($resources)){
            //adding resources
//                foreach ( $resources as $resource ) {
//                    if( !$acl->hasResource($resource)) {
//                        $acl->addResource(new GenericResource($resource));
//                    }
//                }
//            }
            //adding restrictions
            foreach ( $allResources as $resource ) {
//                echo 'haha ';
//                echo $role . ' ' . ' ' . $resource . '<br />';

                if( $role == 'it' && $resource == 'webassignment' ) {
//                    echo 'aha';
                    $acl->deny($role, $resource);
//                    die();
                }
                $acl->allow($role, $resource);
            }
//            echo '<br />';
        }
//        die();
        $e->getViewModel()->acl = $acl;
    }

    public function checkAcl(MvcEvent $e)
    {
        $route = $e->getRouteMatch()->getMatchedRouteName();
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        $userRole = $userLogin['role'];
//        var_dump($userRole);
//        var_dump($route);
//die();
        //you set your role
//        $userRole = 'guest';
        if( $e->getViewModel()->acl->isAllowed($userRole,$route) ) {
            $e->getResponse()->setStatusCode(404);
            $e->getResponse()->sendHeaders();
            return;
        }
    }


    public function getServiceConfig() {
        return array(
            'invokables'    =>  array(
                'EventListeners' =>  'Listeners\Event\Listener',
            ),
            'factories' => array(
                'Content\ContentForm\Model\SearchTable' => function($sm) {
                        $this->_adapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $table = new \Content\ContentForm\Model\SearchTable($this->_adapter);
                        return $table;
                    },
            ),



        );
    }
}

