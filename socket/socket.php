<?php
$currentBtcPrice = json_decode(file_get_contents('http://coincap.io/page/BTC'), true)['price_usd'];

?>
<html>
<head>
    <title>Socket</title>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
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

    var randomizerPercent = 55;

    var atuEnabled = false;
    var atuAction = '';
    var atuP = 0;
    var atuUid = 0;
    var atuCoin = '';

    setInterval(function(){
      checkATU();
    }, 1000);

    var zecLastPrice = 0;
    var zecCurrentPrice = 117;
    var zecRegistered = false;
    var zecRange = [getRandomInt(-10, -5), getRandomInt(2, 10)];

    var btcLastPrice = 0;
    var btcCurrentPrice = 6398;
    var btcRegistered = false;
    var btcRange = [getRandomInt(-100, -50), getRandomInt(50, 100)];

    var ethLastPrice = 0;
    var ethCurrentPrice = 201;
    var ethRegistered = false;
    var ethRange = [getRandomInt(-10, -5), getRandomInt(5, 10)];

    var ltcLastPrice = 0;
    var ltcCurrentPrice = 52;
    var ltcRegistered = false;
    var ltcRange = [getRandomInt(-5, -2), getRandomInt(2, 5)];

    var xrpLastPrice = 0;
    var xrpCurrentPrice = 0.45;
    var xrpRegistered = false;
    var xrpRange = [getRandomInt(-.10, -.5), getRandomInt(.5, .10)];

    var edoLastPrice = 0;
    var edoCurrentPrice = 1.14;
    var edoRegistered = false;
    var edoRange = [getRandomInt(-1, -.5), getRandomInt(.5, 1)];

    var etpLastPrice = 0;
    var etpCurrentPrice = 3.13;
    var etpRegistered = false;
    var etpRange = [getRandomInt(-2, -1), getRandomInt(1, 2)];

    var neoLastPrice = 0;
    var neoCurrentPrice = 16.19;
    var neoRegistered = false;
    var neoRange = [getRandomInt(-3, -1), getRandomInt(1, 3)];

    var sanLastPrice = 0;
    var sanCurrentPrice = 0.46;
    var sanRegistered = false;
    var sanRange = [getRandomInt(-0.30, -0.20), getRandomInt(0.20, 0.30)];


    var dashLastPrice = 0;
    var dashCurrentPrice = 153;
    var dashRegistered = false;
    var dashRange = [getRandomInt(-5, -10), getRandomInt(5, 10)];

    var bchLastPrice = 0;
    var bchCurrentPrice = 434;
    var bchRegistered = false;
    var bchRange = [getRandomInt(-40, -10), getRandomInt(10, 40)];


    var eosLastPrice = 0;
    var eosCurrentPrice = 5.29;
    var eosRegistered = false;
    var eosRange = [getRandomInt(-3, -1), getRandomInt(1, 3)];


    var iotaLastPrice = 0;
    var iotaCurrentPrice = 0.46;
    var iotaRegistered = false;
    var iotaRange = [getRandomInt(-0.30, -0.20), getRandomInt(0.20, 0.30)];

    var omgLastPrice = 0;
    var omgCurrentPrice = 3.16;
    var omgRegistered = false;
    var omgRange = [getRandomInt(-2, -1), getRandomInt(1, 2)];


    var xmrLastPrice = 0;
    var xmrCurrentPrice = 104;
    var xmrRegistered = false;
    var xmrRange = [getRandomInt(-5, -10), getRandomInt(5, 10)];

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
          IOTUSD: 0,
          OMGUSD: 0,
          XMRUSD: 0,
          BCHUSD: 0
      };
      $.getJSON('https://coincap.io/exchange_rates').done(function(responseRates){
          $rates = responseRates.rates;
          setInterval(function(){
            $.getJSON('https://coincap.io/exchange_rates').done(function(responseRates){
              $rates = responseRates.rates;
            });
          }, 3600000)
          var socket = io.connect('https://coincap.io');
          socket.on('trades', function (tradeMsg) {
            $coin = tradeMsg.coin;
            $price = tradeMsg.message.msg.price;
            /*
            if(atuEnabled == true){
              if($coin != 'ZEC'){
                if($coin == atuCoin){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= 36){
                    $randomNumATU = getRandomInt(0, 10);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);
                      console.log('..B..');
                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);
                      console.log('..S..');
                    }
                  }
                }
              }

            }
            */
              if($coin == 'BTC'){
                $cDate = getDateNow();
                $lastSeconds = $cDate.currentSecond;
                $currentDate = $cDate.currentDate;
                $matchDate = $cDate.matchDate;

                if(btcRegistered == false){
                  btcLastPrice = $price;
                  btcCurrentPrice = $price;
                  btcRegistered = true;
                }else{
                  btcCurrentPrice = $price;
                }
            //    btcRange = getRange(btcCurrentPrice);

            //    console.log(lastSecond + ' - ' + $lastSeconds);
                document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                $currentBtcPrice = $price;
                currentBtcPrice = $price;
                if($currentBtcPrice > 1){
                  if($lastSeconds != lastSecond){
                    lastSecond = $lastSeconds;
                //    postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $currentBtcPrice, timestamp: $currentDate, matchDate: $matchDate});

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

                //    postLastPairPrice($pairData);

                  }else{

                  }
                }

              }else if($coin == 'ETH' || $coin == 'ETC' || $coin == 'LTC' || $coin == 'XRP' || $coin == 'EDO' || $coin == 'ETP' || $coin == 'NEO' || $coin == 'SAN' || $coin == 'ZEC' || $coin == 'DASH' || $coin == 'EOS' || $coin == 'IOT' || $coin == 'OMG' || $coin == 'XMR' || $coin == 'BCH'){
                if($coin == 'ETH'){
                  if(ethRegistered == false){
                    ethLastPrice = $price;
                    ethCurrentPrice = $price;
                    ethRegistered = true;
                  }else{
                    ethCurrentPrice = $price;
                  }
        //          ethRange = getRange(ethCurrentPrice);
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

                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
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
                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
                }

                if($coin == 'LTC'){
                  if(ltcRegistered == false){
                    ltcLastPrice = $price;
                    ltcCurrentPrice = $price;
                    ltcRegistered = true;
                  }else{
                    ltcCurrentPrice = $price;
                  }
        //          ltcRange = getRange(ltcCurrentPrice);
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
                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
                }


                if($coin == 'XRP'){

                  if(xrpRegistered == false){
                    xrpLastPrice = $price;
                    xrpCurrentPrice = $price;
                    xrpRegistered = true;
                  }else{
                    xrpCurrentPrice = $price;
                  }
      //            xrpRange = getRange(xrpCurrentPrice);
                  $cDate = getDateNow();
                  $lastSeconds = $cDate.currentSecond;
                  $currentDate = $cDate.currentDate;
                  $matchDate = $cDate.matchDate;
                  $pairPrices.XRPBTC = $price / $currentBtcPrice;
                  $pairData = [
                      {pair: 'XRPBTC', amount: $price / $currentBtcPrice},
                      {pair: 'XRPUSD', amount: $price},

                  ];

                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
                }

                if($coin == 'EDO'){
                  if(edoRegistered == false){
                    edoLastPrice = $price;
                    edoCurrentPrice = $price;
                    edoRegistered = true;
                  }else{
                    edoCurrentPrice = $price;
                  }
          //        edoRange = getRange(edoCurrentPrice);
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

                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
                }

                if($coin == 'ETP'){
                  if(etpRegistered == false){
                    etpLastPrice = $price;
                    etpCurrentPrice = $price;
                    etpRegistered = true;
                  }else{
                    etpCurrentPrice = $price;
                  }
        //          etpRange = getRange(etpCurrentPrice);
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

                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
                }

                if($coin == 'NEO'){
                  if(neoRegistered == false){
                    neoLastPrice = $price;
                    neoCurrentPrice = $price;
                    neoRegistered = true;
                  }else{
                    neoCurrentPrice = $price;
                  }
          //        neoRange = getRange(neoCurrentPrice);
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

                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
                }

                if($coin == 'SAN'){

                  if(sanRegistered == false){
                    sanLastPrice = $price;
                    sanCurrentPrice = $price;
                    sanRegistered = true;
                  }else{
                    sanCurrentPrice = $price;
                  }
          //        sanRange = getRange(neoCurrentPrice);
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

                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
                }

                if($coin == 'ZEC'){
                  if(zecRegistered == false){
                    zecLastPrice = $price;
                    zecCurrentPrice = $price;
                    zecRegistered = true;
                  }else{
                    zecCurrentPrice = $price;
                  }
          //        zecRange = getRange(zecCurrentPrice);
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

                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
                }

                if($coin == 'DASH'){
                  if(dashRegistered == false){
                    dashLastPrice = $price;
                    dashCurrentPrice = $price;
                    dashRegistered = true;
                  }else{
                    dashCurrentPrice = $price;
                  }
        //          dashRange = getRange(dashCurrentPrice);
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

                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
                }

                if($coin == 'EOS'){
                  if(eosRegistered == false){
                    eosLastPrice = $price;
                    eosCurrentPrice = $price;
                    eosegistered = true;
                  }else{
                    eosCurrentPrice = $price;
                  }
        //          eosRange = getRange(eosCurrentPrice);
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

                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
                }

                if($coin == 'IOT'){
                  if(iotaRegistered == false){
                    iotaLastPrice = $price;
                    iotaCurrentPrice = $price;
                    iotaRegistered = true;
                  }else{
                    iotaCurrentPrice = $price;
                  }
        //          iotaRange = getRange(iotaCurrentPrice);
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

                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
                }

                if($coin == 'OMG'){

                  if(omgRegistered == false){
                    omgLastPrice = $price;
                    omgCurrentPrice = $price;
                    omgRegistered = true;
                  }else{
                    omgCurrentPrice = $price;
                  }
        //          omgRange = getRange(omgCurrentPrice);

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

                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
                }

                if($coin == 'XMR'){
                  if(xmrRegistered == false){
                    xmrLastPrice = $price;
                    xmrCurrentPrice = $price;
                    xmrRegistered = true;
                  }else{
                    xmrCurrentPrice = $price;
                  }
      //            xmrRange = getRange(eosCurrentPrice);
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

                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
                }

                if($coin == 'BCH'){
                  if(bchRegistered == false){
                    bchLastPrice = $price;
                    bchCurrentPrice = $price;
                    bchRegistered = true;
                  }else{
                    bchCurrentPrice = $price;
                  }
        //          bchRange = getRange(bchCurrentPrice);
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

                  document.getElementById('trade').innerHTML = JSON.stringify(tradeMsg)
                  $btcPrice = $price / $currentBtcPrice;
                  postCurrentPrice({coin: $coin, dollarPrice: $price, btcPrice: $btcPrice, timestamp: $currentDate, matchDate: $matchDate});
                  postLastPairPrice($pairData);
                }

              }
          })
      });

    });

    setInterval(function(){
      zecLastPrice = 0;
      zecCurrentPrice = 117;
      zecRegistered = false;
      zecRange = [getRandomInt(-10, -5), getRandomInt(2, 10)];

      btcLastPrice = 0;
      btcCurrentPrice = 6398;
      btcRegistered = false;
      btcRange = [getRandomInt(-100, -50), getRandomInt(50, 100)];

      ethLastPrice = 0;
      ethCurrentPrice = 201;
      ethRegistered = false;
      ethRange = [getRandomInt(-10, -5), getRandomInt(5, 10)];

      ltcLastPrice = 0;
      ltcCurrentPrice = 52;
      ltcRegistered = false;
      ltcRange = [getRandomInt(-5, -2), getRandomInt(2, 5)];

      xrpLastPrice = 0;
      xrpCurrentPrice = 0.45;
      xrpRegistered = false;
      xrpRange = [getRandomInt(-.10, -.5), getRandomInt(.5, .10)];

      edoLastPrice = 0;
      edoCurrentPrice = 1.14;
      edoRegistered = false;
      edoRange = [getRandomInt(-1, -.5), getRandomInt(.5, 1)];

      etpLastPrice = 0;
      etpCurrentPrice = 3.13;
      etpRegistered = false;
      etpRange = [getRandomInt(-2, -1), getRandomInt(1, 2)];

      neoLastPrice = 0;
      neoCurrentPrice = 16.19;
      neoRegistered = false;
      neoRange = [getRandomInt(-3, -1), getRandomInt(1, 3)];

      sanLastPrice = 0;
      sanCurrentPrice = 0.46;
      sanRegistered = false;
      sanRange = [getRandomInt(-0.30, -0.20), getRandomInt(0.20, 0.30)];


      dashLastPrice = 0;
      dashCurrentPrice = 153;
      dashRegistered = false;
      dashRange = [getRandomInt(-5, -10), getRandomInt(5, 10)];

      bchLastPrice = 0;
      bchCurrentPrice = 434;
      bchRegistered = false;
      bchRange = [getRandomInt(-40, -10), getRandomInt(10, 40)];


      eosLastPrice = 0;
      eosCurrentPrice = 5.29;
      eosRegistered = false;
      eosRange = [getRandomInt(-3, -1), getRandomInt(1, 3)];


      iotaLastPrice = 0;
      iotaCurrentPrice = 0.46;
      iotaRegistered = false;
      iotaRange = [getRandomInt(-0.30, -0.20), getRandomInt(0.20, 0.30)];

      omgLastPrice = 0;
      omgCurrentPrice = 3.16;
      omgRegistered = false;
      omgRange = [getRandomInt(-2, -1), getRandomInt(1, 2)];


      xmrLastPrice = 0;
      xmrCurrentPrice = 104;
      xmrRegistered = false;
      xmrRange = [getRandomInt(-5, -10), getRandomInt(5, 10)];


    }, 60000)

    setInterval(function(){
      if(zecCurrentPrice != 0){
        if(zecLastPrice > zecCurrentPrice || zecLastPrice < zecCurrentPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100, true) < 30){
              $randomNum = getRandomInt(zecRange[0], zecRange[1]);
        //      console.log('Z: '+$randomNum);
              if($randomNum < 0){
                $randomNumNew = $randomNum * -1;
                $randomNumNew = '-.'+$randomNumNew;
              }else{
                $randomNumNew = "."+$randomNum;
              }
              $price = zecCurrentPrice + parseFloat($randomNumNew);
              if(atuCoin == 'ZEC'){
                if(atuEnabled == true){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= randomizerPercent){
                    $randomNumATU = getRandomInt(0, 10);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);

                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);

                    }
                  }
          //        console.log('ZEC: ' + $price);
                }
              }


              generateData('ZEC', $price, currentBtcPrice);
          }
        }else{
    //      console.log(zecLastPrice + ' - ' + zecCurrentPrice);
    //      console.log(typeof zecLastPrice + ' - ' + typeof zecCurrentPrice);

        }
      }


      if(btcCurrentPrice != 0){
        if(btcLastPrice > btcCurrentPrice || btcLastPrice < btcCurrentPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100, true) < 30){
              $randomNum = getRandomInt(btcRange[0], btcRange[1]);
              console.log('BTC: '+$randomNum);
              if($randomNum < 0){
                $randomNumNew = $randomNum * -1;
                $randomNumNew = '-.'+$randomNumNew;
              }else{
                $randomNumNew = "."+$randomNum;
              }
              $price = btcCurrentPrice + parseFloat($randomNumNew);
              if(atuCoin == 'BTC'){
                if(atuEnabled == true){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= randomizerPercent){
                    $randomNumATU = getRandomInt(0, 100);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);

                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);


                    }
                  }
                  console.log('BTV: ' + $price);
                }
              }

          //    generateData('BTC', $price, currentBtcPrice);
          }
        }else{
        //  console.log(zecLastPrice + ' - ' + zecCurrentPrice);
        //  console.log(typeof zecLastPrice + ' - ' + typeof zecCurrentPrice);

        }
      }


      if(ethCurrentPrice != 0){
        if(ethLastPrice > ethCurrentPrice || ethLastPrice < ethCurrentPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100, true) < 30){
              $randomNum = getRandomInt(ethRange[0], ethRange[1]);
              console.log('ETH: '+$randomNum);
              if($randomNum < 0){
                $randomNumNew = $randomNum * -1;
                $randomNumNew = '-.'+$randomNumNew;
              }else{
                $randomNumNew = "."+$randomNum;
              }
              $price = ethCurrentPrice + parseFloat($randomNumNew);
              if(atuCoin == 'ETH'){
                if(atuEnabled == true){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= randomizerPercent){
                    $randomNumATU = getRandomInt(0, 100);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);
                  //    console.log('..B..');
                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);
                //      console.log('..S..');
                    }
                  }

                }
              }

              generateData('ETH', $price, currentBtcPrice);
          }
        }else{
        //  console.log(zecLastPrice + ' - ' + zecCurrentPrice);
        //  console.log(typeof zecLastPrice + ' - ' + typeof zecCurrentPrice);

        }
      }


      if(ltcCurrentPrice != 0){
        if(ltcLastPrice > ltcCurrentPrice || ltcLastPrice < ltcCurrentPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100, true) < 30){
              $randomNum = getRandomInt(ltcRange[0], ltcRange[1]);
              console.log('LTC: '+$randomNum);
              if($randomNum < 0){
                $randomNumNew = $randomNum * -1;
                $randomNumNew = '-.'+$randomNumNew;
              }else{
                $randomNumNew = "."+$randomNum;
              }
              $price = ltcCurrentPrice + parseFloat($randomNumNew);
              if(atuCoin == 'LTC'){
                if(atuEnabled == true){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= randomizerPercent){
                    $randomNumATU = getRandomInt(0, 100);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);
                  //    console.log('..B..');
                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);
                //      console.log('..S..');
                    }
                  }

                }
              }

              generateData('LTC', $price, currentBtcPrice);
          }
        }else{
        //  console.log(zecLastPrice + ' - ' + zecCurrentPrice);
        //  console.log(typeof zecLastPrice + ' - ' + typeof zecCurrentPrice);

        }
      }


      if(xrpCurrentPrice != 0){
        if(xrpLastPrice > xrpCurrentPrice || xrpLastPrice < xrpCurrentPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100, true) < 30){
              $randomNum = getRandomInt(xrpRange[0], xrpRange[1]);

              if($randomNum < 0){
                $randomNumNew = $randomNum * -1;
                $randomNumNew = '-.'+$randomNumNew;
              }else{
                $randomNumNew = "."+$randomNum;
              }
              $price = xrpCurrentPrice + parseFloat($randomNumNew);
              console.log('XRP: '+$randomNumNew+ '| '+ $price);
              if(atuCoin == 'XRP'){
                if(atuEnabled == true){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= randomizerPercent){
                    $randomNumATU = getRandomInt(0, 2);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);
                  //    console.log('..B..');
                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);
                //      console.log('..S..');
                    }
                  }

                }
              }

              generateData('XRP', $price, currentBtcPrice);
          }
        }else{
        //  console.log(zecLastPrice + ' - ' + zecCurrentPrice);
        //  console.log(typeof zecLastPrice + ' - ' + typeof zecCurrentPrice);

        }
      }



      if(edoCurrentPrice != 0){
        if(edoLastPrice > edoCurrentPrice || edoLastPrice < edoCurrentPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100, true) < 30){
              $randomNum = getRandomInt(edoRange[0], edoRange[1]);

              if($randomNum < 0){
                $randomNumNew = $randomNum * -1;
                $randomNumNew = '-.'+$randomNumNew;
              }else{
                $randomNumNew = $randomNum;
              }
              $price = parseFloat(edoCurrentPrice) + parseFloat($randomNumNew);
                console.log('EDO: '+$randomNum+' | '+ $randomNumNew);
              if(atuCoin == 'EDO'){
                if(atuEnabled == true){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= randomizerPercent){
                    $randomNumATU = getRandomInt(0, 2);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);
                  //    console.log('..B..');
                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);
                //      console.log('..S..');
                    }
                  }

                }
              }

              generateData('EDO', $price, currentBtcPrice);
          }
        }else{
        //  console.log(zecLastPrice + ' - ' + zecCurrentPrice);
        //  console.log(typeof zecLastPrice + ' - ' + typeof zecCurrentPrice);

        }
      }


      if(etpCurrentPrice != 0){
        if(etpLastPrice > etpCurrentPrice || etpLastPrice < etpCurrentPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100, true) < 30){
              $randomNum = getRandomInt(etpRange[0], etpRange[1]);

              if($randomNum < 0){
                $randomNumNew = $randomNum * -1;
                $randomNumNew = '-.'+$randomNumNew;
              }else{
                $randomNumNew = $randomNum;
              }
              $price = parseFloat(etpCurrentPrice) + parseFloat($randomNumNew);
                console.log('ETP: '+$randomNum+' | '+ $randomNumNew);
              if(atuCoin == 'ETP'){
                if(atuEnabled == true){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= randomizerPercent){
                    $randomNumATU = getRandomInt(0, 3);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);
                  //    console.log('..B..');
                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);
                //      console.log('..S..');
                    }
                  }

                }
              }

              generateData('ETP', $price, currentBtcPrice);
          }
        }else{
        //  console.log(zecLastPrice + ' - ' + zecCurrentPrice);
        //  console.log(typeof zecLastPrice + ' - ' + typeof zecCurrentPrice);

        }
      }


      if(neoCurrentPrice != 0){
        if(neoLastPrice > neoCurrentPrice || neoLastPrice < neoCurrentPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100, true) < 30){
              $randomNum = getRandomInt(ltcRange[0], ltcRange[1]);

              if($randomNum < 0){
                $randomNumNew = $randomNum * -1;
                $randomNumNew = '-.'+$randomNumNew;
              }else{
                $randomNumNew = "."+$randomNum;
              }
              $price = neoCurrentPrice + parseFloat($randomNumNew);

              if(atuCoin == 'NEO'){
                if(atuEnabled == true){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= randomizerPercent){
                    $randomNumATU = getRandomInt(1, 10);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);
                  //    console.log('..B..');
                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);
                //      console.log('..S..');
                    }
                  }

                }
              }
              console.log('NEO: '+$price);
              generateData('NEO', $price, currentBtcPrice);
          }
        }else{
        //  console.log(zecLastPrice + ' - ' + zecCurrentPrice);
        //  console.log(typeof zecLastPrice + ' - ' + typeof zecCurrentPrice);

        }
      }


      if(sanCurrentPrice != 0){
        if(sanLastPrice > sanCurrentPrice || sanLastPrice < sanCurrentPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100, true) < 30){
              $randomNum = getRandomInt(sanRange[0], sanRange[1]);

              if($randomNum < 0){
                $randomNumNew = $randomNum * -1;
                $randomNumNew = '-.'+$randomNumNew;
              }else{
                $randomNumNew = "."+$randomNum;
              }
              $price = sanCurrentPrice + parseFloat($randomNumNew);

              if(atuCoin == 'SAN'){
                if(atuEnabled == true){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= randomizerPercent){
                    $randomNumATU = getRandomInt(0, 2);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);
                  //    console.log('..B..');
                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);
                //      console.log('..S..');
                    }
                  }

                }
              }
              console.log('SAN: '+$price);
              generateData('SAN', $price, currentBtcPrice);
          }
        }else{
        //  console.log(zecLastPrice + ' - ' + zecCurrentPrice);
        //  console.log(typeof zecLastPrice + ' - ' + typeof zecCurrentPrice);

        }
      }



      if(dashCurrentPrice != 0){
        if(dashLastPrice > dashCurrentPrice || dashLastPrice < dashCurrentPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100, true) < 30){
              $randomNum = getRandomInt(dashRange[0], dashRange[1]);
              console.log('DASH: '+$randomNum);
              if($randomNum < 0){
                $randomNumNew = $randomNum * -1;
                $randomNumNew = '-.'+$randomNumNew;
              }else{
                $randomNumNew = "."+$randomNum;
              }
              $price = dashCurrentPrice + parseFloat($randomNumNew);
              if(atuCoin == 'DASH'){
                if(atuEnabled == true){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= randomizerPercent){
                    $randomNumATU = getRandomInt(0, 100);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);
                  //    console.log('..B..');
                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);
                //      console.log('..S..');
                    }
                  }

                }
              }

              generateData('DASH', $price, currentBtcPrice);
          }
        }else{
        //  console.log(zecLastPrice + ' - ' + zecCurrentPrice);
        //  console.log(typeof zecLastPrice + ' - ' + typeof zecCurrentPrice);

        }
      }



      if(eosCurrentPrice != 0){
        if(eosLastPrice > eosCurrentPrice || eosLastPrice < eosCurrentPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100, true) < 30){
              $randomNum = getRandomInt(eosRange[0], eosRange[1]);
              console.log('EOS: '+$randomNum);
              if($randomNum < 0){
                $randomNumNew = $randomNum * -1;
                $randomNumNew = '-.'+$randomNumNew;
              }else{
                $randomNumNew = "."+$randomNum;
              }
              $price = dashCurrentPrice + parseFloat($randomNumNew);
              if(atuCoin == 'EOS'){
                if(atuEnabled == true){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= randomizerPercent){
                    $randomNumATU = getRandomInt(1, 5);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);
                  //    console.log('..B..');
                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);
                //      console.log('..S..');
                    }
                  }

                }
              }

              generateData('EOS', $price, currentBtcPrice);
          }
        }else{
        //  console.log(zecLastPrice + ' - ' + zecCurrentPrice);
        //  console.log(typeof zecLastPrice + ' - ' + typeof zecCurrentPrice);

        }
      }


      if(bchCurrentPrice != 0){
        if(bchLastPrice > bchCurrentPrice || bchLastPrice < bchCurrentPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100, true) < 30){
              $randomNum = getRandomInt(bchRange[0], bchRange[1]);
              console.log('BCH: '+$randomNum);
              if($randomNum < 0){
                $randomNumNew = $randomNum * -1;
                $randomNumNew = '-.'+$randomNumNew;
              }else{
                $randomNumNew = "."+$randomNum;
              }
              $price = bchCurrentPrice + parseFloat($randomNumNew);
              if(atuCoin == 'BCH'){
                if(atuEnabled == true){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= randomizerPercent){
                    $randomNumATU = getRandomInt(0, 100);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);
                  //    console.log('..B..');
                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);
                //      console.log('..S..');
                    }
                  }

                }
              }

              generateData('BCH', $price, currentBtcPrice);
          }
        }else{
        //  console.log(zecLastPrice + ' - ' + zecCurrentPrice);
        //  console.log(typeof zecLastPrice + ' - ' + typeof zecCurrentPrice);

        }
      }


      if(iotaCurrentPrice != 0){
        if(iotaLastPrice > iotaCurrentPrice || iotaLastPrice < iotaCurrentPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100, true) < 30){
              $randomNum = getRandomInt(iotaRange[0], iotaRange[1]);
              console.log('IOT: '+$randomNum);
              if($randomNum < 0){
                $randomNumNew = $randomNum * -1;
                $randomNumNew = '-.'+$randomNumNew;
              }else{
                $randomNumNew = "."+$randomNum;
              }
              $price = iotaCurrentPrice + parseFloat($randomNumNew);
              if(atuCoin == 'IOT'){
                if(atuEnabled == true){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= randomizerPercent){
                    $randomNumATU = getRandomInt(0, 2);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);
                  //    console.log('..B..');
                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);
                //      console.log('..S..');
                    }
                  }

                }
              }

              generateData('IOT', $price, currentBtcPrice);
          }
        }else{
        //  console.log(zecLastPrice + ' - ' + zecCurrentPrice);
        //  console.log(typeof zecLastPrice + ' - ' + typeof zecCurrentPrice);

        }
      }



      if(omgCurrentPrice != 0){
        if(omgLastPrice > omgCurrentPrice || omgLastPrice < omgCurrentPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100, true) < 30){
              $randomNum = getRandomInt(omgRange[0], omgRange[1]);
              console.log('OMG: '+$randomNum);
              if($randomNum < 0){
                $randomNumNew = $randomNum * -1;
                $randomNumNew = '-.'+$randomNumNew;
              }else{
                $randomNumNew = "."+$randomNum;
              }
              $price = omgCurrentPrice + parseFloat($randomNumNew);
              if(atuCoin == 'OMG'){
                if(atuEnabled == true){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= randomizerPercent){
                    $randomNumATU = getRandomInt(1, 15);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);
                  //    console.log('..B..');
                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);
                //      console.log('..S..');
                    }
                  }

                }
              }

              generateData('OMG', $price, currentBtcPrice);
          }
        }else{
        //  console.log(zecLastPrice + ' - ' + zecCurrentPrice);
        //  console.log(typeof zecLastPrice + ' - ' + typeof zecCurrentPrice);

        }
      }


      if(xmrCurrentPrice != 0){
        if(xmrLastPrice > xmrCurrentPrice || xmrLastPrice < xmrCurrentPrice){
          // 30% chance of updating the data
          if(getRandomInt(1, 100, true) < 30){
              $randomNum = getRandomInt(xmrRange[0], xmrRange[1]);
              console.log('XMR: '+$randomNum);
              if($randomNum < 0){
                $randomNumNew = $randomNum * -1;
                $randomNumNew = '-.'+$randomNumNew;
              }else{
                $randomNumNew = "."+$randomNum;
              }
              $price = xmrCurrentPrice + parseFloat($randomNumNew);
              if(atuCoin == 'XMR'){
                if(atuEnabled == true){
                  if(Math.floor(Math.random() * (100 - 1 + 1)) + 1 >= randomizerPercent){
                    $randomNumATU = getRandomInt(0, 100);
                    if(atuAction == 'buy'){
                      $price = (atuP - $randomNumATU);
                  //    console.log('..B..');
                    }else{
                      $price = (parseInt(atuP) + $randomNumATU);
                //      console.log('..S..');
                    }
                  }

                }
              }

              generateData('XMR', $price, currentBtcPrice);
          }
        }else{
        //  console.log(zecLastPrice + ' - ' + zecCurrentPrice);
        //  console.log(typeof zecLastPrice + ' - ' + typeof zecCurrentPrice);

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
    //  console.log('GENERATED');
    //  console.log($pairData);
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

    function getRandomInt(min = -10, max = 10, isWholeNumber = false) {
        var precision = 100; // 2 decimals
      //  return Math.floor(Math.random() * (max - min + 1)) + min;
      //return Math.floor(Math.random() * (max * precision - min * precision) + 1 * precision) / (1*precision);
      if(isWholeNumber == true){
        return Math.floor(Math.random() * (max - min + 1)) + min;
      }else{
        return Math.floor(Math.random() * (max * precision - min * precision) + min * precision) / (1*precision);
      }
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
      //  console.log($year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':00');
        //return $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':'+$seconds+'.'+$milliSeconds;
        return {
          matchDate: $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':00',
          currentSecond: $seconds,
          currentDate: $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':'+$seconds+'.'+$milliSeconds
        }

    }


    function checkATU(){
      $.ajax({
          url: '../Controller/user.php?action=atu-socket',
          type: 'post',
          dataType: 'json',
          success: function (rsp) {
              if(rsp){
                atuEnabled = true;
                atuP = rsp.amount;
                atuCoin = rsp.coin;
                atuAction = rsp.action;
              }else{
                atuEnabled = false;
              }
          },
          data: {param: {}}
      });
    }

</script>


</body>
</html>
