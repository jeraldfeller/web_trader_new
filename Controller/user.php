<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/History.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Users.php';


$users = new Users();


$action = $_GET['action'];

switch ($action){
    case 'login':
        $data = json_decode($_POST['param'], true);
        $return = $users->userLoginFunction($data);
        echo $return;
        break;
    case 'create':
        $data = json_decode($_POST['param'], true);
        $return = $users->createUserFunction($data);
        echo $return;
    break;
    case 'pending-trades':
        $data = json_decode($_POST['param'], true);
        $return = $users->getPendingTradesFunction($data);
        echo $return;
        break;
    case 'register-trade':
        $data = json_decode($_POST['param'], true);
        $return = $users->registerTradesFunction($data);
        echo $return;
        break;
    case 'update-trades':
        $data = json_decode($_POST['param'], true);
        $return = $users->userUpdateTradesFunction($data);
        echo $return;
        break;
    case 'add-funds':
        $data = json_decode($_POST['param'], true);
        $return = $users->userPurchaseFunds($data);
        echo $return;
        break;
    case 'request-withdraw':
      $data = json_decode($_POST['param'], true);
      $email = 'admin@nanopips.com';
      $to      = $email;
      $subject = 'Widthdraw Request';
      $message = '';
      $message .= 'Fullname: '. $data['fullName'];
      $message .= "\r\n";
      $message .= 'Address: '. $data['address'];
      $message .= "\r\n";
      $message .= 'Amount: '. $data['amount'];
      $message .= "\r\n";
      $headers = 'From: webtrader@nanopips.com' . "\r\n" .
          'X-Mailer: PHP/' . phpversion();

      mail($to, $subject, $message, $headers);

      echo json_encode(true);
      break;
    case 'send-profile':

      $userId = $_GET['userId'];
      $data = $_POST;
      $file1 = $_FILES["profile_files1"];
      $file2 = $_FILES["profile_files2"];

      $file1Name = $file1['name'];
      $target_dir = '../tmp/user/'.$userId;
      if(!file_exists($target_dir)){
        mkdir($target_dir, 0700);
      }

      if($file1 != '' || $file1 != null){
        $target_file1 = $target_dir .'/'. $file1Name;
        move_uploaded_file($file1['tmp_name'], $target_file1);
      }

      $file2Name = $file2['name'];
      if($file2 != '' || $file2 != null){
        $target_file2 = $target_dir .'/'. $file2Name;
        move_uploaded_file($file2['tmp_name'], $target_file2);
      }

        $user = $users->getUserDataById($userId);

        $users->updateUser($userId, $data['profile_first_name'], $data['profile_last_name'], $data['profile_address'], $file1Name, $file2Name);

        $userData = $users->getUserDataById($userId);

        $message = 'Name: ' . $data['profile_first_name'] . ' ' . $data['profile_last_name'] . "<br>";
        $message .= 'Email: ' . $user['email'].'<br>';
        $message .= 'Address: ' . $data['profile_address'];
        $resp = $users->sendEmailFunc(SERVICE_EMAIL, SERVICE_EMAIL_NAME, SERVICE_EMAIL, 'Account verificaion request', $message, true, $file1Name, $file2Name, $target_file1, $target_file2);
        if($resp){
          $bodyMessage = '<p>Dear Client,</p>
          <p class="text-center">This is email is to inform you that we have received your inquiry and will respond within one business day.</p>';
          $button = '<a href="http://webtrader.nanopips.com/" class="btn btn-success btn-lg login-btn"  style="background: #13D384;">Login Here</a>';
          $footer = '<p class="text-center">In the meantime, you can refer to our  <a href="http://webtrader.nanopips.com/faq.html">FAQ</a> Section for further assistance.</p>';
          $body = $users->emailTemplate('Email Receieved', $bodyMessage, $button, $footer);
          $resp = $users->sendEmailFunc(SERVICE_EMAIL, SERVICE_EMAIL_NAME, $userData['email'], 'Email Receieved', $body, false);
          if($resp){
            $stat = true;
          }else{
            $stat = false;
          }
        }else{
          $stat = false;
        }
        echo json_encode($stat);

      break;

      case 'request-password-reset':
        $data = json_decode($_POST['param'], true);
        $email = $data['email'];
        $token = $users->createRequestToken($email);
        if($token != false){
          $bodyMessage = '<p class="text-center">Click this link to change your password.</p>';
          $button = '<a href="http://webtrader.nanopips.com/change-password.php?token='.$token.'" class="btn btn-success btn-lg login-btn"  style="background: #13D384;">Change Password</a>';
          $footer = '<p class="text-center">You can manage your password and secret question answers from inside your NanoPips Account area.</p>';
          $body = $users->emailTemplate('Password Change', $bodyMessage, $button, $footer);
          $resp = $users->sendEmailFunc(SERVICE_EMAIL, SERVICE_EMAIL_NAME, $email, 'Password Change', $body, false);


          $response = array(
            'success' => true,
            'response' => array('message' => '')
          );
        }else{
          $response = array(
            'success' => false,
            'response' => array('message' => 'Email is not registered, please try again.')
          );
        }

        echo json_encode($response);
      break;

      case 'change-password':
      $data = json_decode($_POST['param'], true);
      $password = $data['password'];
      $token = $data['token'];
      $resp = $users->changePasswordRequest($password, $token);
      echo json_encode($resp);
      break;

      case 'request-authentication':
        $data = $_POST['param'];
        $user = $users->getUserDataById($data['userId']);
        $code = $users->generateRandomString(6);
        $users->updateValidationCode($data['userId'], $code);
        $bodyMessage = '<p class="text-center">Use this code to verify your email address:<br>
                       <span class="btn btn-success btn-lg" style="background: #13D384; font-weight: bold;">'.$code.'</span><br>
                       This code will expire in 60 Minutes.</p>';

        $body = $users->emailTemplate('Verify Your Email', $bodyMessage, '', '');
        $resp = $users->sendEmailFunc(SERVICE_EMAIL, SERVICE_EMAIL_NAME, $user['email'], 'Verify Your Email', $body, false);

        echo true;
      break;

      case 'confirm-authentication':
        $data = $_POST['param'];
        echo $users->confirmValidationCode($data['userId'], $data['code']);
      break;

      case 'change-password':
        $data = $_POST['param'];
        echo $users->changePassword($data);
      break;

      case 'change-secret':
        $data = $_POST['param'];
        echo $users->changeSecret($data);
      break;

      case 'transaction-history':
        $data = $_POST['param'];
        if($data['action'] == 'deposit'){
          $history = $users->getDepositHistory($data['userId']);
        }

        echo $history;
      break;
      case 'accept-terms':
        $data = $_POST['param'];
        echo $users->acceptTerms($data['userId']);
      break;

      case 'get-users':
        echo $users->getAllUsers();
      break;

      case 'get-user-by-id':
        $data = $_POST['param'];
        if(isset($data['admin'])){
          $isAdmin = $data['admin'];
        }else{
          $isAdmin = true;
        }
        echo json_encode($users->getUserDataById($data['id'], $isAdmin));
      break;

      case 'verify-documents':
        $data = $_POST['param'];
        echo $users->verifyUser($data['id'], $data['verified']);
      break;

      case 'update-balance':
        $data = $_POST['param'];
        echo $users->updateUserBalance($data['id'], $data['action'], $data['amount']);
      break;

      case 'get-trade-stats':
        $totalClientLosses = $users->getTotalClientLosses()['total'];
        $totalPercentTrades = $users->getTotalPercentTrades()['total'];
        $totalRemainingBalance = $users->getTotalRemainingBalance()['total'];
        $totalBalances = $users->getTotalBalances()['total'];


        echo json_encode(array(
          'totalClientLosses' => ($totalClientLosses != null ? $totalClientLosses : 0),
          'totalPercentTrades' => ($totalPercentTrades != null ? $totalPercentTrades : 0),
          'totalRemainingBalances' => ($totalRemainingBalance != null ? $totalRemainingBalance : 0),
          'totalBalances' => ($totalBalances != null ? $totalBalances : 0),
        ));
      break;

      case 'get-positive-balance':
        echo $users->getPositiveBalance();
      break;

      case 'atu':
        $data = $_POST['param'];
        echo $users->checkATU($data['userId'], $data['amount'], $data['action'], $data['coin'], $data['transactionId']);
      break;
      case 'atu-socket':
        echo json_encode($users->checkATUSocket());
      break;
      case 'update-atu':
        $data = $_POST['param'];
        echo $users->updateATU($data['userId'], $data['transactionId']);
      break;
}
