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
$user = $users->getUserDataById($userData['id']);
$data = json_decode($history->getCoinHistoryNew(strtolower($filter), 'desktop'), true);
$history->generateCoinHistory($data, $userData['id'], 'desktop');
//$data = json_decode(file_get_contents('https://api.hitbtc.com/api/2/public/candles/'.$filter.$currencyPair.'?period=M1&limit=120'), true);
if($filter == 'BTC'){
    $currentBtcPrice = $data[count($data) - 1]['close'];

}else{
    $currentBtcPrice = json_decode(file_get_contents('http://coincap.io/page/BTC'), true)['price_usd'];
}
$currentCoinPrice = $data[count($data) - 1]['close'];
$dataFiltered = json_encode($data);



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
    <script src="http://d3js.org/d3.v4.min.js"></script>
    <script src="http://techanjs.org/techan.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" media="all" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>
    <style>
        #chartdiv {
            width		: 100%;
          /*  height		: 500px; */
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

        .balance-container{
          border-style: double;
          border-color: #3daf43;
          padding: 10px;
        }
        .text-bold{
            font-weight: bold;
        }
        text {
        fill: #000;
    }

    text.symbol {
        fill: #BBBBBB;
    }

    path {
        fill: none;
        stroke-width: 1;
    }

    path.candle {
        stroke: #000000;
    }

    path.candle.body {
        stroke-width: 0;
    }

    path.candle.up {
        fill: #00AA00;
        stroke: #00AA00;
    }

    path.candle.down {
        fill: #FF0000;
        stroke: #FF0000;
    }

    .close.annotation.up path {
        fill: #00AA00;
    }

    path.volume {
        fill: #DDDDDD;
    }

    .indicator-plot path.line {
        fill: none;
        stroke-width: 1;
    }

    .ma-0 path.line {
        stroke: #1f77b4;
    }

    .ma-1 path.line {
        stroke: #aec7e8;
    }

    .ma-2 path.line {
        stroke: #ff7f0e;
    }


    path.macd {
        stroke: #0000AA;
    }

    path.signal {
        stroke: #FF9999;
    }

    path.zero {
        stroke: #BBBBBB;
        stroke-dasharray: 0;
        stroke-opacity: 0.5;
    }

    path.difference {
        fill: #BBBBBB;
        opacity: 0.5;
    }

    path.rsi {
        stroke: #000000;
    }

    path.overbought, path.oversold {
        stroke: #FF9999;
        stroke-dasharray: 5, 5;
    }

    path.middle, path.zero {
        stroke: #BBBBBB;
        stroke-dasharray: 5, 5;
    }

    .analysis path, .analysis circle {
        stroke: blue;
        stroke-width: 0.8;
    }

    .trendline circle {
        stroke-width: 0;
        display: none;
    }

    .mouseover .trendline path {
        stroke-width: 1.2;
    }

    .mouseover .trendline circle {
        stroke-width: 1;
        display: inline;
    }

    .dragging .trendline path, .dragging .trendline circle {
        stroke: darkblue;
    }

    .interaction path, .interaction circle {
        pointer-events: all;
    }

    .interaction .body {
        cursor: move;
    }

    .trendlines .interaction .start, .trendlines .interaction .end {
        cursor: nwse-resize;
    }

    .supstance path {
        stroke-dasharray: 2, 2;
    }

    .supstances .interaction path {
        pointer-events: all;
        cursor: ns-resize;
    }

    .mouseover .supstance path {
        stroke-width: 1.5;
    }

    .dragging .supstance path {
        stroke: darkblue;
    }

    .crosshair {
        cursor: crosshair;
    }

    .crosshair path.wire {
        stroke: #DDDDDD;
        stroke-dasharray: 1, 1;
    }

    .crosshair .axisannotation path {
        fill: #DDDDDD;
    }

    .tradearrow path.tradearrow {
        stroke: none;
    }

    .tradearrow path.buy {
        fill: #0000FF;
    }

    .tradearrow path.sell {
        fill: #9900FF;
    }

    .tradearrow path.highlight {
        fill: none;
        stroke-width: 2;
    }

    .tradearrow path.highlight.buy {
        stroke: #0000FF;
    }

    .tradearrow path.highlight.sell {
        stroke: #9900FF;
    }
    .annotation{
      opacity: 1 !important;
      font-size: 11px !important;
      text-shadow: none !important;
    }
    </style>
    <script>
        var feed;
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
    <div id="chartdiv" style="min-width: 1000px"></div>

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
            //var data = [];
            /*
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
                data.push(
                    {
                        "date" : $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':00',
                        "open" : $open.toFixed(6),
                        "high" : $high.toFixed(6),
                        "low"  : $low.toFixed(6),
                        "close" : $close.toFixed(6)
                    }

                );

            }
*/
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



