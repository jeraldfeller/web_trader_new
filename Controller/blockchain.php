<?php
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/History.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Users.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/include.php';
$action = $_GET['action'];
switch ($action) {
  case 'create-invoice':
      $data = json_decode($_POST['param'], true);
      $userId = $_SESSION['id'];
      $invoice_id = time();
      $price_in_usd = $data['amount'];
      $product_url = 'nanopips';
      $price_in_btc = file_get_contents($blockchain_root . "tobtc?currency=USD&value=" . $price_in_usd);

      $db = new mysqli($mysql_host, $mysql_username, $mysql_password) or die(__LINE__ . ' Invalid connect: ' . mysqli_error());

      $db->select_db($mysql_database) or die( "Unable to select database. Run setup first.");

      //Add the invoice to the database
      $stmt = $db->prepare("replace INTO invoices (invoice_id, user_id, price_in_usd, price_in_btc, product_url) values(?,?,?,?,?)");
      $stmt->bind_param("iidds",$invoice_id, $userId, $price_in_usd, $price_in_btc, $product_url);
      $result = $stmt->execute();

      if (!$result) {
          die(__LINE__ . ' Invalid query: ' . mysqli_error($db));
      }

      $callback_url = $mysite_root . "/includes/callback.php?invoice_id=" . $invoice_id . "&secret=" . $secret;

      $resp = file_get_contents($blockchain_receive_root . "v2/receive?key=" . $my_api_key . "&callback=" . urlencode($callback_url) . "&xpub=" . $my_xpub);
      $response = json_decode($resp);


      //Add the invoice to the database
      $stmt = $db->prepare("UPDATE invoices SET address = ? WHERE invoice_id = ?");
      $stmt->bind_param("si", $response->address, $invoice_id);
      $result = $stmt->execute();

      if (!$result) {
          die(__LINE__ . ' Invalid query: ' . mysqli_error($db));
      }
      /*
      $to      = 'jeraldfeller@gmail.com';
      $subject = 'Nanopips purchase';
      $message = 'Waiting for your confirmations.';
      $headers = 'From: webtrader@nanopips.com' . "\r\n" .
          'X-Mailer: PHP/' . phpversion();
      if(!mail($to, $subject, $message, $headers)){
        var_dump(error_get_last()['message']);
      };
      */
      print json_encode(array('input_address' => $response->address ));
      /*
      echo json_encode(array(
        'amount' => $price_in_usd,
        'invoiceId' => $invoice_id,
      ));
      */
    break;
}

?>
