<?php
if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn']) {
    $userData = $_SESSION['userData'];
    $userSess = $_SESSION['userSess'];
    if($userData['dollar_amount'] == 0){
      $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
      $uri_segments = explode('/', $uri_path);

      if($uri_segments[1] != 'buy-funds.php'){
        //  Header('Location: buy-funds.php');
      }
    }
}else{
    Header('Location:  index.php');
}
