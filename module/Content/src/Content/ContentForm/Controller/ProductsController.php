<?php
/**
 * Created by wsalazar.
 * Date: 6/16/14
 * Time: 10:40 AM
 */

namespace Content\ContentForm\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\ClassMethods as cHydrator;
use Zend\View\Model\ViewModel;
use Content\ContentForm\Entity\Products;
use Zend\Session\Container;
use FileParser\File\Model\CSV;


/**
 * Class ProductsController
 * @package Content\Products\Controller
 */
class ProductsController extends AbstractActionController {

    protected $formTable;
    protected $imageTable;

    //protected $skuData = array();
    /**
     * @return ViewModel
     */
    public function indexAction(){
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }

        $file = new CSV(__DIR__.'/../../../../../../missing_names.csv');
        $fields = $file->populate(['sku','name']);
        echo '<pre>';
        var_dump($fields);
        $conn = mysqli_connect(HOST, USER, PASS, DB);

        foreach ($fields as $key => $sku) {
            $select = "select entity_id from spex.product where productid = '". $sku['sku']."'";
            $lid = mysqli_query($conn,$select);
            $entityId = mysqli_fetch_row($lid);
//            var_dump($entityId);
            echo $sku['sku'] . ' ' . $entityId[0] . ' ' . $sku['name'] . "<br />";

            $insert = "INSERT INTO productattribute_varchar (entity_id, attribute_id, value, dataState, lastModifiedDate, changedby)
            values(" . $entityId[0] . ", 96,'" . $sku['name']. "',0,'" .  date('Y-m-d h:i:s') ."', 27)";
            if( !mysqli_query($conn,$insert) ){
                die(mysqli_error($conn));
            }
        }
die();
        $queriedData = new Products();
        $sku = $this->params()->fromRoute('sku');
        $form = $this->getFormTable();

        if($sku){
            /*Checks to see if Sku exists*/
            $entityID = $form->validateSku($sku);

            /*If sku does not exist, redirect user back to search page.*/
            if(!$entityID){
                return $this->redirect()->toRoute('search');
            }
            //insert error handle for invalid sku here

            //lookupdata
            $skuData = $form->lookupForm($entityID['entity_id']);

            //hydrate data to form entity
            $hydrator = new cHydrator;
            $hydrator->hydrate($skuData,$queriedData);
        }
        else{
            return $this->redirect()->toRoute('search');
        }

        $view = new ViewModel(array('data'=>$queriedData,'originalData' => $skuData));

        return $view;
    }
//  load accessories action was here

//  load categories action was here.

//  submit form action was here.

//  brand load action was here.

//  manufacturer load action was here.

//  image save action was here.


    public function getFormTable(){
        if (!$this->formTable) {
            $sm = $this->getServiceLocator();
            $this->formTable = $sm->get('Content\ContentForm\Model\ProductsTable');
        }
        return $this->formTable;
    }

    public function getImageTable(){
        if (!$this->imageTable) {
            $sm = $this->getServiceLocator();
            $this->imageTable = $sm->get('Content\ContentForm\Model\ImageTable');
        }
        return $this->imageTable;
    }
}
