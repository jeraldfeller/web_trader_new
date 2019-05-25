<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Users extends History

{
    public $debug = FALSE;
    protected $db_pdo;

    public function checkATUSocket(){
        $pdo = $this->getPdo();
        $sql = 'SELECT * FROM `atu` WHERE `status` = 1 LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdo = null;
        return $result;
    }

    public function checkATU($userId, $amount, $action, $coin, $transactionId){
        $pdo = $this->getPdo();
        $sql = 'SELECT * FROM `atu`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result){
          $sql = 'SELECT * FROM `atu` WHERE `user_id` = '.$userId;
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          if($result){
            $sql = 'INSERT INTO `atu` SET `status` = 1, `user_id` = '.$userId.', `amount` = '.$amount.', `action` = "'.$action.'", `coin` ="'.$coin.'", `transaction_id`='.$transactionId;
          //  $sql = 'UPDATE `atu` SET `status` = 1, `user_id` = '.$userId.', `amount` = '.$amount.', `action` = "'.$action.'", `coin` ="'.$coin.'" WHERE `id` = 1';
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $return = true;
          }else{
            $return = false;
          }

        }else{

          $sql = 'INSERT INTO `atu` SET `status` = 1, `user_id` = '.$userId.', `amount` = '.$amount.', `action` = "'.$action.'", `coin` ="'.$coin.'", `transaction_id`='.$transactionId;
        //  $sql = 'UPDATE `atu` SET `status` = 1, `user_id` = '.$userId.', `amount` = '.$amount.', `action` = "'.$action.'", `coin` ="'.$coin.'" WHERE `id` = 1';
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $return = true;
        }
        $pdo = null;

        return $return;
    }

    public function updateATU($userId, $transactionId){
        $pdo = $this->getPdo();
        $sql = 'DELETE FROM `atu` WHERE `user_id` = '.$userId.' AND `transaction_id` = '.$transactionId;

        //$sql = 'UPDATE `atu` SET `user_id` = null, `status` = 0 WHERE `user_id` = '.$userId;
        $stmt = $pdo->prepare($sql);

        $stmt->execute();
        $pdo = null;
        return true;
    }

    public function resetATU(){
      $pdo = $this->getPdo();
      $sql = 'UPDATE `atu` SET `user_id` = null, `status` = 0';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $pdo = null;
      return true;
    }

    public function getAllUsers(){
      $pdo = $this->getPdo();
      $sql = 'SELECT * FROM `users`';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $result = array();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $result[] = $row;
      }
      $pdo = null;
      return json_encode($result);
    }
    public function userLoginFunction($data)
    {

        // check if credential match

        $pdo = $this->getPdo();
        $sql = 'SELECT * FROM `users` WHERE `email` = "' . $data['email'] . '" AND `password` = "' . $data['password'] . '"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if($result != false){
                $return = array('success' => true,
                    'id' => $result['id'],
                    'email' => $result['email'],
                    'password' => $result['password'],
                    'dollar_amount' => $result['dollar_amount'],
                    'first_name' => $result['first_name'],
                    'last_name' => $result['last_name'],
                    'address' => $result['address'],
                    'user_level' => $result['user_level']
                );

                $_SESSION['isLoggedIn'] = true;
                $_SESSION['userData'] = $return;
                $_SESSION['id'] = $result['id'];

                $success = true;
                $response = array($return);

                // register last login
                $this->insertLastLogin($result['id'], $data['dateNow']);
                $logHistory = $this->getLastLogin($result['id']);
                $_SESSION['userSess'] = array('info' => $return, 'log_history' => $logHistory);


                if(isset($logHistory[1])){
                  $currentIp = $logHistory[0]['ip_address'];
                  $lastIp = $logHistory[1]['ip_address'];
                  $city = $logHistory[0]['city'];
                  $region = $logHistory[0]['region'];
                  $country = $logHistory[0]['location'];
                  $dateTime = $logHistory[0]['date_time'];


                  if($currentIp != $lastIp){
                    $this->sendAlertMessage($result['email'], $city, $region, $country, $dateTime, $currentIp);
                  }
                }
        }else{
            $success = false;
            $response = array(
                'message' => 'Incorrect email or password.'
            );
        }


        return
            json_encode(
                array(
                    'success' => $success,
                    'response' => $response
                )
            );
    }

    public function getLastLogin($userId, $limit = 11){
      $pdo = $this->getPdo();
      $sql = 'SELECT * FROM `last_login` WHERE `user_id` = '.$userId.' ORDER BY `date_time` DESC LIMIT '.$limit.'';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $result = array();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $result[] = $row;
      }

      return $result;
    }

    public function updateUser($userId, $firstname, $address, $lastname, $file1, $file2){
      $pdo = $this->getPdo();
      $sql = 'UPDATE `users` SET `verified` = "pending", `first_name` = "'.$firstname.'", `last_name` = "'.$lastname.'", `address` = "'.$address.'", `file_1` ="'.$file1.'", `file_2` ="'.$file2.'" WHERE `id` = '.$userId.'';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $pdo = null;
      return true;
    }

    public function createUserFunction($data){
      $pdo = $this->getPdo();
      $sql = 'SELECT * FROM `users` WHERE `email` = "'.$data['email'].'"';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if($result){
        $return = 0;
      }else{
        $sql = 'INSERT INTO `users` SET `first_name` = "'.$data['firstname'].'", `last_name` = "'.$data['lastname'].'", `email` ="'.$data['email'].'", `password` = "'.$data['password'].'"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $return = 1;
      }
      $lastId = $pdo->lastInsertId();
      $accountNo = $lastId.'-'.$this->generateRandomString(5, true);
      $sql = 'UPDATE `users` SET `account_number` = "'.$accountNo.'" WHERE `id` = '. $lastId;
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $pdo = null;

      if($return){
        if($return){
          $bodyMessage = '
          <p class="text-center">Congratulations on opening your NanoPips Account</p><br>
          <p class="text-center">Your NanoPips Account Number is:</p>
          <p class="text-center"><b>'.$accountNo.'</b></p>';
          $button = '<a href="http://webtrader.nanopips.com/" class="btn btn-success btn-lg login-btn"  style="background: #13D384;">Login Here</a>';
          $footer = '';
          $body = $this->emailTemplate('Welcome New User', $bodyMessage, $button, $footer);
          $resp = $this->sendEmailFunc(SERVICE_EMAIL, SERVICE_EMAIL_NAME, $data['email'], 'Welcome New User', $body, false);
        }
      }

      return $return;
    }
    public function insertLastLogin($id, $dateNow){
      //$tags = json_decode(file_get_contents('http://api.ipstack.com/'.$_SERVER['REMOTE_ADDR'].'?access_key=fd9d2e346127903e00b0429448743701&format=1'));
      $tags = json_decode(file_get_contents('http://ip-api.com/json/'.$_SERVER['REMOTE_ADDR']), true);
      $country = $tags['country'];
      $city = $tags['city'];
      $region = $tags['regionName'];
      $isp = $tags['isp'];
      $ip = $tags['query'];

      $dateNow = date('Y-m-d H:i:s');
      $pdo = $this->getPdo();
      $sql = 'INSERT INTO `last_login` SET `user_id` = '.$id.', `ip_address` = "' . $ip . '", `location` = "'.$country.'", `city` ="'.$city.'", `region` = "'.$region.'", `isp` ="'.$isp.'", `date_time` = "' . $dateNow . '"';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();

      return true;
    }

    public function getPendingTradesFunction($data){
        $timeNow = time();
        $pdo = $this->getPdo();
        // $sql = 'SELECT * FROM `trades` WHERE `user` = '.$data['userId'].' AND `closing_time` >= '.$timeNow.'';
        $sql = 'SELECT * FROM `trades` WHERE `user` = '.$data['userId'].' AND `status` = "live"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        // if($cnt == "") {
	      //     $sql = 'SELECT *
        //           FROM `trades` WHERE `user` = ' .$data['userId'].' ORDER BY `id` DESC LIMIT 50';
    	  //     $stmt = $pdo->prepare($sql);
    	  //     ini_set('memory_limit', '-1');
    	  //     $stmt->execute();
	      // }


        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['remaining'] = $row['closing_time'] - $timeNow;
            $result[] = $row;
        }

        return json_encode($result);
    }

    public function registerTradesFunction($data){
        $expires = $data['expires'];
        $entryTime = time();
        $closingTime = $entryTime + $expires;
        $type = $data['type'];
        $pdo = $this->getPdo();
        $sql = 'INSERT INTO `trades` (`entry_time`, `closing_time`, `expires`, `type`, `user`, `ticker`, `open`, `leverage`, `payout`, `status`, `pair`, `trade_percent_cut`, `trade_percent_cut_amount`) VALUES ('.$entryTime.', '.$closingTime.', '.$expires.', "'.$type.'", '.$data['userId'].', "' . $data['ticker']. '", '.$data['amount'].', '.$data['leverage'] . ', '.$data['payout'].', "' . $data['status'] . '", "' . $data['pair'] . '", '.$data['percentCut'].', '.$data['percentCutAmount'].')';
        $stmt = $pdo->prepare($sql);

        $stmt->execute();
        $tradeId = $pdo->lastInsertId();


        if($data['percentCut'] == 15){
          $sql = 'UPDATE `users_balance` SET `status` = 1 WHERE `user_id` = '.$data['userId'].' AND `id` = '.$data['balanceId'];
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
        }

        return json_encode(array('data' => $data, 'tradeId' => $tradeId, 'balanceStatus' => $this->getBalanceStatus($data['userId'])));
    }

    public function userUpdateTradesFunction($data){
        $dateTime = date('Y-m-d H:i:s');
        $pdo = $this->getPdo();
        // $historyData = json_decode($this->getCoinHistory(strtolower('BTC'), 'desktop'), true);
        // $currentBtcPrice = $historyData[count($data) - 1]['close'];
        $winningAmount = $data['winAmount'];
        $losingAmount = $data['leverage'];
        // update funds
        if($data['status'] == 'win'){
            $sql = 'UPDATE `users` SET `dollar_amount` = (`dollar_amount` + '.$winningAmount.') WHERE `id` = '.$data['userId'].'';
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }else if($data['status'] == 'lost'){
            $sql = 'UPDATE `users` SET `dollar_amount` = (`dollar_amount` - '.$losingAmount.') WHERE `id` = '.$data['userId'].'';
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }

        // update atu;





        // register logs
        $user = $this->getUserDataById($data['userId']);

        $sql = 'UPDATE `trades` SET `exec_time` = "' . $dateTime. '", `close` = '.$data['close'].', `balance` = ' . $user['dollar_amount']. ', `status` = "' . $data['status'] . '" WHERE `id` = '.$data['tradeId'].'';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();




        $sql = 'DELETE FROM `atu` WHERE `transaction_id` = '.$data['tradeId'];
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        if($user['dollar_amount'] <= 10){
          // get the overall total Balance
          $sql = 'SELECT SUM(`amount`) as totalBalanceAmount FROM `users_balance` WHERE `user_id` ='.$data['userId'];
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $totalBalanceAmount = $stmt->fetch(PDO::FETCH_ASSOC)['totalBalanceAmount'];

          $sql = 'UPDATE `users` SET `loss_amount` = '.($totalBalanceAmount-$user['dollar_amount']).' WHERE `id` = '.$data['userId'];
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
        }
        if(isset($_SESSION['userData'])){
            $_SESSION['userData']['dollar_amount'] = $user['dollar_amount'];
        }

        $pdo = null;
        return
            json_encode(
                array(
                    'success' => true,
                    'response' => array(
                        'funds' => $user['dollar_amount'],
                        'dollar_amount' => $user['dollar_amount']
                    )
                )
            );
    }

    public function userPurchaseFunds($data){

        $pdo = $this->getPdo();
        $sql = 'UPDATE `users` SET `dollar_amount` = (`dollar_amount` + '.$data['amount'].') WHERE `id` = '.$data['userId'].'';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }





    public function  cronGetTrades(){
        $timeNow = time();
        $pdo = $this->getPdo();
        $sql = 'SELECT * FROM `trades` WHERE `status` = "live" AND `closing_time` < '.$timeNow.'';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        return json_encode($result);
    }

    function getBalanceStatus($id){
      $pdo = $this->getPdo();
      $sql = 'SELECT `id`, `trade_percentage_amount` FROM `users_balance` WHERE `status` = 0 AND `user_id` = '.$id.' ORDER BY `id` LIMIT 1';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $return = $stmt->fetch(PDO::FETCH_ASSOC);
      $pdo = null;
      if($return == null){
        $return = array(
          'trade_percentage_amount' => 0,
          'id' => 0
        );
      }
      return $return;
    }
    function getUserDataById($id, $isAdmin = false, $offset = 0, $limit = 10){
        $pdo = $this->getPdo();
        $sql = 'SELECT * FROM `users` WHERE `id` = "' . $id . '"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // get balance Status
        $result['balanceStatus'] = $this->getBalanceStatus($id);
        if($isAdmin == true){

          // get total trade amount lost
          $sql = 'SELECT SUM(`leverage`) as totalLost FROM `trades` WHERE `status` = "lost" AND `user` ='.$id;
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $totalLost = $stmt->fetch(PDO::FETCH_ASSOC)['totalLost'];

          // get total trade percentage amount for all trades
          $sql = 'SELECT SUM(`trade_percent_cut_amount`) as totalPercentAmount FROM `trades` WHERE `user` ='.$id;
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $totalPercentAmount = $stmt->fetch(PDO::FETCH_ASSOC)['totalPercentAmount'];

          // get the overall total Balance
          $sql = 'SELECT SUM(`amount`) as totalBalanceAmount FROM `users_balance` WHERE `user_id` ='.$id;
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $totalBalanceAmount = $stmt->fetch(PDO::FETCH_ASSOC)['totalBalanceAmount'];

          // get total Lost
          $sql = 'SELECT `loss_amount` FROM `users` WHERE `id` ='.$id;
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $totalLost = $stmt->fetch(PDO::FETCH_ASSOC)['loss_amount'];


          // get user balance history
          $sql = 'SELECT * FROM `users_balance` WHERE `user_id` ='.$id.' ORDER BY `exec_time` DESC LIMIT '.$offset.','.$limit;
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $userBalance = array();
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $row['exec_time'] = date('m/d/Y H:i', strtotime($row['exec_time']));
              $userBalance[] = $row;
          }

          // get user withdraw history
          $sql = 'SELECT * FROM `users_withdraw` WHERE `user_id` ='.$id.' ORDER BY `exec_time` DESC LIMIT '.$offset.','.$limit;
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $userWithdraw = array();
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $row['exec_time'] = date('m/d/Y H:i', strtotime($row['exec_time']));
              $userWithdraw[] = $row;
          }



          $totalPercentAmount = ($totalPercentAmount != null ? $totalPercentAmount : 0);
          $totalBalanceAmount = ($totalBalanceAmount != null ? $totalBalanceAmount : 0);

          $result['trades'] = array(
            'totalLost' =>  $totalLost,
            'totalPercentAmount' => $totalPercentAmount,
            'balanceHistory' => $userBalance,
            'withdrawHistory' => $userWithdraw
          );

          $result['totalBalance'] = $totalBalanceAmount;
        }
        $pdo = null;
        unset($result['password']);
        return $result;
    }

    public function getUserById($id){
      $pdo = $this->getPdo();
      $sql = 'SELECT * FROM `users` WHERE `id` = "' . $id . '"';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $pdo = null;

      return $result;
    }

    public function deleteTrade($id){
      $pdo = $this->getPdo();
      $sql = 'DELETE FROM `trades` WHERE `id` = "' . $id . '"';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $pdo = null;

      return true;
    }

    public function updateUserBalance($id, $action, $amount){
      $dateTime = date('Y-m-d H:i:s');
      $pdo = $this->getPdo();
      if($action == 'Deposit'){
        $percentageAmount = $amount * .10;
        $sql = 'UPDATE `users` SET `dollar_amount` = (`dollar_amount` + '.$amount.') WHERE `id` = '.$id;
        $sqlHistory = 'INSERT INTO `users_balance` SET `user_id` = '.$id.', `amount` = '.$amount.', `trade_percentage_amount` = '.$percentageAmount.', `status` = 0, `exec_time` = "'.$dateTime.'"';
      }else{
        $sql = 'UPDATE `users` SET `dollar_amount` = (`dollar_amount` - '.$amount.') WHERE `id` = '.$id;
        $sqlHistory = 'INSERT INTO `users_withdraw` SET `user_id` = '.$id.', `amount` = '.$amount .', `exec_time` = "'.$dateTime.'"';
      }

      $stmt = $pdo->prepare($sql);
      $stmt->execute();

      $stmt = $pdo->prepare($sqlHistory);
      $stmt->execute();


      if($action == 'Withdraw'){
        $user = $this->getUserDataById($id);
        if($user['dollar_amount'] <= 10){
          // get the overall total Balance
          $sql = 'SELECT SUM(`amount`) as totalBalanceAmount FROM `users_balance` WHERE `user_id` ='.$id;
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $totalBalanceAmount = $stmt->fetch(PDO::FETCH_ASSOC)['totalBalanceAmount'];

          // get all losing trades
          $sql = 'SELECT SUM(`leverage`) as total FROM `trades` WHERE status = "lost" AND `user` = '.$id;
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $totalTradesLost = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

          $lostAmount = $totalTradesLost;

          $sql = 'UPDATE `users` SET `loss_amount` = '.$lostAmount.' WHERE `id` = '.$id;
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
        }
      }

      // insert to trades

      $sql = 'INSERT INTO `trades` SET `user` ='.$id.', `exec_time` = "'.$dateTime.'", `type` = "'.$action.'", `status` = "update", `payout` = '.$amount;
      $stmt = $pdo->prepare($sql);
      $stmt->execute();

      $pdo = null;
      return true;
    }
    function verifyUser($id, $status){
      $pdo = $this->getPdo();
      $sql = 'UPDATE `users` SET `verified` = "'.$status.'" WHERE `id` = '.$id;
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $pdo = null;
      return true;
    }

    function acceptTerms($id){
        $pdo = $this->getPdo();
        $sql = 'UPDATE `users` SET `accepted_terms` = 1 WHERE `id` = "' . $id . '"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return true;
    }

    public function changePasswordRequest($password, $token){
      $pdo = $this->getPdo();
      // math token to user
      $sql = 'SELECT `user_id` FROM `password_reset_token` WHERE `token` = "'.$token.'" AND `status` = 0';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if($result){
        $userId = $result['user_id'];
        // update password
        $sql = 'UPDATE `users` SET `password` = "'.$password.'" WHERE `id` = '.$userId;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        // update token Status;
        $sql = 'UPDATE `password_reset_token` SET `status` = 1 WHERE `token` = "'.$token.'"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $return = array(
          'success' => true,
          'response' => array('message' => '')
        );
      }else{
        $return = array(
          'success' => false,
          'response' => array('message' => 'Ivalid token, please try to request again.')
        );
      }
      $pdo = null;

      return $return;
    }

    public function createRequestToken($email){
      $date = date('Y-m-d');
      $pdo = $this->getPdo();
      // search if email exists
      $sql = 'SELECT `id` FROM `users` WHERE `email` = "'.$email.'" LIMIT 1';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      if($user){
        $userId = $user['id'];
        $token = $this->generateRandomString(10, false).$user['id'];
        // register token and assign to user
        $sql = 'INSERT INTO `password_reset_token` SET `token` = "'.$token.'", `user_id` = '.$userId.', `date_created` = "'.$date.'"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $return = $token;
      }else{
        $return = false;
      }
      $pdo = null;

      return $return;
    }


    public function sendAlertMessage($emailTo, $city, $region, $country, $date, $ip){
      $date = date("F j, Y", strtotime($date));
      $bodyMessage = '<p class="text-center">We notice a login from another address:</p>';
      $bodyMessage .= '<p class="text-center">'.$region.', '.$city.',<br>
                      '.$country.' on '.$date.'<br>
                      IP: '.$ip.'</p>';
      $button = '<a href="http://webtrader.nanopips.com/" class="btn btn-success btn-lg login-btn"  style="background: #13D384;">This was not me</a>';
      $footer = '<p class="text-center">If this was not you, click this button to login and change your security options.</p>';
      $body = $this->emailTemplate('Authorized Login', $bodyMessage, $button, $footer);
      $resp = $this->sendEmailFunc(SERVICE_EMAIL, SERVICE_EMAIL_NAME, $emailTo, 'Authorized Login', $body, false);
      return true;
    }

    public function updateValidationCode($id, $code){
      $date = date('Y-m-d H:00:00');
      $pdo = $this->getPdo();
      $sql = 'UPDATE `users` SET `verification_code` = "'.$code.'", `verification_time_request` = "'.$date.'", `verified_code` = 0 WHERE `id` = '.$id;
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
    }
    public function confirmValidationCode($id, $code){
      $date = date('Y-m-d H:00:00');
      $pdo = $this->getPdo();
      $sql = 'SELECT verification_code, verification_time_request FROM `users` WHERE `id` = '.$id;
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $userCode = $result['verification_code'];
      $time = $result['verification_time_request'];
      if($code == $userCode){
        if($date == $time){
          $sql = 'UPDATE `users` SET `verified_code` = "yes" WHERE `id` = '.$id;
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $success = true;
          $message = 'Verification successfull.';
          $type = '1';
        }else{
          $success = false;
          $message = 'Verification code expired, please request again.';
          $type = '2';
        }
      }else{
        $success = false;
        $message = 'Verification code does not match.';
        $type = '3';
      }
      return json_encode(array(
        'success' => $success,
        'message' => $message,
        'type' => $type,
        'date1' => $date,
        'date2' => $time
      ));
    }


    public function changePassword($data){
      $oldPassword = $data['oldPassword'];
      $password1 = $data['password1'];
      $password2 = $data['password1'];
      $userId = $data['userId'];
      if($password1 == $password2){
        $pdo = $this->getPdo();
        $sql = 'SELECT `id` FROM `users` WHERE `password` = "'.$oldPassword.'" AND `id` = '.$userId;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result){
          $success = true;
          $message = 'Password successfully changed.';
        }else{
          $success = false;
          $message = 'Old password does not match, please try again.';
        }
      }else{
        $success = false;
        $message = 'Password doesnt match, please try again.';
      }

      return json_encode(array(
        'success' => $success,
        'message' => $message,
      ));
    }

    public function changeSecret($data){
      $currentSecret = $this->getUserSecret($data['userId']);
      $sql = 'DELETE FROM `secret_questions_to_users` WHERE `user_id` = '.$data['userId'];
      $pdo = $this->getPdo();
      $stmt = $pdo->prepare($sql);
      $stmt->execute();

      for($x = 0; $x < count($data['qa']); $x++){
        $sql = 'INSERT INTO `secret_questions_to_users` SET `user_id` = '.$data['userId'].', `secret_question_id` = '.$data['qa'][$x][0].', `answer` = "'.$data['qa'][$x][1].'"';
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
      }

      return true;

    }

    public function getUserSecret($userId){
      $pdo = $this->getPdo();
      $sql = 'SELECT `secret_question_id`, `answer` FROM `secret_questions_to_users` WHERE `user_id` = '.$userId;
      $stmt = $pdo->prepare($sql);
      $stmt->execute();

      $content = array();
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $content[] = $row;
      }
      return $content;
    }
    public function getSecretQuestions(){
      $pdo = $this->getPdo();
      $sql = 'SELECT * FROM `secret_questions`';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $content = array();
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $content[] = $row;
      }
      return $content;
    }


    public function generateRandomString($length = 10, $numeric = false) {
        if($numeric == false){
          $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }else{
          $characters = '0123456789';
        }

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function sendMessage($subject, $emailTo, $message){
      $email = new PHPMailer();
      $email->isSMTP(true);
      $email->From      = NO_REPLY_EMAIL;
      $email->FromName      = NO_REPLY_EMAIL;
      $email->Subject   = 'Account notification';
      $email->Body      = $message;
      $email->IsHTML(true);
      $email->AddAddress( $emailTo );
      $return = $email->Send();
      return $return;
    }


    public function getDepositHistory($userId){
      $pdo = $this->getPdo();
      $sql = 'SELECT i.invoice_id, i.price_in_usd, p.timestamp FROM `invoices` i, `invoice_payments` p
              WHERE i.user_id = '.$userId.'
              AND i.invoice_id = p.invoice_id
             ';

      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $result = array();
      $x = 0;
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $result[] = $row;
          $result[$x]['timestamp'] = date('m/d/Y', strtotime($row['timestamp']));
          $result[$x]['notes'] = '';
          $x++;
      }

      $pdo = null;

      return json_encode($result);
    }


    public function getTotalClientLosses(){
      $pdo = $this->getPdo();
      $sql = 'SELECT SUM(`loss_amount`) as total FROM `users` WHERE `user_level` != "admin"';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $total = $stmt->fetch(PDO::FETCH_ASSOC);
      $pdo = null;

      return $total;
    }

    public function getTotalRemainingBalance(){
      $pdo = $this->getPdo();
      $sql = 'SELECT SUM(`dollar_amount`) as total FROM `users` WHERE `user_level` != "admin"';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $total = $stmt->fetch(PDO::FETCH_ASSOC);
      $pdo = null;

      return $total;
    }

    public function getTotalBalances(){
      $pdo = $this->getPdo();
      $sql = 'SELECT SUM(b.amount) as total FROM `users_balance` b, `users` u WHERE b.user_id = u.id AND u.user_level != "admin"';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $total = $stmt->fetch(PDO::FETCH_ASSOC);
      $pdo = null;

      return $total;
    }

    public function getTotalPercentTrades(){
      $pdo = $this->getPdo();
      $sql = 'SELECT SUM(t.leverage - t.trade_percent_cut_amount) as total FROM `trades` t, `users` u WHERE `type` = "buy" OR `type` = "sell" AND t.user = u.id AND u.user_level != "admin"';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $total = $stmt->fetch(PDO::FETCH_ASSOC);
      $pdo = null;

      return $total;
    }

    public function getPositiveBalance(){
      $pdo = $this->getPdo();
      $sql = 'SELECT u.email,
              (
              	u.dollar_amount - (SELECT SUM(amount) FROM `users_balance` WHERE `user_id` = u.id AND `user_level` != "admin")
              )
              as positiveBalance
              FROM `users` u
              WHERE
              u.user_level != "admin"
              AND u.dollar_amount > (SELECT SUM(amount) FROM `users_balance` WHERE `user_id` = u.id)';

      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $result = array();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = $row;
      }
      $pdo = null;

      return json_encode($result);
    }

    public function sendEmailFunc($from, $fromName, $to, $subject, $body, $hasAttachment = false, $file1Name = '', $file2Name = '', $target_file1 = '', $target_file2 = ''){
      $email = new PHPMailer();
      $email->isSMTP(true);
      $email->SMTPAuth = true;
      $email->SMTPSecure = 'tls';
      $email->Host = "smtp.gmail.com";
      $email->Port = 587;
      $email->Username = $from;
      $email->Password = "Italian$123";
      $email->From = $from;
      $email->FromName = $fromName;
      $email->Subject   = $subject;
      $email->Body      = $body;
      $email->IsHTML(true);
      $email->AddAddress( $to );
      if($hasAttachment == true){
        $email->AddAttachment( $target_file1 , $file1Name );
        if($file2Name != ''){
          $email->AddAttachment( $target_file2 , $file2Name );
        }
      }
      $return = $email->Send();

      return $return;
    }

    public function emailTemplate($title, $bodyMessage, $button, $footer){
      return '<html>
      <head>
        <style>
        p {
          font-size: 1.3em;
        }
        * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
      }
      div {
        display: block;
      }
      a {
          color: #337ab7;
          text-decoration: none;
      }
      body {
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        font-size: 14px;
        line-height: 1.42857143;
        color: #333 !important;
        background-color: #fff;
      }
      .main-container{
        margin-left: 0;
        margin-bottom: 12px;
      }
      .footer{
          max-width: 720px;
      }
      .login-container{
        display: flex;
        flex-direction: column;
      }
      @media (min-width: 992px){
      .col-md-12 {
        width: 100%;
      }
      .col-md-6{
        width: 50%;
      }
      .col-md-offset-6 {
          margin-left: 25%;
      }
      }
      @media (min-width: 992px){
      .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9 {
        float: left;
      }
      }
      .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
        position: relative;
        min-height: 1px;
        padding-right: 15px;
        padding-left: 15px;
      }

      * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
      }
      user agent stylesheet
      div {
        display: block;
      }
      body {
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        font-size: 14px;
        line-height: 1.42857143;
        color: #333;
        background-color: #fff;
      }
      html {
        font-size: 10px;
        -webkit-tap-highlight-color: rgba(0,0,0,0);
      }
        .col-md-12 {
            width: 100%;
            float: left;
            position: relative;
            min-height: 1px;
            padding-right: 15px;
            padding-left: 15px;
        }

        .login-wrapper {
        border: 10px solid rgba(0,162,0, 0.5);
        margin-top: 15%;
      }
      .login-container {
        background: #fff;
      }
      .row {
        margin-right: -15px;
        margin-left: -15px;
      }

      .logo-header {
        float: left;
        margin: 24px;
      }
      .login-line {
          border-top: 3px solid #BBBBBB;
          border-radius: 4px;
      }
      .col-md-4 {
          width: 33.33333333%;
      }
      .login-header-text {
          font-size: 1.5em;
          color: #BBBBBB;
      }

      .text-center{
        text-align: center;
      }
      .login-wrapper{
        max-width: 720px;

      }
      .login-btn {
          width: auto;
          height: auto;
          font-size: auto;
          font-weight: none;
      }
      .login-header-text{
        line-height: 2 !important;
        font-size: 1.4em !important;
      }
      .btn-group-lg>.btn, .btn-lg {
          padding: 10px 16px;
          font-size: 18px;
          line-height: 1.3333333;
          border-radius: 6px;
      }
      .btn-success {
          color: #fff;
          background-color: #5cb85c;
          border-color: #4cae4c;
      }
      .btn {
          display: inline-block;
          padding: 6px 12px;
          margin-bottom: 0;
          font-size: 14px;
          font-weight: 400;
          line-height: 1.42857143;
          text-align: center;
          white-space: nowrap;
          vertical-align: middle;
          -ms-touch-action: manipulation;
          touch-action: manipulation;
          cursor: pointer;
          -webkit-user-select: none;
          -moz-user-select: none;
          -ms-user-select: none;
          user-select: none;
          background-image: none;
          border: 1px solid transparent;
          border-radius: 4px;
      }
      @media screen and (max-width: 1330px){
      .login-btn {
          /* width: 50%; */

          font-size: 1.2em;
          /* font-weight: bold; */
      }
      }

      .login-header-container{
        margin-top: -18px;
        text-align: center !important;
        border: 2px solid #e3e3e3;
      }
      .btn-text{
        color: #fff;
      }


        </style>
      </head>
      <body>
      <div class="col-md-12 main-container">
              <div class="login-wrapper" style="margin-top: 5% !important;">
                <div class="login-container">
                  <div class="row" style="padding: 25px;">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                      <div class="logo-header">
                        <img src="http://webtrader.nanopips.com/assets/img/logo.png" style="width: 156px; height: 42px;">
                      </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 12px;">

                      <div class="col-md-6 col-md-offset-6 login-header-container" style="">
                        <span class="login-header-text"><!--$title-->'.$title.'</span>
                      </div>

                    </div>
                    <div class="col-md-12">
                        <!-- $bodyMessage -->
                        '.$bodyMessage.'
                    </div>
                    <div class="col-md-12 spacer text-center">
                        <!-- $button -->
                        '.$button.'
                    </div>
                    <div class="col-md-12 spacer text-center">
                        <!-- $footer -->
                        '.$footer.'
                    </div>
                    <div class="col-md-12 text-center footer">
                      <a href="http://webtrader.nanopips.com/request-password-reset.php">Forgot Password<a/>
                      <br>
                      <a href="http://webtrader.nanopips.com/signup.php">Register<a/>
                    </div>
                    </div>

                  </div>
                </div>
                <div class="col-md-12 text-center footer">
                  <small class="">2018 NanoPips.com | All Rights Reserved</small>
                </div>
              </div>


      </body>
      </html>
';
    }

    public function pdoQuoteValue($value)
    {
        $pdo = $this->getPdo();
        return $pdo->quote($value);
    }

    public function getPdo()
    {
        if (!$this->db_pdo)
        {
            if ($this->debug)
            {
                $this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PWD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            }
            else
            {
                $this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PWD);
            }
        }
        return $this->db_pdo;
    }
}
