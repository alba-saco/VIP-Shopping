<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: authorization, content-type, x-requested-with');
require_once __DIR__ . '/config.php';

// Takes raw data from the request
$search = file_get_contents('php://input');
$keywords = explode(" ", $search);
$length =count($keywords);

function searchDB($keywords, $length){
    $db = new Connect;
    $results = array();
    for ($i=0; $i<$length; $i++) {
        $data = $db->prepare("SELECT * FROM items WHERE (color = '$keywords[$i]') OR (material = '$keywords[$i]') OR (category = '$keywords[$i]')");
        $data->execute();
        while($OutputData = $data->fetch(PDO::FETCH_ASSOC)){
            for ($i=0; $i<$length; $i++){
                if($OutputData['category'] == $keywords[$i]){
                    array_push($results, array('item_id' => $OutputData['item_id'], 'price' => $OutputData['price'],'color' => $OutputData['color'],'size' => $OutputData['size'],'material' => $OutputData['material'],'category' => $OutputData['category'],'node' => $OutputData['node']));
                }
            }
        }
    }
    return json_encode($results);
};

echo searchDB($keywords, $length);

?>