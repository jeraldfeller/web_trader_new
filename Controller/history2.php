<?php
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/History.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Options.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Users.php';
$users = new Users();
$history = new History();
$options = new Options();

$action = $_GET['action'];

switch ($action){
    case 'get':
        $data = json_decode($_POST['param'], true);
        $data = $history->getCoinHistoryApi(strtolower($data['coin']));
        echo $data;
        break;
    case 'get-last':
            $data = json_decode($_POST['param'], true);
            $data = $history->getCoinHistory(strtolower($data['coin']), 'api');
            echo $data;
            break;
    case 'trade-history':
        $data = json_decode($_POST['param'], true);
        $data = $history->getTradeHistory($data['userId'], $data['from'], $data['to']);
        echo json_encode($data);
      break;
    case 'load-history':
      $data = json_decode($_POST['param'], true);
      $filter = $data['filter'];
      $tickInterval = $data['tickInterval'];
      $device = $data['device'];
      $userId = $data['userId'];
      if($data['mm'] == 'mm'){
		$data = json_decode($history->getCoinTradeHistory(strtolower($filter), $device), true); 
      }else{
		$data = json_decode($history->getCoinHistoryMMNew(strtolower($filter), $device, $tickInterval), true);
      }

      if($filter == 'BTC'){
          if(count($data) == "") {
            $currentBtcPrice = "";
          } else {
            $currentBtcPrice = $data[count($data) - 1]['close'];
          }

      }else{
          $currentBtcPrice = json_decode(file_get_contents('http://coincap.io/page/BTC'), true)['price_usd'];
      }

      if(count($data) == "") {
        $currentCoinPrice = "";
      } else {
        $currentCoinPrice = $data[count($data) - 1]['close'];
      }


      $dataFiltered = $data;

      $user = $users->getUserDataById($userId);
      $funds = $user['dollar_amount'];

      if($_SESSION['userData']['user_level'] == 'admin'){
        $maxTradeAmount = 9999999;
      }else{
        $maxTradeAmount = $options->getOption('trade_limit');
      }

      /*
       * REMOVE as only the dollar amount will be shown
       */
       /*
      if($user['dollar_amount'] > 0 ){
        //$funds = number_format($user['dollar_amount'] / $currentBtcPrice * 10000, 2);
        $funds = str_replace(',', '',number_format($currentBtcPrice * 10000, 2));
      }else{
        $funds = 0;
      }
      */

      echo json_encode(array(
        'funds' => $funds,
        'dataFiltered' => $dataFiltered,
        'currentCoinPrice' => $currentCoinPrice,
        'currentBtcPrice' => $currentBtcPrice,
        'maxTradeAmount' => $maxTradeAmount
      ));

      break;
}
