<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/25/14
 * Time: 11:08 AM
 */

//use SoapClient;

require_once 'config/autoload/local.php';

$proxy = new SoapClient(SOAP_URL);
$sessionId = $proxy->login(SOAP_USER, SOAP_USER_PASS);

$attribute = array(
    'network'               =>  'Network',
//    'size_cloths'           =>  'Size [Cloths]',
//    'size_sunglasses'       =>  'Size(mm) Sunglasses',
//    'resolution_camera'     =>  'Resolution[camera]',
//    'sensor_size_camera'    =>  'SensorSize[camera]',
//    'zoom_prime_lens'       =>  'Zoom/Prime[Lens]',
//    'focal_length_lens'     =>  'FocalLength[Lens]',
//    'cine_lens'             =>  'Cine[Lens]',
//    'aperture_lens'             =>  'Aperture[Lens]',
//    'video_resolution'             =>  'Video Resolution',
//    'camera_style'             =>  'Camera Style',
//    'head_type_tripod'             =>  'HeadType[tripod]',
//    'leg_lock_type_tripod'             =>  'LegLockType[tripod]',
//    'max_height_tripod'             =>  'MaxHeight[tripod]',
//    'folded_length_tripod'             =>  'FoldedLength[tripod]',
//    'support_weight_tripod'             =>  'SupportWeight[tripod]',
//    'material_tripod'             =>  'Material[tripod]',
//    'type_of_bag_camerabags'             =>  'TypeofBag[camerabags]',
//    'flash_type_on_camera_flashes'             =>  'FlashType[OnCameraFlashes]',
//    'power_binoculars'             =>  'Power[binoculars]',
//    'objective_diameter_binoculars'             =>  'ObjectiveDiameter[binoculars]',
//    'prism_type_binoculars'             =>  'PrismType[binoculars]',
//    'use_binoculars'             =>  'Use[binoculars]',
//    'size'             =>  'Size',
);


$attOptions = array('network' =>    array(
    'GSM','CDMA'
),
//    'size_cloths'  =>  array(
//        'Small','Medium','Large','Extra-Large'
//    ),
//    'size_sunglasses'   =>  array(
//        '49mm', '50mm', '51mm', '52mm', '53mm', '54mm', '55mm'
//    ),'resolution_camera'   =>  array(
//        'Upto7Megapixels', '8-10Megapixels', '11-15Megapixels', '16-18Megapixels', '19-22Megapixels', '23Megapixelsandover'
//    ),'sensor_size_camera'   =>  array(
//        'FullFrame', 'APS-C', 'FourThirds'
//    ),'zoom_prime_lens'   =>  array(
//        'Zoom', 'Prime'
//    ),'focal_length_lens'   =>  array(
//        'IfZoomdropdownrange', 'IfPrimedropdownFixed'
//    ),'cine_lens'   =>  array(
//        'Yes', 'No'
//    ),'aperture_lens'   =>  array(
//        'Range', 'Fixed'
//    ),'video_resolution'   =>  array(
//        '720p', '1080p', '4K', 'SD', '8K'
//    ),'camera_style'   =>  array(
//        'PointandShoot/Compact', 'DSLR'
//    ),'head_type_tripod'   =>  array(
//        'BallHead', 'Pan/TiltHead', 'GripActionHead', 'GearedHead'
//    ),'leg_lock_type_tripod'   =>  array(
//        'FlipLock', 'TwistLock', 'Other'
//    ),'max_height_tripod'   =>  array(
//        "30''andunder", "31''-45''", "46-55''", "56-60''", "61''-65''", "66-70''", "71-over"
//    ),'folded_length_tripod'   =>  array(
//        "9''to46''brokenoutto3''options"
//    ),'support_weight_tripod'   =>  array(
//        '2lbto49lbbrokendownbylboptions'
//    ),'material_tripod'   =>  array(
//        'Aluminum', 'CarbonFiber', 'Titanium', 'Magnesium'
//    ),'type_of_bag_camerabags'   =>  array(
//        'ShoulderBags', 'BackpacksandSlings', 'Fittedcases', 'CameraBagsforWomen', 'RollingCases', 'WaistBags'
//    ),'flash_type_on_camera_flashes'   =>  array(
//        'TTL', 'Auto(nonTTL)', 'Manual'
//    ),'power_binoculars'   =>  array(
//        'Options10x-32x', 'Zoom'
//    ),'objective_diameter_binoculars'   =>  array(
//        '15-82'
//    ),'prism_type_binoculars'   =>  array(
//        'Porro', 'Roof'
//    ),'use_binoculars'   =>  array(
//        'Opera', 'Sports', 'Marine', 'Outdoor'
//    ),'size'   =>  array(
//        'Compact', 'Standard', 'Large'
//    )
);

