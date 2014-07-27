<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/25/14
 * Time: 11:08 AM
 */
ini_set('display_errors',1);
require_once 'config/autoload/local.php';
$proxy = new SoapClient(SOAP_URL);
$sessionId = $proxy->login(SOAP_USER, SOAP_USER_PASS);

$attributes = array(
    'resolution_camera'     =>  array(
        'Resolution[camera]'    => array(
            '49mm', '50mm', '51mm', '52mm', '53mm', '54mm', '55mm'
        )
    ),
    'sensor_size_camera'    =>  array(
        'SensorSize[camera]'    =>  array(
            'FullFrame', 'APS-C', 'FourThirds'
        )
    ),
    'zoom_prime_lens'       =>  array(
        'Zoom/Prime[Lens]'  =>  array(
            'Zoom', 'Prime'
        )
    ),
    'focal_length_lens'     =>  array(
        'FocalLength[Lens]' =>  array(
            'IfZoomdropdownrange', 'IfPrimedropdownFixed'
        )
    ),
    'cine_lens'             =>  array(
        'Cine[Lens]'    =>  array(
            'Yes', 'No'
        )
    ),
    'aperture_lens'             =>  array(
        'Aperture[Lens]'    =>  array(
            'Range', 'Fixed'
        )
    ),
    'video_resolution'             =>   array(
        'Video Resolution'  =>  array(
            '720p', '1080p', '4K', 'SD', '8K'
        )
    ),
    'camera_style'             =>  array(
        'Camera Style'  =>  array(
            'PointandShoot/Compact', 'DSLR'
        )
    ),
    'head_type_tripod'             =>  array(
        'HeadType[tripod]'  =>  array(
            'BallHead', 'Pan/TiltHead', 'GripActionHead', 'GearedHead'
        )
    ),
    'leg_lock_type_tripod'             =>  array(
        'LegLockType[tripod]'   =>   array(
            'FlipLock', 'TwistLock', 'Other'
        )
    ),
    'max_height_tripod'             =>  array(
        'MaxHeight[tripod]' =>  array(
            "30''andunder", "31''-45''", "46-55''", "56-60''", "61''-65''", "66-70''", "71-over"
        )
    ),
    'folded_length_tripod'             =>  array(
        'FoldedLength[tripod]'  =>  array(
            "9''to46''brokenoutto3''options"
        )
    ),
    'support_weight_tripod'             =>  array(
        'SupportWeight[tripod]' =>  array(
            '2lbto49lbbrokendownbylboptions'
        )
    ),
    'material_tripod'             =>  array(
        'Material[tripod]'  =>  array(
            'Aluminum', 'CarbonFiber', 'Titanium', 'Magnesium'
        )
    ),
    'type_of_bag_camerabags'             =>  array(
        'TypeofBag[camerabags]' =>  array(
            'ShoulderBags', 'BackpacksandSlings', 'Fittedcases', 'CameraBagsforWomen', 'RollingCases', 'WaistBags'
        )
    ),
    'flash_type_on_camera_flashes'             =>  array(
        'FlashType[OnCameraFlashes]'    =>  array(
            'TTL', 'Auto(nonTTL)', 'Manual'
        )
    ),
    'power_binoculars'             =>  array(
        'Power[binoculars]' =>  array(
            'Options10x-32x', 'Zoom'
        )
    ),
    'objective_diameter_binoculars'             =>  array(
        'ObjectiveDiameter[binoculars]' =>  array(
            '15-82'
        )
    ),
    'prism_type_binoculars'             =>  array(
        'PrismType[binoculars]' =>  array(
            'Porro', 'Roof'
        )
    ),
    'use_binoculars'             =>  array(
        'Use[binoculars]'   =>  array(
            'Opera', 'Sports', 'Marine', 'Outdoor'
        )
    ),
    'size'             =>  array(
        'Size'  =>  array(
            'Compact', 'Standard', 'Large'
        )
    ),
);

echo "<pre>";
// Create new attribute
foreach( $attributes as $att => $attVal){
    $attCode = $att;
    $attValue = array_shift($attributes);
    $attributeValue = current(array_keys($attValue));
    $attributeToCreate = array(
        "attribute_code" => $attCode,
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
                "store_id" => 0,
                "label" => $attributeValue
            )
        )
    );
    $attOptions = array_shift($attValue);
    for($i = 0; $i < count($attOptions); $i++){
        $optionToAdd[$i] = array(
            "label" => array(
                array(
                    "store_id"  =>  0,
                    "value" => $attOptions[$i]
                ),
            ),
            "order" => $i+1,
            "is_default" => $order = ($i == 0) ? 1 : 0
        );
    }
    echo "<br />";
    try{
        $attributeId = $proxy->call(
            $sessionId,
            "product_attribute.create",
            array(
                $attributeToCreate,
            )
        );
        for($i = 0; $i < count($attOptions); $i++){
            $soapRes = $proxy->call(
                $sessionId,
                "product_attribute.addOption",
                array(
                    $attributeId,
                    $optionToAdd[$i]
                )
            );
            echo $soapRes;
        }
    } catch(SoapFault $e){
        echo $e->getMessage();
    }
}