// this method is called when chart is first inited as we listen for "dataUpdated" event
            var dataFile = 'data/data-desktop-'+<?=$userData['id']?>+'.csv';

            var dim = {
            width: screen.width - 50, height: 700,
            margin: { top: 20, right: 50, bottom: 30, left: 50 },
            ohlc: { height: 505 },
            indicator: { height: 65, padding: 5 }
            };
            dim.plot = {
            width: dim.width - dim.margin.left - dim.margin.right,
            height: dim.height - dim.margin.top - dim.margin.bottom
            };
            dim.indicator.top = dim.ohlc.height+dim.indicator.padding;
            dim.indicator.bottom = dim.indicator.top+dim.indicator.height+dim.indicator.padding;

            var indicatorTop = d3.scaleLinear()
            .range([dim.indicator.top, dim.indicator.bottom]);

            var parseDate = d3.timeParse("%Y-%m-%d %H:%M:%S");
            //var parseDate = d3.timeParse("%d-%b-%y");
            var zoom = d3.zoom()
            .on("zoom", zoomed);

            var x = techan.scale.financetime()
            .range([0, dim.plot.width]);

            var y = d3.scaleLinear()
            .range([dim.ohlc.height, 0]);


            var yPercent = y.copy();   // Same as y at this stage, will get a different domain later

            var yInit, yPercentInit, zoomableInit;

            var yVolume = d3.scaleLinear()
            .range([y(0), y(0.2)]);

            var candlestick = techan.plot.candlestick()
            .xScale(x)
            .yScale(y);

            var tradearrow = techan.plot.tradearrow()
            .xScale(x)
            .yScale(y)
            .y(function(d) {
                // Display the buy and sell arrows a bit above and below the price, so the price is still visible
                if(d.type === 'buy') return y(d.low)+5;
                if(d.type === 'sell') return y(d.high)-5;
                else return y(d.price);
            });

            var sma0 = techan.plot.sma()
            .xScale(x)
            .yScale(y);

            var sma1 = techan.plot.sma()
            .xScale(x)
            .yScale(y);

            var ema2 = techan.plot.ema()
            .xScale(x)
            .yScale(y);

            var volume = techan.plot.volume()
            .accessor(candlestick.accessor())   // Set the accessor to a ohlc accessor so we get highlighted bars
            .xScale(x)
            .yScale(yVolume);

            var trendline = techan.plot.trendline()
            .xScale(x)
            .yScale(y);

            var supstance = techan.plot.supstance()
            .xScale(x)
            .yScale(y);

            var xAxis = d3.axisBottom(x);

            var timeAnnotation = techan.plot.axisannotation()
            .axis(xAxis)
            .orient('bottom')
            .format(d3.timeFormat('%Y-%m-%d'))
            .width(65)
            .translate([0, dim.plot.height]);

            var yAxis = d3.axisRight(y);

            var ohlcAnnotation = techan.plot.axisannotation()
            .axis(yAxis)
            .orient('right')
            .format(d3.format(',.2f'))
            .translate([x(1), 0]);

            var closeAnnotation = techan.plot.axisannotation()
            .axis(yAxis)
            .orient('right')
            .accessor(candlestick.accessor())
            .format(d3.format(',.2f'))
            .translate([x(1), 0]);

            var buyAnnotation = techan.plot.axisannotation()
            .axis(yAxis)
            .orient('right')
            .accessor(candlestick.accessor())
            .format(d3.format(',.2f'))
            .translate([x(1), 0]);

            var percentAxis = d3.axisLeft(yPercent)
            .tickFormat(d3.format('+.1%'));

            var percentAnnotation = techan.plot.axisannotation()
            .axis(percentAxis)
            .orient('left');

            var volumeAxis = d3.axisRight(yVolume)
            .ticks(3)
            .tickFormat(d3.format(",.3s"));

            var volumeAnnotation = techan.plot.axisannotation()
            .axis(volumeAxis)
            .orient("right")
            .width(35);

            var macdScale = d3.scaleLinear()
            .range([indicatorTop(0)+dim.indicator.height, indicatorTop(0)]);

            var rsiScale = macdScale.copy()
            .range([indicatorTop(1)+dim.indicator.height, indicatorTop(1)]);

            var macd = techan.plot.macd()
            .xScale(x)
            .yScale(macdScale);

            var macdAxis = d3.axisRight(macdScale)
            .ticks(3);

            var macdAnnotation = techan.plot.axisannotation()
            .axis(macdAxis)
            .orient("right")
            .format(d3.format(',.2f'))
            .translate([x(1), 0]);

            var macdAxisLeft = d3.axisLeft(macdScale)
            .ticks(3);

            var macdAnnotationLeft = techan.plot.axisannotation()
            .axis(macdAxisLeft)
            .orient("left")
            .format(d3.format(',.2f'));

            var rsi = techan.plot.rsi()
            .xScale(x)
            .yScale(rsiScale);

            var rsiAxis = d3.axisRight(rsiScale)
            .ticks(3);

            var rsiAnnotation = techan.plot.axisannotation()
            .axis(rsiAxis)
            .orient("right")
            .format(d3.format(',.2f'))
            .translate([x(1), 0]);

            var rsiAxisLeft = d3.axisLeft(rsiScale)
            .ticks(3);

            var rsiAnnotationLeft = techan.plot.axisannotation()
            .axis(rsiAxisLeft)
            .orient("left")
            .format(d3.format(',.2f'));

            var ohlcCrosshair = techan.plot.crosshair()
            .xScale(timeAnnotation.axis().scale())
            .yScale(ohlcAnnotation.axis().scale())
            .xAnnotation(timeAnnotation)
            .yAnnotation([ohlcAnnotation, percentAnnotation, volumeAnnotation])
            .verticalWireRange([0, dim.plot.height]);

            var macdCrosshair = techan.plot.crosshair()
            .xScale(timeAnnotation.axis().scale())
            .yScale(macdAnnotation.axis().scale())
            .xAnnotation(timeAnnotation)
            .yAnnotation([macdAnnotation, macdAnnotationLeft])
            .verticalWireRange([0, dim.plot.height]);

            var rsiCrosshair = techan.plot.crosshair()
            .xScale(timeAnnotation.axis().scale())
            .yScale(rsiAnnotation.axis().scale())
            .xAnnotation(timeAnnotation)
            .yAnnotation([rsiAnnotation, rsiAnnotationLeft])
            .verticalWireRange([0, dim.plot.height]);

            var svg = d3.select("#chartdiv").append("svg")
            .attr("width", dim.width)
            .attr("height", dim.height);

            var defs = svg.append("defs");

            defs.append("clipPath")
            .attr("id", "ohlcClip")
            .append("rect")
            .attr("x", 0)
            .attr("y", 0)
            .attr("width", dim.plot.width)
            .attr("height", dim.ohlc.height);

            defs.selectAll("indicatorClip").data([0, 1])
            .enter()
            .append("clipPath")
            .attr("id", function(d, i) { return "indicatorClip-" + i; })
            .append("rect")
            .attr("x", 0)
            .attr("y", function(d, i) { return indicatorTop(i); })
            .attr("width", dim.plot.width)
            .attr("height", dim.indicator.height);

            svg = svg.append("g")
            .attr("transform", "translate(" + dim.margin.left + "," + dim.margin.top + ")");

            svg.append('text')
            .attr("class", "symbol")
            .attr("x", 20)
            .text("");

            svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + dim.plot.height + ")");

            var ohlcSelection = svg.append("g")
            .attr("class", "ohlc")
            .attr("transform", "translate(0,0)");

            ohlcSelection.append("g")
            .attr("class", "axis")
            .attr("transform", "translate(" + x(1) + ",0)")
            .append("text")
            .attr("transform", "rotate(-90)")
            .attr("y", -12)
            .attr("dy", ".71em")
            .style("text-anchor", "end")
            .text("Price ($)");

            ohlcSelection.append("g")
            .attr("class", "close annotation up");

            ohlcSelection.append("g")
            .attr("class", "volume")
            .attr("clip-path", "url(#ohlcClip)");

            ohlcSelection.append("g")
            .attr("class", "candlestick")
            .attr("clip-path", "url(#ohlcClip)");

            ohlcSelection.append("g")
            .attr("class", "indicator sma ma-0")
            .attr("clip-path", "url(#ohlcClip)");

            ohlcSelection.append("g")
            .attr("class", "indicator sma ma-1")
            .attr("clip-path", "url(#ohlcClip)");

            ohlcSelection.append("g")
            .attr("class", "indicator ema ma-2")
            .attr("clip-path", "url(#ohlcClip)");

            ohlcSelection.append("g")
            .attr("class", "percent axis");

            ohlcSelection.append("g")
            .attr("class", "volume axis");

            var indicatorSelection = svg.selectAll("svg > g.indicator").data(["macd", "rsi"]).enter()
             .append("g")
                .attr("class", function(d) { return d + " indicator"; });

            indicatorSelection.append("g")
            .attr("class", "axis right")
            .attr("transform", "translate(" + x(1) + ",0)");

            indicatorSelection.append("g")
            .attr("class", "axis left")
            .attr("transform", "translate(" + x(0) + ",0)");

            indicatorSelection.append("g")
            .attr("class", "indicator-plot")
            .attr("clip-path", function(d, i) { return "url(#indicatorClip-" + i + ")"; });

            // Add trendlines and other interactions last to be above zoom pane
            svg.append('g')
            .attr("class", "crosshair ohlc");

            svg.append("g")
            .attr("class", "tradearrow")
            .attr("clip-path", "url(#ohlcClip)");

            svg.append('g')
            .attr("class", "crosshair macd");

            svg.append('g')
            .attr("class", "crosshair rsi");

            svg.append("g")
            .attr("class", "trendlines analysis")
            .attr("clip-path", "url(#ohlcClip)");
            svg.append("g")
            .attr("class", "supstances analysis")
            .attr("clip-path", "url(#ohlcClip)");

            d3.select("button").on("click", reset);



            d3.csv(dataFile, function(error, data) {
                var accessor = candlestick.accessor(),
                    indicatorPreRoll = 33;  // Don't show where indicators don't have data

                data = data.map(function(d) {
                    return {

                        date: parseDate(d.Date),
                        open: +d.Open,
                        high: +d.High,
                        low: +d.Low,
                        close: +d.Close,
              //          volume: +d.Volume
                    };
                }).sort(function(a, b) { return d3.ascending(accessor.d(a), accessor.d(b)); });


                feed = data;

                console.log('type of');
                console.log(data[67]);
                x.domain(techan.scale.plot.time(data).domain());
                y.domain(techan.scale.plot.ohlc(data.slice(indicatorPreRoll)).domain());
                yPercent.domain(techan.scale.plot.percent(y, accessor(data[indicatorPreRoll])).domain());
                //yVolume.domain(techan.scale.plot.volume(data).domain());

                var trendlineData = [
                    { start: { date: new Date(2014, 2, 11), value: 72.50 }, end: { date: new Date(2014, 5, 9), value: 63.34 } },
                    { start: { date: new Date(2013, 10, 21), value: 43 }, end: { date: new Date(2014, 2, 17), value: 70.50 } }
                ];

                var supstanceData = [
                    { start: new Date(2014, 2, 11), end: new Date(2014, 5, 9), value: 63.64 },
                    { start: new Date(2013, 10, 21), end: new Date(2014, 2, 17), value: 55.50 }
                ];

                var trades = [
                    { date: data[67].date, type: "buy", price: data[67].low, low: data[67].low, high: data[67].high },
                    { date: data[100].date, type: "sell", price: data[100].high, low: data[100].low, high: data[100].high },
                    { date: data[130].date, type: "buy", price: data[130].low, low: data[130].low, high: data[130].high },
                    { date: data[170].date, type: "sell", price: data[170].low, low: data[170].low, high: data[170].high }
                ];

                var macdData = techan.indicator.macd()(data);
                macdScale.domain(techan.scale.plot.macd(macdData).domain());
                var rsiData = techan.indicator.rsi()(data);
                rsiScale.domain(techan.scale.plot.rsi(rsiData).domain());

                svg.select("g.candlestick").datum(data).call(candlestick);
                svg.select("g.close.annotation").datum([data[data.length-1]]).call(closeAnnotation);
              //  svg.select("g.buy.annotation").datum([data[data.length-5]]).call(buyAnnotation);
              //  svg.select("g.volume").datum(data).call(volume);
                svg.select("g.sma.ma-0").datum(techan.indicator.sma().period(10)(data)).call(sma0);
                svg.select("g.sma.ma-1").datum(techan.indicator.sma().period(20)(data)).call(sma1);
                svg.select("g.ema.ma-2").datum(techan.indicator.ema().period(50)(data)).call(ema2);
                svg.select("g.macd .indicator-plot").datum(macdData).call(macd);
                svg.select("g.rsi .indicator-plot").datum(rsiData).call(rsi);

                svg.select("g.crosshair.ohlc").call(ohlcCrosshair).call(zoom);
                svg.select("g.crosshair.macd").call(macdCrosshair).call(zoom);
                svg.select("g.crosshair.rsi").call(rsiCrosshair).call(zoom);
                svg.select("g.trendlines").datum(trendlineData).call(trendline).call(trendline.drag);
                svg.select("g.supstances").datum(supstanceData).call(supstance).call(supstance.drag);

                svg.select("g.tradearrow").datum(trades).call(tradearrow);

                // Stash for zooming
                zoomableInit = x.zoomable().domain([indicatorPreRoll, data.length]).copy(); // Zoom in a little to hide indicator preroll
                yInit = y.copy();
                yPercentInit = yPercent.copy();

                draw();


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
                        $curentDataLength = data.length;

                        $lastDate = getParseDate(data[$curentDataLength - 1].date);

                        //   console.log('Current Date: ' + $currentDate + ' | Last Date: ' + $lastDate);

                        if($currentDate == $lastDate){
                          console.log('CURRENT');
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

                            data[$curentDataLength - 1] = {
                                "date" : parseDate($lastDate),
                                "open" : $open.toFixed(6),
                                "high" : $high.toFixed(6),
                                "low"  : $low.toFixed(6),
                                "close" : $close.toFixed(6)
                            }



                        }else{
                            console.log('NEW');
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
                            data.shift(); // removes first index
                            data.push( {
                                "date" : parseDate($currentDate),
                                "open" : parseFloat($open.toFixed(6)),
                                "high" : parseFloat($high.toFixed(6)),
                                "low"  : parseFloat($low.toFixed(6)),
                                "close" : parseFloat($close.toFixed(6))
                            } );

                            console.log(data[data.length - 1]);

                        }

                        /*
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
                        */
                        if($filter == 'BTC'){
                          console.log('redraw');
                        }
                        reDraw(data);

                    }

                });

            });

            //console.log(feed);

            function reDraw(data){

              var accessor = candlestick.accessor(),
                  indicatorPreRoll = 33;

                  x.domain(techan.scale.plot.time(data).domain());
                  y.domain(techan.scale.plot.ohlc(data.slice(indicatorPreRoll)).domain());
                  yPercent.domain(techan.scale.plot.percent(y, accessor(data[indicatorPreRoll])).domain());
                  var trendlineData = [
                      { start: { date: new Date(2014, 2, 11), value: 72.50 }, end: { date: new Date(2014, 5, 9), value: 63.34 } },
                      { start: { date: new Date(2013, 10, 21), value: 43 }, end: { date: new Date(2014, 2, 17), value: 70.50 } }
                  ];

                  var supstanceData = [
                      { start: new Date(2014, 2, 11), end: new Date(2014, 5, 9), value: 63.64 },
                      { start: new Date(2013, 10, 21), end: new Date(2014, 2, 17), value: 55.50 }
                  ];

                  var trades = [
                      { date: data[67].date, type: "buy", price: data[67].low, low: data[67].low, high: data[67].high },
                      { date: data[100].date, type: "sell", price: data[100].high, low: data[100].low, high: data[100].high },
                      { date: data[130].date, type: "buy", price: data[130].low, low: data[130].low, high: data[130].high },
                      { date: data[170].date, type: "sell", price: data[170].low, low: data[170].low, high: data[170].high }
                  ];

                  var macdData = techan.indicator.macd()(data);
                  macdScale.domain(techan.scale.plot.macd(macdData).domain());
                  var rsiData = techan.indicator.rsi()(data);
                  rsiScale.domain(techan.scale.plot.rsi(rsiData).domain());

                  svg.select("g.candlestick").datum(data).call(candlestick);
                  svg.select("g.close.annotation").datum([data[data.length-1]]).call(closeAnnotation);

                //  svg.select("g.volume").datum(data).call(volume);
                  svg.select("g.sma.ma-0").datum(techan.indicator.sma().period(10)(data)).call(sma0);
                  svg.select("g.sma.ma-1").datum(techan.indicator.sma().period(20)(data)).call(sma1);
                  svg.select("g.ema.ma-2").datum(techan.indicator.ema().period(50)(data)).call(ema2);
                  svg.select("g.macd .indicator-plot").datum(macdData).call(macd);
                  svg.select("g.rsi .indicator-plot").datum(rsiData).call(rsi);

                  svg.select("g.crosshair.ohlc").call(ohlcCrosshair).call(zoom);
                  svg.select("g.crosshair.macd").call(macdCrosshair).call(zoom);
                  svg.select("g.crosshair.rsi").call(rsiCrosshair).call(zoom);
                  svg.select("g.trendlines").datum(trendlineData).call(trendline).call(trendline.drag);
                  svg.select("g.supstances").datum(supstanceData).call(supstance).call(supstance.drag);

                  svg.select("g.tradearrow").datum(trades).call(tradearrow);

                  // Stash for zooming
                  zoomableInit = x.zoomable().domain([indicatorPreRoll, data.length]).copy(); // Zoom in a little to hide indicator preroll
                  yInit = y.copy();
                  yPercentInit = yPercent.copy();

              //console.log(data);

              draw();
            }

            function reset() {
            zoom.scale(1);
            zoom.translate([0,0]);
            draw();
            }

            function zoomed() {
            x.zoomable().domain(d3.event.transform.rescaleX(zoomableInit).domain());
            y.domain(d3.event.transform.rescaleY(yInit).domain());
            yPercent.domain(d3.event.transform.rescaleY(yPercentInit).domain());

            draw();
            }

            function draw() {
              svg.select("g.x.axis").call(xAxis);
              svg.select("g.ohlc .axis").call(yAxis);
              svg.select("g.volume.axis").call(volumeAxis);
              svg.select("g.percent.axis").call(percentAxis);
              svg.select("g.macd .axis.right").call(macdAxis);
              svg.select("g.rsi .axis.right").call(rsiAxis);
              svg.select("g.macd .axis.left").call(macdAxisLeft);
              svg.select("g.rsi .axis.left").call(rsiAxisLeft);

              // We know the data does not change, a simple refresh that does not perform data joins will suffice.
              svg.select("g.candlestick").call(candlestick.refresh);
              svg.select("g.close.annotation").call(closeAnnotation.refresh);
            //  svg.select("g.buy.annotation").call(buyAnnotation.refresh);

              svg.select("g.volume").call(volume.refresh);
              svg.select("g .sma.ma-0").call(sma0.refresh);
              svg.select("g .sma.ma-1").call(sma1.refresh);
              svg.select("g .ema.ma-2").call(ema2.refresh);
              svg.select("g.macd .indicator-plot").call(macd.refresh);
              svg.select("g.rsi .indicator-plot").call(rsi.refresh);
              svg.select("g.crosshair.ohlc").call(ohlcCrosshair.refresh);
              svg.select("g.crosshair.macd").call(macdCrosshair.refresh);
              svg.select("g.crosshair.rsi").call(rsiCrosshair.refresh);
              svg.select("g.trendlines").call(trendline.refresh);
              svg.select("g.supstances").call(supstance.refresh);
              svg.select("g.tradearrow").call(tradearrow.refresh);
            }

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

        function getParseDate($dateNow){
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