echo "<pre>";
// Create new attribute
$countAtts = 0;
$countOps = 0;
foreach ($attribute as $attKey => $attValue){

    $attributeToCreate[] = array(
        "attribute_code" => $attKey,
        "scope" => "global",
        "frontend_input" => "select",
        "is_unique" => 0,
        "is_required" => 0,
        "is_configurable" => 1,
        "is_searchable" => 0,
        "is_visible_in_advanced_search" => 1,
        "used_in_product_listing" => 0,
        "additional_fields" => array(
            "is_filterable" => 0,
            "is_filterable_in_search" => 0,
            "position" => 0,
            "used_for_sort_by" => 0
        ),
        "frontend_label" => array(
            array(
//                "store_id" => 0,
                "label" => $attValue
            )
        )
    );

    $countAtts++;
    echo 'attkey ' . $attKey . ' attValue ' . $attValue . "\n";

    foreach($attOptions as $options => $op){
        foreach($op as $key => $value) {
            if($options == $attKey){


                echo 'does this work?';
                $optionToAdd[] = array(
                    "label" => array(
                        array(
                            "value" => $value),
                    ),
                    "order" => 0,
                    "is_default" => 0
                );
                echo $value . "\n";
            }
        }
        $countOps++;
        echo 'this is the count for count ops' . $countOps. "\n";

        $storeOpCount[] = $countOps;

        echo 'hahaha'.$storeOpCount[0]. "\n";
        echo 'this is the count '. count($optionToAdd[0]). "\n";
//        var_dump($optionToAdd[0]);
//        var_dump($optionToAdd[1]);

        $countOps=0;

    }

}
//echo 'this is the count '. count($optionToAdd[$storeOpCount[0]]);
//var_dump($attributeToCreate);
//var_dump($optionToAdd);
$cntSoap = 0;
$cntSoapOp = 0;
//try{
//echo 'count atts' . $countAtts;
    for($cntAtts = 0; $cntAtts < $countAtts; $cntAtts++){
        echo 'this is attributeToCreate';
        var_dump( $attributeToCreate[$cntAtts]);
        $attributeId[] = $proxy->call(
            $sessionId,
            "product_attribute.create",
            array(
                $attributeToCreate[$cntAtts],
            )
        );

        echo count($optionToAdd[$storeOpCount[$cntAtts]]);
//        echo "This is the session id" . $attributeId[$cntSoap] . "\n";
//        for($cntSoapOp = 0; $cntSoapOp < count($optionToAdd[$storeOpCount[$cntAtts]]); $cntSoapOp++){
        foreach($attOptions as $options => $op){
            echo 'hello';
            echo 'what is this count ' . count($attOptions[$options]);
            for($cntSoapOp = 0; $cntSoapOp < count($attOptions[$options]); $cntSoapOp++){
                $soapRes[] = $proxy->call(
                    $sessionId,
                    "product_attribute.addOption",
                    array(
                        $attributeId[$cntSoap],
                        $optionToAdd[$cntSoapOp]
                    )
                );
                echo 'this is optionToAdd';
                var_dump($optionToAdd[$cntSoapOp]);
            }
        }


        $cntSoap++;
    }

    echo $soapRes;
//} catch(SoapFault $e){
//    echo $e->getMessage();
//}