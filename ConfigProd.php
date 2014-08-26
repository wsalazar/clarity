<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/26/14
 * Time: 11:29 AM
 */

include 'config/autoload/local.php';
include 'init_autoloader.php';
use Zend\Soap\Client;
$attributeSetId = '';
$soapHandle = new Client(SOAP_URL);
$session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
$fetchAttributeList = [$session, 'product_attribute_set.list'];
$attributeSets = $soapHandle->call('call', $fetchAttributeList);
foreach($attributeSets as $setId => $name){
    if($name['name'] == 'Default'){
        $attributeSetId = $name['set_id'];
    }
}
$productSku = '0123456789ABC-config';
$newProductData = [
    'name'              => 'test product # 1',
    // websites - Array of website ids to which you want to assign a new product
    'websites'          => [3], // array(1,2,3,...)
    'short_description' => 'short description',
    'description'       => 'description',
    'price'             => 12.05,
    'status'            => 2,
    'visibility'        => 1,
    'manufacturer'      =>  1,
    'color'      =>  1,
];
$packet = [$session, 'product.create', ['configurable',$attributeSetId, $productSku,$newProductData ]];

$entityId = $soapHandle->call('call',$packet);
var_dump($entityId);