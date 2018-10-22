<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
session_start();

define('DB_USER', 'nanopips_admin');
define('DB_PWD', 'dfab7c358bb163');
define('DB_NAME', 'nanopips_stock');
define('DB_HOST', 'localhost');
define('DB_DSN', 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME .'');



define('SERVICE_EMAIL', 'clientservice@nanopips.com'); 
define('ADMIN_EMAIL', 'admin@nanopips.com');
define('NO_REPLY_EMAIL', 'admin@nanopips.com');
define('SERVICE_EMAIL_NAME', 'Nanopips Client Service');
define('ADMIN_EMAIL_NAME', 'Nanopips Admin');

//Load Composer's autoloader
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

?>
