<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/26/14
 * Time: 4:08 PM
 */

namespace Api\Magento\Model;

use Zend\Soap\Client;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use SoapClient;

class Soap extends AbstractSoap
{

    protected $imgPk = array();


    public function __construct()
    {
        parent::__construct(SOAP_URL);

    }

    public function soapMedia($media = array())
    {
        $packet = [];
        $imageBatch = array();
//        if(!is_array($media)) {
//            throw new \InvalidArgumentException(
//                sprintf("Bad argument in class %s for function %s in line %s.",__CLASS__, __FUNCTION__, __LINE__)
//            );
//        }
//            $options = array('login'=>SOAP_USER, 'password'=>SOAP_USER_PASS);
//        $soapHandle = new Client(SOAP_URL);
//            if $options does not work for logging in then try the following.
//        $session = $this->soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
        foreach($media as $key => $imgFileName) {
//                $imgDomain = $media[$key]['domain'];//this will change to whatever cdn we will have.
            $imgName = $media[$key]['filename'];
            $imageBatch[$key]['position'] = $media[$key]['position'];
            $imageBatch[$key]['label'] = $media[$key]['label'];
            $imageBatch[$key]['disabled'] = $media[$key]['disabled'];
            $imageBatch[$key]['value_id'] = $media[$key]['value_id'];
            $imageBatch[$key]['sku'] = $media[$key]['sku'];
            $entityId = $media[$key]['entity_id'];
            $imgPath = file_get_contents("public".$imgName);
//                $imgPath = 'http://www.focuscamera.com/media/catalog/product'.$imgName;

//                $fileContents = file_get_contents($imgPath);
            $fileContentsEncoded = base64_encode($imgPath);
//                $fileContentsEncoded = base64_encode($fileContents);
            $file = array(
                'content'   =>  $fileContentsEncoded,
                'mime'  =>  'image/jpeg',
            );
            $imageBatch[$key]['entityId'] = $entityId;
            $imageBatch[$key]['imageFile'] = $file;

        }
        $results = false;
        foreach($imageBatch as $key => $batch){
            $entityId = $imageBatch[$key]['entityId'];
            $this->imgPk[] = $imageBatch[$key]['value_id'];
            $fileContents = $imageBatch[$key]['imageFile'];
            $position = $imageBatch[$key]['position'];
            $disabled = $imageBatch[$key]['disabled'];
            $label = $imageBatch[$key]['label'];
            $sku = $imageBatch[$key]['sku'];
            $packet[$key] = [$this->session,PRODUCT_ADD_MEDIA, [
                $sku,
               [
                    'file'  =>  $fileContents,
                    'label' =>  $label,//'no label',
                    'position'  =>  $position,//'0',
//                        'types' =>  array('thumbnail'), //what kind of images is this?
                    'excludes'  =>  0,
                    'remove'    =>  0,
                    'disabled'  =>  0,  //$disabled variable would normally be here.
                ]
            ]];
        }
        return $this->soapCall($packet);
    }

    public function soapCategoriesUpdate($categories)
    {
        $results = false;
//        $soapHandle = new Client(SOAP_URL);
        $packet = array();
//        $session = $$this->soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
        foreach($categories as $key => $fields){
            $entityId = $categories[$key]['entityId'];
            $sku = $categories[$key]['sku'];
            $dataState = (int)$categories[$key]['dataState'];
            $categortyId = $categories[$key]['categortyId'];
            if( 3 === $dataState ){
                $packet[$key] = array($this->session, PRODUCT_DELETE_CATEGORY, array('categoryId'=>$categortyId,'product'=>$entityId ));
            }
            if( 2 === $dataState ){
                $packet[$key] = array($this->session, PRODUCT_ASSIGN_CATEGORY, array('categoryId'=>$categortyId,'product'=>$entityId ));
            }
        }
        return $this->soapCall($packet);
    }

    public function soapContent($data)
    {
        $result = false;
        $soapClient = new SoapClient(SOAP_URL);
        $session = $soapClient->login(SOAP_USER, SOAP_USER_PASS);
        $i = 0;
        $updateBatch = array();
        foreach($data as $key => $value){
            if( isset($value['id']) ) {
                $entityID = $value['id'];
                array_shift($value);
                $updatedValue = current($value);
//                    $this->productAttribute();
//                    $attributeCode = lcfirst(current(array_keys($value)));
                $attributeCode =  current(array_keys($value));
                $attributeCode = $attributeCode == 'title' ? 'name' : $attributeCode;
                $attributeCode = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2',$attributeCode  ));
                //$updatedKey = $this->lookupAttribute(lcfirst(current(array_keys($value))));
//                    echo $updatedKey . ' ' ;
                $updateBatch[$i] = array('entity_id' => $entityID, array($attributeCode => $updatedValue));
                $i++;
            }
        }
        $a = 0;
        while( $a < count($updateBatch) ){
            $x = 0;
            while($x < 10 && $a < count($updateBatch)){
                $queueBatch[$x] = array(PRODUCT_UPDATE, $updateBatch[$a]);
                $x++;
                $a++;
            }
            sleep(15);
            $result = $soapClient->multiCall($session, $queueBatch);
        }
        return $result;
    }

    public function soapAddProducts($newProds)
    {
        $packet = [];
//        $soapHandle = new Client(SOAP_URL);
//        $session = $this->soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
        $fetchAttributeList = [$this->session, 'product_attribute_set.list'];
        $attributeSets = $this->soapHandle->call('call', $fetchAttributeList);
        $attributeSet = current($attributeSets);
        /*
         * This is a static example of how to ctreate a product
         * */
/*        $set = array(
            'name'    =>  '11" MB Air Kate Spade Dots',
            'description'   =>  'Kate Spade MacBook Air Slip Sleeve 11" Dots/Polyurethane',
        );
        $packet = [$this->session, 'catalog_product.create', ['simple', $attributeSet['set_id'], '031460', $set]];
        try{
            $results = $soapHandle->call('call', $packet );
        } catch (\SoapFault $e){
            trigger_error($e->getMessage(), E_USER_ERROR ); //should possibly go in log file?
            $results = $e->getCode(); //should be return to controller?
        }
        return $results;
*/
        $count = 0;
        $set = [];
        foreach($newProds as $index => $fields){
            $keys = array_keys($newProds[$index]);
            $sku = $newProds[$index]['sku'];
            array_shift($keys);
            array_shift($newProds[$index]);
            $packetCount = 0;
            foreach($keys as $ind => $attFields){
                $set[$packetCount] = [
                    $keys[$ind]   =>  $keys[$ind] == 'website' ? [$newProds[$index][$keys[$ind]]] : $newProds[$index][$keys[$ind]],
                ];
                $packetCount++;
            }
            $packet[$count] = [$this->session, 'catalog_product.create', ['simple', $attributeSet['set_id'], $sku, $set]];
            $count++;
        }
        return $this->soapCall($packet);
    }
} 