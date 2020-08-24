<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: authorization, content-type, x-requested-with');
require_once __DIR__ . '/config.php';

$json = file_get_contents('php://input');
$item_id = json_decode($json);

function find_item($item_id){
    $db = new Connect;
    $item = array();
    $data = $db->prepare("SELECT * FROM items WHERE item_id = '$item_id'");
    $data->execute();
    $item= $data->fetch();
    return json_encode($item);
}

header('Content-Type: application/json');
echo find_item($item_id);

?>