<?php
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/session.php';
unset($_SESSION["userData"]);
unset($_SESSION["isLoggedIn"]);
session_destroy();

Header('location: index.php');
?>