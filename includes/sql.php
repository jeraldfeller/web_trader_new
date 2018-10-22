<?php
$servername = "localhost";
$username = "nanopips_admin";
$password = "dfab7c358bb163";

try {
    $conn = new PDO("mysql:host=$servername;dbname=nanopips_stock", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //  echo "Connected successfully";
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
?>
