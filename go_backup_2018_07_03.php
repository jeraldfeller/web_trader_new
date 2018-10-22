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
    $mm5Class = 'btn-primary';
  }else if($tickInterval == '30mm'){
    $mm30Class = 'btn-primary';
  }else if($tickInterval == '60mm'){
    $mm60Class = 'btn-primary';
  }else if($tickInterval == '240mm'){
    $mm240Class = 'btn-primary';
  }
  $data = json_decode($history->getCoinHistoryMMNew(strtolower($filter), 'desktop', $tickInterval), true);
}else{
  $tickInterval = 'mm';
  $mm1Class = 'btn-primary';
  $data = json_decode($history->getCoinHistory(strtolower($filter), 'desktop'), true);
}


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
  //$funds = number_format($user['dollar_amount'] / $currentBtcPrice * 10000, 2);
  $funds = str_replace(',', '',number_format($currentBtcPrice * 10000, 2));
}else{
  $funds = 0;
}
$urlParam = $_GET;


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
            text-align: center;
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

        .full-width{
          width: 100% !important;
        }
        .select_expiry{
          margin-left: 12px;
          cursor: pointer;
        }
        .spacer-2x{
          margin-top: 24px;
        }

        .outline-primary{
          background-color: transparent;
          background-image: none;
          border-color: #007bff;
          cursor: default;
        }
        .nopadding {
           padding: 0 !important;
           margin: 0 !important;
        }
        .trade-payout{
          font-weight: bold;
        }
    </style>
    <script>
        var zoomEvents = [];
        if (screen.width <= 768) {
            location.href = 'go_mobile.php';
        }

        var lastSecond = '';
    </script>
</head>
<body>
<div class="container" style="padding-top:1%; width: 97%;">

    <strong>
        <font color='#13D384' size='5'>Zolo</font>
        <font size='5'>Trader</font>
    </strong> |  |

    <a href='affiliate'>Affiliate</a> | <a href='logout.php'>Logout</a><br />


</div>
<div class="container-fluid">
    <div class="row">
      <div class="col-md-10 col-sm-10 col-xs-10 nopadding">

        <div id="chartdiv" style="height: 760px;"><img src="assets/img/tenor.gif"></div>
      </div>
      <div class="col-md-2 col-sm-2 col-xs-2 nopadding">
        <div class="col-md-12 col-sm-12 col-xs-12 spacer-2x text-center">
          Balance <i class="fa fa-question-circle popOverTrigger"></i>
          <br>
          <span  type="button" class="btn outline-primary full-width" style="padding-left: 2px; padding-right: 2px;"><span class="userFunds"><?php echo $funds; ?></span></span>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 spacer-2x">
          <button class="btn <?=$mm1Class?> btn-sm candle-select-range" data-value="1mm">1M</button>
          <button class="btn <?=$mm5Class?> btn-sm candle-select-range" data-value="5mm">5M</button>
          <button class="btn <?=$mm30Class?> btn-sm candle-select-range" data-value="30mm">30M</button>
          <button class="btn <?=$mm60Class?> btn-sm candle-select-range" data-value="60mm">60M</button>
          <button class="btn <?=$mm240Class?> btn-sm candle-select-range" data-value="240mm">240M</button>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 spacer-2x"></div>
        <div class="col-md-6 col-sm-12 col-xs-12 spacer">
            <button  class="btn btn-primary full-width" id="getHistoryTrigger" title="Trade History">Trade History</button>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12 spacer">
            <button  class="btn btn-primary full-width" title="Profile" id="profileBtn">Profile</button>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 spacer-2x text-center">
          Market
          <br>
          <div class="btn-group full-width">
            <button type="button" class="btn btn-primary dropdown-toggle full-width" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
            </ul>
          </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 spacer-2x text-center">
          Expiry Time
          <br>
          <div class="btn-group full-width">
              <button type="button" class="btn btn-primary dropdown-toggle full-width expire-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 spacer-2x text-center">
          Leverage = <span class=""><img src="assets/img/btc-logo.ico" style="width: 25px; height: 25px; margin-bottom: 5px;"></span><span class="current-exchange-price text-bold"></span> <i class="fa fa-question-circle popOverTrigger"></i>
          <br>
          <input type="hidden" id="current_price">
          <input type="hidden" class="max-quadpips">
          <input type="hidden" class="max-dollar-price">
          <input id="leverage" type="number" value='0.5' min='0.1' class="form-control" aria-label="3.5">
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 spacer-2x"></div>
        <div class="col-md-12 col-sm-12 col-xs-12 spacer-2x"></div>
        <div class="col-md-12 col-sm-12 col-xs-12 spacer-2x">
          <button class="btn btn-success btn-lg full-width" style="font-size: 25px; height: 100px; font-weight: bold;" id='buy' >Buy</button>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 spacer-2x">
          <button class="btn btn-danger btn-lg full-width" style="font-size: 25px; height: 100px; font-weight: bold;" id='sell' >Sell</button>
        </div>
      </div>
    </div>

    <div class="row">
        <div class="col-md-10 col-sm-10 col-xs-10">
          <div class="col-md-12 text-center">
              <span style="font-size: 24px;"><span class="current_price"></span><span class="percent_status"><i class="fa"></i></span></span>
          </div>
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

