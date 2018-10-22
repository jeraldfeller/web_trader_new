<?php
include 'includes/regular/require.php';
require 'Model/History.php';
require 'Model/Users.php';

$users = new Users();
$history = new History();
$dateRange = '1day';
if(isset($_GET['coin'])){
    $filter = $_GET['coin'];
}else{
    $filter = 'BTC';
}
if(isset($_GET['currency'])){
    $currency = $_GET['currency'];
}else{
    $currency = 'USD';
}
$currencyPair = 'USD';
if($filter == 'XRP'){
    $currencyPair = 'BTC';
}

$data = json_decode($history->getCoinHistory(strtolower($filter), 'desktop'), true);
//$data = json_decode(file_get_contents('https://api.hitbtc.com/api/2/public/candles/'.$filter.$currencyPair.'?period=M1&limit=120'), true);
if($filter == 'BTC'){
    $currentBtcPrice = $data[count($data) - 1]['close'];

}else{
    $currentBtcPrice = json_decode(file_get_contents('http://coincap.io/page/BTC'), true)['price_usd'];
}
$currentCoinPrice = $data[count($data) - 1]['close'];
$dataFiltered = json_encode($data);


$user = $users->getUserDataById($userData['id']);
if($user['dollar_amount'] > 0 ){
  $funds = number_format($user['dollar_amount'] / $currentBtcPrice * 10000, 2);
}else{
  $funds = 0;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZoloTrader</title>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="//cdn.rawgit.com/noelboss/featherlight/1.7.9/release/featherlight.min.css" type="text/css" rel="stylesheet" />
    <script src="//cdn.rawgit.com/noelboss/featherlight/1.7.9/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
    <script src="https://www.amcharts.com/lib/3/serial.js"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" media="all" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>
    <style>
        #chartdiv {
            width		: 100%;
            height		: 500px;
            font-size	: 11px;
        }
        .center-block {
            margin-left:auto;
            margin-right:auto;
            display:block;
        }
        .spacer {
            margin-top: 12px; /* define margin as you see fit */
        }
        .text-bold{
            font-weight: bold;
        }
        .amcharts-guide tspan{
            display: block;
            width: 100%;
            height: 44px;
            padding: 5px 24px;
            font-size: 18px;
            line-height: 2;
            color: #fff;
            background-color: #222222;
            background-image: none;
            border: 1px solid #0f0f0f;
            border-radius: 4px;
            text-decoration: overline underline;
            -webkit-text-fill-color: green; /* Will override color (regardless of order) */
            -webkit-text-stroke-width: 20px;
            -webkit-text-stroke-color: black;
            font-weight: bold;
        }
        .amcharts-export-menu {
            display: none;
        }
        .balance-container{
          border-style: double;
          border-color: #3daf43;
          padding: 10px;
        }
    </style>
    <script>
        var zoomEvents = [];
        if (screen.width <= 768) {
            location.href = 'go_mobile.php';
        }
    </script>
</head>
<body>
<div class="container" style="padding-top:1%;">

    <strong>
        <font color='#13D384' size='5'>Zolo</font>
        <font size='5'>Trader</font>
    </strong> |  |
    <a href='log.php?v='>Trade History</a> |
    <a href='affiliate'>Affiliate</a> | <a href='logout.php'>Logout</a><br />

