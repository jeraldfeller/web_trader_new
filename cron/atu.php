<?php
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/History.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Users.php';
$users = new Users();

$users->resetATU();

?>
