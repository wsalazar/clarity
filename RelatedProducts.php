<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/25/14
 * Time: 9:58 AM
 */
include 'config/autoload/local.php';
include 'init_autoloader.php';

use Zend\Soap\Client;

$linkedProducts =
[
['sku'=>'26465',[['related'=>'SF16UYTQMN'],['related'=>'FC-CA15B-L'],['related'=>'FC-SP6'],['related'=>'FC-FS10-R'],['related'=>'FC-CR1'],['related'=>'FC-CL5'],['related'=>'HQAENEL12'],]],
['sku'=>'26466',[['related'=>'SF16UYTQMN'],['related'=>'FC-CA15B-L'],['related'=>'FC-SP6'],['related'=>'FC-FS10-R'],['related'=>'FC-CR1'],['related'=>'FC-CL5'],['related'=>'HQAENEL12'],]],
['sku'=>'26467',[['related'=>'SF16UYTQMN'],['related'=>'FC-CA15B-L'],['related'=>'FC-SP6'],['related'=>'FC-FS10-R'],['related'=>'FC-CR1'],['related'=>'FC-CL5'],['related'=>'HQAENEL12'],]],
['sku'=>'26468',[['related'=>'SF16UYTQMN'],['related'=>'FC-CA15B-L'],['related'=>'FC-SP6'],['related'=>'FC-FS10-R'],['related'=>'FC-CR1'],['related'=>'FC-CL5'],['related'=>'HQAENEL12'],]],
['sku'=>'26462',[['related'=>'SF16UYTQMN'],['related'=>'25880'],['related'=>'FC-SP6'],['related'=>'FC-CR1'],['related'=>'52UVP'],['related'=>'FC-HDMIMICRO'],['related'=>'FC-CA10B'],['related'=>'FC-CL5'],]],
['sku'=>'26463',[['related'=>'SF16UYTQMN'],['related'=>'25880'],['related'=>'FC-SP6'],['related'=>'FC-CR1'],['related'=>'52UVP'],['related'=>'FC-HDMIMICRO'],['related'=>'FC-CA10B'],['related'=>'FC-CL5'],]],
['sku'=>'13313',[['related'=>'SF32UYTQM'],['related'=>'52TPK1'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CL5'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'13216',[['related'=>'SF64UYTQMN'],['related'=>'67TPK1'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CL5'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'13303',[['related'=>'SF64UYTQMN'],['related'=>'67TPK1'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CL5'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'13311-nikon',[['related'=>'SF64UYTQMN'],['related'=>'67TPK1'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CL5'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'1503',[['related'=>'SF32UYTQM'],['related'=>'52TPK1'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CL5'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'1507',[['related'=>'SF32UYTQM'],['related'=>'52TPK1'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CL5'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'1511',[['related'=>'SF32UYTQM'],['related'=>'52TPK1'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CL5'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'1519',[['related'=>'SF32UYTQM'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'FC-CL5'],['related'=>'FC-DGC'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'1520',[['related'=>'SF32UYTQM'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'FC-CL5'],['related'=>'FC-DGC'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'1521',[['related'=>'SF32UYTQM'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'FC-CL5'],['related'=>'FC-DGC'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'1522',[['related'=>'SF32UYTQM'],['related'=>'52TPK1'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CL5'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'1523',[['related'=>'SF32UYTQM'],['related'=>'52TPK1'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CL5'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'1532',[['related'=>'SF32UYTQM'],['related'=>'52TPK1'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CL5'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'1533',[['related'=>'SF32UYTQM'],['related'=>'52TPK1'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CL5'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'1534',[['related'=>'SF32UYTQM'],['related'=>'52TPK1'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CL5'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'25492',[['related'=>'SF32UYTQM'],['related'=>'52TPK1'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CL5'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'25496',[['related'=>'SF32UYTQM'],['related'=>'52TPK1'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CL5'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4808'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],]],
['sku'=>'25498',[['related'=>'SF64UYTQMN'],['related'=>'27011'],['related'=>'FC-MHDMI6'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4809'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],['related'=>'FC-CL5'],]],
['sku'=>'1525',[['related'=>'SF64UYTQMN'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4809'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],['related'=>'FC-CL5'],]],
['sku'=>'1526',[['related'=>'SF64UYTQMN'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4809'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],['related'=>'FC-CL5'],]],
['sku'=>'1527',[['related'=>'SF64UYTQMN'],['related'=>'FC-ENEL14'],['related'=>'FC-MHDMI6'],['related'=>'FC-CR1'],['related'=>'551502'],['related'=>'4809'],['related'=>'GX600B200'],['related'=>'GP3-A1EN'],['related'=>'FC-CL5'],]],
['sku'=>'2156',[['related'=>'52TPK1'],['related'=>'MX537501'],['related'=>'FC-CL5'],['related'=>'FC-LCH1'],['related'=>'628586557901'],['related'=>'VIV-DH-52'],]],
['sku'=>'2164',[['related'=>'77TPK1'],['related'=>'MX537801'],['related'=>'FC-CL5'],['related'=>'FC-LCH1'],['related'=>'628586557901'],['related'=>'VIV-DH-77'],]],
['sku'=>'2166',[['related'=>'52TPK1'],['related'=>'MX534301'],['related'=>'FC-CL5'],['related'=>'FC-LCH1'],['related'=>'628586557901'],['related'=>'VIV-DH-52'],]],
['sku'=>'2178',[['related'=>'67TPK1'],['related'=>'MX534301'],['related'=>'FC-CL5'],['related'=>'FC-LCH1'],['related'=>'628586557901'],['related'=>'VIV-DH-67'],]],
['sku'=>'2185-NIKON',[['related'=>'77TPK1'],['related'=>'FC-CL5'],['related'=>'FC-LCH1'],['related'=>'628586557901'],['related'=>'VIV-DH-77'],]],
['sku'=>'2190',[['related'=>'52TPK1'],['related'=>'MX534301'],['related'=>'FC-CL5'],['related'=>'FC-LCH1'],['related'=>'628586557901'],['related'=>'VIV-DH-52'],]],
['sku'=>'2191-NIKON',[['related'=>'77TPK1'],['related'=>'MX534301'],['related'=>'FC-CL5'],['related'=>'FC-LCH1'],['related'=>'628586557901'],['related'=>'VIV-DH-77'],]],
['sku'=>'2192-NIKON',[['related'=>'72TPK1'],['related'=>'MX537501'],['related'=>'FC-CL5'],['related'=>'FC-LCH1'],['related'=>'628586557901'],['related'=>'VIV-DH-72'],]],
['sku'=>'2196-NIKON',[['related'=>'77TPK1'],['related'=>'MX534301'],['related'=>'FC-CL5'],['related'=>'FC-LCH1'],['related'=>'628586557901'],['related'=>'VIV-DH-77'],]],
['sku'=>'2197',[['related'=>'58TPK1'],['related'=>'MX534301'],['related'=>'FC-CL5'],['related'=>'FC-LCH1'],['related'=>'628586557901'],['related'=>'VIV-DH-58'],]],
['sku'=>'2207-NIKON',[['related'=>'77TPK1'],['related'=>'MX534301'],['related'=>'FC-CL5'],['related'=>'FC-LCH1'],['related'=>'628586557901'],['related'=>'VIV-DH-77'],]],
['sku'=>'2210-NIKON',[['related'=>'72TPK1'],['related'=>'MX534301'],['related'=>'FC-CL5'],['related'=>'FC-LCH1'],['related'=>'628586557901'],['related'=>'VIV-DH-72'],]],
['sku'=>'2213',[['related'=>'67TPK1'],['related'=>'MX534301'],['related'=>'FC-CL5'],['related'=>'FC-LCH1'],['related'=>'628586557901'],['related'=>'VIV-DH-67'],]],
['sku'=>'4808',[['related'=>'FC-4AACH'],['related'=>'FC-SP6'],['related'=>'FC-CL5'],['related'=>'FC-FC1'],['related'=>'VIV-FB-150'],]],
['sku'=>'4809',[['related'=>'FC-4AACH'],['related'=>'FC-SP6'],['related'=>'FC-CL5'],['related'=>'FC-FC1'],['related'=>'VIV-FB-150'],]],
['sku'=>'4810',[['related'=>'FC-4AACH'],['related'=>'FC-SP6'],['related'=>'FC-CL5'],['related'=>'FC-FC1'],['related'=>'VIV-FB-150'],]]
];
echo "<pre>";
$conn = mysqli_connect(HOST, USER, PASS, DB);
$soapHandle = new Client(SOAP_URL);
$session = $soapHandle->call('login',array(SOAP_USER, SOAP_USER_PASS));
$packet = array();
$count = 0;
foreach($linkedProducts as $key => $products){
    $entityId = $linkedProducts[$key]['sku'];
    foreach($products as $index => $related){
        if(is_array($related)){
            $inner = 0;
            foreach($related as $in => $rel){
                $linked = $related[$in]['related'];
                $sql = "select entity_id from product where productid = '".$entityId."'";
                $id = mysqli_query($conn,$sql);
                $entity = mysqli_fetch_row($id);

                $sql = "select entity_id from product where productid = '". $linked."'";
                $lid = mysqli_query($conn,$sql);
                $linkedEntity = mysqli_fetch_row($lid);
                $packet[$count][$inner] = array($session, PRODUCT_ASSIGN_RELATED, array('type'=>'related', 'product'=>$entity[0], 'linkedProduct'=>$linkedEntity[0] ));
                $inner++;
            }
        }
    }
    $count++;
}
$count = 0;
$batch =[];
foreach($packet as $index => $linked){
    foreach($linked as $key => $link){
        $inner = 0;
        var_dump($linked[$key]);
        $results = $soapHandle->call('call',$linked[$key] );
        var_dump($results);
        sleep(2);
    }
}
