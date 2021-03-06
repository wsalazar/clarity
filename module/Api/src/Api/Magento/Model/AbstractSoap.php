<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 9/10/14
 * Time: 9:31 AM
 */

namespace Api\Magento\Model;

use Zend\Soap\Client;

abstract class AbstractSoap
{

    protected $_soapHandle;

    protected $_session;

    public function __construct($soapURL)
    {
        $this->_soapHandle = new Client($soapURL);
        $this->_session = $this->_soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
    }

    protected function _getAttributeSet()
    {
        $fetchAttributeList = [$this->_session, 'product_attribute_set.list'];
        $attributeSets = $this->_soapHandle->call('call', $fetchAttributeList);
        $attributeSet = '';
        foreach ( $attributeSets as $aSets ) {
            if ( (int) $aSets['set_id'] === 9 ) {
                $attributeSet = $aSets['set_id'];
            }
        }
        return $attributeSet;
    }

    protected function _soapCall($packet, $resource = Null, $skuCollection)
    {
        $a = 0;
        $batch = $results = $result = $status = [];
        while( $a < count($packet) ){
            $x = 0;
            while($x < 10 && $a < count($packet)) {
                if( isset($packet[$a]['dataState']) && $packet[$a]['dataState'] == 3 ) {
                    $resource = $packet[$a]['resource'];
                    $batch[$x] = array($resource, $packet[$a]);
                } else if( isset($packet[$a]['dataState']) && $packet[$a]['dataState'] == 2 ) {
                    $resource = $packet[$a]['resource'];
                    $batch[$x] = array($resource, $packet[$a]);
                } else {
//                    echo $resource;
                    $batch[$x] = array($resource, $packet[$a]);
                }
                $x++;
                $a++;
            }
            sleep(15);
            $results[] = $this->_soapHandle->call('multiCall',array($this->_session, $batch));
        }
//        var_dump($results);
        $totalTime = $this->stopStopwatch();
        foreach ( $results as $key => $res ) {
            foreach ( $res as $index => $r ) {
                if( isset($r['faultCode']) && (int)$r['faultCode'] == 1 ) {
                    $result[$key][$index] = False;
                    $this->insertIntoMageLog($skuCollection[$index] ,'Sku already exists', $totalTime, 'Fail');
                } else if( isset($r['isFault']) ) {
                    $result[$key][$index] = False;
                    $this->insertIntoMageLog($skuCollection[$index] ,$r['faultMessage'], $totalTime, 'Fail');
                } else {
                    if(  isset($packet[$index]['dataState']) &&  (int)$packet[$index]['dataState'] === 3 ) {
                        $resource = $packet[$index]['resource'];
                    }
                    if(  isset($packet[$index]['dataState']) &&  (int)$packet[$index]['dataState'] === 2 ) {
                        $resource = $packet[$index]['resource'];
                    }
                    $result[$key][$index] = $results[$key][$index];
                    $this->insertIntoMageLog($skuCollection[$index] ,$resource, $totalTime, 'Success');
                }
            }
        }
//          var_dump($result);
//          die();
        return $result;
    }
} 