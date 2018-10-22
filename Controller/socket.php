<?php
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Socket.php';
$socket = new Socket();


$action = $_GET['action'];

switch ($action){
    case 'post':
        $data = json_decode($_POST['param'], true);
        $socket->postCurrentPrice(strtolower($data['coin']).'_table', $data['dollarPrice'], $data['btcPrice'], $data['timestamp'], strtolower($data['coin']));
        $file = fopen('../coin_live_price/'.$data['coin'].'.json',"w");
        fwrite($file,$_POST['param']);
        fclose($file);
        echo true;
        break;
    case 'exchanges':
      echo $socket->getExchangeRates();
    break;
}
