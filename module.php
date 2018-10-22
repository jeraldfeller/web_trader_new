<?php
//Needs to be OOP soon

//For User Trades
include("includes/sql.php");

function update_user_balance($user, $type, $value) {
		global $conn;
		//Log All these Entries in transaction_log

	if ($type == '1') {
		//add Profit from a trade
		return $conn->query("UPDATE users SET funds=funds+$value WHERE id='$user'");
	} else if ($type == '2') {
		//subtract Loss from a Trade
		return $conn->query("UPDATE users SET funds=funds-$value WHERE id='$user'");
	} else if ($type == '3') {
		//Transaction Fee
		return $conn->query("UPDATE users SET funds=funds-$value WHERE id='$user'");

	}
}


//For Trades
function get_user_balance($user) {
	//does he have the funds
	global $conn;

	$stmt = $conn->query("SELECT funds FROM users WHERE id='$user' order by `id` desc limit 1");
	$row = $stmt->fetchObject();
	return number_format($row->funds,4, ".", "");
}

function get_user_email($user) {
	//does he have the funds

	global $conn;

	$stmt = $conn->query("SELECT email FROM users WHERE id='$user' order by `id` desc limit 1");
	$row = $stmt->fetchObject();
	return $row->email;

}


//User Login Sessions
function is_valid_pass($user, $pass) {
	global $conn;
	$stmt = $conn->query("SELECT id FROM users WHERE email='$user' and password='$pass' order by `id` desc limit 1");
	$row = $stmt->fetchObject();
	return @$row->id;
}


function get_last_price($ticker='BTC') {
	global $conn;

	$stmt = $conn->query("SELECT price FROM price_history WHERE ticker='$ticker' order by id desc limit 1");
	$row = $stmt->fetchObject();
	return $row->price;
}


function get_last_prices_graph($ticker='BTC', $total=60) {
	global $conn;

	//$stmt = $conn->query("SELECT price, sec_to_time(time_to_sec(exec_time) - time_to_sec(exec_time)%(60)) as round, exec_time as intervals  FROM price_history WHERE ticker='$ticker' order by id desc limit $total");

	$stmt = $conn->query("SELECT * FROM price_history WHERE ticker='$ticker' and `exec_time` > NOW() - INTERVAL 1 HOUR group by minute(exec_time), hour(exec_time), day(exec_time) order by id desc limit $total");
	$price_dump = $stmt->fetchAll(PDO::FETCH_ASSOC);


	$time_series = array();

	for ($i=0;$i<count($price_dump);$i++) {

		$time =  $price_dump[$i]['exec_time'];

		$stmt = $conn->query("SELECT exec_time, max(price) as high, min(price) as low, max(id) as x FROM price_history WHERE ticker='$ticker' AND (`exec_time` >= date_sub('$time', interval 0 minute) AND `exec_time` <= date_sub('$time', interval -1 minute))");

		$prices = $stmt->fetchAll(PDO::FETCH_ASSOC);

		//echo $price_dump[$i]['exec_time'] . " " . $price_dump[$i]['price'] . '<br>';
		$id = $prices[0]['x'];

		//print_r($prices);
		$stmt = $conn->query("SELECT price from price_history where id=$id");
		$last_p = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$time_series[$i]['time'] = $price_dump[$i]['exec_time'];
		$time_series[$i]['open'] = $price_dump[$i]['price'];
		$time_series[$i]['high'] = $prices[0]['high'];
		$time_series[$i]['low'] = $prices[0]['low'];
		$time_series[$i]['close'] = $last_p[0]['price'];



	}


/*	echo "<pre>";
	print_r($time_series);
	echo "</pre>";
*/
		return array_reverse($time_series);
/*
	$last_min = 0;
	$last_counter = 0;
	$stock_plot = array();
	for($i=0;$i<count($price_dump);$i++)
	{
		//Initial Set
		if ($last_min == 0) {
			$last_min = substr($price_dump[$i]['round'],-5,2);
			$last_counter = $i;
			continue;
		}

		$curr_min = substr($price_dump[$i]['round'],-5,2);

		if ($curr_min == $last_min) {
		//	echo $curr_min . "-> $last_min -> ". $price_dump[$i]['price'] . "<br>";
		} else {

		//	echo "Last: $last_min - Curr Min: $curr_min [$i]<br>";
			$stock_plot[] = get_val($last_counter, $i, $price_dump[$i]['round'], $price_dump);

			$last_counter = $i;
			$last_min = $curr_min;
			//	echo "switch at $i<br>"; $last_min = 0;
		}
	}
	$stock_plot = array_reverse($stock_plot);

	return $stock_plot;*/
}

