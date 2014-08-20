<?php

include 'config/autoload/local.php';
$conn = mysqli_connect('192.168.0.40', 'root', 'krimson1', 'spex');


$soapClient = new SoapClient(SOAP__NEW_URL);
$session = $soapClient->login(SOAP_USER, SOAP_USER_PASS);
$result = $soapClient->call($session, 'catalog_category.tree');
echo $result['category_id'] . ' ' . $result['parent_id'] . ' ' . $result['name'] . ' ' . $result['level'] . " \n" ;
$sql = "INSERT INTO newcategory (category_id, title, parent_id, position, level) values('" . $result['category_id'] . "', '" . $result['name'] . "','" . $result['parent_id']. "','" .  $result['position']. "','" .  $result['level']."')";
if( !mysqli_query($conn,$sql) ){
    die(mysqli_error($conn));
}
foreach($result['children'] as $key => $field){
    echo "first\n";
    echo $result['children'][$key]['category_id'] . ' ' . $result['children'][$key]['parent_id'] . ' ' . $result['children'][$key]['name'] . ' ' . $result['children'][$key]['level'] . "\n" ;
//    $categoryId $result['children'][$key]['category_id'] . ' ' . $result['children'][$key]['parent_id'] . ' ' . $result['children'][$key]['name'] . ' ' . $result['children'][$key]['level'] . "\n" ;
    $sql = "INSERT INTO newcategory (category_id, title, parent_id, position, level)
    values('" . $result['children'][$key]['category_id'] . "', '" .  $result['children'][$key]['name'] . "', '". $result['children'][$key]['parent_id'] . "', '".   $result['children'][$key]['position']. "', '".   $result['children'][$key]['level']."')";
    if( !mysqli_query($conn,$sql) ){
        die(mysqli_error($conn));
    }
    if( count($result['children'][$key]['children']) ){
        $grandChildren = $result['children'][$key]['children'] ;
        foreach($grandChildren as $key1 => $field1){
            echo "second\n";
            echo $grandChildren[$key1]['category_id'] . ' ' . $grandChildren[$key1]['parent_id'] . ' ' . $grandChildren[$key1]['name'] . ' ' . $grandChildren[$key1]['level'] . "\n" ;
    $sql = "INSERT INTO newcategory (category_id, title, parent_id, position, level)
    values('" . $grandChildren[$key1]['category_id'] . "', '" .  $grandChildren[$key1]['name'] . "', '". $grandChildren[$key1]['parent_id'] . "', '".   $grandChildren[$key1]['position']. "', '".   $grandChildren[$key1]['level']."' )";
    if( !mysqli_query($conn,$sql) ){
        die(mysqli_error($conn));
    }
            if( count($grandChildren[$key1]['children']) ) {
                $greatGrandChildren = $grandChildren[$key1]['children'];
                foreach($greatGrandChildren as $key2 => $field2){
                    echo "third\n";
                    echo $greatGrandChildren[$key2]['category_id'] . ' ' . $greatGrandChildren[$key2]['parent_id'] . ' ' . $greatGrandChildren[$key2]['name'] . ' ' . $greatGrandChildren[$key2]['level'] . ' ' .  $greatGrandChildren[$key2]['position']. "\n" ;
                    $thirdTitle = mysqli_real_escape_string($conn,$greatGrandChildren[$key2]['name']);
                    $sql = "INSERT INTO newcategory (category_id, title, parent_id, position, level)
    values('" . $greatGrandChildren[$key2]['category_id'] . "', '" .  $thirdTitle . "', '". $greatGrandChildren[$key2]['parent_id'] . "', '". $greatGrandChildren[$key2]['position']. "', '".   $greatGrandChildren[$key2]['level']."' )";
    if( !mysqli_query($conn,$sql) ){
        die(mysqli_error($conn));
    }
                    if( count ($greatGrandChildren[$key2]['children']) ){
                        $greatGreatGrandChildren = $greatGrandChildren[$key2]['children'];
                        foreach($greatGreatGrandChildren as $key3 => $field3){
                            echo "fourth\n";
                            echo $greatGreatGrandChildren[$key3]['category_id'] . ' ' . $greatGreatGrandChildren[$key3]['parent_id'] . ' ' . $greatGreatGrandChildren[$key3]['name'] . ' ' . $greatGreatGrandChildren[$key3]['level'] . "\n" ;
    $sql = "INSERT INTO newcategory (category_id, title, parent_id, position, level)
    values('" . $greatGreatGrandChildren[$key3]['category_id'] . "', '" .  $greatGreatGrandChildren[$key3]['name'] . "', '". $greatGreatGrandChildren[$key3]['parent_id'] . "', '".   $greatGreatGrandChildren[$key3]['position']. "', '".
    $greatGreatGrandChildren[$key3]['level']."' )";
    if( !mysqli_query($conn,$sql) ){
        die(mysqli_error($conn));
    }
                            if( count ($greatGreatGrandChildren[$key3]['children']) ) {
                                $justOld = $greatGreatGrandChildren[$key3]['children'];
                                foreach($justOld as $key4 => $field4){
                                    echo "fifth\n";
                                    echo $justOld[$key4]['category_id'] . ' ' . $justOld[$key4]['parent_id'] . ' ' . $justOld[$key4]['name'] . ' ' . $justOld[$key4]['level'] . "\n" ;
                                    $title = mysqli_real_escape_string($conn,$justOld[$key4]['name']);
    $sql = "INSERT INTO newcategory (category_id, title, parent_id, position, level)
    values('" . $justOld[$key4]['category_id'] . "', '" .  $title . "', '". $justOld[$key4]['parent_id'] . "', '".   $justOld[$key4]['position']. "', '".   $justOld[$key4]['level']."' )";
    if( !mysqli_query($conn,$sql) ){
        die(mysqli_error($conn));
    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
