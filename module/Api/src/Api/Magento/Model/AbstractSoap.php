<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/26/14
 * Time: 4:21 PM
 */

namespace Api\Magento\Model;

use Zend\Soap\Client;

class AbstractSoap implements SoapInterface
{

    protected $soapHandle;

    protected $session;

    public function __construct($soapUrl)
    {
        $this->soapHandle = new Client($soapUrl);
        $this->session = $this->soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
    }

    public function soapCall($packet)
    {
        $a = 0;
        $batch = [];
        $results = false;
        while( $a < count($packet) ){
            $x = 0;
            while($x < 10 && $a < count($packet)){
                $batch[$x] = $packet[$a];
                $x++;
                $a++;
            }
            sleep(15);
            $results = $this->soapHandle->call('multiCall', $batch);
        }
        return $results;
    }
} 