function get_val($start, $end, $time, $price_dump) {

	$stock['open'] = $price_dump[$start]['price'];
	$stock['close'] = $price_dump[$end]['price'];

	$stock['low'] = 99999999;
	for($i=$start;$i<$end; $i++){
		if ($stock['low']>$price_dump[$i]['price']) { $stock['low'] = $price_dump[$i]['price']; }
	}

	$stock['high'] = 0;
	for($i=$start;$i<$end; $i++){
		if ($stock['high']<$price_dump[$i]['price']) { $stock['high'] = $price_dump[$i]['price']; }
	}

	$stock['time'] = $time;
	$stock['plot'] = substr($price_dump[$start]['intervals'],0, strpos($price_dump[$start]['intervals'],":")-3). " ". $time;

	return $stock;
}


function trade_made($user, $ticker='BTC', $type, $leverage, $ordernum=0) {
	global $conn;

	if ((get_user_balance($user) > (50)) && (get_user_balance($user)>(20*$leverage))) {
		if (($leverage>0) && ($leverage <=10)) {
			if (is_ticker_valid($ticker) != "FAIL") {
				$ticker = is_ticker_valid($ticker);
			update_user_balance($user,3, $leverage * 20);

			//get last price in DB
			if ($type == '1') {
			//open
				$open = get_last_price($ticker);
				$close = '';
			} else {
				$open = '';
				$close = get_last_price($ticker);
			}


			$stmt = $conn->prepare("INSERT INTO trades(user,ticker,open,close,leverage) VALUES(:field1, :field2, :field3,:field4,:field5)");
			echo $stmt->execute(array(':field1'=>$user, ':field2'=> $ticker, ':field3' => $open, ':field4' => $close, ':field5' => $leverage));
	} else { echo "Ticker Invalid $ticker"; }
	} else { echo "Leverage Amount Invalid"; }
	} else { echo "Insufficient Funds, Need Over $50 minimum and above $" . (20*$leverage); }

}


//Return a list of trades done by the user, OPEN/CLOSED & TOTAL
function user_trades($user, $type='open', $limit=10) {

	global $conn;

	if ($type == 'open') {
		$close_clause = '(open = 0.0000 or close=0.0000)';
		$limit = 999;
	} else {
		$close_clause = "(`open` > 0.0000 and close NOT LIKE '0.0000')";
		$limit = 200;
		//$close_clause = '1=1';
	}

   $stmt = $conn->query("SELECT * FROM trades WHERE user=$user AND $close_clause order by `id` DESC limit $limit");
   return $stmt->fetchAll(PDO::FETCH_ASSOC);

}

function color_me($num) {
	if ($num > 0) {
	return "<font color='green'>$num</font>";
	} else { return "<font color='red'>$num</font>"; }
}

