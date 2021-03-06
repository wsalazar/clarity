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
//        echo '<pre>';
//        var_dump($skuData);
//        echo "==============";
//        var_dump($queriedData);
//        exit();
        $view = new ViewModel(array('data'=>$queriedData,'originalData' => $skuData));
//        $view->setTerminal(true);

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
