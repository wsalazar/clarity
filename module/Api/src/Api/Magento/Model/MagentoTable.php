<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/17/14
 * Time: 5:10 PM
 */

namespace Api\Magento\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Search\Tables\Spex;

class MagentoTable {

    protected $adapter;

    protected $select;

    protected $data = array();

    protected $sql;

    protected $dirtyCount;

    protected $attributeDirtyCount = 0;

    protected $dirtyItems;

    protected $imgPk = array();

    use Spex;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    public function fetchImages()
    {
        $select = $this->sql->select()->from('productattribute_images')->where(['productattribute_images.dataState'=>2])->join(['p'=>'product'],'productattribute_images.entity_id = p.entity_id',['sku'=>'productid']);
        $select->quantifier(Select::QUANTIFIER_DISTINCT);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet->toArray();
//        return $this->productAttribute($this->sql,array(),array('dataState'=>2),'images')->toArray();
    }

    //Lookup Clean was here.

   //Lookup New was here.

   //Lookup New and/or Updated Images.

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
        $results = $this->productAttributeLookup($this->sql);
        foreach($results as $key => $arg){
            $dataType = $results[$key]['dataType'];
            $attributeId = $results[$key]['attId'];
            $attributeCode = $results[$key]['attCode'] === 'name' ? 'title' : $results[$key]['attCode'];
            $newAttribute = $this->fetchAttribute( $dataType,$attributeId,$attributeCode);
            if(is_array($newAttribute)){
                foreach($newAttribute as $newAtt){
                    $result[] = $newAtt;
                }
            }
        }
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
//        $columns = array('id'=>'entity_id', $property => 'value', 'ldate' => 'lastModifiedDate');
//        $where = array( 'attribute_id' => $attributeid, 'productattribute_'.$tableType. '.dataState'=> '1');
//        $joinTables = array(
//            array(
//                array('prod' => 'product'),'prod.entity_id = productattribute_'.$tableType. ' .entity_id ' ,array('item' => 'productid')),
//            array(
//                array('u' => 'users'),'u.userid = productattribute_'.$tableType. ' .changedby ',array('fName' => 'firstname', 'lName' => 'lastname'))
//        );
//        $resultSet = $this->productAttribute($this->sql, $columns, $where, $tableType, null, $joinTables);
//die();
        $select = $this->sql->select();

        $select->from('productattribute_'.$tableType);

