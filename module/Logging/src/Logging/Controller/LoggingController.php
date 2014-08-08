<?php

namespace Logging\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;


class LoggingController extends AbstractActionController
{

    protected $loggingTable;

    /**
     * Description: this action on load will load all rows from the logger table into the data table in the view.
    */
    public function indexAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $logs = $this->getLoggingTable();
        $request = $this->getRequest();
        if($request->isPost()) {
            $logsInfo = $request->getPost();
            $draw = $logsInfo['draw'];
            $sku = (!is_null($logsInfo['search']['value']))? $logsInfo['search']['value']: null;


//            $filterDateRange = (!is_null($logsInfo['filterDateRange'])) ? $logsInfo['filterDateRange'] : null;
//            $dateRange = explode('to',$filterDateRange);
//            $fromDate = trim((string)$dateRange[0]);
//            $toDate = trim((string)$dateRange[1]);
//            $fromDate = date('Y-m-d h:i:s', strtotime($fromDate) );
//            $toDate = date('Y-m-d h:i:s', strtotime($toDate) );


            $searchParams = array('sku'=>$sku);//,'from'=>$fromDate,'to'=>$toDate);
//            var_dump($searchParams);
//            die();

//            $dateRange = array('from'=>$fromDate,'to'=>$toDate);

//            var_dump($filterDateRange);
//            $limit = $loadAccessories['length'];
//            if($limit == '-1'){
//                $limit = 100;
//            }
            $loadedLogs = $logs->lookupLoggingInfo($searchParams);
//            die();
            $result = json_encode(
                array(
                    'draw'  =>  (int)$draw,
                    'data'  =>  $loadedLogs,
                    'recordsTotal'  =>  1000,
//                    'recordsFiltered'   =>  $limit,
                )
            );
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;

        }
    }

    public function revertAction()
    {
        $revert = $this->getLoggingTable();
        $request = $this->getRequest();
        if($request->isPost()) {
            $logsInfo = $request->getPost();
            $draw = $logsInfo['draw'];
            $oldValue = $logsInfo['oldValue'];
            $newValue = $logsInfo['newValue'];
        }
    }

    public function listUsersACtion()
    {
        $users = $this->getLoggingTable();
        $userList = $users->listUser();
        $result = json_encode($userList);
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);
        return $response;
    }

    public function getLoggingTable()
    {
        if (!$this->loggingTable) {
            $sm = $this->getServiceLocator();
            $this->loggingTable = $sm->get('Logging\Model\LoggingTable');
        }
        return $this->loggingTable;
    }
}