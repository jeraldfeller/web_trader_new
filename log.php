<?
session_start();
if (!isset($_SESSION['id'])) {
	header("Location: index.php?error=No+Login+or+Password!");
	exit();
} else {
		include("module.php");
		$trades = user_trades($_SESSION['id'],'all',100);
}

?><html>
<head><title>Trade Log (Last 100)</title>
 <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script></head>
<body><div class="container">
<?
	echo render_order_table($trades, 1);
flush();


?>
</body>
</html>
