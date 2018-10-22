<?php
set_time_limit(61);

$start = (new DateTime())->getTimestamp();

require dirname(__FILE__) . '/../Model/Init.php';
require dirname(__FILE__) . '/../Model/Socket.php';

$socket = new Socket();

$coin = $argv[1];

$page = 'http://coincap.io/page/' . strtoupper($coin);

$table = strtolower($coin) . '_table_new';

while((new DateTime())->getTimestamp() - $start < 60) {
	try {
		$timestamp = date("Y-m-d G:i:s") . ".000000";
		$prices = json_decode(file_get_contents($page));
		$dollarPrice = $prices->price_usd;
		if($coin == 'btc') {
			$btcPrice = $prices->price_usd;
		} else {
			$btcPrice = $prices->price_btc;	
		}

		$socket->postCurrentPrice($table, $dollarPrice, $btcPrice, $timestamp);
		usleep(500000);
	} catch(Exception $e) {
		var_dump($e);
	}
}

?>