<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/26/14
 * Time: 6:13 PM
 */

namespace Api\Magento\Model;


interface SoapInterface {

    /**
     * Will make soap call
     * @param array $packet
     * @return boolean | mixed
     * @internal param array $packet
     */
    public function soapCall($packet);

} 