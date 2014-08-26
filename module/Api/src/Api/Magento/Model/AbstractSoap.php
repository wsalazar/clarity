<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/26/14
 * Time: 4:21 PM
 */

namespace Api\Magento\Model;

use Zend\Soap\Client;

class AbstractSoap {

    protected $soapHandle;

    protected $session;

    public function __construct($soapUrl)
    {
        $this->soapHandle = new Client($soapUrl);
        $this->session = $this->soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
    }
} 