<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/17/14
 * Time: 5:10 PM
 */

namespace Api\Magento\Model;

use Zend\Db\Adapter\Adapter;
use SoapClient;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Search\Tables\Spex;
use Zend\Loader\Exception\InvalidArgumentException;
use Zend\Soap\Client;
use Search\Entity\Images;

class MagentoTable {

    protected $adapter;

    protected $select;

    protected $data = array();

    protected $sql;

    protected $dirtyCount;

    protected $attributeDirtyCount = 0;

    protected $dirtyItems;

    use Spex;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    public function fetchImages()
    {
        return $this->productAttribute($this->sql,array(),array('dataState'=>1),'images')->toArray();
    }

    public function lookupClean()
    {
//        I added this snippet of code so that we can find all product attributes that are clean, not just skus
//        $columns = array(
//            'attributeId'   =>  'attribute_id',
//            'attributeCode' =>  'attribute_code',
//            'backendType'   =>  'backend_type',
//        );
//        $lookupResult = $this->productAttribute($this->sql, $columns, array(), 'lookup' );
//        foreach($lookupResult as $key => $fieldTypes){
//            $attributeId = $lookupResult[$key]['attributeId'];
//            $attributeCode = $lookupResult[$key]['attributeCode'];
//            $backendType = $lookupResult[$key]['backendType'];
//        }
        $select = $this->sql->select();
        $select->from('product');
        $select->columns(array('id' => 'entity_id', 'ldate'=>'lastModifiedDate', 'item' => 'productid'));
        $select->where(array( 'dataState' => '0'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
//        $cleanCount = $resultSet->count();
        return $resultSet->count();
    }

    public function lookupNew()
    {
        $select = $this->sql->select();
        $select->from('product');
//        $select->columns(array('id' => 'entity_id', 'sku' => 'productid', 'ldate'=>'modifieddate', 'item' => 'productid'));
        $select->where(array( 'dataState' => '2'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
//        $cleanCount = $resultSet->count();
        return $resultSet->count();
    }

    public function lookupNewUpdatedImages()
    {
//        public function productAttribute(Sql $sql, array $columns = array(), array $where = array(),  $tableType )
//        $where = new Where();
        $where = array('left'=>'dataState', 'right'=>0);
        $filter = new Where;
        return $this->productAttribute($this->sql, array(), $where, 'images', $filter)->count();
    }

    public function lookupDirt()
    {
        $select = $this->sql->select();
        $select->from('product');
        $select->columns(array('id' => 'entity_id', 'sku' => 'productid', 'ldate'=>'lastModifiedDate', 'item' => 'productid'));
        $select->join(array('u' => 'users'),'u.userid = product.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'));

        $select->where(array( 'dataState' => '1'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $dirtyCount = $resultSet->count();
        $this->setDirtyCount($dirtyCount);
        $result = $resultSet->toArray();
//        TODO have to add my trait for product attribute look up to select table type attribute id and attribute code.
//        TODO from there I would use the table type to access each table using the attribute id.
        $columns = array('dataType'=>'backend_type','attributeId'=>'attribute_id','attributeCode'=>'attribute_code');

        $results = $this->productAttribute($this->sql, $columns, array(), 'lookup')->toArray();
//        $dataType = $results[0]['dataType'];
//        $attributeId = $results[0]['attributeId'];
//        $attributeCode = $results[0]['attributeCode'] === 'name' ? 'title' : $results[0]['attributeCode'];
        foreach($results as $key => $arg){
            $dataType = $results[$key]['dataType'];
            $attributeId = $results[$key]['attributeId'];
            $attributeCode = $results[$key]['attributeCode'] === 'name' ? 'title' : $results[$key]['attributeCode'];
            $newAttribute = $this->fetchAttribute( $dataType,$attributeId,$attributeCode);
            if(is_array($newAttribute)){
                foreach($newAttribute as $newAtt){
                    $result[] = $newAtt;
                }
            }
        }

//        $newAttribute = $this->fetchAttribute( $dataType,$attributeId,$attributeCode);
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Title
//        $newAttribute = $this->fetchAttribute( 'varchar','96','title');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch Price
//        $newAttribute = $this->fetchAttribute( 'decimal','99','price');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch Inventory
//        $newAttribute = $this->fetchAttribute( 'int','1','Inventory');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as  $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Status
//        $newAttribute = $this->fetchAttribute( 'int','273','Status');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch URLkey
//        $newAttribute = $this->fetchAttribute( 'varchar','481','url_key');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Cost
//        $newAttribute = $this->fetchAttribute( 'decimal','100','cost');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Rebate Price
//        $newAttribute = $this->fetchAttribute( 'decimal','1590','rebate');
////        $result[array_keys($newAttribute[0]] = $newAttribute;
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Mail in Rebate Price
//        $newAttribute = $this->fetchAttribute( 'decimal','1593','mailinRebate');
////        $result[array_keys($newAttribute[0]] = $newAttribute;
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Special Price
//        $newAttribute = $this->fetchAttribute( 'decimal','567','specialPrice');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch Special Start Date
//        $newAttribute = $this->fetchAttribute( 'datetime','568','specialEndDate');
////        $result[array_keys($newAttribute[0]] = $newAttribute;
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Special End Date
//        $newAttribute = $this->fetchAttribute( 'datetime','569','specialStartDate');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch Rebate Start Date
//        $newAttribute = $this->fetchAttribute( 'datetime','1591','rebateEndDate');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch Rebate End Date
//        $newAttribute = $this->fetchAttribute( 'datetime','1592','rebateStartDate');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch Mail in Start Date
//        $newAttribute = $this->fetchAttribute( 'datetime','1594','mailinEndDate');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Mail in  End Date
//        $newAttribute = $this->fetchAttribute( 'datetime','1595','mailinStartDate');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch metaTitle
//        $newAttribute = $this->fetchAttribute( 'varchar','103','meta_title');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch metaDescription
//        $newAttribute = $this->fetchAttribute( 'varchar','105','meta_description');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Description
//        $newAttribute = $this->fetchAttribute( 'text','97','description');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch inBox
//        $newAttribute = $this->fetchAttribute('text','1633','inBox');
//        // die(print_r($newAttribute);
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//
//        //Fetch includesFree
//        $newAttribute = $this->fetchAttribute( 'text','1679','includesFree');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//                $result[] = $newAtt;
//            }
//        }
//        //Fetch Short Description
//        $newAttribute = $this->fetchAttribute( 'text','506','short_description');
//        if(is_array($newAttribute)){
//            foreach($newAttribute as $newAtt){
//               $result[] = $newAtt;
//            }
//        }
        $this->setDirtyItems($this->getDirtyCount(), $this->getAggregateAttributeDirtyCount());
        return $result;
    }

    public function setDirtyItems($dirtyProducts, $dirtyAttributes)
    {
        $this->dirtyItems = $dirtyProducts + $dirtyAttributes;
    }

    public function getDirtyItems()
    {
        return $this->dirtyItems;
    }


    public function getAggregateAttributeDirtyCount()
    {
        return $this->attributeDirtyCount;
    }

    public function setAggregateAttributeDirtyCount($attributeDirtyCount)
    {
        $this->attributeDirtyCount += $attributeDirtyCount;
    }

    public function fetchAttribute($tableType, $attributeid, $property)
    {
        $columns = array('id'=>'entity_id', $property => 'value', 'ldate' => 'lastModifiedDate');
        $where = array( 'attribute_id' => $attributeid, 'productattribute_'.$tableType. '.dataState'=> '1');
        $joinTables = array(
            array(
                array('prod' => 'product'),'prod.entity_id = productattribute_'.$tableType. ' .entity_id ' ,array('item' => 'productid')),
            array(
                array('u' => 'users'),'u.userid = productattribute_'.$tableType. ' .changedby ',array('fName' => 'firstname', 'lName' => 'lastname'))
        );
        $resultSet = $this->productAttribute($this->sql, $columns, $where, $tableType, null, $joinTables);
//die();
//        $select = $this->sql->select();
//
//        $select->from('productattribute_'.$tableType);
//
//        $select->columns(array('id'=>'entity_id', $property => 'value', 'ldate' => 'lastModifiedDate'));
//        $select->join(array('p' => 'product'),'p.entity_id = productattribute_'.$tableType. ' .entity_id ' ,array('item' => 'productid'));
//        $select->join(array('u' => 'users'),'u.userid = productattribute_'.$tableType. ' .changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'));
//        $select->where(array( 'attribute_id' => $attributeid, 'productattribute_'.$tableType. '.dataState'=> '1'));
//
//        $statement = $this->sql->prepareStatementForSqlObject($select);
//        $result = $statement->execute();
//
//        $resultSet = new ResultSet;
//
//        if ($result instanceof ResultInterface && $result->isQueryResult()) {
//            $resultSet->initialize($result);
//        }
        $this->setAggregateAttributeDirtyCount($resultSet->count());
        $result = $resultSet->toArray();

        //check if array passed or value given
        if(!(is_array($result)) || current($result)[$property] == ''){
            $result = null;

        }

        return $result;
    }


        public function setDirtyCount($dirtyCount)
        {
            $this->dirtyCount = $dirtyCount;
        }

        public function getDirtyCount()
        {
            return $this->dirtyCount;
        }


//        public function lookupAttribute($key)
//        {
//            switch($key){
//                case 'sku':
//                    return 'product';
//                case 'title':
//                    return 'name';
//                case 'description':
//                    return $key;
//                case 'urlKey':
//                    return 'url_key';
//            }
//
//        }

        public function soapMedia($media = array(), Images $images)
        {
            $imageBatch = array();
            if(!is_array($media)) {
                throw new \InvalidArgumentException(
                    sprintf("Bad argument in class %s for function %s in line %s.",__CLASS__, __FUNCTION__, __LINE__)
                );
            }
//            $options = array('login'=>SOAP_USER, 'password'=>SOAP_USER_PASS);
            $soapHandle = new Client(SOAP_URL);
//            if $options does not work for logging in then try the following.
            $session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
            foreach($media as $key => $imgFileName) {
//                $imgDomain = $media[$key]['domain'];
                $imgName = $media[$key]['filename'];
                $entityId = $media[$key]['entity_id'];
                $imgPath = 'http://www.focuscamera.com/media/catalog/product'.$imgName;
//                echo $imgDomain . ' ' . $imgName . "<br />";

                echo $imgPath . '<br />';
                $fileContents = file_get_contents($imgPath);
                $fileContentsEncoded = base64_encode($fileContents);
                $file = array(
                    'content'   =>  $fileContentsEncoded,
                    'mime'  =>  'image/jpeg',
                );
                $imageBatch[$key]['entityId'] = $entityId;
                $imageBatch[$key]['imageFile'] = $file;

            }
            die();
            foreach($imageBatch as $key => $batch){
                $entityId = $imageBatch[$key]['entityId'];
                $fileContents = $imageBatch[$key]['imageFile'];
                $select = $this->sql->select();
                $select->from('product')->columns(array('sku'=>'productid'))->where(array('entity_id'=>$entityId));
                $statement = $this->sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $resultSet = new ResultSet;
                if ($result instanceof ResultInterface && $result->isQueryResult()) {
                    $resultSet->initialize($result);
                }
                $products = $resultSet->toArray();
                $sku = $products[0]['sku'];
//                $session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
                $packet = array(
                    $sku,
                    array(
                        'file'  =>  $fileContents,
                        'label' =>  $images->getLabel(),//'no label',
                        'position'  =>  $images->getPosition(),//'0',
                        'types' =>  array('thumbnail'), //what kind of images is this?
                        'excludes'  =>  0,
                    )
                );
                $batch = array($session, PRODUCT_ADD_MEDIA, $packet);
                $soapHandle->call('call', $batch);
            }
//            $result = $proxy->call(
//                $session,
//                'catalog_product_attribute_media.create',
//                array(
//                    $productId,
//                    array('file'=>$file, 'label'=>'Label', 'position'=>'100', 'types'=>array('thumbnail'), 'exclude'=>0)
//                )
//            );
        }
        public function soapContent($data)
        {
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

        public function updateToClean($data)
        {
            $result = '';
            foreach($data as $key => $value){
                //this sku part might have to be refactored
                    if(array_key_exists('sku', $data[$key])){
                        $update = $this->sql->update();
                        $update->table('product');
                        $update->set(array('dataState'=>'0'));
                        $update->where(array('productid'=>$data[$key]['sku']));
                        $statement = $this->sql->prepareStatementForSqlObject($update);
                        $result = $statement->execute();
                        $resultSet = new ResultSet;
                        if ($result instanceof ResultInterface && $result->isQueryResult()) {
                            $resultSet->initialize($result);
                        }
                    } else {
                        $entityId = $data[$key]['id'];
//                        $sku = $data[$key]['item'];
                        array_shift($data[$key]);
                        $attributeField = current(array_keys($data[$key]));
                        $attributeField = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2',$attributeField  ));

                        $columns = array('attributeId' => 'attribute_id', 'backendType' => 'backend_type');
                        $where = array('attribute_code' => ($attributeField == 'title') ? 'name' : $attributeField);
                        $results = $this->productAttribute($this->sql, $columns, $where, 'lookup');
                        $attributeId = $results[0]['attributeId'];
                        $tableType = $results[0]['backendType'];
                        $set = array('dataState'=>'0');
                        $where = array('entity_id'=>$entityId, 'attribute_id'=>$attributeId);
                        $result = $this->productUpdateaAttributes($this->sql, $tableType, $set, $where);
                    }
            }
            return $result;
        }
}