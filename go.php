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

$mm1Class = 'btn-default';
$mm5Class = 'btn-default';
$mm30Class = 'btn-default';
$mm60Class = 'btn-default';
$mm240Class = 'btn-default';
if(isset($_GET['c']) && $_GET['c'] != '1mm'){
    $tickInterval = $_GET['c'];
    if($tickInterval == '5mm'){
        $tickSelected = '5M';
        $mm5Class = 'btn-primary';
    }else if($tickInterval == '30mm'){
        $tickSelected = '30M';
        $mm30Class = 'btn-primary';
    }else if($tickInterval == '60mm'){
        $tickSelected = '60M';
        $mm60Class = 'btn-primary';
    }else if($tickInterval == '240mm'){
        $tickSelected = '240M';
        $mm240Class = 'btn-primary';
    }

}else{
    $tickInterval = 'mm';
    $tickSelected = '1M';
    $mm1Class = 'btn-primary';

}


//$data = json_decode(file_get_contents('https://api.hitbtc.com/api/2/public/candles/'.$filter.$currencyPair.'?period=M1&limit=120'), true);


$urlParam = $_GET;

$dateFrom = date('m/d/Y', strtotime('-7 days'));
$dateTo = date('m/d/Y');
$user = $users->getUserDataById($userSess['info']['id']);

?>
<!DOCTYPE html>
<html>
<head>
    <title>NanoPips</title>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <link href="assets/css/bootstrap.css?v=1.1" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="assets/css/switchery.min.css" />
    <link rel="stylesheet" href="assets/css/simple-line-icons.min.css" />
    <script src="assets/js/switchery.min.js"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>

    <link rel="stylesheet" href="assets/css/candlestick-chart.css?v=1.1" />
    <script src="https://d3js.org/d3.v4.min.js"></script>
    <script src="assets/js/techan.min.js"></script>
    <!-- <script src="http://techanjs.org/techan.min.js"></script> -->

    <link rel="stylesheet" href="assets/css/main.css?v=2.7" />





    <script>
      var zoomEvents = [];
      if (screen.width <= 768) {
        var device = 'mobile';
        var zoomValue = '60';
        location.href = "go_mobile.php";
      }else{
        var device = 'desktop';
        var zoomValue = '190';
      }
      var lastSecond = '';
    </script>
</head>
<body>
<nav class="navbar navbar-default navbar-static-top login-navbar">
    <div class="col-md-2 col-sm-3 col-xs-3">
        <div class="logo-header logo-page-header">
            <img src="assets/img/logo.png" style="width: 112px; height:32px;">
        </div>
    </div>
    <div class="col-md-2 col-sm-2">
        <div class=" pull-left switch-container display-none">
            <span class="template-text ">D</span><input type="checkbox" class="js-switch  pull-left" id="btcSwitch" <?=(!isset($_GET['template']) || $_GET['template'] == 'light' ? 'checked' : '')?> /><span class="template-text">L</span>
        </div>
    </div>

    <div class="col-md-2 col-sm-2 col-xs-2 profile-dropdown-container text-center pull-right">
        <div class="btn-group profile-dropdown">
            <button class="btn btn-default btn-lg dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Profile <i class="icon icon-user"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuDivider">
                <li id="getHistoryTrigger"><a href="#">Trade History</a></li>
                <li><a href="profile.php">Account</a></li>
                <li><a href="#">Help</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <?php
        if($user['user_level'] == 'admin'){
            echo '<a class="btn btn-default btn-lg profile-dropdown" href="admin/users.php"><i class="fa fa-gears"></i></a>';
        }
        ?>

    </div>
