<?php
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Crypto.php';
$crypto = new Crypto();


$action = $_GET['action'];

switch ($action){
    case 'update':
        $data = json_decode($_POST['param'], true);

        foreach($data as $key => $amount){
            $crypto->updatePairing($amount['pair'], $amount['amount']);
        }
        echo true;
        break;
    case 'get':{
        echo $crypto->getPairing();
    }
}