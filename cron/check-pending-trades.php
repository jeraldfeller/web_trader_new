<?php
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/History.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Users.php';
$users = new Users();

$timeNow = time();

$trades = json_decode($users->cronGetTrades(), true);

foreach($trades as $row){
    $dateTime = date('Y-m-d H:i:s');
    $userId = $row['user'];
    $tradeId = $row['id'];
    $entryValue = $row['open'];
    $type = $row['type'];
    $leverage = $row['leverage'];
    $winningAmount = $leverage * .70;
    $coin = $row['ticker'];

    $coinData = json_decode(file_get_contents('https://coincap.io/page/'.$coin), true);
    $coinPrice = $coinData['price_usd'];

    if($type == 'buy'){
        if($coinPrice > $entryValue){
            $tradeStatus = 'win';
        }else{
            $tradeStatus = 'lost';
        }
    }else{
        if($coinPrice < $entryValue){
            $tradeStatus = 'win';
        }else{
            $tradeStatus = 'lost';
        }
    }

    $userData = $users->getUserById($userId);

    if($userData){
      $data = array(
          'status' => $tradeStatus,
          'userId' => $userId,
          'tradeId' => $tradeId,
          'close' => $coinPrice,
          'winAmount' => $winningAmount,
          'leverage' => $leverage,
          'dollar_amount' => $userData['dollar_amount']
      );

      $users->userUpdateTradesFunction($data);
    }else{
      $users->deleteTrade($tradeId);
    }




}
