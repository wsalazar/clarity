<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/26/14
 * Time: 4:21 PM
 */

namespace Api\Magento\Model;

use SoapClient;

class AbstractSoap implements SoapInterface
{

    protected $soapHandle;

    protected $session;

    public function __construct($soapUrl)
    {
        $this->soapHandle = new SoapClient($soapUrl);
        $this->session = $this->soapHandle->login(SOAP_USER, SOAP_USER_PASS);
    }

    public function soapCall($packet)
    {
        $a = 0;
        $batch = [];
        $results = false;
        while( $a < count($packet) ){
            $x = 0;
            while($x < 10 && $a < count($packet)){
                $batch[$x] = array(PRODUCT_UPDATE, $packet[$a]);
                $x++;
                $a++;
            }
            $results[] = $this->soapHandle->multiCall($this->session, $batch);
            sleep(2);
        }
        return $results;
    }
} 