        $select->columns(array('id'=>'entity_id', $property => 'value', 'ldate' => 'lastModifiedDate'));
        $select->join(array('p' => 'product'),'p.entity_id = productattribute_'.$tableType. ' .entity_id ' ,array('item' => 'productid'));
        $select->join(array('u' => 'users'),'u.userid = productattribute_'.$tableType. ' .changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'));
        $select->where(array( 'attribute_id' => $attributeid, 'productattribute_'.$tableType. '.dataState'=> '1'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
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
    //Soap Media was here

    public function fetchCategoriesSoap()
    {
        $select = $this->sql->select();
        $filter = new Where();
        $filter->in('productcategory.dataState',array(2,3));
        $select->from('productcategory')
               ->columns(array('entityId'=>'entity_id','categortyId'=>'category_id', 'dataState'=>'dataState'))
               ->join( array('p'=>'product'), 'p.entity_id=productcategory.entity_id',array('sku'=>'productid'))
//               ->where(array('productcategory.dataState'=>2,'productcategory.dataState'=>3),PredicateSet::OP_OR);
               ->where($filter);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        //TODO have to implement a count feature for this.
//        $resultSet->count()
        return $resultSet->toArray();
    }

   //Soap Categories was here

    //Soap Content was here

    public function updateImagesToClean()
    {
        $result ='';
        foreach($this->imgPk as $pk){
            $result = $this->productUpdateaAttributes($this->sql, 'images', array('dataState'=>0), array('value_id'=>$pk));
        }
        return $result;
    }

    public function updateProductCategories($catsToUpdate)
    {
        $result ='';
        foreach($catsToUpdate as $key => $fields){
            $dataState = (int)$catsToUpdate[$key]['dataState'];
            if( $dataState === 2){
                $update = $this->sql->update('productcategory');
                $update->set(array('dataState'=>0))
                       ->where(array('entity_id'=>$catsToUpdate[$key]['entityId'], 'category_id'=>$catsToUpdate[$key]['categortyId']));
                $statement = $this->sql->prepareStatementForSqlObject($update);
                $result = $statement->execute();
            } else {
                $delete = $this->sql->delete('productcategory');
                $delete->where(array('entity_id'=>$catsToUpdate[$key]['entityId'], 'category_id'=>$catsToUpdate[$key]['categortyId']));
                $statement = $this->sql->prepareStatementForSqlObject($delete);
                $result = $statement->execute();
            }
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

    //Soap Add Products

    public function fetchNewItems()
    {
        echo '<pre>';
        //fetches all attribute codes from look up table and looks them up in corresponding attribute tables only if they are new.
        $soapBundle = [];
        $select = $this->sql->select()->from('product')->columns([
            'entityId'  =>  'entity_id',
            'sku'   =>  'productid',
            'productType'  =>  'product_type',
            'website'   =>  'website',
            'dateCreated'   =>  'creationdate',
        ])->where(array('dataState'=>2))->quantifier(Select::QUANTIFIER_DISTINCT);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $products = $resultSet->toArray();
        foreach($products as $index => $value){
            $entityId = $products[$index]['entityId'];
            $attributes = $this->productAttribute($this->sql,['attributeId'=>'attribute_id','dataType'=>'backend_type','attCode'=>'attribute_code'],[],'lookup')->toArray();
            foreach($attributes as $key => $fields){
                $tableType = $attributes[$key]['dataType'];
                $attributeId = (int)$attributes[$key]['attributeId'];
                $attributeCode = $attributes[$key]['attCode'];
                $attributeValues = $this->productAttribute($this->sql, [$attributeCode=>'value'],['entity_id'=>$entityId,'attribute_id'=>$attributeId, 'dataState'=>2],$tableType)->toArray();

                    foreach($attributeValues as $keyValue => $valueOption){
//                         echo $attributeValues[$keyValue][$attributeCode]. '<br />';
//                        $soapBundle[$index]['entityId'] = $entityId;
//                        $soapBundle[$index]['dataState'] = $products[$index]['dataState'];
//                        $soapBundle[$index]['entityId'] = $products[$index]['entityId'];
                        $soapBundle[$index]['sku'] = $products[$index]['sku'];
                        $soapBundle[$index]['website'] = $products[$index]['website'];
//                        $soapBundle[$index]['attCodeValue '] = $attributeValues[$keyValue][$attributeCode];
                        $soapBundle[$index][$attributeCode] = $attributeValues[$keyValue][$attributeCode];
                        $optionID = $attributeValues[$keyValue][$attributeCode];
//                        echo $attributeValues[$keyValue]['manufacturer']. ' ' ;
//                        $this->productAttribute($this->sql, ['manufacturer'=>'value'],['option_id'=>$optionID] ,'option')->toArray();
//                    }
                }
            }
        }
//        die();

        return $soapBundle;
//var_dump($soapBundle);
//die();
//        $rows = array();
//        echo '<pre>';
//        $products = [];


    }

    public function updateNewItems($newProducts)
    {
        foreach($newProducts as $index => $fields){
            $update = $this->sql->update('product');
            $update->set(['dataState'=>0]);
            $update->where(['productid'=>$newProducts[$index]['sku']]);
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $result = $statement->execute();
            $resultSet = new ResultSet;
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet->initialize($result);
            }
            return $resultSet;
        }
        foreach($newProducts as $key => $product){
            $select = $this->sql->select();
            $select->from('product')->columns(['entityId'=>'entity_id'])->where(['productid'=>$newProducts[$key]['sku']]);
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $resultSet = new ResultSet;
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet->initialize($result);
            }
            $entityId = $resultSet->toArray();
            $shiftedProducts = array_shift($newProducts[$key]['website']);
            $productKeys = array_keys($shiftedProducts);
            foreach($productKeys as $keys => $attCodes){
                $lookup = $this->productAttributeLookup($this->sql, ['attribute_code'=>$productKeys[$keys]]);
                $update = $this->sql->update('productattribute_'.$lookup[0]['backend_type']);
                $update->set(['dataState'=>0]);
                $update->where(['entity_id'=>$entityId[0]['entityId'],'attribute_id'=>$lookup[0]['attribute_id']]);
                $updateStmt = $this->sql->prepareStatementForSqlObject($update);
                $upRes = $updateStmt->execute();
                $updateSet = new ResultSet;
                if ($upRes instanceof ResultInterface && $upRes->isQueryResult()) {
                    $resultSet->initialize($result);
                }
                return $updateSet;
            }
        }
    }

}