</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 spacer">
            <div class="col-md-7">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo $filter.$currency; ?> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#" class="select_coin" data-coin="BTC" data-pair="USD">BTCUSD</a></li>
                        <li><a href="#" class="select_coin" data-coin="BTC" data-pair="CNY">BTCCNY</a></li>
                        <li><a href="#" class="select_coin" data-coin="BTC" data-pair="EUR">BTCEUR</a></li>
                        <li><a href="#" class="select_coin" data-coin="BTC" data-pair="JPY">BTCJPY</a></li>
                        <li><a href="#" class="select_coin" data-coin="ETH" data-pair="USD">ETHUSD</a></li>
                        <li><a href="#" class="select_coin" data-coin="ETH" data-pair="BTC">ETHBTC</a></li>
                        <li><a href="#" class="select_coin" data-coin="ETC" data-pair="BTC">ETCBTC</a></li>
                        <li><a href="#" class="select_coin" data-coin="LTC" data-pair="USD">LTCUSD</a></li>
                        <li><a href="#" class="select_coin" data-coin="LTC" data-pair="CNY">LTCCNY</a></li>
                        <li><a href="#" class="select_coin" data-coin="LTC" data-pair="EUR">LTCEUR</a></li>
                        <li><a href="#" class="select_coin" data-coin="LTC" data-pair="JPY">LTCJPY</a></li>
                        <li><a href="#" class="select_coin" data-coin="XRP" data-pair="BTC">XRPBTC</a></li>
                    </ul>
                </div>
                <button class="btn btn-primary expires selected-expire" data-value="60">1 min</button>
                <button class="btn btn-default expires" data-value="120">2 min</button>
                <button class="btn btn-default expires" data-value="180">3 min</button>
                <button class="btn btn-default expires" data-value="300">5 min</button>
                <button class="btn btn-default expires" data-value="900">15 min</button>
                <button class="btn btn-default expires" data-value="1800">30 min</button>
                <button class="btn btn-default expires" data-value="3600">60 min</button>
            </div>
            <div class="col-md-5">
                <span class="text-bold pull-right balance-container">Balance: <span class="userFunds"><?php echo $funds; ?></span></span>
            </div>
        </div>
    </div>
    <div id="chartdiv" style="height: 800px; min-width: 1000px"></div>

    <div class="row">
        <div class="col-md-12">
          <div class="col-md-12 text-center">
              <span style="font-size: 24px;"><span class="current_price"></span><span class="percent_status"><i class="fa"></i></span></span>
          </div>
            <div class="col-md-6 text-right">
              <div class="col-md-10 pull-right" style="border: 2px solid #959191; border-radius: 5px; padding: 5px;">
                <div class="col-md-7">
                  <div class="input-group" >
                      <input type="hidden" id="current_price">
                      <span class="input-group-addon">Trade Amount</span>
                      <input type="hidden" class="max-quadpips">
                      <input type="hidden" class="max-dollar-price">
                      <input id="leverage" type="number" value='0.5' min='0.1' class="form-control" aria-label="3.5">
                  </div>
                </div>
                <div class="col-md-5" style="text-align: left; margin-top: 5px;">
                  = <span class=""><img src="assets/img/btc-logo.ico" style="width: 25px; height: 25px;"></span><span class="current-exchange-price text-bold"></span>
                </div>
              </div>

            </div>


            <div class="col-md-6">
              <div class="col-md-10" style="border: 2px solid #959191; border-radius: 5px; padding: 5px; text-align: center;">
                <button class="btn btn-success btn-sm" style="font-size: 15px; font-weight: bold; width: 45%;" id='buy'>Buy</button>
                <button class="btn btn-danger btn-sm" style="font-size: 15px; font-weight: bold; width: 45%;" id='sell'>Sell</button>
              </div>
            </div>

            <div class="col-md-12 spacer"></div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <thead>
                <tr>
                    <th>Type</th>
                    <th>Pair</th>
                    <th>Timer</th>
                    <th>Status</th>
                    <th>Investment</th>
                    <th>Payout</th>
                </tr>
                </thead>
                <tbody class="trade-log">

                </tbody>
            </table>
        </div>
    </div>

</div>


