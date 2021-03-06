<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Common\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
//        echo 'haha';
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $this->layout('layout/layout');
//        return $this->redirect()->toRoute('home');
        return new ViewModel(
//            array(
//                'firstName' =>  $userLogin['firstname'],
//                'lastName'  =>  $userLogin['lastname'],
//                'username'  =>  $userLogin['username'],
//            )
        );
    }

    public function logoutAction(){

        $loginSession= new Container('login');
        $loginSession->offsetUnset('sessionDataforUser');
        return $this->redirect()->toRoute('auth', array('action'=>'index') );
    }
}