<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="porfile-container-body">
              <div class="col-md-12 text-center" style="margin-top: 24px;">
                <button class="btn btn-primary btn-lg show-withdraw-form ">Withdraw Funds</button>
              </div>
              <div class="col-md-12 text-center" style="margin-top: 24px; ">
                <a href="buy-funds.php" class="btn btn-primary btn-lg">Deposit Funds</a>
              </div>
              <div class="col-md-12 text-center" style="margin-top: 24px;">
                <button class="btn btn-primary btn-lg">Manage Personal Information</button>
              </div>
              <div class="col-md-12 spacer-2x"></div>
              <div class="col-md-12 spacer-2x"></div>
              <div class="col-md-12" style="margin-top: 24px;">
                <a href="logout.php" class="btn btn-primary btn-lg pull-right">Logout</a>
              </div>
            </div>
            <div class="porfile-input-container-body" style="display: none;">
              <div class="col-md-12">
                <h3>Please fill your information.</h3>
                <h3>A NanoPips Specialist will contact you shortly.</h3>
              </div>
              <div class="col-md-12 spacer">
                <input type="text" class="form-control" id="fullName" placeholder="Full Name">
              </div>
              <div class="col-md-12 spacer">
                <input type="text" class="form-control" id="address" placeholder="Address">
              </div>
              <div class="col-md-12 spacer">
                <input type="number" class="form-control" id="widthdrawAmount" placeholder="Amount">
              </div>
              <div class="col-md-12 spacer text-center">
                <button class="btn btn-primary btn-lg sendWithdrawRequest">Send</button>
              </div>
            </div>
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
            <h1>Error: No Funds Available</h1>
          </div>
          <div class="col-md-12 text-center" style="margin-top: 24px;">
            <a href="buy-funds.php" class="btn btn-primary btn-lg">Deposit Now</a>
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
        </div>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">Entry Price</th>
                  <th scope="col">Position</th>
                  <th scope="col">Time</th>
                  <th scope="col">Leverage</th>
                  <th scope="col">Exit Price</th>
                  <th scope="col">Time</th>
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