<script type="text/javascript" language="JavaScript">
    $(document).ready(function(){

        $tradeIndex = 0;

        $isLoadedUpdate = false;
        $isLoadedAdd = false;
        $btcUsd = <?php echo $currentBtcPrice; ?>;
        $currentBtcPrice = <?php echo $currentBtcPrice; ?>;
        $currentCoinPrice = <?php echo $currentCoinPrice; ?>;
        $cur = '<?php echo $currency; ?>';
        $currencyPair = '<?php echo $currencyPair; ?>';
        $userId = <?php echo $userData['id']; ?>;
        $coin = '<?php echo $filter; ?>';
        $cryptoCoin = '<?php echo $filter; ?>';
        $maxQuadPrice = parseFloat(250 / $currentBtcPrice * 10000);
        $('.max-quadpips').val($maxQuadPrice.toFixed(2));
        // Current Curreny Prices
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
        $.getJSON('Controller/crypto.php?action=get').done(function(pairs){
            $.each(pairs, function (index, key) {
                if(key.pair == 'BTCUSD'){
                    $pairPrices.BTCUSD = key.price;
                }else if(key.pair == 'BTCCNY'){
                    $pairPrices.BTCCNY = key.price;
                }else if(key.pair == 'BTCEUR'){
                    $pairPrices.BTCEUR = key.price;
                }else if(key.pair == 'BTCJPY'){
                    $pairPrices.BTCJPY = key.price;
                }else if(key.pair == 'ETHBTC'){
                    $pairPrices.ETHBTC = key.price;
                }else if(key.pair == 'ETHUSD'){
                    $pairPrices.ETHUSD = key.price;
                }else if(key.pair == 'ETCBTC'){
                    $pairPrices.ETCBTC = key.price;
                }else if(key.pair == 'LTCUSD'){
                    $pairPrices.LTCUSD = key.price;
                }else if(key.pair == 'LTCCNY'){
                    $pairPrices.LTCCNY = key.price;
                }else if(key.pair == 'LTCEUR'){
                    $pairPrices.LTCEUR = key.price;
                }else if(key.pair == 'LTCJPY'){
                    $pairPrices.LTCJPY = key.price;
                }else if(key.pair == 'XRPBTC'){
                    $pairPrices.XRPBTC = key.price;
                }

            });
        })


            $('#leverage').on('change', function(){
              $maxQuadPips = parseFloat($('.max-quadpips').val());
              $curFunds = parseFloat($('.userFunds').text());

              if($(this).val() > $curFunds){
                alert('You dont have enough funds');
                $(this).val($curFunds.toFixed(2));
              }

              if($(this).val() > $maxQuadPips){
                  $(this).val($maxQuadPips.toFixed(2));
              }

            $currentExcPrice = $(this).val() / 10000;
            $('.current-exchange-price').text($currentExcPrice.toFixed(6));
        });


        $('.expires').click(function(){
          //$min = $(this).attr('data-value');
          //zoomChart($min, 1);
            $('.expires').each(function(){
                $(this).removeClass('selected-expire').removeClass('btn-primary').addClass('btn-default');
            })

            $(this).removeClass('btn-default').addClass('selected-expire').addClass('btn-primary');
        });


        $('#current_price').val($currentCoinPrice);
        $curExcPrice = $('#leverage').val() / $currentBtcPrice;
        $('.current-exchange-price').text($curExcPrice.toFixed(6));
        $('.select_coin').click(function(){
            location.href = 'go.php?coin='+$(this).attr('data-coin')+'&currency='+$(this).attr('data-pair');
        });
        $('.current_price').text($currentCoinPrice);
        $.getJSON('https://coincap.io/exchange_rates').done(function(responseRates){
            $rates = responseRates.rates;
            $dateRange = '1day';
            $filter = '<?php echo $filter; ?>';
            if($cur != 'BTC'){
                $curCoinPrice = $currentCoinPrice * $rates[$cur];
                $('#current_price').val($curCoinPrice);
                if($filter == 'XRP' || $filter == 'ETC'){
                  $('.current_price').text($curCoinPrice.toFixed(6));
                }else{
                  $('.current_price').text($curCoinPrice.toFixed(2));
                }

            }
            $tickHistory = <?php echo $dataFiltered; ?>;
            $data = [];
            for($x = 0; $x < $tickHistory.length; $x++){
                $tickDate = $tickHistory[$x]['timestamp'];
                $tickOpen = $tickHistory[$x]['open'];
                $tickHigh = $tickHistory[$x]['max'];
                $tickLow = $tickHistory[$x]['min'];
                $tickClose = $tickHistory[$x]['close'];
                $date = new Date($tickDate);
                $year = $date.getFullYear();
                $month = $date.getMonth() + 1;
                if($month < 10){
                    $month = '0'+$month;
                }
                $day = $date.getDate();
                if($day < 10){
                    $day = '0'+$day;
                }
                $hour = $date.getHours();         //
                if($hour < 10){
                    $hour = '0'+$hour;
                }
                $minute = $date.getMinutes();
                if($minute < 10){
                    $minute = '0'+$minute;
                }
                $seconds = $date.getSeconds();
                if($cur == 'BTC'){
                    if($currencyPair == 'BTC'){


                          $open = parseFloat($tickOpen);
                          $high = parseFloat($tickHigh);
                          $low = parseFloat($tickLow);
                          $close = parseFloat($tickClose);

                    }else{
                        $open = parseFloat($tickOpen / $currentBtcPrice);
                        $high = parseFloat($tickHigh / $currentBtcPrice);
                        $low = parseFloat($tickLow / $currentBtcPrice);
                        $close = parseFloat($tickClose / $currentBtcPrice);
                    }

                }else{
                    $open = $tickOpen * $rates[$cur];
                    $high = $tickHigh * $rates[$cur];
                    $low = $tickLow * $rates[$cur];
                    $close = $tickClose * $rates[$cur];
                }
                $data.push(
                    {
                        "date" : $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':00',
                        "open" : $open.toFixed(6),
                        "high" : $high.toFixed(6),
                        "low"  : $low.toFixed(6),
                        "close" : $close.toFixed(6)
                    }

                );

            }

            //$data.splice(0, 40);
            // render chart

            if($filter == 'XRP'){
              $tickValue = $currentCoinPrice

            }else{
              $tickValue = ($cur == 'BTC' ? <?php echo $currentCoinPrice; ?> / $currentBtcPrice : <?php echo $currentCoinPrice; ?> * $rates[$cur]);

            }
            if($filter == 'XRP' || $filter == 'ETC'){
              $tickValue = $tickValue.toFixed(6);
              console.log($tickValue);
            }else{
              $tickValue = $tickValue.toFixed(4);
            }
            $chart = AmCharts.makeChart( "chartdiv", {
                "type": "serial",
                "theme": "light",
                //"dataDateFormat":"YYYY-MM-DD JJ:NN:SS",
                "dataProvider": $data,
                "valueAxes": [ {
                    "position": "right",
                    "guides": [ {
                        "value": $tickValue,
                        "label": $tickValue,
                        "position": "right",
                        "dashLength": 0,
                        "axisThickness": 1,
                        "fillColor": "#000",
                        "axisAlpha": 1,
                        "fillAlpha": 1,
                        "color": "#000",
                        "fontSize": 16,
                        "backgroundColor": "#008D00",
                    },

                    ],

                } ],
                "graphs": [ {
                    "id": "g1",
                    "proCandlesticks": false,
                    "balloonText": "Open:<b>[[open]]</b><br>Low:<b>[[low]]</b><br>High:<b>[[high]]</b><br>Close:<b>[[close]]</b><br>",
                    "closeField": "close",
                    "fillColors": "#3daf43",
                    "highField": "high",
                    "lineColor": "#3daf43",
                    "lineAlpha": 1,
                    "lowField": "low",
                    "fillAlphas": .9,
                    "negativeFillColors": "#db4c3c",
                    "negativeLineColor": "#db4c3c",
                    "openField": "open",
                    "title": "Price:",
                    "type": "candlestick",
                    "valueField": "close"
                } ],
                "valueLineEnabled": true,
                "valueLineBalloonEnabled": true,
                "chartScrollbar": {
                    "graph": "g1",
                    "graphType": "line",
                    "scrollbarHeight": 30
                },
                "chartCursor": {
                    "valueLineEnabled": true,
                    "valueLineBalloonEnabled": true,
                    "categoryBalloonDateFormat": "JJ:NN, DD MMMM",
                    "cursorPosition": "mouse",
                    "selectWithoutZooming": true,
                    "listeners": [{
                        "event": "selected",
                        "method": function(event) {
                            var start = new Date(event.start);
                            var end = new Date(event.end);
                            document.getElementById('info').innerHTML = "Selected: " + start.toLocaleTimeString() + " -- " + end.toLocaleTimeString()
                        }
                    }]
                },
                "categoryField": "date",
                "categoryAxis": {
                    "minPeriod": "mm",
                    "parseDates": true,
                },
                "dataDateFormat": "YYYY-MM-DD HH:NN:SS",
                "export": {
                    "enabled": true
                }
            } );

            $chart.addListener( "rendered", zoomChart );
            //$chart.addListener("zoomed", handleZoom);

            zoomChart(190, 1);
            $.ajax({
                url: 'Controller/user.php?action=pending-trades',
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    for(var i = 0; i < data.length; i++){
                        $amount = parseFloat(data[i].open);
                        if(data[i].type == 'sell'){
                            $fillColor = '#DB333C';
                            $typeText = 'Sell';
                        }else{
                            $fillColor = '#008D00';
                            $typeText = 'Buy';
                        }

                        $chart.valueAxes[0].guides.push(
                            {
                                "value": $amount.toFixed(2),
                                "label": $amount.toFixed(2),
                                "position": "right",
                                "dashLength": 0,
                                "axisThickness": 0.1,
                                "fillColor": $fillColor,
                                "axisAlpha": 1,
                                "fillAlpha": 1,
                                "color": "#008D00",
                                "fontSize": 16,
                                "backgroundColor": $fillColor
                            }
                        );
                        $startIndex = $chart.startIndex;
                        $endIndex = $chart.endIndex - $chart.dataProvider.length;
                        $chart.validateData();
                        zoomChart($startIndex, $endIndex);
                        $trade = '<tr class="trade-index-'+$tradeIndex+'" data-type="'+$typeText.toLowerCase()+'" data-index="'+$tradeIndex+'"><td>'+$typeText+'</td><td>'+data[i].pair+'</td><td><span class="timer active-timer"></span></td><td><button class="btn btn-xs btn-default trade-status">LIVE</button></td><td><span class="trade-investment">'+data[i].leverage+'</span></td><td><span class="trade-payout">'+data[i].payout+'</span></td></tr>';
                        $('.trade-log').prepend($trade);
                        $tradeTransaction.push(
                            {
                                index: $tradeIndex,
                                time: data[i].remaining,
                                amount: data[i].open,
                                leverage: data[i].leverage,
                                payout: data[i].payout,
                                winAmount: data[i].leverage * .70,
                                type: data[i].type,
                                status: 'live',
                                tradeId: data[i].id,
                                pair: data[i].pair,
                                remainder: 5
                            }
                        );
                        $tradeIndex++;
                    }

                },
                data: {param: JSON.stringify({userId: $userId})}
            });


// this method is called when chart is first inited as we listen for "dataUpdated" event

            // get pending trades




            $('.amcharts-chart-div a').css('display', 'none');

            /*
             function handleZoom(event) {
             $endIndex = event.endIndex;
             $startIndex = event.startIndex;
             zoomEvents.push([$startIndex, $endIndex]);
             //  console.log(event);
             }
             */


            $group = [];

            var socket = io.connect('https://coincap.io');
            socket.on('trades', function (tradeMsg) {

                $coin = tradeMsg.coin;
                $price = tradeMsg.message.msg.price;
                if($coin == 'BTC'){
                    $currentBtcPrice = $price;
                    $maxQuadPrice = parseFloat(250 / $currentBtcPrice * 10000);
                    $('.max-quadpips').val($maxQuadPrice.toFixed(2));
                    $curExcPrice = $('#leverage').val() / $currentBtcPrice;
                    $('.current-exchange-price').text($curExcPrice.toFixed(6));
                    $btcUsd = $price;
                    $pairPrices.BTCUSD = $price;
                    $pairPrices.BTCCNY = $btcUsd * $rates['CNY'];
                    $pairPrices.BTCEUR = $btcUsd * $rates['EUR'];
                    $pairPrices.BTCJPY = $btcUsd * $rates['JPY'];

                }else if($coin == 'ETH'){
                    $pairPrices.ETHUSD = $price;
                    $pairPrices.ETHBTC = $price / $currentBtcPrice;

                }else if($coin == 'ETC'){
                    $pairPrices.ETCBTC = $price / $currentBtcPrice;

                }else if($coin == 'LTC'){
                    $pairPrices.LTCUSD = $price;
                    $pairPrices.LTCCNY = $price * $rates['CNY'];
                    $pairPrices.LTCEUR = $price * $rates['EUR'];
                    $pairPrices.LTCJPY = $price * $rates['JPY'];




                }else if($coin == 'XRP'){
                    //$pairPrices.XRPBTC = $price / $currentBtcPrice;
                    $pairPrices.XRPBTC = $price;

                }
                if($filter == $coin){
                    $beforePrice = parseFloat($('#current_price').val());
                    if($filter =='XRP'){
                        $dPrice = $price;
                    }else{
                        $dPrice = ($cur == 'BTC' ? $price / $currentBtcPrice : $price * $rates[$cur]);
                    }

                    if($filter == 'XRP' || $filter == 'ETC'){
                      $('#current_price').val($dPrice.toFixed(6));
                      $('.current_price').text($dPrice.toFixed(6));
                    }else{
                      $('#current_price').val($dPrice.toFixed(2));
                      $('.current_price').text($dPrice.toFixed(2));
                    }

                    if($price > $beforePrice){

                        $('.percent_status').removeClass('text-danger').addClass('text-success');
                        $('.percent_status').find('i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
                    }else{

                        $('.percent_status').removeClass('text-success').addClass('text-danger');
                        $('.percent_status').find('i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
                    }
                    $currentDate = getDateNow();
                    // Get Last Trade
                    $curentDataLength = $chart.dataProvider.length;

                    $lastDate = $chart.dataProvider[$curentDataLength - 1].date;
                    //   console.log('Current Date: ' + $currentDate + ' | Last Date: ' + $lastDate);

                    if($currentDate == $lastDate){
                        $price = tradeMsg.message.msg.price;
                        $group.push($price);

                        $ohlcData = computeOHLC($group);

                        if($cur == 'BTC'){
                            if($currencyPair == 'BTC'){
                                $open = parseFloat($tickOpen);
                                $high = parseFloat($tickHigh);
                                $low = parseFloat($tickLow);
                                $close = parseFloat($tickClose);
                            }else{
                                $open = parseFloat($tickOpen / $currentBtcPrice);
                                $high = parseFloat($tickHigh / $currentBtcPrice);
                                $low = parseFloat($tickLow / $currentBtcPrice);
                                $close = parseFloat($tickClose / $currentBtcPrice);
                            }
                        }else{
                            $open = parseFloat($ohlcData[0] * $rates[$cur]);
                            $high = parseFloat($ohlcData[1] * $rates[$cur]);
                            $low = parseFloat($ohlcData[2] * $rates[$cur]);
                            $close = parseFloat($ohlcData[3] * $rates[$cur]);


                        }

                        $chart.dataProvider[$curentDataLength - 1] = {
                            "date" : $lastDate,
                            "open" : $open.toFixed(6),
                            "high" : $high.toFixed(6),
                            "low"  : $low.toFixed(6),
                            "close" : $close.toFixed(6)
                        }



                    }else{

                        $group = [];

                        $group.push($price);

                        $ohlcData = computeOHLC($group);

                        if($cur == 'BTC'){
                            if($currencyPair == 'BTC'){
                                $open = parseFloat($tickOpen);
                                $high = parseFloat($tickHigh);
                                $low = parseFloat($tickLow);
                                $close = parseFloat($tickClose);
                            }else{
                                $open = parseFloat($tickOpen / $currentBtcPrice);
                                $high = parseFloat($tickHigh / $currentBtcPrice);
                                $low = parseFloat($tickLow / $currentBtcPrice);
                                $close = parseFloat($tickClose / $currentBtcPrice);
                            }
                        }else{
                            $open = parseFloat($ohlcData[0] * $rates[$cur]);
                            $high = parseFloat($ohlcData[1] * $rates[$cur]);
                            $low = parseFloat($ohlcData[2] * $rates[$cur]);
                            $close = parseFloat($ohlcData[3] * $rates[$cur]);
                        }
                        $chart.dataProvider.shift(); // removes first index
                        $chart.dataProvider.push( {
                            "date" : $currentDate,
                            "open" : $open.toFixed(6),
                            "high" : $high.toFixed(6),
                            "low"  : $low.toFixed(6),
                            "close" : $close.toFixed(6)
                        } );



                    }

                    if($filter == 'XRP'){
                      $chart.valueAxes[0].guides[0].label = $dPrice.toFixed(4);
                      $chart.valueAxes[0].guides[0].value = $dPrice.toFixed(4);
                    }else{
                      $chart.valueAxes[0].guides[0].label = $dPrice.toFixed(2);
                      $chart.valueAxes[0].guides[0].value = $dPrice.toFixed(2);
                    }

                    $startIndex = $chart.startIndex;
                    $endIndex = $chart.endIndex - $chart.dataProvider.length;
                    $chart.validateData();
                    $('.amcharts-chart-div a').css('display', 'none');
                    zoomChart($startIndex, $endIndex);

                }
            $('.amcharts-chart-div a').css('display', 'none');

            });



            function computeOHLC(values){
                $low = Math.min.apply(Math, values);
                $high = Math.max.apply(Math, values);
                $open = values[0];
                $close = values[values.length - 1];

                return [$open, $high, $low, $close];
            }




        });


        // Buy & Sell, Arrows
        $arrowTick = [];

        $tradeTransaction = [];
        $('#buy').click(function(){
            $amount = parseFloat($('#current_price').val());
            $leverage = parseFloat($('#leverage').val());
            $payout = ($leverage * .70) + $leverage;
            $winAmount = $leverage * .70;
            $chart.valueAxes[0].guides.push(
                {
                    "value": $amount.toFixed(2),
                    "label": $amount.toFixed(2),
                    "position": "right",
                    "dashLength": 0,
                    "axisThickness": 0.1,
                    "fillColor": "#008D00",
                    "axisAlpha": 1,
                    "fillAlpha": 1,
                    "color": "#008D00",
                    "fontSize": 16,
                    "backgroundColor": "#008D00"
                }
            );
            $startIndex = $chart.startIndex;
            $endIndex = $chart.endIndex - $chart.dataProvider.length;
            $chart.validateData();
            zoomChart($startIndex, $endIndex);
            $('.amcharts-chart-div a').css('display', 'none');
            //validate();
            //insert_order('buy');
            $trade = '<tr class="trade-index-'+$tradeIndex+'" data-type="buy" data-index="'+$tradeIndex+'"><td>Buy</td><td>'+$cryptoCoin+$cur+'</td><td><span class="timer active-timer"></span></td><td><button class="btn btn-xs btn-default trade-status">LIVE</button></td><td><span class="trade-investment">'+$leverage+'</span></td><td><span class="trade-payout">'+$payout+'</span></td></tr>';
            $('.trade-log').prepend($trade);

            $logTradeData = {
                expires: parseInt($('.selected-expire').attr('data-value')),
                type: 'buy',
                status: 'live',
                amount: $amount,
                leverage: $leverage,
                payout: $payout,
                winAmount: $winAmount,
                ticker: $filter,
                userId: $userId,
                pair: $cryptoCoin+$cur
            };
            $.ajax({
                url: 'Controller/user.php?action=register-trade',
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    $tradeTransaction.push(
                        {
                            index: $tradeIndex,
                            time: parseInt($('.selected-expire').attr('data-value')),
                            amount: $amount,
                            leverage: $leverage,
                            payout: $payout,
                            winAmount: $winAmount,
                            type: 'buy',
                            status: 'live',
                            tradeId: data.tradeId,
                            pair: $coin+$cur,
                            remainder: 5
                        }
                    );
                    $tradeIndex++;
                },
                data: {param: JSON.stringify($logTradeData)}
            });


            //countDownTimer(0, 'buy');

        });

        $('#sell').click(function(){
            $amount = parseFloat($('#current_price').val());
            $chart.valueAxes[0].guides.push(
                {
                    "value": $amount.toFixed(2),
                    "label": $amount.toFixed(2),
                    "position": "right",
                    "dashLength": 0,
                    "axisThickness": 0.1,
                    "fillColor": "#DB333C",
                    "axisAlpha": 1,
                    "fillAlpha": 1,
                    "color": "#008D00",
                    "fontSize": 16,
                    "backgroundColor": "#DB333C"
                }
            );
            $startIndex = $chart.startIndex;
            $endIndex = $chart.endIndex - $chart.dataProvider.length;
            $chart.validateData();
            zoomChart($startIndex, $endIndex);
            $('.amcharts-chart-div a').css('display', 'none');
            $leverage = parseFloat($('#leverage').val());
            $payout = ($leverage * .70) + $leverage;
            $winAmount = $leverage * .70;
            $trade = '<tr class="trade-index-'+$tradeIndex+'" data-type="sell" data-index="'+$tradeIndex+'"><td>Sell</td><td>'+$cryptoCoin+$cur+'</td><td><span class="timer active-timer"></span></td><td><button class="btn btn-xs btn-default trade-status">LIVE</button></td><td><span class="trade-investment">'+$leverage+'</span></td><td><span class="trade-payout">'+$payout+'</span></td></tr>';
            $('.trade-log').prepend($trade);
            $logTradeData = {
                expires: parseInt($('.selected-expire').attr('data-value')),
                type: 'sell',
                status: 'live',
                amount: $amount,
                leverage: $leverage,
                payout: $payout,
                winAmount: $winAmount,
                ticker: $filter,
                userId: $userId,
                pair: $cryptoCoin+$cur,
                remainder: 5
            };
            $.ajax({
                url: 'Controller/user.php?action=register-trade',
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    $tradeTransaction.push(
                        {
                            index: $tradeIndex,
                            time: parseInt($('.selected-expire').attr('data-value')),
                            amount: $amount,
                            leverage: $leverage,
                            payout: $payout,
                            winAmount: $winAmount,
                            type: 'sell',
                            status: 'live',
                            tradeId: data.tradeId
                        }
                    );

                    $tradeIndex++;
                },
                data: {param: JSON.stringify($logTradeData)}
            });

        });

        // Timer

        setInterval(function(){

            for($a = 0; $a < $tradeTransaction.length; $a++){
                $index = $tradeTransaction[$a]['index'];
                $row = $('.trade-index-'+$index+'');
                $minute = $tradeTransaction[$a]['time'];
                $status = $tradeTransaction[$a]['status'];
                $leverage = $tradeTransaction[$a]['leverage'];
                $winAmount = $tradeTransaction[$a]['winAmount'];
                $tradeId = $tradeTransaction[$a]['tradeId'];
                $remainder = $tradeTransaction[$a]['remainder'];
                $pairing = $tradeTransaction[$a]['pair'];
                if($status == 'live'){

                    $type = $tradeTransaction[$a]['type'];
                    $entryValue = $tradeTransaction[$a]['amount'];

                    if($minute == 0){
                        $amount = parseFloat($('#current_price').val());
                            $amountPair = $pairPrices[$pairing];
                        console.log('amount');
                        console.log($amountPair);
                        if($type == 'buy'){
                            if($amount > $entryValue){
                                $row.find('.trade-status').removeClass('btn-default').addClass('btn-success').text('WIN');
                                $tradeStatus = 'win';
                            }else{
                                $row.find('.trade-status').removeClass('btn-default').addClass('btn-danger').text('LOSE');
                                $row.find('.trade-payout').text('0');
                                $tradeStatus = 'lost';
                            }

                        }else{
                            if($amount < $entryValue){
                                $row.find('.trade-status').removeClass('btn-default').addClass('btn-success').text('WIN');
                                $tradeStatus = 'win';
                            }else{
                                $tradeStatus = 'lost';
                                $row.find('.trade-status').removeClass('btn-default').addClass('btn-danger').text('LOSE');
                                $row.find('.trade-payout').text('0');
                            }
                        }

                        $tradeData = {
                            close: $amount,
                            winAmount: $winAmount,
                            leverage: $leverage,
                            status: $tradeStatus,
                            tradeId: $tradeId,
                            userId: $userId
                        }

                        logTrades($tradeData);
                        $row.find('.timer').text('');
                        $chart.valueAxes[0].guides[$index + 1].label = 0;
                        $chart.valueAxes[0].guides[$index + 1].value = 0;
                        $startIndex = $chart.startIndex;
                        $endIndex = $chart.endIndex - $chart.dataProvider.length;
                        $chart.validateData();
                        zoomChart($startIndex, $endIndex);
                        $('.amcharts-chart-div a').css('display', 'none');
                        $tradeTransaction[$a]['status'] = 'complete';

                    }

                    if($minute > 59){
                        $fullMin = Math.floor($minute / 60);
                        $minSecs = $minute - $fullMin  * 60;
                        if($fullMin < 10){
                            $fullMinDisplay = '0'+$fullMin;
                        }else{
                            $fullMinDisplay = $fullMin;
                        }
                        if($minSecs < 10){
                            $minSecsDisplay = '0'+$minSecs;
                        }else{
                            $minSecsDisplay = $minSecs;
                        }
                        $row.find('.timer').text($fullMinDisplay+':'+$minSecsDisplay);
                    }
                    else if($minute < 10){
                        $row.find('.timer').text('00:0'+$minute);
                    }else{
                        $row.find('.timer').text('00:'+$minute);
                    }

                    $tradeTransaction[$a]['time'] = $minute - 1;


                }else{
                    if($remainder == 0){
                        $row.remove();
                    }
                    $tradeTransaction[$a]['remainder'] = $remainder - 1;
                }

            }
        }, 1000);
        // end timer

        function logTrades($data){
            $.ajax({
                url: 'Controller/user.php?action=update-trades',
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    $('.userFunds').text(data.response.funds);
                },
                data: {param: JSON.stringify($data)}
            });
        }
        function getDateNow(){
            $dateNow = Date.now();
            $date = new Date($dateNow);
            $year = $date.getFullYear();
            $month = $date.getMonth() + 1;
            if($month < 10){
                $month = '0'+$month;
            }
            $day = $date.getDate();
            if($day < 10){
                $day = '0'+$day;
            }
            $hour = $date.getHours();         //
            if($hour < 10){
                $hour = '0'+$hour;
            }
            $minute = $date.getMinutes();
            if($minute < 10){
                $minute = '0'+$minute;
            }
            $seconds = $date.getSeconds();

            return $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':00';
        }

        function zoomChart($start, $end) {
            // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
            try {
                $chart.zoomToIndexes( $start, $chart.dataProvider.length - $end );
            }
            catch(err) {
                console.log('');
            }

        }

    });



</script>

</body>
</html>