function render_order_table($data, $button=0) {
	//$table = "Open Orders: ". count($data);
	//$table .= var_dump($data);
	$table ="<table id='orders' class='table'>
				<tr>
				<td>Time</td>
				<td>Leverage</td>
				<td>Price</td>
				";
	if ($button ==0) { $table .=  "<td>Cover</td>"; }
				$table .= "<td>P/L</td>
				</tr>";


	$total = count($data);
	$running_total = 0;
	for($i=0;$i<$total;$i++){

		if ($data[$i]['open']==0.0000) {
			$price = "Sold " . $data[$i]['ticker'] . " @ " . $data[$i]['close'];
			$price_other = $data[$i]['open'];
			$ticker = $data[$i]['ticker'];
			$pl = $data[$i]['leverage'] * (number_format(($data[$i]['close']-get_last_price($ticker)),4, ".", ""));
			$running_total += $data[$i]['leverage'] * ($data[$i]['close']-get_last_price($ticker));
		} else {

			if ($button !=0) {
			$price = "Buy " . $data[$i]['ticker'] .  " $".$data[$i]['open'] . " | Sell: $" . $data[$i]['close'];
			} else {
			$price = "Bought ". $data[$i]['ticker'] . " @ " . $data[$i]['open'];
			}

				/*if (($data[$i]['balance'] >0) && ($data[$i]['close'] > $data[$i]['open'])){
					$price = "<font color='green'>+Bought</font> at: $". $data[$i]['open'] . " | Closed at: $" . $data[$i]['close'];
				} else if (($data[$i]['balance'] <0) && ($data[$i]['close'] < $data[$i]['open'])) {
				$price = "(Sold or Lost Long) Bought at: $". $data[$i]['open'] . " | Closed at: $" . $data[$i]['close'];

			} else if (($data[$i]['balance'] >0) && ($data[$i]['open'] > $data[$i]['close'])) {
			$price = "<font color='red'>+Sold</font> at: $". $data[$i]['close'] . " | Closed at: $" . $data[$i]['open'];

			}*/ /*else {
					$price = "<font color='red'>-Sold</font> at: $". $data[$i]['close'] . " | Closed at: $" . $data[$i]['open'];
				}*/



			$price_other = $data[$i]['close'];
			$ticker = $data[$i]['ticker'];
			$pl = $data[$i]['leverage'] * (number_format((get_last_price($ticker) - $data[$i]['open']),4,".", ""));
			$running_total += $data[$i]['leverage'] * ((get_last_price($ticker) - $data[$i]['open']));
		}


		$table .= "<tr>";
		$table .= "<td>". $data[$i]['exec_time'] . "</td>";
		$table .= "<td>". $data[$i]['leverage'] . "</td>";


		$table .= "<td>$price</td>";
	if ($button ==0) {
		$table .= "<td><button class='btn btn-danger' type='button' onclick='javascript:cover_it(". $data[$i]['id']. ")'>Close</button></td>";

		$fee = $data[$i]['leverage'] * 20;


		$table .= "<td>$". color_me($pl-$fee) . "</td>";
	} else {
	/*	if (strpos("Bought", $price)>0) {
		$pl = $data[$i]['close'] - $data[$i]['open'];
		} else {
		$pl = $data[$i]['open'] - $data[$i]['close'];
		}
		$pl = $data[$i]['balance'];
		//$pl = $data[$i]['leverage'] * $pl;
		$pl = "$" . color_me(number_format($pl,4,".", ""));*/

		$pl = "$" . color_me(number_format($data[$i]['balance'],4,".", "")-($data[$i]['leverage'] * 20));
		$table .= "<td>". $pl . "</td>";
	}

		$table .= "</tr>";

	}
	if ($button ==0) {

		//	$table .= "<tr><td colspan='5' align='right'>Total (Excl Fee): $". color_me(number_format(($running_total),4,".", "")) . "</td></tr>";
	}

$table .= "</table>";


return $table;
}

//function to closer or cover a open position, and to credit funds
function cover($user, $id) {

	global $conn;
//is it open or closed
 	$stmt = $conn->query("SELECT * FROM trades WHERE user='$user' AND id=$id order by `id` DESC limit 1");
 	$pos = $stmt->fetchAll(PDO::FETCH_ASSOC);

 	$ticker = $pos[0]['ticker'];
 	$last_price = get_last_price($ticker);

 if ($pos[0]['open'] == 0) {
	 //its a short
	 $sell_price = $pos[0]['close'];
	 $funds = $pos[0]['leverage'] * ($sell_price-$last_price);
	 $conn->query("UPDATE trades SET balance=balance+$funds, open=$last_price WHERE user='$user' and id='$id'");

 } else {
	  $buy_price = $pos[0]['open'];
	  $funds = $pos[0]['leverage'] * ($last_price-$buy_price);
	  $conn->query("UPDATE trades SET balance=balance+$funds, close=$last_price WHERE user='$user' and id='$id'");
 }

 	  update_user_balance($user,1,$funds);

	return 1;
}


