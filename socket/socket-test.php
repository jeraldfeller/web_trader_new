<?php
$currentBtcPrice = json_decode(file_get_contents('http://coincap.io/page/BTC'), true)['price_usd'];

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
    var zecLastPrice = 0;
    var zecCurrentPrice = 0;
    var zecRegistered = false;
    var zecRange = [0, 0];
    var currentBtcPrice = <?=$currentBtcPrice?>;
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
          XRPBTC: 0,
          EDOUSD: 0,
          ETPUSD: 0,
          NEOUSD: 0,
          SANUSD: 0,
          ZECUSD: 0,
          DASHUSD: 0,
          XRPUSD: 0,
          EOSUSD: 0,
          IOTAUSD: 0,
          OMGUSD: 0,
          XMRUSD: 0,
          BCHUSD: 0
      };
      $price = 0;

      $.getJSON('http://coincap.io/exchange_rates').done(function(responseRates){
          $rates = responseRates.rates;
          var socket = io.connect('https://coincap.io');
          socket.on('trades', function (tradeMsg) {
            $coin = tradeMsg.coin;
            $price = tradeMsg.message.msg.price;


              if($coin == 'BTC'){
                $cDate = getDateNow();
                $lastSeconds = $cDate.currentSecond;
                $currentDate = $cDate.currentDate;
                $matchDate = $cDate.matchDate;
            //    console.log(lastSecond + ' - ' + $lastSeconds);
          //      document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                $currentBtcPrice = $price;
                currentBtcPrice = $price;
                if($currentBtcPrice > 1){
                  if($lastSeconds != lastSecond){
                    lastSecond = $lastSeconds;
                  //  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $currentBtcPrice, timestamp: $currentDate, matchDate: $matchDate});

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

                  //  postLastPairPrice($pairData);

                  }else{

                  }
                }

              }else if($coin == 'ETH' || $coin == 'ETC' || $coin == 'LTC' || $coin == 'XRP' || $coin == 'EDO' || $coin == 'ETP' || $coin == 'NEO' || $coin == 'SAN' || $coin == 'ZEC' || $coin == 'DASH' || $coin == 'EOS' || $coin == 'IOTA' || $coin == 'OMG' || $coin == 'XMR' || $coin == 'BCH'){
                if($coin == 'ETH'){
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
                  $pairPrices.ETHUSD = $price;
                  $pairPrices.ETHBTC = $price / $currentBtcPrice;
                  $pairData = [
                      {pair: 'ETHUSD', amount: $price},
                      {pair: 'ETHBTC', amount: $price / $currentBtcPrice}

                  ];

          //        document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
              //    postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
              //    postLastPairPrice($pairData);
                }

                if($coin == 'ETC'){
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
                  $pairPrices.ETCBTC = $price / $currentBtcPrice;
                  $pairData = [
                      {pair: 'ETCBTC', amount: $price / $currentBtcPrice}
                  ];
          //        document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
            //      postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
              //    postLastPairPrice($pairData);
                }

                if($coin == 'LTC'){
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
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
            //      document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
            //      postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
            //      postLastPairPrice($pairData);
                }


                if($coin == 'XRP'){
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
                  $pairPrices.XRPBTC = $price / $currentBtcPrice;
                  $pairData = [
                      {pair: 'XRPBTC', amount: $price / $currentBtcPrice},
                      {pair: 'XRPUSD', amount: $price},

                  ];

          //        document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
          //        postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
          //        postLastPairPrice($pairData);
                }

                if($coin == 'EDO'){
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
                  $pairPrices.EDOUSD = $price;
                  $pairPrices.EDOBTC = $price / $currentBtcPrice;
                  $pairData = [
                      {pair: 'EDOUSD', amount: $price},
                      {pair: 'EDOBTC', amount: $price / $currentBtcPrice}

                  ];

          //        document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
          //        postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
          //        postLastPairPrice($pairData);
                }

                if($coin == 'ETP'){
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
                  $pairPrices.ETPUSD = $price;
                  $pairPrices.ETPBTC = $price / $currentBtcPrice;
                  $pairData = [
                      {pair: 'ETPUSD', amount: $price},
                      {pair: 'ETPBTC', amount: $price / $currentBtcPrice}

                  ];

        //          document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
      //            postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
      //            postLastPairPrice($pairData);
                }

                if($coin == 'NEO'){
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
                  $pairPrices.NEOUSD = $price;
                  $pairPrices.NEOBTC = $price / $currentBtcPrice;
                  $pairData = [
                      {pair: 'NEOUSD', amount: $price},
                      {pair: 'NEOBTC', amount: $price / $currentBtcPrice}

                  ];

          //        document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
          //        postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
          //        postLastPairPrice($pairData);
                }

                if($coin == 'SAN'){
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
                  $pairPrices.SANUSD = $price;
                  $pairPrices.SANBTC = $price / $currentBtcPrice;
                  $pairData = [
                      {pair: 'SANUSD', amount: $price},
                      {pair: 'SANBTC', amount: $price / $currentBtcPrice}

                  ];

            //      document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
            //      postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
            //      postLastPairPrice($pairData);
                }

                if($coin == 'ZEC'){
                  if(zecRegistered == false){
                    zecLastPrice = $price;
                    zecCurrentPrice = $price;
                    zecRegistered = true;
                  }else{
                    zecCurrentPrice = $price;
                  }


                  zecRange = getRange(zecCurrentPrice);
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
                  $pairPrices.ZECUSD = $price;
                  $pairPrices.ZECBTC = $price / $currentBtcPrice;
                  $pairData = [
                      {pair: 'ZECUSD', amount: $price},
                      {pair: 'ZECBTC', amount: $price / $currentBtcPrice}

                  ];

                  //console.log('REAL');
                  //console.log($pairData);

                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
                }

                if($coin == 'DASH'){
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
                  $pairPrices.DASHUSD = $price;
                  $pairPrices.DASHBTC = $price / $currentBtcPrice;
                  $pairData = [
                      {pair: 'DASHUSD', amount: $price},
                      {pair: 'DASHBTC', amount: $price / $currentBtcPrice}

                  ];

          //        document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
            //      postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
              //    postLastPairPrice($pairData);
                }

                if($coin == 'EOS'){
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
                  $pairPrices.EOSUSD = $price;
                  $pairPrices.EOSBTC = $price / $currentBtcPrice;
                  $pairData = [
                      {pair: 'EOSUSD', amount: $price},
                      {pair: 'EOSBTC', amount: $price / $currentBtcPrice}

                  ];

              //    document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
        //          postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
        //          postLastPairPrice($pairData);
                }

                if($coin == 'IOT'){
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
                  $pairPrices.IOTUSD = $price;
                  $pairPrices.IOTBTC = $price / $currentBtcPrice;
                  $pairData = [
                      {pair: 'IOTUSD', amount: $price},
                      {pair: 'IOTBTC', amount: $price / $currentBtcPrice}

                  ];

              //    document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
          //        postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
          //      postLastPairPrice($pairData);
                }

                if($coin == 'OMG'){
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
                  $pairPrices.OMGUSD = $price;
                  $pairPrices.OMGBTC = $price / $currentBtcPrice;
                  $pairData = [
                      {pair: 'OMGUSD', amount: $price},
                      {pair: 'OMGBTC', amount: $price / $currentBtcPrice}

                  ];

              //    document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
      //            postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
        //          postLastPairPrice($pairData);
                }

                if($coin == 'XMR'){
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
                  $pairPrices.XMRUSD = $price;
                  $pairPrices.XMRBTC = $price / $currentBtcPrice;
                  $pairData = [
                      {pair: 'XMRUSD', amount: $price},
                      {pair: 'XMRBTC', amount: $price / $currentBtcPrice}

                  ];

              //    document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
          //        postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
          //        postLastPairPrice($pairData);
                }

                if($coin == 'BCH'){
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
                  $pairPrices.BCHUSD = $price;
                  $pairPrices.BCHBTC = $price / $currentBtcPrice;
                  $pairData = [
                      {pair: 'BCHUSD', amount: $price},
                      {pair: 'BCHBTC', amount: $price / $currentBtcPrice}

                  ];

              //    document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
            //      postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
          //        postLastPairPrice($pairData);
                }

              }
          })
      });

    });


    setInterval(function(){
      if(zecCurrentPrice != 0){
        if(zecCurrentPrice == zecLastPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100) < 30){
      //        console.log(zecRange);
              $randomNum = getRandomInt(zecRange[0], zecRange[1]);
              $price = zecCurrentPrice + $randomNum;
              generateData('ZEC', $price, currentBtcPrice);
          }
        }else{
    //      console.log(zecLastPrice + ' - ' + zecCurrentPrice);
        }
      }
    }, 1000);

    function getRange($price){
      if($price > 99 && $price < 1000){
        $range = [-10, 10];
      }else{
        $range = [-1, 2];
      }

      return $range;
    }

    function generateData($coin, $price, $currentBtcPrice){
      $coinUsd = $coin+'USD';
      $coinBtc = $coin+'BTC';
      $cDate = getDateNow();
      $lastSeconds = $cDate.currentSecond;
      $currentDate = $cDate.currentDate;
      $matchDate = $cDate.matchDate;
      $pairPrices.$coinUsd = $price;
      $pairPrices.$coinBtc = $price / $currentBtcPrice;
      $pairData = [
          {pair: $coinUsd, amount: $price},
          {pair: $coinBtc, amount: $price / $currentBtcPrice}

      ];
//      console.log('GENERATED');
//      console.log($pairData);
      document.getElementById('trade').innerHTML = 'UPDATE ZEC';
      $btcPrice = $price / $currentBtcPrice;
      postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
      postLastPairPrice($pairData);
    }

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

    function getRandomInt(min = -10, max = 10) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
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
      //  console.log($year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':00');
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
