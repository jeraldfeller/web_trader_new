<?php

require $_SERVER['DOCUMENT_ROOT'] . '/Model/Init.php';

require $_SERVER['DOCUMENT_ROOT'] . '/Model/HistoryNew.php';

$historyNew = new HistoryNew();





$action = $_GET['action'];



switch ($action){

    case 'get':

        $data = json_decode($_POST['param'], true);

        $data = $historyNew->getCoinHistoryApi(strtolower($data['coin']));

        echo $data;

        break;

    case 'get-last':

            $data = json_decode($_POST['param'], true);

            $data = $historyNew->getCoinHistory(strtolower($data['coin']), 'api');

            echo $data;

            break;

}

