<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/17/14
 * Time: 4:04 PM
 */

namespace Api\Magento\Controller;

use Zend\Db\Exception\UnexpectedValueException;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Search\Tables\Spex;

class MagentoController  extends AbstractActionController
{
    use Spex;

    protected $magentoTable;

    protected $skuData;

    protected $dirtyAttributeSkus = array();

    public function magentoAction()
    {
        $mtime = microtime();
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];
        $starttime = $mtime;
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $this->skuData = array();
        /*This method looks up all the dirty products and attributes*/
        $this->skuData = $this->getMagentoTable()->lookupDirt();
        /*Fetch the count to all products and product attributes*/
        $changedProductsCount = $this->getServiceLocator()->get('Api\Magento\Model\FetchProductsMetric')->fetchChangedProductsCount();

        /*TODO Have to add UI to display the images to be sent over soap to Magento side*/
//        $images = $this->getMagentoTable()->lookupNewUpdatedImages();
        /*Fetches the count to all images that have been added. Does not include images who's labels have changed. Data State of 1 for updated*/
        $images = $this->getServiceLocator()->get('Api\Magento\Model\FetchProductsMetric')->fetchNewImagesCount();



        $tableHeaders = array('ID','SKU','Attribute Field','New Attribute Value','Last Modified Date','Changed By');
        $session = new Container('dirty_skus');
        $dirtySkus = array();
        $session->dirtyProduct = $this->skuData;
//        $mtime = microtime();
//        $mtime = explode(" ",$mtime);
//        $mtime = $mtime[1] + $mtime[0];
//        $finishtime = $mtime;
//        $totalTime = round(($finishtime-$starttime),4);
        return new ViewModel(
            array(
//                'loadTime'  =>  $totalTime,
                'updateHeaders' => $tableHeaders,
                'sku'   =>  $this->skuData,
//                'cleanCount'    => $cleanCount,
//                'newCount'    => $newCount,
                'newImages'    => $images,
                'changedProductsCount' => $changedProductsCount,
            )
        );
    }

    protected function soapItemAction()
    {
        $categorySoapResponse = $response = $res = false;
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $session = new Container('dirty_skus');
        $dirtyData = $session->dirtyProduct;

        /*Fetch categories*/
//        $categories = $this->getMagentoTable()->fetchCategoriesSoap();
//        $relatedProds = $this->getMagentoTable()->fetchRelatedProducts();
//        if(!empty($categories)){
            /*Make api call to delete and update Sku with new category*/
//            $categorySoapResponse = $this->getServiceLocator()->get('Api\Magento\Model\Soap')->soapCategoriesUpdate($categories);
//        }
        if(!empty($dirtyData)){
            /*Update Mage with up-to-date products*/
            $response = $this->getServiceLocator()->get('Api\Magento\Model\Soap')->soapContent($dirtyData);
        }
//        if(!empty($relatedProds)){
            /*Update Mage with up-to-date products*/
//            $response = $this->getServiceLocator()->get('Api\Magento\Model\Soap')->soapRelatedProducts($relatedProds);
//        }
echo '<pre>';
        if( $categorySoapResponse || $response){
            foreach($response as $key => $soapRes){
                foreach($soapRes as $ind => $soap){
                    switch($soap['faultCode']){
                        case '100':
                            throw new \UnexpectedValueException('Requested store view not found.');
                            break;
                        case '101':
                            throw new \UnexpectedValueException('Product does not exist');
                            break;
                        case '102':
                            throw new \UnexpectedValueException('Invalid data given. Details in error message.');
                            break;
                    }

//                if( preg_match('/Product/', $soapResponse)){
//                    $res = $soapResponse;
//                }
//            SQLSTATE[40001]: Serialization failure: 1213 Deadlock found when trying to get lock; try restarting transaction
//            I suppose this happens when there is too much traffic. I think once content team moves over to zend it will not deadlock anymore.
//                if( preg_match('/Serialization failure/',$soapResponse )){
//                    $res = $soapResponse ;
//                }
                    if(true === $soap){
                        $res = $soap;
                    }

                }
            }
            if($res === true || (!is_null($categorySoapResponse) &&  $categorySoapResponse === true) ){
//                TODO have to find what out what the update statement actually returns.
//                $updateCategories = $this->getMagentoTable()->updateProductCategories($categories);
                $updateFields = $this->getMagentoTable()->updateToClean($dirtyData);
                if($updateFields || $updateCategories){
                    return $this->redirect()->toRoute('apis');
              }
            }
            if( preg_match('/Category not exist/',$categorySoapResponse) ){
//            'Category not exist' for $categorySoapResponse
                trigger_error('Category does not exist for Magento Admin');
                throw new \UnexpectedValueException('Category does not exist in Magento Admin');
            }
        }
    }

    public function soapNewItemsAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $newProducts = $this->getMagentoTable()->fetchNewItems();
        if( $newProductResponse = $this->getMagentoTable()->soapAddProducts($newProducts) ){
            switch((int)$newProductResponse){
                case 100:
                    throw new \UnexpectedValueException('Requested Store View not Found');
                    break;
                case 102:
                    throw new \UnexpectedValueException('Invalid data given');
                    break;
                case 104:
                    throw new \UnexpectedValueException('Product Type is not in allowed types');
                    break;
                case 105:
                    throw new \UnexpectedValueException('Product attribute set is not existed');
                    break;
                case 106:
                    throw new \UnexpectedValueException('Product attribute set is not belong catalog product entity type');
                    break;
                default:
                    $this->getMagentoTable()->updateNewItems($newProducts);
                    return $this->redirect()->toRoute('apis', array('action'=>'magento'));
                    break;
            }
        }
    }

    public function soapImagesAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $images = $this->getMagentoTable()->fetchImages();
        if($this->getServiceLocator()->get('Api\Magento\Model\Soap')->soapMedia($images)) {
            if($this->getMagentoTable()->updateImagesToClean()){
                return $this->redirect()->toRoute('apis', array('action'=>'magento'));
            }
        }
    }
    //TODO maybe add getServiceLocator as a service to be injected into all controllers
    public function getMagentoTable()
    {
        if (!$this->magentoTable) {
            $sm = $this->getServiceLocator();
            $this->magentoTable = $sm->get('Api\Magento\Model\MagentoTable');
        }
        return $this->magentoTable;
    }
}