<script type="text/javascript" language="JavaScript">
    $(document).ready(function(){
        $mm = '<?=$tickInterval?>';
        $('.sendWithdrawRequest').click(function(){

          if($('#fullName').val() == '' || $('#address').val() == '' || $('#widthdrawAmount').val() == ''){
            alert('Please complete the input required.');
          }else{
            $sendData = {
              userId: $userId,
              fullName: $('#fullName').val(),
              address: $('#address').val(),
              amount: $('#widthdrawAmount').val()
            }

            $.ajax({
                url: 'Controller/user.php?action=request-withdraw',
                type: 'post',
                dataType: 'json',
                success: function (data) {
                  alert('Request successfully sent.');
                  $('#profileModal').modal('hide');
                },
                data: {param: JSON.stringify($sendData)}
            });


          }

        });

        $('.show-withdraw-form').click(function(){
            $('.porfile-container-body').css('display', 'none');
            $('.porfile-input-container-body').css('display', 'block');
        });

        $('#profileBtn').click(function(){
            $('.porfile-container-body').css('display', 'block');
            $('.porfile-input-container-body').css('display', 'none');
            $('#profileModal').modal('show');
        });

        $('.popOverTrigger').popover({
          container: 'body',
          trigger: 'hover',
          placement: 'top',
          content: 'This value is your NanoPips value displayed as BTC x 10,000 to give you simpler whole numbers to trade with.'
        });
        $tracks = new Object;
        $tracks['track_0'] = ['amcharts-guide-live-guide', 'black'];
        localStorage.setItem("tracks", JSON.stringify($tracks));
        $tradeIndex = 0;

        $isLoadedUpdate = false;
        $isLoadedAdd = false;
        $btcUsd = <?php echo $currentBtcPrice; ?>;
        $currentBtcPrice = <?php echo $currentBtcPrice; ?>;
        $currentCoinPrice = toFixedNew(<?php echo $currentCoinPrice; ?>, 6);
        $cur = '<?php echo $currency; ?>';
        $currencyPair = '<?php echo $currencyPair; ?>';
        $userId = <?php echo $userData['id']; ?>;
        $coin = '<?php echo $filter; ?>';
        $cryptoCoin = '<?php echo $filter; ?>';
        $maxQuadPrice = parseFloat(250 / $currentBtcPrice * 10000);
        $('.max-quadpips').val(toFixedNew($maxQuadPrice, 2));
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
            $expireValue = $(this).attr('data-min-value');
            $('.expires').each(function(){
                $(this).removeClass('selected-expire');
            })

            $('.expire-dropdown-current').html($expireValue);

            $(this).removeClass('btn-default').addClass('selected-expire').addClass('btn-primary');
        });


        $('#current_price').val($currentCoinPrice);
        $curExcPrice = $('#leverage').val() / 10000;
        $('.current-exchange-price').text(toFixedNew($curExcPrice,6));
        $('.select_coin').click(function(){
            location.href = 'go.php?coin='+$(this).attr('data-coin')+'&currency='+$(this).attr('data-pair');
        });
        $('.candle-select-range').click(function(){
          $candleRange = $(this).attr('data-value');
          $urlParam = <?php echo json_encode($urlParam); ?>;
          console.log($urlParam);

          if(typeof $urlParam.coin != 'undefined' && typeof $urlParam.currency != 'undefined'){
            if (screen.width <= 768) {
                location.href = 'go_mobile.php?coin='+$urlParam.coin+'&currency='+$urlParam.currency+'&c='+$candleRange
            }else{
                location.href = 'go.php?coin='+$urlParam.coin+'&currency='+$urlParam.currency+'&c='+$candleRange
            }
          }else{
            if (screen.width <= 768) {
                  location.href = 'go_mobile.php?c='+$candleRange
            }else{
                  location.href = 'go.php?c='+$candleRange
            }

          }

        });
        $('.current_price').text($currentCoinPrice);
        $.getJSON('https://coincap.io/exchange_rates').done(function(responseRates){
            $rates = responseRates.rates;
            $dateRange = '1day';
            $filter = '<?php echo $filter; ?>';
            if($cur != 'BTC'){
                $curCoinPrice = $currentCoinPrice * $rates[$cur];
                $('#current_price').val($curCoinPrice);
                if($filter == 'XRP' || $filter == 'ETC' ){
                  $('.current_price').text(toFixedNew($curCoinPrice, 6));
                }else{
                  $('.current_price').text(toFixedNew($curCoinPrice, 2));
                }

            }else{
              if($filter == 'ETC'){
                $curCoinPrice = $currentCoinPrice / $currentBtcPrice;
                console.log($cur + 'XX: '+ $rates[$cur])
                $('.current_price').text(toFixedNew($curCoinPrice, 6));
              }
            }
            $tickHistory = <?php echo $dataFiltered; ?>;
            $data_trade = [];
            for($x = 0; $x < $tickHistory.length; $x++){
                $tickDate = $tickHistory[$x]['timestamp'];
                $tickOpen = $tickHistory[$x]['open'];
                $tickHigh = $tickHistory[$x]['max'];
                $tickLow = $tickHistory[$x]['min'];
                $tickClose = $tickHistory[$x]['close'];

                $date = new Date($tickDate);
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

                $data_trade.push(
                    {
                        "date" : $tickDate,
                        "open" : toFixedNew($open, 6),
                        "high" :  toFixedNew($high, 6),
                        "low"  :  toFixedNew($low, 6),
                        "close" :  toFixedNew($close, 6)
                    }

                );

                if($tickHistory.length-1 == $x){
                  $group = [parseFloat(toFixedNew($open, 6)),parseFloat(toFixedNew($high, 6)),parseFloat(toFixedNew($low, 6)),parseFloat(toFixedNew($close, 6))];
                }
            }

            console.log($data_trade);

            //$data.splice(0, 40);
            // render chart

            if($filter == 'XRP'){
              $tickValue = $currentCoinPrice

            }else{
              $tickValue = ($cur == 'BTC' ? <?php echo $currentCoinPrice; ?> / $currentBtcPrice : <?php echo $currentCoinPrice; ?> * $rates[$cur]);

            }
            if($filter == 'XRP' || $filter == 'ETC' || $filter == 'LTC'){
              $tickValue = toFixedNew($tickValue, 6);
        //      console.log($tickValue);
            }else{
              $tickValue = toFixedNew($tickValue, 4);
            }
            $chart = AmCharts.makeChart( "chartdiv", {
                "hideCredits":true,
                "type": "serial",
                "theme": "light",
                //"dataDateFormat":"YYYY-MM-DD JJ:NN:SS",
                "dataProvider": $data_trade,
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
                        "color": "#fff",
                        "fontSize": 16,
                        "backgroundColor": "#008D00",
                        "id": "live-guide"
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
                    "minPeriod": "<?=$tickInterval?>",
                    "parseDates": true,
                },
                "dataDateFormat": "YYYY-MM-DD HH:NN:SS.Q",
                "export": {
                    "enabled": true
                }
            } );

            $chart.addListener( "rendered", zoomChart );
            //$chart.graphsSet.toBack();
            $chart.addListener("zoomed", handleZoom);


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
                            $tradeId =   "live-guide-sell-"+($tradeIndex+1);
                            $tradeClass = 'btn-danger';
                        }else{
                            $fillColor = '#008D00';
                            $typeText = 'Buy';
                            $tradeId =   "live-guide-buy-"+($tradeIndex+1);
                            $tradeClass = 'btn-success';
                        }

                        $chart.valueAxes[0].guides.push(
                            {
                                "value": toFixedNew($amount, 2),
                                "label": toFixedNew($amount, 2),
                                "position": "right",
                                "dashLength": 0,
                                "axisThickness": 0.1,
                                "fillColor": $fillColor,
                                "axisAlpha": 1,
                                "fillAlpha": 1,
                                "color": "#fff",
                                "fontSize": 16,
                                "backgroundColor": $fillColor,
                                "id": $tradeId
                            }
                        );
                        $startIndex = $chart.startIndex;
                        $endIndex = $chart.endIndex - $chart.dataProvider.length;
                        $chart.validateData();
                        $tIndex = 'track_'+($tradeIndex+1);
                        $tracks[$tIndex] = ['amcharts-guide-'+$tradeId, $fillColor];
                        addRect($tracks);
                        localStorage.setItem("tracks", JSON.stringify($tracks));

                        zoomChart($startIndex, $endIndex);

                        $trade = '<tr class="trade-index-'+$tradeIndex+'" data-type="'+$typeText.toLowerCase()+'" data-index="'+$tradeIndex+'"><td>'+$typeText+'</td><td>'+data[i].pair+'</td><td><span class="timer active-timer"></span></td><td><button class="btn btn-xs '+$tradeClass+' trade-status">LIVE</button></td><td><span class="trade-investment">'+data[i].leverage+'</span></td><td><span class="trade-payout">'+data[i].payout+'</span></td></tr>';
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
            console.log($tracks);
              zoomChart(190, 1);

            addRect($tracks);
            $('.amcharts-chart-div a').css('display', 'none');



             function handleZoom(event) {
               $tracks = JSON.parse(localStorage.getItem("tracks"));
               addRect($tracks);
             }





            console.log('Cur: ' + $cur);
            console.log('Filter: ' + $filter);
            console.log('Pair: ' + $currencyPair);
            var sock = 0;
            $coin = $filter;
            setInterval(function(){
              $.getJSON( "coin_live_price/"+$filter+".json", function( json ) {
                $price = json.dollarPrice;
                $currentDate = json.timestamp;
                $currentMatchDate = json.matchDate;
                if($coin == 'BTC'){
                    $currentBtcPrice = $price;
                    $maxQuadPrice = parseFloat(250 / $currentBtcPrice * 10000);
                    $('.max-quadpips').val(toFixedNew($maxQuadPrice, 2));
                    $curExcPrice = $('#leverage').val() / 10000;
                    $('.current-exchange-price').text(toFixedNew($curExcPrice, 6));
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


                    $beforePrice = parseFloat($('#current_price').val());
                    if($filter =='XRP'){
                        $dPrice = $price;
                    }else{
                        $dPrice = ($cur == 'BTC' ? $price / $currentBtcPrice : $price * $rates[$cur]);
                    }

                //    console.log($dPrice);
                    if($filter == 'XRP' || $filter == 'ETC' || $filter == 'ETH'){
                      $('#current_price').val(toFixedNew($dPrice, 6));
                      $('.current_price').text(toFixedNew($dPrice, 6));
                    }else{
                      $('#current_price').val(toFixedNew($dPrice, 6));
                      $('.current_price').text(toFixedNew($dPrice, 6));
                    }

                    if($price > $beforePrice){

                        $('.percent_status').removeClass('text-danger').addClass('text-success');
                        $('.percent_status').find('i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
                    }else{

                        $('.percent_status').removeClass('text-success').addClass('text-danger');
                        $('.percent_status').find('i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
                    }

                    // Get Last Trade
                    $curentDataLength = $chart.dataProvider.length;

                    $lastDate = $chart.dataProvider[$curentDataLength - 1].date;
                    $lastDate = $lastDate.split(':');

                    $lastDate = $lastDate[0]+':'+$lastDate[1]+':00';
                  //  console.log('Last: ' + $lastDate);
                    //   console.log('Current Date: ' + $currentDate + ' | Last Date: ' + $lastDate);
                //    console.log('Last:'+$lastDate);
                      $operator = '===';

                      if($mm == 'mm'){
                        if($currentMatchDate == $lastDate){

                            $group.push($dPrice);

                            $ohlcData = computeOHLC($group);

                            if($cur == 'BTC'){
                                if($currencyPair == 'BTC'){
                                    $open = parseFloat($ohlcData[0]);
                                    $high = parseFloat($ohlcData[1]);
                                    $low = parseFloat($ohlcData[2]);
                                    $close = parseFloat($ohlcData[3]);
                                }else{
                                    if($filter == 'ETH' || $filter == 'ETC'){
                                      $open = parseFloat($ohlcData[0]);
                                      $high = parseFloat($ohlcData[1]);
                                      $low = parseFloat($ohlcData[2]);
                                      $close = parseFloat($ohlcData[3]);
                                    }else{
                                      $open = parseFloat($ohlcData[0] / $currentBtcPrice);
                                      $high = parseFloat($ohlcData[1] / $currentBtcPrice);
                                      $low = parseFloat($ohlcData[2] / $currentBtcPrice);
                                      $close = parseFloat($ohlcData[3] / $currentBtcPrice);
                                    }

                                }
                            }else{
                                if($filter == 'BTC'){
                                  $open = parseFloat($ohlcData[0]);
                                  $high = parseFloat($ohlcData[1]);
                                  $low = parseFloat($ohlcData[2]);
                                  $close = parseFloat($ohlcData[3]);
                                }else{
                                  if($filter == 'LTC'){
                                    if($cur == 'CNY'){
                                      $open = parseFloat($ohlcData[0]);
                                      $high = parseFloat($ohlcData[1]);
                                      $low = parseFloat($ohlcData[2]);
                                      $close = parseFloat($ohlcData[3]);
                                    }
                                  }else{
                                    $open = parseFloat($ohlcData[0] * $rates[$cur]);
                                    $high = parseFloat($ohlcData[1] * $rates[$cur]);
                                    $low = parseFloat($ohlcData[2] * $rates[$cur]);
                                    $close = parseFloat($ohlcData[3] * $rates[$cur]);
                                  }

                                }
                            }

                            $chart.dataProvider[$curentDataLength - 1] = {
                                "date" : $lastDate,
                                "open" : toFixedNew($open, 6),
                                "high" :  toFixedNew($high, 6),
                                "low"  :  toFixedNew($low, 6),
                                "close" :  toFixedNew($close, 6)
                            }
                        }else{

                            $group = [];

                            $group.push($dPrice);
                            $ohlcData = computeOHLC($group);

                            if($cur == 'BTC'){
                                if($currencyPair == 'BTC'){
                                  $open = parseFloat($ohlcData[0]);
                                  $high = parseFloat($ohlcData[1]);
                                  $low = parseFloat($ohlcData[2]);
                                  $close = parseFloat($ohlcData[3]);
                                }else{
                                  if($filter == 'ETH' || $filter == 'ETC'){
                                    $open = parseFloat($ohlcData[0]);
                                    $high = parseFloat($ohlcData[1]);
                                    $low = parseFloat($ohlcData[2]);
                                    $close = parseFloat($ohlcData[3]);
                                  }else{
                                    $open = parseFloat($ohlcData[0] / $currentBtcPrice);
                                    $high = parseFloat($ohlcData[1] / $currentBtcPrice);
                                    $low = parseFloat($ohlcData[2] / $currentBtcPrice);
                                    $close = parseFloat($ohlcData[3] / $currentBtcPrice);
                                  }
                                }
                            }else{
                              if($filter == 'BTC'){
                                $open = parseFloat($ohlcData[0]);
                                $high = parseFloat($ohlcData[1]);
                                $low = parseFloat($ohlcData[2]);
                                $close = parseFloat($ohlcData[3]);
                              }else{
                                if($filter == 'LTC'){
                                  if($cur == 'CNY'){
                                    $open = parseFloat($ohlcData[0]);
                                    $high = parseFloat($ohlcData[1]);
                                    $low = parseFloat($ohlcData[2]);
                                    $close = parseFloat($ohlcData[3]);
                                  }
                                }else{
                                  $open = parseFloat($ohlcData[0] * $rates[$cur]);
                                  $high = parseFloat($ohlcData[1] * $rates[$cur]);
                                  $low = parseFloat($ohlcData[2] * $rates[$cur]);
                                  $close = parseFloat($ohlcData[3] * $rates[$cur]);
                                }

                              }
                            }

                            $chart.dataProvider.shift(); // removes first index
                            $chart.dataProvider.push( {
                                "date" : $currentDate,
                                "open" : toFixedNew($open, 6),
                                "high" :  toFixedNew($high, 6),
                                "low"  :  toFixedNew($low, 6),
                                "close" :  toFixedNew($close, 6)
                            } );



                        }
                      }else{
                        $lastDateMM = getLastMM($lastDate, $mm);
                        $lastDate = $lastDate.replace('T', ' ');
                        if($currentDate > $lastDateMM && $currentDate <= $lastDate){

                            $group.push($dPrice);

                            $ohlcData = computeOHLC($group);

                            if($cur == 'BTC'){
                                if($currencyPair == 'BTC'){
                                    $open = parseFloat($ohlcData[0]);
                                    $high = parseFloat($ohlcData[1]);
                                    $low = parseFloat($ohlcData[2]);
                                    $close = parseFloat($ohlcData[3]);
                                }else{
                                    if($filter == 'ETH' || $filter == 'ETC'){
                                      $open = parseFloat($ohlcData[0]);
                                      $high = parseFloat($ohlcData[1]);
                                      $low = parseFloat($ohlcData[2]);
                                      $close = parseFloat($ohlcData[3]);
                                    }else{
                                      $open = parseFloat($ohlcData[0] / $currentBtcPrice);
                                      $high = parseFloat($ohlcData[1] / $currentBtcPrice);
                                      $low = parseFloat($ohlcData[2] / $currentBtcPrice);
                                      $close = parseFloat($ohlcData[3] / $currentBtcPrice);
                                    }

                                }
                            }else{
                                if($filter == 'BTC'){
                                  $open = parseFloat($ohlcData[0]);
                                  $high = parseFloat($ohlcData[1]);
                                  $low = parseFloat($ohlcData[2]);
                                  $close = parseFloat($ohlcData[3]);
                                }else{
                                  if($filter == 'LTC'){
                                    if($cur == 'CNY'){
                                      $open = parseFloat($ohlcData[0]);
                                      $high = parseFloat($ohlcData[1]);
                                      $low = parseFloat($ohlcData[2]);
                                      $close = parseFloat($ohlcData[3]);
                                    }
                                  }else{
                                    $open = parseFloat($ohlcData[0] * $rates[$cur]);
                                    $high = parseFloat($ohlcData[1] * $rates[$cur]);
                                    $low = parseFloat($ohlcData[2] * $rates[$cur]);
                                    $close = parseFloat($ohlcData[3] * $rates[$cur]);
                                  }

                                }
                            }

                            $chart.dataProvider[$curentDataLength - 1] = {
                                "date" : $lastDate,
                                "open" : toFixedNew($open, 6),
                                "high" :  toFixedNew($high, 6),
                                "low"  :  toFixedNew($low, 6),
                                "close" :  toFixedNew($close, 6)
                            }
                        }else{

                            $group = [];

                            $group.push($dPrice);
                            $ohlcData = computeOHLC($group);

                            if($cur == 'BTC'){
                                if($currencyPair == 'BTC'){
                                  $open = parseFloat($ohlcData[0]);
                                  $high = parseFloat($ohlcData[1]);
                                  $low = parseFloat($ohlcData[2]);
                                  $close = parseFloat($ohlcData[3]);
                                }else{
                                  if($filter == 'ETH' || $filter == 'ETC'){
                                    $open = parseFloat($ohlcData[0]);
                                    $high = parseFloat($ohlcData[1]);
                                    $low = parseFloat($ohlcData[2]);
                                    $close = parseFloat($ohlcData[3]);
                                  }else{
                                    $open = parseFloat($ohlcData[0] / $currentBtcPrice);
                                    $high = parseFloat($ohlcData[1] / $currentBtcPrice);
                                    $low = parseFloat($ohlcData[2] / $currentBtcPrice);
                                    $close = parseFloat($ohlcData[3] / $currentBtcPrice);
                                  }
                                }
                            }else{
                              if($filter == 'BTC'){
                                $open = parseFloat($ohlcData[0]);
                                $high = parseFloat($ohlcData[1]);
                                $low = parseFloat($ohlcData[2]);
                                $close = parseFloat($ohlcData[3]);
                              }else{
                                if($filter == 'LTC'){
                                  if($cur == 'CNY'){
                                    $open = parseFloat($ohlcData[0]);
                                    $high = parseFloat($ohlcData[1]);
                                    $low = parseFloat($ohlcData[2]);
                                    $close = parseFloat($ohlcData[3]);
                                  }
                                }else{
                                  $open = parseFloat($ohlcData[0] * $rates[$cur]);
                                  $high = parseFloat($ohlcData[1] * $rates[$cur]);
                                  $low = parseFloat($ohlcData[2] * $rates[$cur]);
                                  $close = parseFloat($ohlcData[3] * $rates[$cur]);
                                }

                              }
                            }

                            $currentDate = getCurrentMMDate($lastDate, $mm);
                            console.log('Current Last: ' + $currentDate);
                            $chart.dataProvider.shift(); // removes first index
                            $chart.dataProvider.push( {
                                "date" : $currentDate,
                                "open" : toFixedNew($open, 6),
                                "high" :  toFixedNew($high, 6),
                                "low"  :  toFixedNew($low, 6),
                                "close" :  toFixedNew($close, 6)
                            } );



                        }
                      }



                      if($filter == 'XRP' || $filter == 'ETC' || $filter == 'ETH' || $filter == 'LTC'){
                        $lPrice = toFixedNew($dPrice, 6);
                        $chart.valueAxes[0].guides[0].label = toFixedNew($dPrice, 6);
                        $chart.valueAxes[0].guides[0].value = toFixedNew($dPrice, 6);
                      //  console.log('P: ' + toFixedNew($dPrice, 6));

                      }else{
                        $lPrice = toFixedNew($dPrice, 2);
                        $chart.valueAxes[0].guides[0].label = toFixedNew($dPrice, 2);
                        $chart.valueAxes[0].guides[0].value = toFixedNew($dPrice, 2);
                      }


                      $startIndex = $chart.startIndex;
                      $endIndex = $chart.endIndex - $chart.dataProvider.length;
                      $chart.validateData();
                      $('.amcharts-chart-div a').css('display', 'none');
                      zoomChart($startIndex, $endIndex);

                      addRect($tracks);

                      // check win/lose situation
                      for($h = 0; $h < $tradeTransaction.length; $h++){
                        $landingAmount = $tradeTransaction[$h].amount;
                        $tType = $tradeTransaction[$h].type;
                        $tIndex = $tradeTransaction[$h].index;
                        $tPayout = $tradeTransaction[$h].payout;
                        $tStatus = $tradeTransaction[$h].status;
                        if($tStatus == 'live'){
                          if($tType == 'buy'){
                            if($landingAmount <= $lPrice){
                              $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-danger').addClass('text-success').text($tPayout);
                            }else{
                              $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-success').addClass('text-danger').text(0);
                            }
                          }else{
                            if($landingAmount >= $lPrice){
                              $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-danger').addClass('text-success').text($tPayout);
                            }else{
                              $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-success').addClass('text-danger').text(0);
                            }
                          }
                        }
                      }
              });
            }, 1000);

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
            $funds = parseFloat(<?=$funds?>);
            if($funds > 0){
              $amount = parseFloat($('#current_price').val());
              $leverage = parseFloat($('#leverage').val());
              $payout = ($leverage * .70) + $leverage;
              $winAmount = $leverage * .70;

              $chart.valueAxes[0].guides.push(
                  {
                      "value": toFixedNew($amount, 6),
                      "label": toFixedNew($amount, 6),
                      "position": "right",
                      "dashLength": 0,
                      "axisThickness": 0.1,
                      "fillColor": "#008D00",
                      "axisAlpha": 1,
                      "fillAlpha": 1,
                      "color": "#fff",
                      "fontSize": 16,
                      "backgroundColor": "#008D00",
                      "id": "live-guide-buy-"+($tradeIndex+1)
                  }
              );
              $startIndex = $chart.startIndex;
              $endIndex = $chart.endIndex - $chart.dataProvider.length;
              $chart.validateData();
              zoomChart($startIndex, $endIndex);


              $('.amcharts-chart-div a').css('display', 'none');
              //validate();
              //insert_order('buy');
              $trade = '<tr class="trade-index-'+$tradeIndex+'" data-type="buy" data-index="'+$tradeIndex+'"><td>Buy</td><td>'+$cryptoCoin+$cur+'</td><td><span class="timer active-timer"></span></td><td><button class="btn btn-xs btn-success trade-status">LIVE</button></td><td><span class="trade-investment">'+$leverage+'</span></td><td><span class="trade-payout">'+$payout+'</span></td></tr>';
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
              $tIndex = 'track_'+($tradeIndex+1);
              $tracks[$tIndex] = ['amcharts-guide-live-guide-buy-'+($tradeIndex+1), '#008D00'];

              addRect($tracks);
              localStorage.setItem("tracks", JSON.stringify($tracks));
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
            }else{
              $('#depositModal').modal('show');
            }

        });

        $('#sell').click(function(){
            $funds =  parseFloat(<?=$funds?>);
            if($funds > 0){
              $amount = parseFloat($('#current_price').val());
              $chart.valueAxes[0].guides.push(
                  {
                    "value": toFixedNew($amount, 6),
                    "label": toFixedNew($amount, 6),
                      "position": "right",
                      "dashLength": 0,
                      "axisThickness": 0.1,
                      "fillColor": "#DB333C",
                      "axisAlpha": 1,
                      "fillAlpha": 1,
                      "color": "#fff",
                      "fontSize": 16,
                      "backgroundColor": "#DB333C",
                      "id": "live-guide-sell-"+($tradeIndex+1)

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
              $trade = '<tr class="trade-index-'+$tradeIndex+'" data-type="sell" data-index="'+$tradeIndex+'"><td>Sell</td><td>'+$cryptoCoin+$cur+'</td><td><span class="timer active-timer"></span></td><td><button class="btn btn-xs btn-danger trade-status">LIVE</button></td><td><span class="trade-investment">'+$leverage+'</span></td><td><span class="trade-payout">'+$payout+'</span></td></tr>';
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

              $tIndex = 'track_'+($tradeIndex+1);
              $tracks[$tIndex] = ['amcharts-guide-live-guide-sell-'+($tradeIndex+1), '#DB333C'];
              addRect($tracks);
              localStorage.setItem("tracks", JSON.stringify($tracks));
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

            }else{
              $('#depositModal').modal('show');
            }

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
                $payout = $tradeTransaction[$a]['payout'];
                if($status == 'live'){

                    $type = $tradeTransaction[$a]['type'];
                    $entryValue = $tradeTransaction[$a]['amount'];

                    if($minute == 0){
                        $amount = parseFloat($('#current_price').val());
                            $amountPair = $pairPrices[$pairing];

                        if($type == 'buy'){
                            if($amount > $entryValue){
                                $row.find('.trade-status').removeClass('btn-default').addClass('btn-success').text('WIN');
                                $tradeStatus = 'win';
                                $row.find('.trade-payout').removeClass('text-danger').addClass('text-success').text($payout);

                            }else{
                                $row.find('.trade-status').removeClass('btn-default').addClass('btn-danger').text('LOSE');
                                $row.find('.trade-payout').text('0');
                                $tradeStatus = 'lost';
                                $row.find('.trade-payout').removeClass('text-success').addClass('text-danger').text(0);
                            }

                        }else{
                            if($amount < $entryValue){
                                $row.find('.trade-status').removeClass('btn-default').addClass('btn-success').text('WIN');
                                $row.find('.trade-payout').removeClass('text-danger').addClass('text-success').text($payout);
                                $tradeStatus = 'win';
                            }else{
                                $tradeStatus = 'lost';
                                $row.find('.trade-status').removeClass('btn-default').addClass('btn-danger').text('LOSE');
                                $row.find('.trade-payout').text('0');
                                $row.find('.trade-payout').removeClass('text-success').addClass('text-danger').text(0);
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

                        $tIndex = 'track_'+($index+1);
                        delete $tracks[$tIndex];
                        localStorage.setItem("tracks", JSON.stringify($tracks));
                        console.log(localStorage.getItem('tracks'));
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


                // check win los situation

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
            $seconds = $date.getSeconds();

            $minute = $date.getMinutes();
            $milliSeconds = $date.getMilliseconds();
            if($minute < 10){
                $minute = '0'+$minute;
            }


          //  console.log($year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':00');
            return {
              currentSecond: $seconds,
              currentDate: $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':00',
              currrentMatchDate: $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':'+$seconds+'.'+$milliSeconds
            }

        }

        function zoomChart($start, $end) {
            // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
            try {
                $chart.zoomToIndexes( $start, $chart.dataProvider.length - $end );
            }
            catch(err) {
            //    console.log('');
            }

        }


        $('#getHistoryTrigger').click(function(){
          $('#tradeHistoryModal').modal('show');
          $('#tradeHistoryBody').html('<tr><td colspan="7 text-center">Loading data....</td></tr>');
          $.ajax({
              url: 'Controller/history.php?action=trade-history',
              type: 'post',
              dataType: 'json',
              success: function (data) {
                $tableBody = '';
                  console.log(data);
                if(data.length == 0){
                  $tableBody += '<tr><td colspan="7 text-center">No data available</td></tr>';
                }else{
                  for($z = 0; $z < data.length; $z++){

                    $entryPrice = data[$z].entryPrice;
                    $position = data[$z].position;
                    $entryTime = data[$z].entryTime;
                    $lvrg = data[$z].leverage;
                    $exitPrice = data[$z].closingPrice;
                    $exitTime = data[$z].closingTime;
                    $pnl = data[$z].pnl;
                    $status = data[$z].status;
                    if($status == 'win'){
                      $textClass = 'text-success';
                      $pnl = '+'+$pnl;
                    }else{
                      $textClass = 'text-danger';
                      $pnl = '-'+$pnl;
                    }
                    $tableBody += '<tr>'
                                  +'<td>'+ $entryPrice + '</td>'
                                  +'<td>'+ $position + '</td>'
                                  +'<td>'+ $entryTime + '</td>'
                                  +'<td>'+ $lvrg + '</td>'
                                  +'<td>'+ $exitPrice + '</td>'
                                  +'<td>'+ $exitTime + '</td>'
                                  +'<td class="'+$textClass+'">'+ $pnl + '</td>'
                                +'</tr>';
                  }

                }

                $('#tradeHistoryBody').html($tableBody);

              },
              data: {param: JSON.stringify({userId: $userId})}
          });

        });


    });


    function addRect($tracks){
      $.each($tracks, function(index, key){
        $node = key[0];
        $fill = key[1];
        textElm = document.getElementsByClassName($node);

        if(typeof textElm[2] != 'undefined'){
          parent = textElm[2].parentNode;
          $transRaw = $('.'+$node+':eq(2)').attr('transform');
          $transSplit = $transRaw.split(',');
          $transSplit_1 = $transSplit[0];
          $transSplit_2 = $transSplit[1];

          $finalX = $transSplit_1.replace(/[^0-9.,]*/g, '');
          $finalY = $transSplit_2.replace(/[^0-9.,]*/g, '');
          SVGRect = textElm[2].getBBox();
          var rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");

              rect.setAttribute("x", $finalX-10);
              rect.setAttribute("y", $finalY-12);
              rect.setAttribute("width", SVGRect.width + 20);
              rect.setAttribute("height", SVGRect.height + 5);
              rect.setAttribute("fill", $fill);
              parent.insertBefore(rect, textElm[2])
        }


      });
    }
    function toFixedNew(num, fixed) {
        var re = new RegExp('^-?\\d+(?:\.\\d{0,' + (fixed || -1) + '})?');
        return num.toString().match(re)[0];
    }

    function getLastMM($date, $mm){
      if($mm == '5mm'){
        $unixMin = 300;
      }else if($mm == '30mm'){
        $unixMin = 1800;
      }else if($mm == '60mm'){
        $unixMin = 3600;
      }else if($mm == '240mm'){
        $unixMin = 14400;
      }
      $date = new Date($date).getTime() / 1000;
      $newDate = new Date(($date-$unixMin) * 1000);

      $year = $newDate.getFullYear();
      $month = $newDate.getMonth() + 1;
      if($month < 10){
          $month = '0'+$month;
      }
      $day = $newDate.getDate();
      if($day < 10){
          $day = '0'+$day;
      }
      $hour = $newDate.getHours();         //
      if($hour < 10){
          $hour = '0'+$hour;
      }

      $seconds = $newDate.getSeconds();

      $minute = $newDate.getMinutes();
      if($minute < 10){
          $minute = '0'+$minute;
      }
      $milliSeconds = $newDate.getMilliseconds();


    //  console.log($year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':00');
      return $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':00';

    }
    function getCurrentMMDate($date, $mm){
      if($mm == '5mm'){
        $unixMin = 300;
      }else if($mm == '30mm'){
        $unixMin = 1800;
      }else if($mm == '60mm'){
        $unixMin = 3600;
      }else if($mm == '240'){ 
        $unixMin = 14400;
      }
      $date = new Date($date).getTime() / 1000;
      $newDate = new Date(($date+$unixMin) * 1000);

      $year = $newDate.getFullYear();
      $month = $newDate.getMonth() + 1;
      if($month < 10){
          $month = '0'+$month;
      }
      $day = $newDate.getDate();
      if($day < 10){
          $day = '0'+$day;
      }
      $hour = $newDate.getHours();         //
      if($hour < 10){
          $hour = '0'+$hour;
      }

      $seconds = $newDate.getSeconds();

      $minute = $newDate.getMinutes();
      if($minute < 10){
          $minute = '0'+$minute;
      }
      $milliSeconds = $newDate.getMilliseconds();


      //  console.log($year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':00');
      return $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute+':00';
    }
</script>

</body>
</html>
