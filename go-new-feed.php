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
        text {
        fill: #000;
    }

    path {
        fill: none;
        stroke-width: 1;
    }

    path.ohlc {
        stroke: #000000;
        stroke-width: 1;
    }

    path.ohlc.up {
        stroke: #00AA00;
    }

    path.ohlc.down {
        stroke: #FF0000;
    }

    .ma-0 path.line {
        stroke: #1f77b4;
    }

    .ma-1 path.line {
        stroke: #aec7e8;
    }

    path.volume {
        fill: #EEEEEE;
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
                <h2><span class="current_price"></span><span class="percent_status"><i class="fa"></i></span></h2>
            </div>
            <div class="col-md-5 text-right">
                <div class="input-group pull-right" style="width: 200px;">
                    <input type="hidden" id="current_price">
                    <span class="input-group-addon">Trade</span>
                    <input type="hidden" class="max-quadpips">
                    <input type="hidden" class="max-dollar-price">
                    <input id="leverage" type="number" value='0.5' min='0.1' class="form-control" aria-label="3.5">
                </div>

            </div>
            <div class="col-md-3">
                = <span class=""><img src="assets/img/btc-logo.ico" style="width: 25px; height: 25px;"></span><span class="current-exchange-price text-bold"></span>

            </div>
            <div class="col-md-3">
              <button class="btn btn-success btn-lg" style="font-size: 32px;" id='buy'>Buy</button>
              <button class="btn btn-danger btn-lg" style="font-size: 32px;" id='sell'>Sell</button>
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

            $('.current-exchange-price').text($(this).val() / 10000);
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
        $('.current-exchange-price').text( $('#leverage').val() / $currentBtcPrice);
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
            var data = [];
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

            var margin = {top: 20, right: 20, bottom: 30, left: 50},
            width = 960 - margin.left - margin.right,
            height = 500 - margin.top - margin.bottom;

            //var parseDate = d3.timeParse("%d-%b-%y");
            var parseDate = d3.timeParse("%Y-%m-%d %H:%M:%S");
            var x = techan.scale.financetime()
                    .range([0, width]);

            var y = d3.scaleLinear()
                    .range([height, 0]);

            var yVolume = d3.scaleLinear()
                    .range([y(0), y(0.2)]);

            var ohlc = techan.plot.ohlc()
                    .xScale(x)
                    .yScale(y);

            var sma0 = techan.plot.sma()
                    .xScale(x)
                    .yScale(y);

            var sma0Calculator = techan.indicator.sma()
                    .period(10);

            var sma1 = techan.plot.sma()
                    .xScale(x)
                    .yScale(y);

            var sma1Calculator = techan.indicator.sma()
                    .period(20);

            var volume = techan.plot.volume()
                    .accessor(ohlc.accessor())   // Set the accessor to a ohlc accessor so we get highlighted bars
                    .xScale(x)
                    .yScale(yVolume);

            var xAxis = d3.axisBottom(x);

            var yAxis = d3.axisLeft(y);

            var volumeAxis = d3.axisRight(yVolume)
                    .ticks(3)
                    .tickFormat(d3.format(",.3s"));

            var timeAnnotation = techan.plot.axisannotation()
                    .axis(xAxis)
                    .orient('bottom')
                    .format(d3.timeFormat('%Y-%m-%d'))
                    .width(65)
                    .translate([0, height]);

            var ohlcAnnotation = techan.plot.axisannotation()
                    .axis(yAxis)
                    .orient('left')
                    .format(d3.format(',.2f'));

            var volumeAnnotation = techan.plot.axisannotation()
                    .axis(volumeAxis)
                    .orient('right')
                    .width(35);

            var crosshair = techan.plot.crosshair()
                    .xScale(x)
                    .yScale(y)
                    .xAnnotation(timeAnnotation)
                    .yAnnotation([ohlcAnnotation, volumeAnnotation])
                    .on("move", move);

            var svg = d3.select("#chartdiv").append("svg")
                    .attr("width", width + margin.left + margin.right)
                    .attr("height", height + margin.top + margin.bottom);

            var defs = svg.append("defs");

            defs.append("clipPath")
                    .attr("id", "ohlcClip")
                .append("rect")
                    .attr("x", 0)
                    .attr("y", 0)
                    .attr("width", width)
                    .attr("height", height);

            svg = svg.append("g")
                    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

            var ohlcSelection = svg.append("g")
                    .attr("class", "ohlc")
                    .attr("transform", "translate(0,0)");

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

            svg.append("g")
                    .attr("class", "x axis")
                    .attr("transform", "translate(0," + height + ")");

            svg.append("g")
                    .attr("class", "y axis")
                .append("text")
                    .attr("transform", "rotate(-90)")
                    .attr("y", 6)
                    .attr("dy", ".71em")
                    .style("text-anchor", "end")
                    .text("Price ($)");

            svg.append("g")
                    .attr("class", "volume axis");

            svg.append('g')
                    .attr("class", "crosshair ohlc");

            var coordsText = svg.append('text')
                    .style("text-anchor", "end")
                    .attr("class", "coords")
                    .attr("x", width - 5)
                    .attr("y", 15);

            var feed;

            d3.csv(dataFile, function(error, csv) {
                var accessor = ohlc.accessor();

                feed = csv.map(function(d) {
                    return {
                        date: parseDate(d.Date),
                        open: +d.Open,
                        high: +d.High,
                        low: +d.Low,
                        close: +d.Close,
                        volume: +d.Volume
                    };
                }).sort(function(a, b) { return d3.ascending(accessor.d(a), accessor.d(b)); });

                // Start off an initial set of data
                redraw(feed.slice(0, 163));
            });

            function redraw(data) {
                var accessor = ohlc.accessor();

                x.domain(data.map(accessor.d));
                // Show only 150 points on the plot
                x.zoomable().domain([data.length-130, data.length]);

                // Update y scale min max, only on viewable zoomable.domain()
                y.domain(techan.scale.plot.ohlc(data.slice(data.length-130, data.length)).domain());
                yVolume.domain(techan.scale.plot.volume(data.slice(data.length-130, data.length)).domain());

                // Setup a transition for all that support
                svg
        //          .transition() // Disable transition for now, each is only for transitions
                    .each(function() {
                        var selection = d3.select(this);
                        selection.select('g.x.axis').call(xAxis);
                        selection.select('g.y.axis').call(yAxis);
                        selection.select("g.volume.axis").call(volumeAxis);

                        selection.select("g.candlestick").datum(data).call(ohlc);
                        selection.select("g.sma.ma-0").datum(sma0Calculator(data)).call(sma0);
                        selection.select("g.sma.ma-1").datum(sma1Calculator(data)).call(sma1);
                        selection.select("g.volume").datum(data).call(volume);

                        svg.select("g.crosshair.ohlc").call(crosshair);
                    });

                // Set next timer expiry
                /*
                setTimeout(function() {
                    var newData;

                    if(data.length < feed.length) {
                        // Simulate a daily feed
                        newData = feed.slice(0, data.length+1);
                    }
                    else {
                        // Simulate intra day updates when no feed is left
                        var last = data[data.length-1];
                        // Last must be between high and low
                        last.close = Math.round(((last.high - last.low)*Math.random())*10)/10+last.low;

                        newData = data;
                    }

                    redraw(newData);
                }, (Math.random()*1000)+400); // Randomly pick an interval to update the chart
                */
            }

            function move(coords) {
                coordsText.text(
                        timeAnnotation.format()(coords.x) + ", " + ohlcAnnotation.format()(coords.y)
                );
            }

                console.log(feed);

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
                    $('.current-exchange-price').text( $('#leverage').val() / $currentBtcPrice);
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
