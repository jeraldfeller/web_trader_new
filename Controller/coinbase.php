<?php
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Users.php';


$action = $_GET['action'];
switch ($action) {
  case 'create-charge':
    $data = json_decode($_POST['param'], true);

    $amount = $data['amount'];
    $userId = $_SESSION['id'];
    $email = $_SESSION['userData']['email'];

    $post = json_encode(
      array(
        'name' => 'nanopips',
        'description' => 'Purchase nanopips',
        'local_price' => array(
          'amount' => $amount,
          'currency' => 'USD'
        ),
        'pricing_type' => 'fixed_price',
        'metadata' => array(
          'customer_id' => $userId,
          'customer_email' => $email
        )
      )
    );


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.commerce.coinbase.com/charges");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "Content-Type: application/json",
      "X-CC-Api-Key: 97871044-036d-4270-a941-8b10ffae927b",
      "X-CC-Version: 2018-03-22"
    ));

    $response = curl_exec($ch);
    curl_close($ch);


    echo $response;


    break;

  case 'get-charge':
    $data = json_decode($_POST['param'], true);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.commerce.coinbase.com/charges/".$data['code']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
  //  curl_setopt($ch, CURLOPT_POST, TRUE);
  //  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //  "Content-Type: application/json",
      "X-CC-Api-Key: 97871044-036d-4270-a941-8b10ffae927b",
      "X-CC-Version: 2018-03-22"
    ));

    $response = curl_exec($ch);
    curl_close($ch);


    echo $response;
    break;

  case 'complete-charge':
      $user = new User();
      $data = json_decode($_POST['param'], true);

      $amount = $data['amount'];
      $userId = $_SESSION['id'];

      $user->userPurchaseFunds(array('userId' => $userId, 'amount' => $amount));

      echo true;

    break;
  default:
    # code...
    break;
}

?>
