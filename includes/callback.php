<?php

include 'include.php';

$db = new mysqli($mysql_host, $mysql_username, $mysql_password) or die(__LINE__ . ' Invalid connect: ' . mysqli_error());
$db->select_db($mysql_database) or die( "Unable to select database. Run setup first.");

$invoice_id = $_GET['invoice_id'];
$transaction_hash = $_GET['transaction_hash'];
$value_in_btc = $_GET['value'] / 100000000;


$stmt = $db->prepare("select user_id, price_in_usd, address from invoices where invoice_id = ?");
$stmt->bind_param("i",$invoice_id);
$success = $stmt->execute();

if (!$success) {
    die(__LINE__ . ' Invalid query: ' . mysql_error());
}

$result = $stmt->get_result();
while($row = $result->fetch_array()) {
  $my_address = $row['address'];
  $userId = $row['user_id'];
  $amount = $row['price_in_usd'];
}

$result->close();
$stmt->close();

$stmt = $db->prepare("select id, email from users where id = ?");
$stmt->bind_param("i", $userId);
$success = $stmt->execute();
if (!$success) {
    die(__LINE__ . ' Invalid query: ' . mysql_error());
}

$result = $stmt->get_result();
while($row = $result->fetch_array()) {
  $email = $row['email'];
}

$result->close();
$stmt->close();


if ($_GET['address'] != $my_address) {
    echo 'Incorrect Receiving Address';
  return;
}

if ($_GET['secret'] != $secret) {
  echo 'Invalid Secret';
  return;
}

if ($_GET['confirmations'] >= 4) {
  //Add the invoice to the database
  $timestamp = strtotime();
  $stmt = $db->prepare("replace INTO invoice_payments (invoice_id, transaction_hash, value, timestamp) values(?, ?, ?, ?)");
  $stmt->bind_param("isdi",$invoice_id, $transaction_hash, $value_in_btc, $timestamp);
  $stmt->execute();

  //Delete from pending
  $stmt = $db->prepare(" delete from pending_invoice_payments where invoice_id = ? limit 1");
  $stmt->bind_param("i",$invoice_id);
  $result = $stmt->execute();

  // update user funds
  $stmt = $db->prepare("UPDATE users SET dollar_amount = (dollar_amount + $amount) WHERE id = ?");
  $stmt->bind_param("i", $userId);
  $result = $stmt->execute();


  if($result) {
	   echo "*ok*";
      $to      = $email;
      $subject = 'Success! Your deposit has been transferred to your account.';
      $message = 'Dear Valued Client,' . "\r\n";
      $message .= 'Your funds amount of BTC'.$value_in_btc.' has been processed to your account successfully.' . "\r\n";
      $message .= "\r\n";
      $message .= "\r\n";
      $message .= 'Please refer to ID # '.$invoice_id.' for your records.' . "\r\n";
      $message .= "\r\n";
      $message .= "\r\n";
      $message .= 'If you require further information, please email support@nanopips.com' . "\r\n";
      $message .= "\r\n";
      $message .= "\r\n";
      $message .= 'Thank you for choosing NanoPips.' . "\r\n";
      $message .= "\r\n";
      $message .= "\r\n";
      $message .= 'Regards,' . "\r\n";
      $message .= "\r\n";
      $message .= "\r\n";
      $message .= 'NanoPips Client Service Department';
      $headers = 'From: webtrader@nanopips.com' . "\r\n" .
          'X-Mailer: PHP/' . phpversion();

      mail($to, $subject, $message, $headers);
  }
} else {
  //Waiting for confirmations
  //create a pending payment entry
  $stmt = $db->prepare("replace INTO pending_invoice_payments (invoice_id, transaction_hash, value) values(?, ?, ?)");
  $stmt->bind_param("isd",$invoice_id, $transaction_hash, $value_in_btc);
  $stmt->execute();


  $stmt = $db->prepare("select email_sent from invoices where invoice_id = ?");
  $stmt->bind_param("i", $invoice_id);
  $success = $stmt->execute();
  if (!$success) {
      die(__LINE__ . ' Invalid query: ' . mysql_error());
  }

  $result = $stmt->get_result();
  while($row = $result->fetch_array()) {
    $emailSent = $row['email_sent'];
  }

  if($emailSent == 0){
    $stmt = $db->prepare("UPDATE invoices set email_sent = 1 WHERE invoice_id = ?");
    $stmt->bind_param("i",$invoice_id);
    $stmt->execute();


     $to      = $email;
     $subject = 'Confirmation NanoPips Deposit';
     $message = 'Dear Valued Client,' . "\r\n";
     $message .= 'Your funds transfer request is being processed to your account.' . "\r\n";
     $message .= 'Please allow up to 24 hours for this process to complete.' . "\r\n";
     $message .= 'Please refer to ID # '.$invoice_id.' as your transaction ID.' . "\r\n";
     $message .= "\r\n";
     $message .= "\r\n";
     $message .= 'If you require further information, please email support@nanopips.com' . "\r\n";
     $message .= "\r\n";
     $message .= "\r\n";
     $message .= 'Thank you for choosing NanoPips.' . "\r\n";
     $message .= "\r\n";
     $message .= "\r\n";
     $message .= 'Regards,' . "\r\n";
     $message .= "\r\n";
     $message .= "\r\n";
     $message .= 'NanoPips Client Service Department';

     $headers = 'From: webtrader@nanopips.com' . "\r\n" .
         'X-Mailer: PHP/' . phpversion();

     mail($to, $subject, $message, $headers);
  }
  echo "Waiting for confirmations";
}

?>