</nav>
<div class="container-fluid">
    <div class="row main-container">
        <div class="col-md-12">
          <div class="col-md-1 col-sm-1 col-xs-2 spacer text-center" style="margin-right: 56px;">
                Market
                <br>
                <div class="btn-group full-width">
                    <button type="button" class="market-btn btn btn-sm outline-primary dropdown-toggle full-width dropdown-filter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo $filter.$currency; ?> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu full-width">
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

                        <li><a href="#" class="select_coin" data-coin="EDO" data-pair="USD">EDOUSD</a></li>
                        <li><a href="#" class="select_coin" data-coin="ETP" data-pair="USD">ETPUSD</a></li>
                        <li><a href="#" class="select_coin" data-coin="NEO" data-pair="USD">NEOUSD</a></li>
                        <li><a href="#" class="select_coin" data-coin="SAN" data-pair="USD">SANUSD</a></li>
                        <li><a href="#" class="select_coin" data-coin="ZEC" data-pair="USD">ZECUSD</a></li>
                        <li><a href="#" class="select_coin" data-coin="DASH" data-pair="USD">DASHUSD</a></li>
                        <li><a href="#" class="select_coin" data-coin="BCH" data-pair="BTC">BCHBTC</a></li>
                        <li><a href="#" class="select_coin" data-coin="EOS" data-pair="BTC">EOSBTC</a></li>
                        <li><a href="#" class="select_coin" data-coin="IOT" data-pair="BTC">IOTBTC</a></li>
                        <li><a href="#" class="select_coin" data-coin="OMG" data-pair="USD">OMGUSD</a></li>
                        <li><a href="#" class="select_coin" data-coin="XMR" data-pair="USD">XMRUSD</a></li>
                        <li><a href="#" class="select_coin" data-coin="BCH" data-pair="USD">BCHUSD</a></li>
                        <li><a href="#" class="select_coin" data-coin="DASH" data-pair="BTC">DASHBTC</a></li>
                        <li><a href="#" class="select_coin" data-coin="LTC" data-pair="BTC">LTCBTC</a></li>
                        <li><a href="#" class="select_coin" data-coin="SAN" data-pair="BTC">SANBTC</a></li>
                        <li><a href="#" class="select_coin" data-coin="EDO" data-pair="BTC">EDOBTC</a></li>
                        <li><a href="#" class="select_coin" data-coin="ETP" data-pair="BTC">ETPBTC</a></li>
                        <li><a href="#" class="select_coin" data-coin="NEO" data-pair="BTC">NEOBTC</a></li>
                        <li><a href="#" class="select_coin" data-coin="ZEC" data-pair="BTC">ZECBTC</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-2 col-sm-2 col-xs-2 spacer pull-left">
                Chart Time Frame
                <br>
                <div class="btn-group" style="width: 60%;">
                    <button type="button" class="btn btn-sm outline-primary dropdown-toggle full-width dropdown-filter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo $tickSelected ?> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu full-width">
                        <li><a href="#" class="candle-select-range" data-value="1mm">1M</a></li>
                        <li><a href="#" class="candle-select-range" data-value="5mm">5M</a></li>
                        <li><a href="#" class="candle-select-range" data-value="30mm">30M</a></li>
                        <li><a href="#" class="candle-select-range" data-value="60mm">60M</a></li>
                        <li><a href="#" class="candle-select-range" data-value="240mm">240M</a></li>
                    </ul>
                </div>
            </div>


            <div class="col-md-2 col-sm-2 col-xs-2 spacer text-center pull-right" style="margin-right: 56px;">
                Balance
                <br>
                <div class="balance-loader text-center">
                    <div class="loader" style="width:50px; height: 50px;"></div>
                </div>
                <span  type="button" class=" balance-loader-display btn outline-primary full-width btn-small-text balance" data-value="nbtc" style="display: none; padding-left: 2px; padding-right: 2px;">$<span class="userFunds">0.00</span></span>
            </div>

        </div>

        <div class="col-md-12 col-sm-12 col-xs-12 nopadding">
            <div id="candlestick-container" style="">
            </div>
        </div>


    </div>

    <div class="row container-bottom" style="margin-bottom: 48px;">
        <div class="col-md-7">
            <div class="row">
                <div class="col-lg-12 col-md-10 col-sm-10 col-xs-10">
                    <div class="col-lg-12 col-md-12 text-center">
                        <span style="font-size: 24px;"><span class="current_price"></span><span class="percent_status"><i class="fa"></i></span></span>
                    </div>
                    <div class="col-lg-12 col-md-12 table-border live-trade-container">
                        <div class="live-trade-loader text-center">
                            <div class="loader" style="width:50px; height: 50px;"></div>
                        </div>

                        <table class="table live-trade-table" style="display: none;">
                            <thead>
                            <tr>
                                <th>Type</th>
                                <th>Entry Price</th>
                                <th>Pair</th>
                                <th>Timer</th>
                                <th>Exit Price</th>
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
        </div>

        <div class="col-md-5">
            <div class="col-md-4 col-sm-4 col-xs-4 spacer">
                <span style="font-size: .8em;">Expiry Time</span>
                <br>
                <div class="btn-group full-width">
                    <button type="button" class="btn btn-sm outline-primary dropdown-toggle full-width expire-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="expire-dropdown-current">1 min</span> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu full-width">
                        <li class="expires select_expiry selected-expire" data-min-value="1 min" data-value="60">1 min</li>
                        <li class="expires select_expiry" data-min-value="2 min" data-value="120">2 min</li>
                        <li class="expires select_expiry" data-min-value="3 min" data-value="180">3 min</li>
                        <li class="expires select_expiry" data-min-value="5 min" data-value="300">5 min</li>
                        <li class="expires select_expiry" data-min-value="15 min" data-value="900">15 min</li>
                        <li class="expires select_expiry" data-min-value="30 min" data-value="1800">30 min</li>
                        <li class="expires select_expiry" data-min-value="60 min" data-value="3600">60 min</li>
                    </ul>
                </div>
                <div class="form-inline" style="margin-top: 12px;">
                    <input type="hidden" id="current_price">
                    <input type="hidden" class="max-quadpips">
                    <input type="hidden" class="max-dollar-price">
                    Trade Amount
                    <input id="leverage" type="text" value='1' class="form-control full-width" aria-label="3.5" >
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4 spacer-3x">
                <button class="btn btn-success btn-lg full-width" style="font-size: 25px; height: 98px; font-weight: bold;" id='buy' >Buy</button>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4 spacer-3x">
                <button class="btn btn-danger btn-lg full-width" style="font-size: 25px; height: 98px; font-weight: bold;" id='sell' >Sell</button>
            </div>
        </div>


    </div>

    <div class="modal fade" id="disclaimerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-disclaimer">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h3>Disclaimer</h3>
                        </div>
                        <div class="col-md-12 text-justify">
                            <p>You are about to activate One Click Trading mode. By clicking "I Accept these Terms and Conditions" below, you acknowledge that you have read and understood the following terms and conditions, and you agree to be bound hereby. Your current version of the platform allows you to choose between the following modes for order submission. You agree that you will be bound by the procedures and conditions specified herein with respect to each such mode.</p>
                            <p>1. The Default mode for order submission is a two-step process: you first select a new market window then you select an order quantity amount to submit by clicking either Buy or Sell buttons depending on the particular market selected and your trading intentions. Your order will not be submitted until you have completed both of the aforementioned steps.</p>
                            <p>2. The One Click Trading mode for order submission is a one-step process. Your order will be submitted when you:</p>
                            <p>- click either bid (SELL) or ask (BUY) rate buttons on this platform on the right side panel.</p>
                            <p>THERE WILL BE NO SUBSEQUENT CONFIRMATION PROMPT FOR YOU TO CLICK. YOU WILL NOT BE ABLE TO WITHDRAW OR CHANGE YOUR ORDER ONCE YOU CLICK. UNDER NORMAL MARKET CONDITIONS AND SYSTEM PERFORMANCE, A MARKET ORDER WILL BE PROMPTLY FILLED AFTER SUBMISSION AND YOU WILL HAVE ENTERED INTO A BINDING TRANSACTION.</p>
                            <p>By activating this platform and the One Click Trading mode, you understand that your orders will be submitted by clicking the Buy or Sell buttons as described above, without any further order confirmation. You agree to accept all risks associated with the use of the order submission mode you have chosen, including, without limitation, the risk of errors, omissions or mistakes made in submitting any order.</p>
                            <p>You agree to fully indemnify and hold harmless NanoPips Inc. from any and all losses, costs and expenses that it may incur as a result of any such errors, omissions or mistakes by you, your trading manager or any other person inputting trades on the platform on your behalf. </p>
                        </div>
                        <div class="col-md-12 text-center spacer">
                            <button class="btn outline-success accept-disclaimer">I Accept These Terms & Conditions</button>
                        </div>
                        <div class="col-md-12 text-center spacer">
                            <a href="#" data-dismiss="modal" aria-label="Close">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="depositModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h1 class="no-funds-text">Error: No Funds Available</h1>
                        </div>
                        <div class="col-md-12 text-center" style="margin-top: 24px;">
                            <a href="https://www.nanopips.com/funding.php" class="btn btn-primary btn-lg">Deposit Now</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="tradeHistoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row">
                        <div class="col-md-11">
                            <h2 class="modal-title text-center">Trade History Report</h2>

                        </div>
                        <div class="col-md-1">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="col-md-12 form-inline">
                            Date:
                            <div class="input-group date form-inline">
                                <input type="text" class="form-controle date-from form-control date-history" value="<?=$dateFrom?>">
                                <div class="input-group-addon">
                                    <span class="fa fa-calendar"></span>
                                </div>
                            </div>
                            -
                            <div class="input-group date form-inline">
                                <input type="text" class="form-controle date-to form-control date-history" value="<?=$dateTo?>">
                                <div class="input-group-addon">
                                    <span class="fa fa-calendar"></span>
                                </div>
                            </div>
                            <span class="pull-right" style="font-size: 1.5em;">Total P/L: <span class="totalPnL"></span></span>
                        </div>

                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-loader text-center">
                                <div class="loader" style="width:50px; height: 50px;"></div>
                            </div>
                            <table class="table trade-table" style="display: none;">
                                <thead>
                                <tr>
                                    <th scope="col">Entry Price</th>
                                    <th scope="col">Position</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Trade Amount</th>
                                    <th scope="col">Exit Price</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Pair</th>
                                    <th scope="col">Cost</th>
                                    <th scope="col">PnL</th>
                                </tr>
                                </thead>
                                <tbody id="tradeHistoryBody">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="assets/js/common.js"></script>
    <script type="text/javascript" language="JavaScript">
      var intViewportWidth = window.innerWidth;
      var lastPrice = 0;
      $(document).ready(function(){
        var _efx8 = <?=$user['accepted_terms']?>;
        var totalTradeAmount = 0;
        $userId = <?php echo $userSess['info']['id']; ?>;
        $mm = '<?=$tickInterval?>';
        if(_efx8 == false){
          $('#disclaimerModal').modal('show');
        }
        $('.accept-disclaimer').click(function(){
          $btn = $(this);
          $btn.html('<i class="fa fa-spinner fa-spin"></i>');
          $.ajax({
            url: 'Controller/user.php?action=accept-terms',
            type: 'post',
            dataType: 'json',
            success: function (r) {
              $btn.html('I Accept These Terms & Conditions');
              _efx8 = true;
              $('#disclaimerModal').modal('hide');
            },
            data: {param: {userId: $userId}}
          });
        });





        // START CHART
        var COIN_ID = "BTC/ETH";
        var API_URL =
          "https://api.coincap.io/v2/candles?exchange=poloniex&interval=m1&baseId=ethereum&quoteId=bitcoin";
        var CONTAINER_ID = "candlestick-container";

        var feed;

        var dim = {
          width: intViewportWidth,
          height: 500,
          margin: { top: 20, right: 80, bottom: 30, left: 80 },
          ohlc: { height: 305 },
          indicator: { height: 65, padding: 5 }
        };
        dim.plot = {
          width: dim.width - dim.margin.left - dim.margin.right,
          height: dim.height - dim.margin.top - dim.margin.bottom
        };
        dim.indicator.top = dim.ohlc.height + dim.indicator.padding;
        dim.indicator.bottom =
          dim.indicator.top + dim.indicator.height + dim.indicator.padding;

        var indicatorTop = d3
          .scaleLinear()
          .range([dim.indicator.top, dim.indicator.bottom]);

        var parseDate = d3.timeParse("%Q");

        var x = techan.scale.financetime().range([0, dim.plot.width]);

        var y = d3.scaleLinear().range([dim.ohlc.height, 0]);

        var yPercent = y.copy(); // Same as y at this stage, will get a different domain later

        var yVolume = d3.scaleLinear().range([y(0), y(0.2)]);

        var candlestick = techan.plot
          .candlestick()
          .xScale(x)
          .yScale(y);

        var sma0 = techan.plot
          .sma()
          .xScale(x)
          .yScale(y);

        var sma1 = techan.plot
          .sma()
          .xScale(x)
          .yScale(y);

        var ema2 = techan.plot
          .ema()
          .xScale(x)
          .yScale(y);

        var volume = techan.plot
          .volume()
          .accessor(candlestick.accessor()) // Set the accessor to a ohlc accessor so we get highlighted bars
          .xScale(x)
          .yScale(yVolume);

        var xAxis = d3.axisBottom(x);

        var timeAnnotation = techan.plot
          .axisannotation()
          .axis(xAxis)
          .orient("bottom")
          .format(d3.timeFormat("%Y-%m-%d %I:%M %p"))
          .width(110)
          .translate([0, dim.plot.height]);

        var yAxis = d3.axisRight(y);

        var ohlcAnnotation = techan.plot
          .axisannotation()
          .axis(yAxis)
          .orient("right")
          .format(d3.format(",.4r"))
          .translate([x(1), 0]);

        var closeAnnotation = techan.plot
          .axisannotation()
          .axis(yAxis)
          .orient("right")
          .accessor(candlestick.accessor())
          .format(d3.format(",.4r"))
          .translate([x(1), 0]);

        var percentAxis = d3.axisLeft(yPercent).tickFormat(d3.format("+.2%"));

        var percentAnnotation = techan.plot
          .axisannotation()
          .axis(percentAxis)
          .orient("left");

        var volumeAxis = d3
          .axisRight(yVolume)
          .ticks(3)
          .tickFormat(d3.format(",.3s"));

        var volumeAnnotation = techan.plot
          .axisannotation()
          .axis(volumeAxis)
          .orient("right")
          .width(35);

        var macdScale = d3
          .scaleLinear()
          .range([indicatorTop(0) + dim.indicator.height, indicatorTop(0)]);

        var rsiScale = macdScale
          .copy()
          .range([indicatorTop(1) + dim.indicator.height, indicatorTop(1)]);

        var macd = techan.plot
          .macd()
          .xScale(x)
          .yScale(macdScale);

        var macdAxis = d3.axisRight(macdScale).ticks(3);

        var macdAnnotation = techan.plot
          .axisannotation()
          .axis(macdAxis)
          .orient("right")
          .format(d3.format(",.3r"))
          .translate([x(1), 0]);

        var macdAxisLeft = d3.axisLeft(macdScale).ticks(3);

        var macdAnnotationLeft = techan.plot
          .axisannotation()
          .axis(macdAxisLeft)
          .orient("left")
          .format(d3.format(",.3r"));

        var rsi = techan.plot
          .rsi()
          .xScale(x)
          .yScale(rsiScale);

        var rsiAxis = d3.axisRight(rsiScale).ticks(3);

        var rsiAnnotation = techan.plot
          .axisannotation()
          .axis(rsiAxis)
          .orient("right")
          .format(d3.format(",.3r"))
          .translate([x(1), 0]);

        var rsiAxisLeft = d3.axisLeft(rsiScale).ticks(3);

        var rsiAnnotationLeft = techan.plot
          .axisannotation()
          .axis(rsiAxisLeft)
          .orient("left")
          .format(d3.format(",.3r"));

        var ohlcCrosshair = techan.plot
          .crosshair()
          .xScale(timeAnnotation.axis().scale())
          .yScale(ohlcAnnotation.axis().scale())
          .xAnnotation(timeAnnotation)
          .yAnnotation([ohlcAnnotation, percentAnnotation, volumeAnnotation])
          .verticalWireRange([0, dim.plot.height]);

        var macdCrosshair = techan.plot
          .crosshair()
          .xScale(timeAnnotation.axis().scale())
          .yScale(macdAnnotation.axis().scale())
          .xAnnotation(timeAnnotation)
          .yAnnotation([macdAnnotation, macdAnnotationLeft])
          .verticalWireRange([0, dim.plot.height]);

        var rsiCrosshair = techan.plot
          .crosshair()
          .xScale(timeAnnotation.axis().scale())
          .yScale(rsiAnnotation.axis().scale())
          .xAnnotation(timeAnnotation)
          .yAnnotation([rsiAnnotation, rsiAnnotationLeft])
          .verticalWireRange([0, dim.plot.height]);

        var svg = d3
          .select("#" + CONTAINER_ID)
          .append("svg")
          .attr("width", dim.width)
          .attr("height", dim.height);

        var defs = svg.append("defs");

        defs
          .append("clipPath")
          .attr("id", "ohlcClip")
          .append("rect")
          .attr("x", 0)
          .attr("y", 0)
          .attr("width", dim.plot.width)
          .attr("height", dim.ohlc.height);

        defs
          .selectAll("indicatorClip")
          .data([0, 1])
          .enter()
          .append("clipPath")
          .attr("id", function(d, i) {
            return "indicatorClip-" + i;
          })
          .append("rect")
          .attr("x", 0)
          .attr("y", function(d, i) {
            return indicatorTop(i);
          })
          .attr("width", dim.plot.width)
          .attr("height", dim.indicator.height);

        svg = svg
          .append("g")
          .attr(
            "transform",
            "translate(" + dim.margin.left + "," + dim.margin.top + ")"
          );

        svg
          .append("text")
          .attr("class", "symbol")
          .attr("x", 20)
          .text(COIN_ID);

        svg
          .append("g")
          .attr("class", "x axis")
          .attr("transform", "translate(0," + dim.plot.height + ")");

        var ohlcSelection = svg
          .append("g")
          .attr("class", "ohlc")
          .attr("transform", "translate(0,0)");

        ohlcSelection
          .append("g")
          .attr("class", "axis")
          .attr("transform", "translate(" + x(1) + ",0)");
        // .append("text")
        // .attr("transform", "rotate(-90)")
        // .attr("y", -12)
        // .attr("dy", ".71em")
        // .style("text-anchor", "end")
        // .text("Price ($)");

        ohlcSelection.append("g").attr("class", "close annotation");

        ohlcSelection
          .append("g")
          .attr("class", "volume")
          .attr("clip-path", "url(#ohlcClip)");

        ohlcSelection
          .append("g")
          .attr("class", "candlestick")
          .attr("clip-path", "url(#ohlcClip)");

        ohlcSelection
          .append("g")
          .attr("class", "indicator sma ma-0")
          .attr("clip-path", "url(#ohlcClip)");

        ohlcSelection
          .append("g")
          .attr("class", "indicator sma ma-1")
          .attr("clip-path", "url(#ohlcClip)");

        ohlcSelection
          .append("g")
          .attr("class", "indicator ema ma-2")
          .attr("clip-path", "url(#ohlcClip)");

        ohlcSelection.append("g").attr("class", "percent axis");

        ohlcSelection.append("g").attr("class", "volume axis");

        var indicatorSelection = svg
          .selectAll("svg > g.indicator")
          .data(["macd", "rsi"])
          .enter()
          .append("g")
          .attr("class", function(d) {
            return d + " indicator";
          });

        indicatorSelection
          .append("g")
          .attr("class", "axis right")
          .attr("transform", "translate(" + x(1) + ",0)");

        indicatorSelection
          .append("g")
          .attr("class", "axis left")
          .attr("transform", "translate(" + x(0) + ",0)");

        indicatorSelection
          .append("g")
          .attr("class", "indicator-plot")
          .attr("clip-path", function(d, i) {
            return "url(#indicatorClip-" + i + ")";
          });

        // Add trendlines and other interactions last to be above zoom pane
        svg.append("g").attr("class", "crosshair ohlc");

        svg
          .append("g")
          .attr("class", "tradearrow")
          .attr("clip-path", "url(#ohlcClip)");

        svg.append("g").attr("class", "crosshair macd");

        svg.append("g").attr("class", "crosshair rsi");

        var apiCallSeconds = 60;
        var accessor = candlestick.accessor();
        var indicatorPreRoll = 33;
        var randomizedDatum;


        $.ajax({
          url: 'Controller/history2.php?action=load-history',
          type: 'post',
          dataType: 'json',
          data: {param: JSON.stringify({
            userId: $userId,
            filter: '<?php echo $filter; ?>',
            tickInterval: '<?php echo $tickInterval; ?>',
            device: device,
            mm: $mm
          })},
          success: function (rspdata) {
              console.log(rspdata);
              $maxTradeAmount = rspdata.maxTradeAmount;
              $('.balance-loader').css('display', 'none');
              $('.balance-loader-display').css('display', 'block');
              $('.userFunds').html(toFixedNew(rspdata.funds, 2));
              $isLoadedUpdate = false;
              $isLoadedAdd = false;

              // GET LIVE TRADE table
              $.ajax({
                  url: 'Controller/user.php?action=pending-trades',
                  type: 'post',
                  dataType: 'json',
                  success: function (data) {
                      $('.live-trade-loader').css('display', 'none');
                      $('.live-trade-table').css('display', 'table');
                      for(var i = 0; i < data.length; i++){
                          $amount = parseFloat(data[i].open);
                          if(data[i].type == 'sell'){
                              $fillColor = '#DB333C';
                              $typeText = 'Sell';
                              $tradeId =   "live-guide-sell-"+($tradeIndex+1);
                              $tradeClass = 'btn-danger';
                              $tradeText = 'text-danger';
                          }else{
                              $fillColor = '#008D00';
                              $typeText = 'Buy';
                              $tradeId =   "live-guide-buy-"+($tradeIndex+1);
                              $tradeClass = 'btn-success';
                              $tradeText = 'text-success';
                          }



                          $tIndex = 'track_'+($tradeIndex+1);


                          $trade = '<tr class="trade-index-'+$tradeIndex+'" data-type="'+$typeText.toLowerCase()+'" data-index="'+$tradeIndex+'"><td class="'+$tradeText+'">'+$typeText+'</td><td class="td-text">'+toFixedNew($amount, 8)+'</td><td class="td-text">'+data[i].pair+'</td><td><span class="timer active-timer td-text"></span></td><td class="td-text"></td><td><button class="btn btn-xs '+$tradeClass+' trade-status">LIVE</button></td><td><span class="trade-investment td-text">'+data[i].leverage+'</span></td><td><span class="trade-payout">'+data[i].payout+'</span></td></tr>';
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
                                  remainder: 10
                              }
                          );
                          $tradeIndex++;
                      }

                      getUserById($userId);
                  },
                  data: {param: JSON.stringify({userId: $userId})}
              });
              // END LIVE TRADE TABLE

              // LIVE FEED
              var feed = rspdata.dataFiltered
                .slice(rspdata.dataFiltered.length - 60 - indicatorPreRoll)
                .map(function(d) {
                  return {
                    date: parseDate(d.period),
                    open: +d.open,
                    high: +d.max,
                    low: +d.min,
                    close: +d.close,
                    volume: +d.volume
                  };
                })
                .sort(function(a, b) {
                  return d3.ascending(accessor.d(a), accessor.d(b));
                });

              console.log(feed);
              redraw(feed); // populate chart from database

              // date variable to see date changes
               var lastDate = Date.parse(feed[feed.length - 1].date);
               lastPrice = toFixedNew(feed[feed.length - 1].close, 1);
               $('.current_price').text(lastPrice, 1);
               $maxQuadPrice = parseFloat($maxTradeAmount / lastPrice * 10000);
               $('.max-quadpips').val(toFixedNew($maxQuadPrice, 2));
              // start fetching data
              setInterval(function(){
                $v = Math.round((new Date()).getTime());
                var data = feed;
                $.getJSON( "coin_live_price/trade.json?v="+$v, function( json ) {
                  var close = json.close;

                  // display current price
                  $('.current_price').text(toFixedNew(close, 1));
                  if(lastPrice > close){
                    $('.percent_status').removeClass('text-success').addClass('text-danger');
                    $('.percent_status i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
                  }else{
                    $('.percent_status').removeClass('text-danger').addClass('text-success');
                    $('.percent_status i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
                  }


                  var high = json.high;
                  var low = json.low;
                  var open = json.open;
                  var volume = json.volume;
                  var date = parseDate(json.unix);
                  var closeInterpolator = d3.interpolate(json.close, close);
                  var highInterpolator = d3.interpolate(json.high, high);
                  var lowInterpolator = d3.interpolate(json.low, low);
                  var steps = 1; // Number of steps during the transition
                  var transitionValues = d3.range(steps).map(function(d) {
                    const t = (d + 1) / steps;
                    return {
                      date: date,
                      open: open,
                      high: highInterpolator(t),
                      low: lowInterpolator(t),
                      close: closeInterpolator(t),
                      volume: volume
                    };
                  });
                  // The actual transition
                  var i = 0;
                  var transition = setInterval(function() {
                    if(lastDate === json.unix){ // if lastDate == date update last object
                                      // redraw(data.slice().concat(transitionValues[i]));
                                      data[data.length -1 ] = transitionValues[i];
                                      redraw(data);
                                      i++;
                    }else{ // remove first index and add new

                      data.shift(); // removes first index
                      data.push(transitionValues[i]);

                      redraw(data);
                      i++;
                    }
                    // set last date
                    lastDate = json.unix;
                    lastPrice = toFixedNew(close, 1);
                    if (i === steps) clearInterval(transition);
                  }, 50);
                });
              }, 1000);
          }
        });



        function redraw(data) {
          x.domain(techan.scale.plot.time(data.slice(indicatorPreRoll)).domain());
          y.domain(techan.scale.plot.ohlc(data.slice(indicatorPreRoll)).domain());
          yPercent.domain(
            techan.scale.plot.percent(y, accessor(data[indicatorPreRoll])).domain()
          );
          yVolume.domain(techan.scale.plot.volume(data).domain());

          var macdData = techan.indicator.macd()(data);
          macdScale.domain(techan.scale.plot.macd(macdData).domain());
          var rsiData = techan.indicator.rsi()(data);
          rsiScale.domain(techan.scale.plot.rsi(rsiData).domain());

          svg
            .select("g.candlestick")
            .datum(data)
            .call(candlestick);

          svg
            .select("g.close.annotation")
            .datum([data[data.length - 1]])
            .call(closeAnnotation)
            .classed("up", function(d) {
              d3.select("#LatestVal").text(d[0].close);
              return d[0].close >= d[0].open;
            })
            .classed("down", function(d) {
              d3.select("#LatestVal").text(d[0].close);
              return d[0].open > d[0].close;
            });
          svg
            .select("g.volume")
            .datum(data)
            .call(volume);
          svg
            .select("g.sma.ma-0")
            .datum(techan.indicator.sma().period(10)(data))
            .call(sma0);
          svg
            .select("g.sma.ma-1")
            .datum(techan.indicator.sma().period(20)(data))
            .call(sma1);
          svg
            .select("g.ema.ma-2")
            .datum(techan.indicator.ema().period(50)(data))
            .call(ema2);
          svg
            .select("g.macd .indicator-plot")
            .datum(macdData)
            .call(macd);
          svg
            .select("g.rsi .indicator-plot")
            .datum(rsiData)
            .call(rsi);

          svg.select("g.crosshair.ohlc").call(ohlcCrosshair);
          svg.select("g.crosshair.macd").call(macdCrosshair);
          svg.select("g.crosshair.rsi").call(rsiCrosshair);

          svg.select("g.x.axis").call(xAxis.ticks(d3.timeMinute, 5));
          svg.select("g.ohlc .axis").call(yAxis);
          svg.select("g.volume.axis").call(volumeAxis);
          svg.select("g.percent.axis").call(percentAxis);
          svg.select("g.macd .axis.right").call(macdAxis);
          svg.select("g.rsi .axis.right").call(rsiAxis);
          svg.select("g.macd .axis.left").call(macdAxisLeft);
          svg.select("g.rsi .axis.left").call(rsiAxisLeft);
        }
        // END CHART
        // END LIVE feed


        // Buy & Sell, Arrows
        $tradeIndex = 0;
        $arrowTick = [];
        $tradeTransaction = [];

        $cryptoCoin = 'BTC/ETH';
        $cur = 'USD';
        $filter = 'BTC';
        $coin = 'BTC';
        $('#buy').unbind().click(function(){
          $buyButton = $(this);
          $buyButton.attr('disabled', true);
          if(_efx8 == true){
            $funds = parseFloat($('.userFunds').text());
            if($funds > 0){
              if($funds < 10){
                $('.no-funds-text').html('Error: Balance is below required margin amount');
                $('#depositModal').modal('show');
              }else{
                $leverage = parseFloat($('#leverage').val());
                totalTradeAmount += $leverage;
                if(totalTradeAmount > $maxTradeAmount){
                  alert('Maximum Trade Limit Exceeded. Reduce Trade Amount Size.')
                  totalTradeAmount -= $leverage;
                }else{
                  if(($funds - totalTradeAmount) < 0){
                    $('.no-funds-text').html('Error: Balance is below required margin amount');
                    $('#depositModal').modal('show');
                    totalTradeAmount -= $leverage;
                  }else{
                    $newBal = $funds - $leverage;
                    $('.userFunds').text(toFixedNew($newBal, 2));
                    $amount = lastPrice;

                    $chargePercent = $leverage - ($leverage * .70);
                    $chargePercent = $leverage - $chargePercent;


                    if($chargePercent >= $tradePercentageAmount){
                      $leverageCut = .85;
                      $percentCut = 15;
                    }else{
                      $leverageCut = .70;
                      $percentCut = 30;
                    }
                    $percentCutAmount = $leverage - ($leverage * $leverageCut);
                    $payout = ($leverage * $leverageCut) + $leverage;
                    $leverageDisplay = parseFloat($('#leverage').val());
                    $payoutDisplay = ($leverage * $leverageCut) + $leverage;
                    $winAmount = $leverage * $leverageCut;

                    //NOTE: put a marker in the chart


                    $trade = '<tr class="trade-index-'+$tradeIndex+'" data-type="buy" data-index="'+$tradeIndex+'"><td class="text-success">Buy</td><td class="td-text">'+$amount+'</td><td class="td-text">'+$cryptoCoin+$cur+'</td><td class="td-text"><span class="timer active-timer"></span></td><td class="exit-price td-text"></td><td><button class="btn btn-xs btn-success trade-status">LIVE</button></td><td><span class="trade-investment td-text">'+$leverageDisplay+'</span></td><td><span class="trade-payout">'+$payoutDisplay+'</span></td></tr>';
                    $('.trade-log').prepend($trade);

                    $logTradeData = {
                        expires: parseInt($('.selected-expire').attr('data-value')),
                        type: 'buy',
                        status: 'live',
                        amount: $amount,
                        leverage: $leverage,
                        payout: $payout,
                        leverageDisplay: $leverageDisplay,
                        payoutDisplay: $payoutDisplay,
                        winAmount: $winAmount,
                        ticker: $filter,
                        userId: $userId,
                        pair: $cryptoCoin+$cur,
                        remainder: 10,
                        percentCut: $percentCut,
                        percentCutAmount: $percentCutAmount,
                        balanceId: $balanceId
                    };

                    console.log($logTradeData);
                    $tIndex = 'track_'+($tradeIndex+1);
                    // $tracks[$tIndex] = ['amcharts-guide-live-guide-buy-'+($tradeIndex+1), '#008D00'];
                    //localStorage.setItem("tracks", JSON.stringify($tracks));
                    $.ajax({
                        url: 'Controller/user.php?action=register-trade',
                        type: 'post',
                        dataType: 'json',
                        success: function (data) {

                            $tradeTransaction.push(
                                {
                                    index: $tradeIndex,
                                    time: parseInt($('.selected-expire').attr('data-value')),
                                    amount: data.data.amount,
                                    leverage: data.data.leverageDisplay,
                                    payout: data.data.payoutDisplay,
                                    leverageDisplay: data.data.leverageDisplay,
                                    payoutDisplay: data.data.payoutDisplay,
                                    winAmount: data.data.winAmount,
                                    type: 'buy',
                                    status: 'live',
                                    tradeId: data.tradeId,
                                    pair: $coin+$cur,
                                    remainder: 10
                                }
                            );

                            console.log('R');
                            console.log($tradeTransaction);

                            $balanceId = data.balanceStatus.id;
                            $tradePercentageAmount = data.balanceStatus.trade_percentage_amount;

                            $tradeIndex++;
                        },
                        data: {param: JSON.stringify($logTradeData)}
                    });

                    //countDownTimer(0, 'buy');
                  }
                }
              }

              }else{
                // $('.no-funds-text').html('Error: Maximum Funds Margin Limit Exceeded');
                $('.no-funds-text').html('Error: Margin not available');
                $('#depositModal').modal('show');
              }
          }else{
            $('#disclaimerModal').modal('show');
          }

          setTimeout(function(){
            $buyButton.removeAttr('disabled');
          }, 300);
        });


        $('#sell').unbind().click(function(){
            $sellButton = $(this);
            $sellButton.attr('disabled', true);
            $funds =  parseFloat($('.userFunds').text());
            if(_efx8 == true){
              if($funds > 0){
                if($funds < 10){
                  $('.no-funds-text').html('Error: Balance is below required margin amount');
                  $('#depositModal').modal('show');
                }else{
                  $leverage = parseFloat($('#leverage').val());
                  totalTradeAmount += $leverage;
                  if(totalTradeAmount > $maxTradeAmount){
                    alert('Maximum Trade Limit Exceeded. Reduce Trade Amount Size.')
                    totalTradeAmount -= $leverage;
                  }else{
                    if(($funds - totalTradeAmount) < 0){
                      $('.no-funds-text').html('Error: Balance is below required margin amount');
                      $('#depositModal').modal('show');
                      totalTradeAmount -= $leverage;
                    }else{
                      $newBal = $funds - $leverage;
                      $('.userFunds').text(toFixedNew($newBal, 2));
                      $amount = lastPrice;

                      $chargePercent = $leverage - ($leverage * .70);
                      $chargePercent = $leverage - $chargePercent;

                      if($chargePercent >= $tradePercentageAmount){
                        $leverageCut = .85;
                        $percentCut = 15;
                      }else{
                        $leverageCut = .70;
                        $percentCut = 30;
                      }

                      $percentCutAmount = $leverage - ($leverage * $leverageCut);
                      $payout = ($leverage * $leverageCut) + $leverage;
                      $leverageDisplay = parseFloat($('#leverage').val());
                      $payoutDisplay = ($leverage * $leverageCut) + $leverage;
                      $winAmount = $leverage * $leverageCut;

                      $trade = '<tr class="trade-index-'+$tradeIndex+'" data-type="sell" data-index="'+$tradeIndex+'"><td class="text-danger">Sell</td><td class="td-text">'+$amount+'</td><td class="td-text">'+$cryptoCoin+$cur+'</td><td><span class="timer active-timer td-text"></span></td><td class="exit-price td-text"></td><td><button class="btn btn-xs btn-success trade-status">LIVE</button></td><td><span class="trade-investment td-text">'+$leverageDisplay+'</span></td><td><span class="trade-payout">'+$payoutDisplay+'</span></td></tr>';
                      $('.trade-log').prepend($trade);
                      $logTradeData = {
                          expires: parseInt($('.selected-expire').attr('data-value')),
                          type: 'sell',
                          status: 'live',
                          amount: $amount,
                          leverage: $leverageDisplay,
                          payout: $payoutDisplay,
                          leverageDisplay: $leverageDisplay,
                          payoutDisplay: $payoutDisplay,
                          winAmount: $winAmount,
                          ticker: $filter,
                          userId: $userId,
                          pair: $cryptoCoin+$cur,
                          remainder: 10,
                          percentCut: $percentCut,
                          percentCutAmount: $percentCutAmount,
                          balanceId: $balanceId
                      };

                      $tIndex = 'track_'+($tradeIndex+1);

                      $.ajax({
                          url: 'Controller/user.php?action=register-trade',
                          type: 'post',
                          dataType: 'json',
                          success: function (data) {
                              $tradeTransaction.push(
                                  {
                                      index: $tradeIndex,
                                      time: parseInt($('.selected-expire').attr('data-value')),
                                      amount: data.data.amount,
                                      leverage: data.data.leverageDisplay,
                                      payout: data.data.payoutDisplay,
                                      leverageDisplay: data.data.leverageDisplay,
                                      payoutDisplay: data.data.payoutDisplay,
                                      winAmount: data.data.winAmount,
                                      type: 'sell',
                                      status: 'live',
                                      tradeId: data.tradeId,
                                      remainder: 10
                                  }
                              );
                              $balanceId = data.balanceStatus.id;
                              $tradePercentageAmount = data.balanceStatus.trade_percentage_amount;
                              $tradeIndex++;
                          },
                          data: {param: JSON.stringify($logTradeData)}
                      });
                    }
                  }

                }

              }else{
                $('.no-funds-text').html('Error: No Funds Available');
                $('#depositModal').modal('show');
              }
            }else{
              $('#disclaimerModal').modal('show');
            }

            setTimeout(function(){
              $sellButton.removeAttr('disabled');
            }, 300);
        });

        // Timer

        setInterval(function(){

            for($a = 0; $a < $tradeTransaction.length; $a++){
                $index = $tradeTransaction[$a]['index'];
                $row = $('.trade-index-'+$index);
                $minute = $tradeTransaction[$a]['time'];
                $status = $tradeTransaction[$a]['status'];
                $leveraget = $tradeTransaction[$a]['leverage'];
                $winAmount = $tradeTransaction[$a]['winAmount'];
                $tradeId = $tradeTransaction[$a]['tradeId'];
                $remainder = parseInt($tradeTransaction[$a]['remainder']);
                $pairing = $tradeTransaction[$a]['pair'];
                $payoutt = $tradeTransaction[$a]['payout'];
                if($status == 'live'){

                    $type = $tradeTransaction[$a]['type'];
                    $entryValue = $tradeTransaction[$a]['amount'];

                    $amount = lastPrice;
                    $profit = toFixedNew((($amount*10) - ($entryValue*10)), 1); // move decimal places to right by tenths

                    console.log($amount, $entryValue);
                    console.log('Profit', $profit);

                    if($profit >= 0){
                        $row.find('.trade-payout').removeClass('text-danger').addClass('text-success').text('+'+$profit);
                    }else{
                      $row.find('.trade-payout').removeClass('text-success').addClass('text-danger').text($profit);
                    }

                    if($minute == 0){

                        if($type == 'buy'){
                            if($amount > $entryValue){
                                $row.find('.trade-status').removeClass('btn-default').addClass('btn-success').text('CLOSE');
                                $tradeStatus = 'win';
                                // $row.find('.trade-payout').removeClass('text-danger').addClass('text-success').text($payoutt);
                                $row.find('.exit-price').text($amount);

                            }else if($amount == $entryValue){
                              $row.find('.trade-status').removeClass('btn-default').addClass('btn-default').text('CLOSE');
                              $row.find('.trade-payout').text('0');
                              $tradeStatus = 'even';
                              // $row.find('.trade-payout').removeClass('text-success').removeClass('text-danger').text(0);
                              $row.find('.exit-price').text($amount);
                            }else{
                                $row.find('.trade-status').removeClass('btn-default').addClass('btn-danger').text('CLOSE');
                                $row.find('.trade-payout').text($profit);
                                $tradeStatus = 'lost';
                                $row.find('.trade-payout').removeClass('text-success').addClass('text-danger').text(0);
                                $row.find('.exit-price').text($amount);
                            }

                        }else{
                            if($amount < $entryValue){
                                $row.find('.trade-status').removeClass('btn-default').addClass('btn-success').text('CLOSE');
                                $row.find('.trade-payout').removeClass('text-danger').addClass('text-success').text($payoutt);
                                $row.find('.exit-price').text($amount);
                                $tradeStatus = 'win';
                            }else if($amount == $entryValue){
                              $tradeStatus = 'even';
                              $row.find('.trade-status').removeClass('btn-default').addClass('btn-default').text('CLOSE');
                              $row.find('.trade-payout').text('0');
                              $row.find('.trade-payout').removeClass('text-success').removeClass('text-danger').text(0);
                              $row.find('.exit-price').text($amount);
                            }else{
                                $tradeStatus = 'lost';
                                $row.find('.trade-status').removeClass('btn-default').addClass('btn-danger').text('CLOSE');
                                $row.find('.trade-payout').text('0');
                                $row.find('.trade-payout').removeClass('text-success').addClass('text-danger').text(0);
                                $row.find('.exit-price').text($amount);
                            }
                        }

                        $tradeData = {
                            close: $amount,
                            winAmount: $profit,
                            leverage: $leveraget,
                            status: $tradeStatus,
                            tradeId: $tradeId,
                            userId: $userId
                        }

                        if($tradeStatus == 'win'){
                          console.log($tradeData);
                        }


                        logTrades($tradeData);

                        $('.amcharts-chart-div a').css('display', 'none');
                        $tradeTransaction[$a]['status'] = 'complete';
                        totalTradeAmount -= $leveraget;

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


                }else if($status == 'complete'){

                    if($remainder == 0){
                        $('tr.trade-index-'+$index).remove();
                    }

                    $tradeTransaction[$a]['remainder'] = $remainder - 1;
                }


                // check win los situation

            }
        }, 1000);
        // end timer



        // show trade history
        $('#getHistoryTrigger').click(function(){
          $('#tradeHistoryModal').modal('show');
          $('.trade-table').css('display', 'none');
          $('.table-loader').css('display', 'block');
          $('#tradeHistoryBody').html('<tr><td colspan="7 text-center">Loading data....</td></tr>');
          $.ajax({
              url: 'Controller/history2.php?action=trade-history',
              type: 'post',
              dataType: 'json',
              success: function (data) {
                $('.table-loader').css('display', 'none');
                $('.trade-table').css('display', 'table');
                $tableBody = '';
                  console.log(data);
                if(data.length == 0){
                  $tableBody += '<tr><td colspan="8" class="text-center">No data available</td></tr>';
                }else{
                  $totalPnl = 0;
                  for($z = 0; $z < data.length; $z++){

                    $entryPrice = data[$z].entryPrice;
                    $position = data[$z].position;
                    $entryTime = data[$z].entryTime;
                    $lvrg = data[$z].leverage;
                    $exitPrice = data[$z].closingPrice;
                    $exitTime = data[$z].closingTime;
                    $pnl = data[$z].pnl;
                    $status = data[$z].status;
                    $pair = data[$z].pair;
                    $cost = data[$z].trade_percentage_amount;
                    $tradePercentCut = data[$z].trade_percent_cut;
                    if($tradePercentCut == 15){
                      $rebateText = "";
                      $spacer = "";
                    }else{
                      $rebateText = "";
                      $spacer = "";
                    }

                    if($status == 'win'){
                      $textClass = 'text-success';
                      $pnl = '+'+$pnl;
                    }else if($status == 'even'){
                      $textClass = 'text-default';
                      $pnl = 0;
                    }else if($status == 'Deposit' || $status == 'Withdraw'){
                      $textClass = 'text-default';
                    }else{
                      $textClass = 'text-danger';
                      $pnl = '-'+$pnl;
                    }


                    if($status == 'win' || $status == 'lost'){
                      $totalPnl += parseFloat($pnl);
                    }
                    $tableBody += '<tr>'
                                  +'<td>'+$spacer+ $entryPrice + '</td>'
                                  +'<td>'+$spacer+ $position + '</td>'
                                  +'<td>'+$spacer+ $entryTime + '</td>'
                                  +'<td>'+ $rebateText+$lvrg + '</td>'
                                  +'<td>'+ $rebateText+$exitPrice + '</td>'
                                  +'<td>'+$spacer+ $exitTime + '</td>'
                                  +'<td>'+$spacer+ $pair + '</td>'
                                  +'<td>'+$spacer+ $cost + '</td>'
                                  +'<td class="'+$textClass+'">'+$spacer+ $pnl + '</td>'
                                +'</tr>';
                  }

                  $totalPnlClass = 'text-success';
                  $totalPnlSymbol = '+';
                  if($totalPnl < 0){
                    $totalPnlClass = 'text-danger';
                    $totalPnlSymbol = '';
                  }else if($totalPnl == 0){
                    $totalPnlClass = '';
                    $totalPnlSymbol = '';
                  }
                  $('.totalPnL').html('<span class="'+$totalPnlClass +'">'+$totalPnlSymbol+$totalPnl+'</span>');

                }

                $('#tradeHistoryBody').html($tableBody);

              },
              data: {param: JSON.stringify({userId: $userId, from: $('.date-from').val(), to: $('.date-to').val()})}
          });

        });



        function logTrades($data){
            $.ajax({
                url: 'Controller/user.php?action=update-trades',
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    $('.userFunds').text(toFixedNew(data.response.funds, 2));
                },
                data: {param: JSON.stringify($data)}
            });
        }


        // date datepicker
        $('.date-from').datepicker({
            format: 'mm/dd/yyyy',
        });
        $('.date-to').datepicker({
            format: 'mm/dd/yyyy'
        });


        // expire time
        $('.expires').click(function(){
          //$min = $(this).attr('data-value');
          //zoomChart($min, 1);
            $expireValue = $(this).attr('data-min-value');
            $('.expires').each(function(){
                $(this).removeClass('selected-expire');
            })

            $('.expire-dropdown-current').html($expireValue);

            $(this).removeClass('btn-default').addClass('selected-expire').addClass('btn-primary');
        });
      });
    </script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
<!--    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-127217195-1"></script>-->
<!--    <script>-->
<!--      window.dataLayer = window.dataLayer || [];-->
<!--      function gtag(){dataLayer.push(arguments);}-->
<!--      gtag('js', new Date());-->
<!---->
<!--      gtag('config', 'UA-127217195-1');-->
<!--    </script>-->


</body>
</html>
