<?php
namespace Authenticate;

use Zend\View\HelperPluginManager;
use Zend\Mvc\MvcEvent;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Session\Container;
use Zend\Db\Sql\Sql;

class Module
{
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
    }

    public function dbRoles()
    {
        $e = new MvcEvent();
echo get_class($e->getApplication());
        $adapter = $e->getApplication()->getServiceManager()->get('Zend\Db\Adapter\Adapter');
        $adapterClass =  get_class($adapter);
        $results = $adapter->query('SELECT * FROM useraccess', $adapterClass::QUERY_MODE_EXECUTE);
        var_dump($results);
        die();
        $roles = [];
        foreach ( $results as $result ) {
            $roles[$result['user_role']][] = $result['resource'];
        }
        return $roles;
    }

    public function getViewHelperConfig()
    {
        return [
            'factories' =>  [
                'navigation'    =>  function(HelperPluginManager $pm){
                        $e = new MvcEvent();
                        $sm = $pm->getServiceLocator();
                        $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $sql = new Sql($adapter);
                        $select = $sql->select()->from('useraccess');
                        $statement = $sql->prepareStatementForSqlObject($select);
                        $result = $statement->execute();

                        $resultSet = new ResultSet;
                        if ($result instanceof ResultInterface && $result->isQueryResult()) {
                            $resultSet->initialize($result);
                        }
                        $results = $resultSet->toArray();
                        foreach ( $results as $key => $result ) {
                            $acl = new Acl;
                            $parentRole = $result['user_role_type'];
                            $role = $result['user_role'];
                            $resource = $result['resource'];
                            $permission = (int)$result['permission'];
                            if( !empty($parentRole) ) {
                                if( !$acl->hasRole($parentRole) ) {
                                    $acl->addRole($parentRole);
                                }
                                if( !$acl->hasRole($role) ) {
                                    $acl->addRole($role, $parentRole);
                                }
                                if( !$acl->hasResource($resource) ) {
                                    $acl->addResource($resource);
                                }
                            } else {
                                if( !$acl->hasRole($role) ) {
                                    $acl->addRole($role);
                                }
                                if( !$acl->hasResource($resource) ) {
                                    $acl->addResource($resource);
                                }
                            }
                            if( $permission ) {
                                $acl->allow($role, $resource);
                            } else {
                                $acl->deny($role, $resource);
                            }

                        }
                        $auth = $sm->get('Zend\Authentication\AuthenticationService');
                        $role = '';
                        if ($auth->hasIdentity()) {
                            $userId = $auth->getIdentity();
                            $select = $sql->select()->from('users')->where(['userid'=>$userId]);
                            $statement = $sql->prepareStatementForSqlObject($select);
                            $result = $statement->execute();

                            $resultSet = new ResultSet;
                            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                                $resultSet->initialize($result);
                            }
                            $role = $resultSet->toArray()[0]['role'];
                        }
//                        $e->getViewModel()->acl = $acl;
//
//                        $route = $e->getRouteMatch()->getMatchedRouteName();
//                        if( $e->getViewModel()->acl->isAllowed($role,$route) ) {
//                            $e->getResponse()->setStatusCode(404);
//                            $e->getResponse()->sendHeaders();
//                            return;
//                        }


                        // Get an instance of the proxy helper
                        $navigation = $pm->get('Zend\View\Helper\Navigation');

                        // Store ACL and role in the proxy helper:

                        $navigation->setAcl($acl)
                                   ->setRole($role);

//var_dump($navigation);
//                        die();
                        // Return the new navigation helper instance
                        return $navigation;
                    }
            ],
        ];
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
//            $e->getResponse()->setStatusCode(404);
            $e->getResponse()->sendHeaders();
            return;
        }
    }


}
