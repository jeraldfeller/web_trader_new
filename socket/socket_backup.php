<?php
$currentBtcPrice = json_decode(file_get_contents('http://coincap.io/page/BTC'), true)['price_usd'];
$coin = $_GET['coin'];
?>
<html>
<head>
    <title>Socket</title>
    <script src="http://code.jquery.com/jquery-3.2.1.min.js"></script>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" media="all" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>
    <script>
      var lastSecond = '';
    </script>
</head>
<body>
<div class="container-fluid">
  <div id='trade'> open console </div>
</div>

<script type="text/javascript" language="JavaScript">
    $(document).ready(function(){
      $currentBtcPrice = <?=$currentBtcPrice?>;
      $currentCoin = '<?=$coin?>';
      $pairPrices = {
          BTCUSD: $currentBtcPrice,
          BTCCNY: 0,
          BTCEUR: 0,
          BTCJPY: 0,
          ETHBTC: 0,
          ETHUSD: 0,
          ETCBTC: 0,
          LTCUSD: 0,
          LTCCNY: 0,
          LTCEUR: 0,
          LTCJPY: 0,
          XRPBTC: 0
      };
      $.getJSON('http://coincap.io/exchange_rates').done(function(responseRates){
          $rates = responseRates.rates;
          var socket = io.connect('https://coincap.io');
          socket.on('trades', function (tradeMsg) {
            $coin = tradeMsg.coin;
            $price = tradeMsg.message.msg.price;

            if($currentCoin == 'BTC'){
              if($coin == 'BTC'){
                $cDate = getDateNow();
                $lastSeconds = $cDate.currentSecond;
                $currentDate = $cDate.currentDate;
                $matchDate = $cDate.matchDate;
                console.log(lastSecond + ' - ' + $lastSeconds);
                document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                $currentBtcPrice = $price;
                if($currentBtcPrice > 1){
                  if($lastSeconds != lastSecond){
                    lastSecond = $lastSeconds;
                    postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $currentBtcPrice, timestamp: $currentDate, matchDate: $matchDate});

                    $pairPrices.BTCUSD = $price;
                    $pairPrices.BTCCNY = $currentBtcPrice * $rates['CNY'];
                    $pairPrices.BTCEUR = $currentBtcPrice * $rates['EUR'];
                    $pairPrices.BTCJPY = $currentBtcPrice * $rates['JPY'];
                    $pairData = [
                        {pair: 'BTCUSD', amount: $price},
                        {pair: 'BTCCNY', amount: $currentBtcPrice * $rates['CNY']},
                        {pair: 'BTCEUR', amount: $currentBtcPrice * $rates['EUR']},
                        {pair: 'BTCJPY', amount: $currentBtcPrice * $rates['JPY']}

                    ];

                    postLastPairPrice($pairData);

                  }else{
                    console.log('No Update');
                  }
                }
              }
            }

            if($currentCoin == 'ETH'){
              if($coin == 'ETH'){
                $pairPrices.ETHUSD = $price;
                $pairPrices.ETHBTC = $price / $currentBtcPrice;
                $pairData = [
                    {pair: 'ETHUSD', amount: $price},
                    {pair: 'ETHBTC', amount: $price / $currentBtcPrice}

                ];

                document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                $btcPrice = $price / $currentBtcPrice;
                postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                postLastPairPrice($pairData);
              }
            }

            if($currentCoin == 'ETC'){
              if($coin == 'ETC'){
                $pairPrices.ETCBTC = $price / $currentBtcPrice;
                $pairData = [
                    {pair: 'ETCBTC', amount: $price / $currentBtcPrice}
                ];
                document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                $btcPrice = $price / $currentBtcPrice;
                postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                postLastPairPrice($pairData);
              }
            }

            if($currentCoin == 'LTC'){
              if($coin == 'LTC'){
                $pairPrices.LTCUSD = $price;
                $pairPrices.LTCCNY = $price * $rates['CNY'];
                $pairPrices.LTCEUR = $price * $rates['EUR'];
                $pairPrices.LTCJPY = $price * $rates['JPY'];

                $pairData = [
                    {pair: 'LTCUSD', amount: $price},
                    {pair: 'LTCCNY', amount: $price * $rates['CNY']},
                    {pair: 'LTCEUR', amount: $price * $rates['EUR']},
                    {pair: 'LTCJPY', amount: $price * $rates['JPY']}

                ];
                document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                $btcPrice = $price / $currentBtcPrice;
                postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                postLastPairPrice($pairData);
              }
            }

            if($currentCoin == 'XRP'){
              if($coin == 'XRP'){
                $pairPrices.XRPBTC = $price / $currentBtcPrice;
                $pairData = [
                    {pair: 'XRPBTC', amount: $price / $currentBtcPrice}

                ];

                document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                $btcPrice = $price / $currentBtcPrice;
                postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                postLastPairPrice($pairData);
              }
            }



          })
      });

    });

    function postCurrentPrice($data){
      $.ajax({
          url: '../Controller/socket.php?action=post',
          type: 'post',
          dataType: 'json',
          success: function (data) {

          },
          data: {param: JSON.stringify($data)}
      });
    }

    function postLastPairPrice($data){
      $.ajax({
          url: '../Controller/crypto.php?action=update',
          type: 'post',
          dataType: 'json',
          success: function (data) {

          },
          data: {param: JSON.stringify($pairData)}
      });
    }


    function getDateNow(){
        $dateNow = Date.now();
        $date = new Date($dateNow);
        $year = $date.getUTCFullYear();
        $month = $date.getUTCMonth() + 1;
        if($month < 10){
            $month = '0'+$month;
        }
        $day = $date.getUTCDate();
        if($day < 10){
            $day = '0'+$day;
        }
        $hour = $date.getUTCHours();         //
        if($hour < 10){
            $hour = '0'+$hour;
        }
        $minute = $date.getMinutes();
        if($minute < 10){
            $minute = '0'+$minute;
        }

        $seconds = $date.getSeconds();
        $milliSeconds = $date.getMilliseconds();
        console.log($year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':00');
        //return $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':'+$seconds+'.'+$milliSeconds;
        return {
          matchDate: $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':00',
          currentSecond: $seconds,
          currentDate: $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':'+$seconds+'.'+$milliSeconds
        }

    }
</script>


</body>
</html>
