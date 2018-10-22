<?php
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Model/Options.php';
$options = new Options();

$action = $_GET['action'];
 
switch ($action) {
  case 'get':
    echo json_encode($options->getOption($_GET['name']));
    break;
  case 'update':
    $data = $_POST['param'];
    echo json_encode($options->updateOption($data['name'], $data['value']));
    break;
  default:
    // code...
    break;
}