//Affiliate list
	function user_referrals($id) {
		global $conn;
		$stmt = $conn->query("SELECT `email`, `signed_up` from users where ref_user='$id'");
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}


//List their transactions
//Get Order #, UserID, Date, Comission on the Trade

	function get_ref_table($my_id) {
		global $conn;

		$stmt = $conn->query("SELECT a.id, b.id, b.exec_time, b.user, b.leverage
							  FROM users as a,
								`trades` as b
							  WHERE
								a.ref_user = $my_id
							AND b.user = a.id");

		//$stmt = $conn->query("SELECT * from trades  where user='$id'");
		return $stmt->fetchAll(PDO::FETCH_ASSOC);

	}

	function render_ref_table($table_d) {

		$table = "<table class='table'><tr><td>Transaction ID</td><td>Time</td><td>User ID</td><td>Leverage</td><td>Commission</td></tr>";

		foreach($table_d as $x) {
			$table .= '<tr>';
			$comi = (6*$x['leverage']);
			$total += $comi;

			$table .= "<td>" . $x['id'] . "</td>";
			$table .= "<td>" . $x['exec_time'] . "</td>";
			$table .= "<td>" . $x['user'] . "</td>";
			$table .= "<td>" . $x['leverage'] . "</td>";
			$table .= "<td>$$comi</td>";

			$table .= '</tr>';
			flush();
		}

		$table .= "<tr><td>Total:</td><td></td><td></td><td></td><td>$$total</td>";

		$table .= "</table>";
		return $table;
	}


	//to display ajax popup to ask user to deposit money or learn how to trade
	function is_user_new($id) {
		global $conn;

		if ($conn->query("select funds from users where id= $id")->fetchObject()->funds == 0) {
			if ($conn->query("select count(id) as a from trades where user= $id")->fetchObject()->a == 0) {
				return '1';
			}
		}

		return 0;
	}



	function is_ticker_valid($curr_ticker) {

		if ($curr_ticker == 'BTC') { }
		else if ($curr_ticker == 'ETH') {  }
		else if ($curr_ticker == 'LTC') { }
		else {
			if ($curr_ticker =='AUDCAD') { }
			else if ($curr_ticker =='AUDJPY') { }
			else if ($curr_ticker =='AUDNZD') {  }
			else if ($curr_ticker =='AUDUSD') {  }
			else if ($curr_ticker =='CADCHF') {  }
			else if ($curr_ticker =='CHFJPY') {  }
			else if ($curr_ticker =='EURAUD') {  }
			else if ($curr_ticker =='EURUSD') {  }
			else if ($curr_ticker =='EURCAD') {  }
			else if ($curr_ticker =='EURCHF') {  }
			else if ($curr_ticker =='EURGBP') {  }
			else if ($curr_ticker =='EURJPY') {  }
			else if ($curr_ticker =='EURNZD') {  }
			else if ($curr_ticker =='GBPCAD') {  }
			else if ($curr_ticker =='GBPJPY') {  }
			else if ($curr_ticker =='GBPUSD') {  }
			else if ($curr_ticker =='NZDJPY') {  }
			else if ($curr_ticker =='NZDUSD') {  }
			else if ($curr_ticker =='USDCAD') {  }
			else if ($curr_ticker =='USDCHF') {  }
			else if ($curr_ticker =='USDJPY') {  }
			else if ($curr_ticker == 'GOLD') { $curr_ticker = 'XAUUSD'; }
			else {
				$curr_ticker = "FAIL";
				}
		}

		return $curr_ticker;
	}

?>
