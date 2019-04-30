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
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="//cdn.rawgit.com/noelboss/featherlight/1.7.9/release/featherlight.min.css" type="text/css" rel="stylesheet" />
    <script src="//cdn.rawgit.com/noelboss/featherlight/1.7.9/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>


    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" media="all" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>
    <link rel="stylesheet" href="assets/css/switchery.min.css" />
    <link rel="stylesheet" href="assets/css/simple-line-icons.min.css" />
    <script src="assets/js/switchery.min.js"></script>
    <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
    <script src="https://www.amcharts.com/lib/3/serial.js"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <?php
    if(isset($_GET['template'])){
      if($_GET['template'] == 'light'){
        echo '<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>';
        echo '<link rel="stylesheet" href="assets/css/main.css?v=2.6" />';
        $template = 'light';
        $fillColor = '#000';
        $textFill = '#fff';
      }else{
        echo '<script src="https://www.amcharts.com/lib/3/themes/black.js"></script>';
        echo '<link rel="stylesheet" href="assets/css/main-dark.css?v=2.6" />';
        $template = 'black';
        $fillColor = '#fff';
        $textFill = '#000';
      }
    }else{
      echo '<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>';
      $template = 'light';
     $fillColor = '#000';
      $textFill = '#fff';
      echo '<link rel="stylesheet" href="assets/css/main.css?v=2.6" />';
    }
    ?>


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
          <div class="col-md-1 col-sm-1 col-xs-2 spacer text-center">
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
        <div id="chartdiv" style="height: 860px;">
          <div class="loader"></div>
          This may take a few minutes.
        </div>

		 <div id="chartdiv1" style="height: 250px!important;width: 97.1%; ">
          <div class="loader"></div>
          <div style="display:inline-block;width:100%;text-align:center;">This may take a few minutes.</div>
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


<script type="text/javascript" language="JavaScript">
    $(document).ready(function(){
      var _efx8 = <?=$user['accepted_terms']?>;
      var totalTradeAmount = 0;
      $userId = <?php echo $userSess['info']['id']; ?>;
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



      $('.depositWithdrawBtn').click(function(){
        $action = $(this).attr('data-action');
        $('.profile-container-body').addClass('display-none');
        $('.deposit-withdraw-container-body').removeClass('display-none');

        if($action == 'deposit'){
          $('.deposit-withdraw-title').text('Deposit History');
          $('.confirmDepositWithdrawBtn').attr('data-action', 'deposit').text('Deposit Coin');

          // get deposit History
          $.ajax({
              url: 'Controller/user.php?action=transaction-history',
              type: 'post',
              dataType: 'json',
              success: function (r) {
                  $dTable = '';
                  for($r = 0; $r < r.length; $r++){
                    $dTable += '<tr><td>'+r[$r].timestamp+'</td><td>$'+r[$r].price_in_usd+'</td><td>'+r[$r].invoice_id+'</td><td>'+r[$r].notes+'</td></tr>';
                  }

                  $('.deposit-withdraw-history').html($dTable);
              },
              data: {
                        param: {
                          userId: $userId,
                          action: 'deposit'
                      }
                    }
          });


        }else{
          $('.deposit-withdraw-title').text('Withdraw History');
          $('.confirmDepositWithdrawBtn').attr('data-action', 'withdraw').text('Withdraw Coin');
        }

        $('.confirmDepositWithdrawBtn').click(function(){
            $action = $(this).attr('data-action');
            if($action == 'deposit'){
              location.href = 'buy-funds.php';
            }else{
              $('.profile-container-body').addClass('display-none');
              $('.withdraw-container').removeClass('display-none');
            }
        });
      })

      $('.changeSecretQuestionBtn').click(function(){
        $('.profile-container-body').addClass('display-none');
        $('.change-secret-container-body').removeClass('display-none');
        $('.confirmChangeSecretBtn').click(function(){
          $('.change-secret-container-body').addClass('display-none');
          $('.change-secret-loader').removeClass('display-none');
          $secretQuestion = $('#secretQuestions').val();
          $secretAnswer = $('#secretAnswer').val();
          if($secretAnswer != ''){
            $.ajax({
                url: 'Controller/user.php?action=change-secret',
                type: 'post',
                dataType: 'json',
                success: function (rspdata) {
                    $('.change-secret-loader').addClass('display-none');
                    $('.profile-container-body').removeClass('display-none');
                    $('.profile-sub').addClass('display-none');
                    alert('Secret question successfully updated.');
                },
                data: {
                          param: {
                            userId: $userId,
                            secretQuestion: $secretQuestion,
                            answer: $secretAnswer
                        }
                      }
            });
          }else{
            alert('Please input secret answer to make an update');
          }
        });
      });

      $('.changePasswordBtn').click(function(){
        $('.profile-container-body').addClass('display-none');
        $('.change-password-container-body').removeClass('display-none');
        $('.confirmChangePasswordBtn').click(function(){
          $('.change-security-container').addClass('display-none');
          $('.change-security-loader').removeClass('display-none');
          $oldPassword = $('#oldPassword').val();
          $password1 = $('#password1').val();
          $password2 = $('#password2').val();
          if($password1 == $password2){
            $.ajax({
                url: 'Controller/user.php?action=change-password',
                type: 'post',
                dataType: 'json',
                success: function (rspdata) {
                  if(rspdata.success == true){
                    $('.profile-container-body').removeClass('display-none');
                    $('.profile-sub').addClass('display-none');
                    alert('Password successfully changed.');
                  }else{
                    alert('Old password does not match, please try again.');
                    $('.change-security-container').removeClass('display-none');
                    $('.change-security-loader').addClass('display-none');
                  }
                },
                data: {
                          param: {
                            userId: $userId,
                            oldPassword: $oldPassword,
                            password1: $password1,
                            password2: $password2
                        }
                      }
            });
          }else{
            $('.change-security-container').removeClass('display-none');
            $('.change-security-loader').addClass('display-none');
            alert('New password doesnt match.');
          }
        });
      });

      $('.authenticationBtn').click(function(){
        $('.profile-container-body').addClass('display-none');
        $('.authentication-container-body').removeClass('display-none');
        $('.authentication-resend-button').addClass('display-none');
        $('.authentication-response-body').addClass('display-none');
        $.ajax({
            url: 'Controller/user.php?action=request-authentication',
            type: 'post',
            dataType: 'json',
            success: function (rspdata) {
              $('.authentication-loader').addClass('display-none');
              $('.authentication-input-body').removeClass('display-none');

              $('.authentication-code-send').click(function(){
                $authCode = $('.authentication-code').val();
                $('.authentication-loader').removeClass('display-none');
                $('.authentication-input-body').addClass('display-none');
                $.ajax({
                    url: 'Controller/user.php?action=confirm-authentication',
                    type: 'post',
                    dataType: 'json',
                    success: function (rspdata) {
                      $('.authentication-loader').addClass('display-none');
                      $('.authentication-input-body').addClass('display-none');
                      $('.authentication-response-body').removeClass('display-none');

                      if(rspdata.type == 2){
                          $ahtml = '<h3>'+rspdata.message +'</h3><br>'
                          $('.authentication-resend-button').removeClass('display-none');
                      }else if(rspdata.type == 3){
                        $ahtml = '<h3>'+rspdata.message +'</h3><br>'
                        $('.authentication-retry-button').removeClass('display-none');
                        $('.authenticationRetryBtn').click(function(){
                          $(this).addClass('display-none');
                          $('.authentication-response-body').addClass('display-none');
                          $('.authentication-input-body').removeClass('display-none');
                        });
                      }else{
                        $ahtml = '<h3>'+rspdata.message +'</h3>';
                      }


                      $('.authentication-response-body').find('div:eq(0)').html($ahtml);
                      console.log(rspdata);
                    },
                    data: {param: {userId: $userId, code: $authCode}}
                });
              });
            },
            data: {param: {userId: $userId}}
        });
      });

      $('#profile_form').on('submit',function(e){

        e.preventDefault();
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: 'Controller/user.php?action=send-profile&userId='+$userId,
            type: 'POST',
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                console.log(myXhr);
                $('.submit_form_btn').attr('disabled', true);
                return myXhr;
            },
            success: function (data) {
                console.log(data);
                if(data == 'true'){
                  $('.profile-container-body').removeClass('display-none');
                  $('.profile-input-container-body').addClass('display-none');
                  alert('Information successfully submitted');
                }else{
                  alert('Something went wrong, please try again.');
                }

                $('.submit_form_btn').removeAttr('disabled');
            },
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
      });

      $('.submitInfoBtn').click(function(){
        $('.profile-container-body').addClass('display-none');
        $('.profile-input-container-body').removeClass('display-none');
      });

      $('.date-from').datepicker({
          format: 'mm/dd/yyyy',
      });
      $('.date-to').datepicker({
          format: 'mm/dd/yyyy'
      });


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
          /*
            $('.profile-container-body').removeClass('display-none');
            $('.profile-sub').addClass('display-none');
            $('.authentication-response-body').addClass('display-none');
            $('.hide-on-profile-init').addClass('display-none');
            $('#profileModal').modal('show');
          */
          location.href= "profile.php";
        });

        $('.popOverTrigger-btcSwitch').popover({
          container: 'body',
          trigger: 'hover',
          placement: 'left',
          html: true,
          content: 'This toggle lets you display your digits as either BTC or dmBTC ( which is BTC x 10,0000 ).'
        });

        $('.popOverTrigger').popover({
          container: 'body',
          trigger: 'hover',
          placement: 'top',
          content: 'This is your balance. Slide the toggle to display it as either BTC or dmBTC ( which is BTC x 10,000).'
        });
        $('.popOverTrigger-leverage').popover({
          container: 'body',
          trigger: 'hover',
          placement: 'top',
          html: true,
          content: 'This is your Trade Amount converted to BTC from dmBTC. Toggle the switch to choose BTC or dmBTC ( which is BTC x 10,000 ).'
        });
        $('.popOverTrigger-leverage-input').popover({
          container: 'body',
          trigger: 'hover',
          placement: 'top',
          html: true,
          content: 'This is your Trade Amount displayed in either BTC or dmBTC. Toggle the switch to choose BTC or dmBTC ( which is BTC x 10,000 )'
        });

        $('.popOverTrigger-table').popover({
          container: 'body',
          trigger: 'hover',
          placement: 'top',
          html: true,
          content: 'These are your live trades running in real time. Toggle the switch to display BTC or dmBTC (which is BTC x 10,000).'
        });

        $tracks = new Object;
        $tracks['track_0'] = ['amcharts-guide-live-guide', '<?=$fillColor?>'];
        localStorage.setItem("tracks", JSON.stringify($tracks));
        $tradeIndex = 0;




        $('.select_coin').click(function(){
            $urlParam = <?php echo json_encode($urlParam); ?>;
            if(typeof $urlParam.c != 'undefined'){
              $urlC = '&c='+$urlParam.c;
            }else{
              $urlC ='';
            }
            if(typeof $urlParam.template != 'undefined'){
              $urlTemplate = '&template='+$urlParam.template;
            }else{
              $urlTemplate ='';
            }
            location.href = 'go2.php?coin='+$(this).attr('data-coin')+'&currency='+$(this).attr('data-pair')+$urlC+$urlTemplate;


        });
        $('.candle-select-range').click(function(){
          $candleRange = $(this).attr('data-value');
          $urlParam = <?php echo json_encode($urlParam); ?>;
          if(typeof $urlParam.coin != 'undefined'){
            $urlCoin = '&coin='+$urlParam.coin;
          }else{
            $urlCoin ='';
          }
          if(typeof $urlParam.currency != 'undefined'){
            $urlCurrency = '&currency='+$urlParam.currency;
          }else{
            $urlCurrency ='';
          }
          if(typeof $urlParam.template != 'undefined'){
            $urlTemplate = '&template='+$urlParam.template;
          }else{
            $urlTemplate ='';
          }

          location.href = 'go2.php?c='+$candleRange+$urlCoin+$urlCurrency+$urlTemplate


        });

        // call History



        $.ajax({
            url: 'Controller/history2.php?action=load-history',
            type: 'post',
            dataType: 'json',
            success: function (rspdata) {
              $maxTradeAmount = rspdata.maxTradeAmount;
              $('.balance-loader').css('display', 'none');
              $('.balance-loader-display').css('display', 'block');
              $('.userFunds').html(toFixedNew(rspdata.funds, 2));
              $isLoadedUpdate = false;
              $isLoadedAdd = false;
              $btcUsd = rspdata.currentBtcPrice;
              $currentBtcPrice = rspdata.currentBtcPrice;
              $currentCoinPrice = toFixedNew(rspdata.currentCoinPrice, 8);
              $cur = '<?php echo $currency; ?>';
              $currencyPair = '<?php echo $currencyPair; ?>';
              $userId = <?php echo $userSess['info']['id']; ?>;
              $coin = '<?php echo $filter; ?>';
              $cryptoCoin = '<?php echo $filter; ?>';
              $maxQuadPrice = parseFloat($maxTradeAmount / $currentBtcPrice * 10000);
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
                  XRPBTC: 0,
                  EDOUSD: 0,
                  ETPUSD: 0,
                  NEOUSD: 0,
                  SANUSD: 0,
                  ZECUSD: 0,
                  DASHUSD: 0,
                  BCHBTC: 0,
                  EOSBTC: 0,
                  IOTBTC: 0,
                  OMGUSD: 0,
                  XMRUSD: 0,
                  BCHUSD: 0,
                  DASHBTC: 0,
                  LTCBTC: 0,
                  SANBTC: 0,
                  EDOBTC: 0,
                  ETPBTC: 0,
                  NEOBTC: 0,
                  ZECBTC: 0
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
                      }else if(key.pair == 'EDOUSD'){
                          $pairPrices.EDOUSD = key.price;
                      }else if(key.pair == 'ETPUSD'){
                          $pairPrices.ETPUSD = key.price;
                      }else if(key.pair == 'NEOUSD'){
                          $pairPrices.NEOUSD = key.price;
                      }else if(key.pair == 'SANUSD'){
                          $pairPrices.SANUSD = key.price;
                      }else if(key.pair == 'ZECUSD'){
                          $pairPrices.ZECUSD = key.price;
                      }else if(key.pair == 'DASHUSD'){
                          $pairPrices.DASHUSD = key.price;
                      }else if(key.pair == 'BCHBTC'){
                          $pairPrices.BCHBTC = key.price;
                      }else if(key.pair == 'EOSBTC'){
                          $pairPrices.EOSBTC = key.price;
                      }else if(key.pair == 'IOTBTC'){
                          $pairPrices.IOTBTC = key.price;
                      }else if(key.pair == 'OMGUSD'){
                          $pairPrices.OMGUSD = key.price;
                      }else if(key.pair == 'XMRUSD'){
                          $pairPrices.XMRUSD = key.price;
                      }else if(key.pair == 'BCHUSD'){
                          $pairPrices.BCHUSD = key.price;
                      }else if(key.pair == 'DASHBTC'){
                          $pairPrices.DASHBTC = key.price;
                      }else if(key.pair == 'LTCBTC'){
                          $pairPrices.LTCBTC = key.price;
                      }else if(key.pair == 'SANBTC'){
                          $pairPrices.SANBTC = key.price;
                      }else if(key.pair == 'EDOBTC'){
                          $pairPrices.EDOBTC = key.price;
                      }else if(key.pair == 'ETPBTC'){
                          $pairPrices.ETPBTC = key.price;
                      }else if(key.pair == 'NEOBTC'){
                          $pairPrices.NEOBTC = key.price;
                      }else if(key.pair == 'ZECBTC'){
                          $pairPrices.ZECBTC = key.price;
                      }

                  });
              })


                  $('#leverage').on('change', function(){

                    $curFunds = parseFloat($('.userFunds').text());

                    if($(this).val() > $curFunds){
                      alert('You dont have enough funds');
                      //$(this).val($curFunds.toFixed(2));
                    }

                    if($(this).val() > $maxTradeAmount){
                      alert('Maximum Trade Limit Exceeded. Reduce Trade Amount Size.');
                      //$(this).val($maxTradeAmount);
                    }
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

              $('.current_price').text($currentCoinPrice);
              $.getJSON('https://coincap.io/exchange_rates').done(function(responseRates){
                  $rates = responseRates.rates;
                  $dateRange = '1day';
                  $filter = '<?php echo $filter; ?>';
                  if($cur != 'BTC'){
                      $curCoinPrice = $currentCoinPrice * $rates[$cur];
                      $('#current_price').val($curCoinPrice);
                      if($filter == 'XRP' || $filter == 'ETC' ){
                        $('.current_price').text(toFixedNew($curCoinPrice, 8));
                      }else{
                        $('.current_price').text(toFixedNew($curCoinPrice, 2));
                      }

                  }else{
                    if($filter == 'ETC' || $filter == 'EDO' || $filter == 'SAN'  || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' || $filter == 'EOS'  || $filter == 'OMG'  || $filter == 'XMR'){
                      $curCoinPrice = $currentCoinPrice / $currentBtcPrice;
                      $('.current_price').text(toFixedNew($curCoinPrice, 8));
                    }
                  }
                  $tickHistory = rspdata.dataFiltered
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
                      console.log($tickDate);
                      $data_trade.push(
                          {
                              "date" : $tickDate,
                              "open" : toFixedNew($open, 8),
                              "high" :  toFixedNew($high, 8),
                              "low"  :  toFixedNew($low, 8),
                              "close" :  toFixedNew($close, 8),

                          }

                      );

                      if($tickHistory.length-1 == $x){
                        $group = [parseFloat(toFixedNew($open, 8)),parseFloat(toFixedNew($high, 8)),parseFloat(toFixedNew($low, 8)),parseFloat(toFixedNew($close, 8))];
                      }
                  }
                  console.log('data trade');
                  console.log($data_trade);

                  //$data.splice(0, 40);
                  // render chart

                  if($filter == 'XRP'){
                    $tickValue = $currentCoinPrice

                  }else{
                    $tickValue = ($cur == 'BTC' ? $currentCoinPrice / $currentBtcPrice : $currentCoinPrice * $rates[$cur]);

                  }
                  if($filter == 'XRP' || $filter == 'ETC' || $filter == 'LTC' || $filter == 'EDO' || $filter == 'SAN' || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' || $filter == 'EOS' || $filter == 'OMG'  || $filter == 'XMR'){
                    $tickValue = toFixedNew($tickValue, 8);
              //      console.log($tickValue);
                  }else{
                    $tickValue = toFixedNew($tickValue, 4);
                  }
                 console.log( $(".amcharts-chart-div").find('.amcharts-guide-fill-live-guide').val());
                  var config = {
                      "hideCredits":true,
                      "type": "serial",
                      "theme": "<?=$template?>",
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
                              "fillColor": "<?=$fillColor?>",
                              "axisAlpha": 1,
                              "fillAlpha": 1,
                              "color": "<?=$textFill?>",
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
                          "fillAlphas": 1,
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
                  }
                  $chart = AmCharts.makeChart( "chartdiv", config );

                  $chart.addListener( "rendered", zoomChart );
                  //$chart.graphsSet.toBack();
                  $chart.addListener("zoomed", handleZoom);


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

                              $chart.valueAxes[0].guides.push(
                                  {
                                      "value": toFixedNew($amount, 8),
                                      "label": "Entry Price \n"+toFixedNew($amount, 8),
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


      // this method is called when chart is first inited as we listen for "dataUpdated" event

                  // get pending trades
                  console.log($tracks);
                    zoomChart(zoomValue, 1);

                  addRect($tracks);
                  $('.amcharts-chart-div a').css('display', 'none');



                   function handleZoom(event) {
                     $tracks = JSON.parse(localStorage.getItem("tracks"));
                     addRect($tracks);
                   }


                  // switchery
				  if( typeof btnSwitch != typeof undefined ) {
					  btnSwitch.onchange = function() {
						$chart.clear();
						if(btnSwitch.checked == true){
						  $scheme = 'light';
						}else{
						  $scheme = 'black';
						}

						$urlParam = <?php echo json_encode($urlParam); ?>;
						if(typeof $urlParam.coin != 'undefined'){
						  $urlCoin = '&coin='+$urlParam.coin;
						}else{
						  $urlCoin ='';
						}
						if(typeof $urlParam.currency != 'undefined'){
						  $urlCurrency = '&currency='+$urlParam.currency;
						}else{
						  $urlCurrency ='';
						}
						if(typeof $urlParam.template != 'undefined'){
						  $urlTemplate = '&template='+$urlParam.template;
						}else{
						  $urlTemplate ='';
						}

						if(typeof $urlParam.c != 'undefined'){
						  $urlC = '&c='+$urlParam.c;
						}else{
						  $urlC ='';
						}


						location.href = "go2.php?template="+$scheme+$urlC+$urlCoin+$urlCurrency;
					  }
				  }


                  console.log('Cur: ' + $cur);
                  console.log('Filter: ' + $filter);
                  console.log('Pair: ' + $currencyPair);
                  var sock = 0;
                  $coin = $filter;
                  $price = 0;

                  setInterval(function(){
                    $v = Math.round((new Date()).getTime());
                    $.getJSON( "coin_live_price/"+$filter+".json?v="+$v, function( json ) {
                    })
                    $.getJSON( "coin_live_price/"+$filter+".json?v="+$v, function( json ) {
                      $updated = true;
                      $price = json.dollarPrice;
                      $btcPrice = $price / $currentBtcPrice;
                      $btcPriceConv = json.btcPrice;
                      $currentDate = json.timestamp;
                      console.log('CD', $currentDate)
                      console.log(json);
                      $currentMatchDate = json.matchDate;
                      if($coin == 'BTC'){
                          $currentBtcPrice = $price;
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
                          $pairPrices.LTCBTC = $price / $currentBtcPrice;
                      }else if($coin == 'XRP'){
                          $pairPrices.XRPBTC = $price;
                      }else if($coin == 'EDO'){
                          $pairPrices.EDOUSD = $price;
                          $pairPrices.EDOBTC = $price / $currentBtcPrice;
                      }else if($coin == 'ETP'){
                          $pairPrices.ETPUSD = $price;
                          $pairPrices.ETPBTC = $price / $currentBtcPrice;
                      }else if($coin == 'NEO'){
                          $pairPrices.NEOUSD = $price;
                          $pairPrices.NEOBTC = $price / $currentBtcPrice;
                      }else if($coin == 'SAN'){
                          $pairPrices.SANUSD = $price;
                          $pairPrices.SANBTC = $price / $currentBtcPrice;
                      }else if($coin == 'ZEC'){
                          $pairPrices.ZECUSD = $price;
                          $pairPrices.ZECBTC = $btcPrice;
                      }else if($coin == 'DASH'){
                          $pairPrices.DASHUSD = $price;
                          $pairPrices.DASHBTC = $price / $currentBtcPrice;
                      }else if($coin == 'BCH'){
                          $pairPrices.BCHBTC = $price / $currentBtcPrice;
                      }else if($coin == 'EOS'){
                          $pairPrices.EOSBTC = $price / $currentBtcPrice;
                      }else if($coin == 'IOT'){
                          $pairPrices.IOTBTC = $price / $currentBtcPrice;
                      }else if($coin == 'OMG'){
                          $pairPrices.OMGUSD = $price;
                      }else if($coin == 'XMR'){
                          $pairPrices.XMRUSD = $price;
                      }else if($coin == 'BCH'){
                          $pairPrices.BCHUSD = $price;
                      }


                          $beforePrice = parseFloat($('#current_price').val());
                          if($filter =='XRP'){
                              $dPrice = $price;
                          }else{
                              $dPrice = ($cur == 'BTC' ? $btcPrice : $price * $rates[$cur]);
                          }

                      //    console.log($dPrice);
                          if($filter == 'XRP' || $filter == 'ETC' || $filter == 'ETH' || $filter == 'EDO' || $filter == 'SAN'  || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' || $filter == 'EOS'  || $filter == 'OMG'  || $filter == 'XMR'){
                            $('#current_price').val(toFixedNew($dPrice, 8));
                            $('.current_price').text(toFixedNew($dPrice, 8));
                          }else{
                            $('#current_price').val(toFixedNew($dPrice, 8));
                            $('.current_price').text(toFixedNew($dPrice, 8));
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
                                          if($filter == 'ETH' || $filter == 'EDO' || $filter == 'ETC' || $filter == 'SAN' || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' || $filter == 'EOS'  || $filter == 'OMG'  || $filter == 'XMR'){

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
                                      "open" : toFixedNew($open, 8),
                                      "high" :  toFixedNew($high, 8),
                                      "low"  :  toFixedNew($low, 8),
                                      "close" :  toFixedNew($close, 8)
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
                                        if($filter == 'ETH' || $filter == 'EDO' || $filter == 'ETC' || $filter == 'SAN' || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' ||  $filter == 'EOS'  || $filter == 'OMG'  || $filter == 'XMR'){

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
                                      "open" : toFixedNew($open, 8),
                                      "high" :  toFixedNew($high, 8),
                                      "low"  :  toFixedNew($low, 8),
                                      "close" :  toFixedNew($close, 8)
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
                                          console.log('B');
                                      }else{
                                          if($filter == 'ETH' || $filter == 'EDO' || $filter == 'ETC' || $filter == 'SAN' || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' ||  $filter == 'EOS'  || $filter == 'OMG'  || $filter == 'XMR'){
                                            $open = parseFloat($ohlcData[0]);
                                            $high = parseFloat($ohlcData[1]);
                                            $low = parseFloat($ohlcData[2]);
                                            $close = parseFloat($ohlcData[3]);
                                          }else{
                                            console.log('A');
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
                                      "open" : toFixedNew($open, 8),
                                      "high" :  toFixedNew($high, 8),
                                      "low"  :  toFixedNew($low, 8),
                                      "close" :  toFixedNew($close, 8)
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
                                        if($filter == 'ETH' || $filter == 'EDO' || $filter == 'ETC' || $filter == 'SAN' || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' ||  $filter == 'EOS'  || $filter == 'OMG'  || $filter == 'XMR'){

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
                                      "open" : toFixedNew($open, 8),
                                      "high" :  toFixedNew($high, 8),
                                      "low"  :  toFixedNew($low, 8),
                                      "close" :  toFixedNew($close, 8)
                                  } );



                              }
                            }



                            if($filter == 'XRP' || $filter == 'ETC' || $filter == 'ETH' || $filter == 'LTC' || $filter == 'EDO' || $filter == 'SAN' || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' ||  $filter == 'EOS' || $filter == 'OMG' || $filter == 'XMR'){
                              $lPrice = toFixedNew($dPrice, 8);
                              $chart.valueAxes[0].guides[0].label = toFixedNew($dPrice, 8);
                              $chart.valueAxes[0].guides[0].value = toFixedNew($dPrice, 8);
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

                              $tStatus = $tradeTransaction[$h].status;
                              $tPayout = $tradeTransaction[$h].payout;

                              if($tStatus == 'live'){
                                if($tType == 'buy'){
                                  if($landingAmount <= $lPrice){
                                    $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-danger').addClass('text-success').text($tPayout);
                                  }else if($landingAmount == $lPrice){
                                    $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-danger').removeClass('text-success').text(0);
                                  }else{
                                    $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-success').addClass('text-danger').text(0);
                                  }
                                }else{
                                  if($landingAmount >= $lPrice){
                                    $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-danger').addClass('text-success').text($tPayout);
                                  }else if($landingAmount == $lPrice){
                                    $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-danger').removeClass('text-success').text(0);
                                  }else{
                                    $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-success').addClass('text-danger').text(0);
                                  }
                                }
                              }
                            }
                    });

                    console.log('data provider');
                    console.log($chart.dataProvider);
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
                          $amount = parseFloat($('#current_price').val());

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

                          $chart.valueAxes[0].guides.push(
                              {
                                  "value": toFixedNew($amount, 8),
                                  "label": "Entry Price \n"+ toFixedNew($amount, 8),
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
                          console.log('W');
                          console.log($logTradeData);
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
                            $amount = parseFloat($('#current_price').val());
                            $chart.valueAxes[0].guides.push(
                                {
                                  "value": toFixedNew($amount, 8),
                                  "label": "Entry Price \n"+toFixedNew($amount, 8),
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

                          if($minute == 0){
                              $amount = parseFloat($('#current_price').val());
                                  $amountPair = $pairPrices[$pairing];

                              if($type == 'buy'){
                                  if($amount > $entryValue){
                                      $row.find('.trade-status').removeClass('btn-default').addClass('btn-success').text('CLOSE');
                                      $tradeStatus = 'win';
                                      $row.find('.trade-payout').removeClass('text-danger').addClass('text-success').text($payoutt);
                                      $row.find('.exit-price').text($amount);

                                  }else if($amount == $entryValue){
                                    $row.find('.trade-status').removeClass('btn-default').addClass('btn-default').text('CLOSE');
                                    $row.find('.trade-payout').text('0');
                                    $tradeStatus = 'even';
                                    $row.find('.trade-payout').removeClass('text-success').removeClass('text-danger').text(0);
                                    $row.find('.exit-price').text($amount);
                                  }else{
                                      $row.find('.trade-status').removeClass('btn-default').addClass('btn-danger').text('CLOSE');
                                      $row.find('.trade-payout').text('0');
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
                                  winAmount: $winAmount,
                                  leverage: $leveraget,
                                  status: $tradeStatus,
                                  tradeId: $tradeId,
                                  userId: $userId
                              }

                              if($tradeStatus == 'win'){
                                console.log($tradeData);
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
            },
            data: {param: JSON.stringify({
              userId: $userId,
              filter: '<?php echo $filter; ?>',
              tickInterval: '<?php echo $tickInterval; ?>',
              device: device,
              mm: $mm
            }
          )}
        });


        // on trade history

        $('.date-history').change(function(){
          $('.table-loader').css('display', 'block');
          $('.trade-table').css('display', 'none');
          $.ajax({
              url: 'Controller/history2.php?action=trade-history',
              type: 'post',
              dataType: 'json',
              success: function (data) {
                $('.table-loader').css('display', 'none');
                $('.trade-table').css('display', 'table');
                $tableBody = '';
                  console.log(data);
                  $totalPnl = 0;
                if(data.length == 0){
                  $tableBody += '<tr><td colspan="8" class="text-center">No data available</td></tr>';
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

                $('#tradeHistoryBody').html($tableBody);

              },
              data: {param: JSON.stringify({userId: $userId, from: $('.date-from').val(), to: $('.date-to').val()})}
          });
        });



    });

      setInterval(function(){
        addRect($tracks);
      }, 500);

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

      function getRandomInt(min = -10, max = 10) {
          return Math.floor(Math.random() * (max - min + 1)) + min;
      }

      function getUserById($id){
        $.ajax({
            url: '../Controller/user.php?action=get-user-by-id',
            type: 'post',
            dataType: 'json',
            success: function (rsp) {
                $tradePercentageAmount = rsp.balanceStatus.trade_percentage_amount;
                $balanceId = rsp.balanceStatus.id;
            },
            data: {param: {id: $id, admin: 0}}
        });
      }
</script>

<script type="text/javascript" language="JavaScript">
    $(document).ready(function(){
      var _efx8 = <?=$user['accepted_terms']?>;
      var totalTradeAmount = 0;
      $userId = <?php echo $userSess['info']['id']; ?>;
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


      var btnSwitch = document.querySelector('.js-switch');
      var templateSwitch = new Switchery(btnSwitch);
      $('.switch-container').removeClass('display-none');
      $('.depositWithdrawBtn').click(function(){
        $action = $(this).attr('data-action');
        $('.profile-container-body').addClass('display-none');
        $('.deposit-withdraw-container-body').removeClass('display-none');

        if($action == 'deposit'){
          $('.deposit-withdraw-title').text('Deposit History');
          $('.confirmDepositWithdrawBtn').attr('data-action', 'deposit').text('Deposit Coin');

          // get deposit History
          $.ajax({
              url: 'Controller/user.php?action=transaction-history',
              type: 'post',
              dataType: 'json',
              success: function (r) {
                  $dTable = '';
                  for($r = 0; $r < r.length; $r++){
                    $dTable += '<tr><td>'+r[$r].timestamp+'</td><td>$'+r[$r].price_in_usd+'</td><td>'+r[$r].invoice_id+'</td><td>'+r[$r].notes+'</td></tr>';
                  }

                  $('.deposit-withdraw-history').html($dTable);
              },
              data: {
                        param: {
                          userId: $userId,
                          action: 'deposit'
                      }
                    }
          });


        }else{
          $('.deposit-withdraw-title').text('Withdraw History');
          $('.confirmDepositWithdrawBtn').attr('data-action', 'withdraw').text('Withdraw Coin');
        }

        $('.confirmDepositWithdrawBtn').click(function(){
            $action = $(this).attr('data-action');
            if($action == 'deposit'){
              location.href = 'buy-funds.php';
            }else{
              $('.profile-container-body').addClass('display-none');
              $('.withdraw-container').removeClass('display-none');
            }
        });
      })

      $('.changeSecretQuestionBtn').click(function(){
        $('.profile-container-body').addClass('display-none');
        $('.change-secret-container-body').removeClass('display-none');
        $('.confirmChangeSecretBtn').click(function(){
          $('.change-secret-container-body').addClass('display-none');
          $('.change-secret-loader').removeClass('display-none');
          $secretQuestion = $('#secretQuestions').val();
          $secretAnswer = $('#secretAnswer').val();
          if($secretAnswer != ''){
            $.ajax({
                url: 'Controller/user.php?action=change-secret',
                type: 'post',
                dataType: 'json',
                success: function (rspdata) {
                    $('.change-secret-loader').addClass('display-none');
                    $('.profile-container-body').removeClass('display-none');
                    $('.profile-sub').addClass('display-none');
                    alert('Secret question successfully updated.');
                },
                data: {
                          param: {
                            userId: $userId,
                            secretQuestion: $secretQuestion,
                            answer: $secretAnswer
                        }
                      }
            });
          }else{
            alert('Please input secret answer to make an update');
          }
        });
      });

      $('.changePasswordBtn').click(function(){
        $('.profile-container-body').addClass('display-none');
        $('.change-password-container-body').removeClass('display-none');
        $('.confirmChangePasswordBtn').click(function(){
          $('.change-security-container').addClass('display-none');
          $('.change-security-loader').removeClass('display-none');
          $oldPassword = $('#oldPassword').val();
          $password1 = $('#password1').val();
          $password2 = $('#password2').val();
          if($password1 == $password2){
            $.ajax({
                url: 'Controller/user.php?action=change-password',
                type: 'post',
                dataType: 'json',
                success: function (rspdata) {
                  if(rspdata.success == true){
                    $('.profile-container-body').removeClass('display-none');
                    $('.profile-sub').addClass('display-none');
                    alert('Password successfully changed.');
                  }else{
                    alert('Old password does not match, please try again.');
                    $('.change-security-container').removeClass('display-none');
                    $('.change-security-loader').addClass('display-none');
                  }
                },
                data: {
                          param: {
                            userId: $userId,
                            oldPassword: $oldPassword,
                            password1: $password1,
                            password2: $password2
                        }
                      }
            });
          }else{
            $('.change-security-container').removeClass('display-none');
            $('.change-security-loader').addClass('display-none');
            alert('New password doesnt match.');
          }
        });
      });

      $('.authenticationBtn').click(function(){
        $('.profile-container-body').addClass('display-none');
        $('.authentication-container-body').removeClass('display-none');
        $('.authentication-resend-button').addClass('display-none');
        $('.authentication-response-body').addClass('display-none');
        $.ajax({
            url: 'Controller/user.php?action=request-authentication',
            type: 'post',
            dataType: 'json',
            success: function (rspdata) {
              $('.authentication-loader').addClass('display-none');
              $('.authentication-input-body').removeClass('display-none');

              $('.authentication-code-send').click(function(){
                $authCode = $('.authentication-code').val();
                $('.authentication-loader').removeClass('display-none');
                $('.authentication-input-body').addClass('display-none');
                $.ajax({
                    url: 'Controller/user.php?action=confirm-authentication',
                    type: 'post',
                    dataType: 'json',
                    success: function (rspdata) {
                      $('.authentication-loader').addClass('display-none');
                      $('.authentication-input-body').addClass('display-none');
                      $('.authentication-response-body').removeClass('display-none');

                      if(rspdata.type == 2){
                          $ahtml = '<h3>'+rspdata.message +'</h3><br>'
                          $('.authentication-resend-button').removeClass('display-none');
                      }else if(rspdata.type == 3){
                        $ahtml = '<h3>'+rspdata.message +'</h3><br>'
                        $('.authentication-retry-button').removeClass('display-none');
                        $('.authenticationRetryBtn').click(function(){
                          $(this).addClass('display-none');
                          $('.authentication-response-body').addClass('display-none');
                          $('.authentication-input-body').removeClass('display-none');
                        });
                      }else{
                        $ahtml = '<h3>'+rspdata.message +'</h3>';
                      }


                      $('.authentication-response-body').find('div:eq(0)').html($ahtml);
                      console.log(rspdata);
                    },
                    data: {param: {userId: $userId, code: $authCode}}
                });
              });
            },
            data: {param: {userId: $userId}}
        });
      });

      $('#profile_form').on('submit',function(e){

        e.preventDefault();
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: 'Controller/user.php?action=send-profile&userId='+$userId,
            type: 'POST',
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                console.log(myXhr);
                $('.submit_form_btn').attr('disabled', true);
                return myXhr;
            },
            success: function (data) {
                console.log(data);
                if(data == 'true'){
                  $('.profile-container-body').removeClass('display-none');
                  $('.profile-input-container-body').addClass('display-none');
                  alert('Information successfully submitted');
                }else{
                  alert('Something went wrong, please try again.');
                }

                $('.submit_form_btn').removeAttr('disabled');
            },
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
      });

      $('.submitInfoBtn').click(function(){
        $('.profile-container-body').addClass('display-none');
        $('.profile-input-container-body').removeClass('display-none');
      });

      $('.date-from').datepicker({
          format: 'mm/dd/yyyy',
      });
      $('.date-to').datepicker({
          format: 'mm/dd/yyyy'
      });


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
          /*
            $('.profile-container-body').removeClass('display-none');
            $('.profile-sub').addClass('display-none');
            $('.authentication-response-body').addClass('display-none');
            $('.hide-on-profile-init').addClass('display-none');
            $('#profileModal').modal('show');
          */
          location.href= "profile.php";
        });


        $tracks = new Object;
        $tracks['track_0'] = ['amcharts-guide-live-guide', '<?=$fillColor?>'];
        localStorage.setItem("tracks", JSON.stringify($tracks));
        $tradeIndex = 0;



        $('.select_coin').click(function(){
            $urlParam = <?php echo json_encode($urlParam); ?>;
            if(typeof $urlParam.c != 'undefined'){
              $urlC = '&c='+$urlParam.c;
            }else{
              $urlC ='';
            }
            if(typeof $urlParam.template != 'undefined'){
              $urlTemplate = '&template='+$urlParam.template;
            }else{
              $urlTemplate ='';
            }
            location.href = 'go2.php?coin='+$(this).attr('data-coin')+'&currency='+$(this).attr('data-pair')+$urlC+$urlTemplate;


        });
        $('.candle-select-range').click(function(){
          $candleRange = $(this).attr('data-value');
          $urlParam = <?php echo json_encode($urlParam); ?>;
          if(typeof $urlParam.coin != 'undefined'){
            $urlCoin = '&coin='+$urlParam.coin;
          }else{
            $urlCoin ='';
          }
          if(typeof $urlParam.currency != 'undefined'){
            $urlCurrency = '&currency='+$urlParam.currency;
          }else{
            $urlCurrency ='';
          }
          if(typeof $urlParam.template != 'undefined'){
            $urlTemplate = '&template='+$urlParam.template;
          }else{
            $urlTemplate ='';
          }

          location.href = 'go2.php?c='+$candleRange+$urlCoin+$urlCurrency+$urlTemplate


        });

        // call History



        $.ajax({
            url: 'Controller/history2.php?action=load-history',
            type: 'post',
            dataType: 'json',
            success: function (rspdata) {
              $maxTradeAmount = rspdata.maxTradeAmount;
              $('.balance-loader').css('display', 'none');
              $('.balance-loader-display').css('display', 'block');
              $('.userFunds').html(toFixedNew(rspdata.funds, 2));
              $isLoadedUpdate = false;
              $isLoadedAdd = false;
              $btcUsd = rspdata.currentBtcPrice;
              $currentBtcPrice = rspdata.currentBtcPrice;
              $currentCoinPrice = toFixedNew(rspdata.currentCoinPrice, 8);
              $cur = '<?php echo $currency; ?>';
              $currencyPair = '<?php echo $currencyPair; ?>';
              $userId = <?php echo $userSess['info']['id']; ?>;
              $coin = '<?php echo $filter; ?>';
              $cryptoCoin = '<?php echo $filter; ?>';
              $maxQuadPrice = parseFloat($maxTradeAmount / $currentBtcPrice * 10000);
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
                  XRPBTC: 0,
                  EDOUSD: 0,
                  ETPUSD: 0,
                  NEOUSD: 0,
                  SANUSD: 0,
                  ZECUSD: 0,
                  DASHUSD: 0,
                  BCHBTC: 0,
                  EOSBTC: 0,
                  IOTBTC: 0,
                  OMGUSD: 0,
                  XMRUSD: 0,
                  BCHUSD: 0,
                  DASHBTC: 0,
                  LTCBTC: 0,
                  SANBTC: 0,
                  EDOBTC: 0,
                  ETPBTC: 0,
                  NEOBTC: 0,
                  ZECBTC: 0
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
                      }else if(key.pair == 'EDOUSD'){
                          $pairPrices.EDOUSD = key.price;
                      }else if(key.pair == 'ETPUSD'){
                          $pairPrices.ETPUSD = key.price;
                      }else if(key.pair == 'NEOUSD'){
                          $pairPrices.NEOUSD = key.price;
                      }else if(key.pair == 'SANUSD'){
                          $pairPrices.SANUSD = key.price;
                      }else if(key.pair == 'ZECUSD'){
                          $pairPrices.ZECUSD = key.price;
                      }else if(key.pair == 'DASHUSD'){
                          $pairPrices.DASHUSD = key.price;
                      }else if(key.pair == 'BCHBTC'){
                          $pairPrices.BCHBTC = key.price;
                      }else if(key.pair == 'EOSBTC'){
                          $pairPrices.EOSBTC = key.price;
                      }else if(key.pair == 'IOTBTC'){
                          $pairPrices.IOTBTC = key.price;
                      }else if(key.pair == 'OMGUSD'){
                          $pairPrices.OMGUSD = key.price;
                      }else if(key.pair == 'XMRUSD'){
                          $pairPrices.XMRUSD = key.price;
                      }else if(key.pair == 'BCHUSD'){
                          $pairPrices.BCHUSD = key.price;
                      }else if(key.pair == 'DASHBTC'){
                          $pairPrices.DASHBTC = key.price;
                      }else if(key.pair == 'LTCBTC'){
                          $pairPrices.LTCBTC = key.price;
                      }else if(key.pair == 'SANBTC'){
                          $pairPrices.SANBTC = key.price;
                      }else if(key.pair == 'EDOBTC'){
                          $pairPrices.EDOBTC = key.price;
                      }else if(key.pair == 'ETPBTC'){
                          $pairPrices.ETPBTC = key.price;
                      }else if(key.pair == 'NEOBTC'){
                          $pairPrices.NEOBTC = key.price;
                      }else if(key.pair == 'ZECBTC'){
                          $pairPrices.ZECBTC = key.price;
                      }

                  });
              })


                  $('#leverage').on('change', function(){

                    $curFunds = parseFloat($('.userFunds').text());

                    if($(this).val() > $curFunds){
                      alert('You dont have enough funds');
                      //$(this).val($curFunds.toFixed(2));
                    }

                    if($(this).val() > $maxTradeAmount){
                      alert('Maximum Trade Limit Exceeded. Reduce Trade Amount Size.');
                      //$(this).val($maxTradeAmount);
                    }
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

              $('.current_price').text($currentCoinPrice);
              $.getJSON('https://coincap.io/exchange_rates').done(function(responseRates){
                  $rates = responseRates.rates;
                  $dateRange = '1day';
                  $filter = '<?php echo $filter; ?>';
                  if($cur != 'BTC'){
                      $curCoinPrice = $currentCoinPrice * $rates[$cur];
                      $('#current_price').val($curCoinPrice);
                      if($filter == 'XRP' || $filter == 'ETC' ){
                        $('.current_price').text(toFixedNew($curCoinPrice, 8));
                      }else{
                        $('.current_price').text(toFixedNew($curCoinPrice, 2));
                      }

                  }else{
                    if($filter == 'ETC' || $filter == 'EDO' || $filter == 'SAN'  || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' || $filter == 'EOS'  || $filter == 'OMG'  || $filter == 'XMR'){
                      $curCoinPrice = $currentCoinPrice / $currentBtcPrice;
                      $('.current_price').text(toFixedNew($curCoinPrice, 8));
                    }
                  }
                  $tickHistory = rspdata.dataFiltered
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
                              "open" : toFixedNew($open, 8),
                              "high" :  toFixedNew($high, 8),
                              "low"  :  toFixedNew($low, 8),
                              "close" :  toFixedNew($close, 8)
                          }

                      );

                      if($tickHistory.length-1 == $x){
                        $group = [parseFloat(toFixedNew($open, 8)),parseFloat(toFixedNew($high, 8)),parseFloat(toFixedNew($low, 8)),parseFloat(toFixedNew($close, 8))];
                      }
                  }

                  console.log($data_trade);

                  //$data.splice(0, 40);
                  // render chart

                  if($filter == 'XRP'){
                    $tickValue = $currentCoinPrice

                  }else{
                    $tickValue = ($cur == 'BTC' ? $currentCoinPrice / $currentBtcPrice : $currentCoinPrice * $rates[$cur]);

                  }
                  if($filter == 'XRP' || $filter == 'ETC' || $filter == 'LTC' || $filter == 'EDO' || $filter == 'SAN' || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' || $filter == 'EOS' || $filter == 'OMG'  || $filter == 'XMR'){
                    $tickValue = toFixedNew($tickValue, 8);
              //      console.log($tickValue);
                  }else{
                    $tickValue = toFixedNew($tickValue, 4);
                  }
                  var config1 = {
                    //"hideCredits":true,
                      "type": "serial",
                      "theme": "<?=$template?>",
                      //"dataDateFormat":"YYYY-MM-DD JJ:NN:SS",
                      "dataProvider": $data_trade,
                      "valueAxes": [ {
                          "position": "right",
						              "boldLabels":true,
                          "guides": [ {
                              "value": $tickValue,
                              "label": $tickValue,
                              "position": "right",
                              "dashLength": 0,
                              "axisThickness": 1,
                              "axisAlpha": 1,
                              "fillAlpha": 0,
                              "color": "<?=$textFill?>",
                              "fontSize": 14,
                              "backgroundColor": "#008D00",
                              "id": "live-guide1",

                          },

                          ],

                      } ],
                      "graphs": [ {
                          "id": "g2",
                          "proCandlesticks": false,
                          "balloonText": "Open:<b>[[open]]</b><br>Low:<b>[[low]]</b><br>High:<b>[[high]]</b><br>Close:<b>[[close]]</b><br>",
                          "closeField": "close",
                          "highField": "high",
                          "lineColor": "<?=$fillColor?>",
                          "lineAlpha": 1,
                          "lowField": "low",
                          "negativeFillColors": "#db4c3c",
                          "negativeLineColor": "#db4c3c",
                          "openField": "open",
                          "title": "Price:",
                          "type": "line",
                          "valueField": "close",

                      } ],
                      "valueLineEnabled": true,
                      "valueLineBalloonEnabled": true,
                      "chartScrollbar": {
                          "graph": "g2",
                          "graphType": "line",
                          "scrollbarHeight": 20
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
						  "boldLabels":true,
						  "axisThickness":3,
						  "axisColor":"#0000"
                      },
                      "dataDateFormat": "YYYY-MM-DD HH:NN:SS.Q",
                      "export": {
                          "enabled": true
                      }
                  }
                  $chart1 = AmCharts.makeChart( "chartdiv1", config1 );

				//console.log('hello charts');

                  $chart1.addListener( "rendered", zoomChart );
                  //$chart1.graphsSet.toBack();
                  $chart1.addListener("zoomed", handleZoom);
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

                              $chart1.valueAxes[0].guides.push(
                                  {
                                      "value": toFixedNew($amount, 8),
                                      "label": "Entry Price \n"+toFixedNew($amount, 8),
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
                              $startIndex = $chart1.startIndex;
                              $endIndex = $chart1.endIndex - $chart1.dataProvider.length;
                              $chart1.validateData();
                              $tIndex = 'track_'+($tradeIndex+1);
                              $tracks[$tIndex] = ['amcharts-guide-'+$tradeId, '#00000'];
                              addRect($tracks);
                              localStorage.setItem("tracks", JSON.stringify($tracks));

                              zoomChart($startIndex, $endIndex);

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


      // this method is called when chart is first inited as we listen for "dataUpdated" event

                  // get pending trades
                  console.log($tracks);
                    zoomChart(zoomValue, 1);

                  addRect($tracks);
                  $('.amcharts-chart-div a').css('display', 'none');

				  function handleZoom(event) {
                     $tracks = JSON.parse(localStorage.getItem("tracks"));
                     addRect($tracks);
                   }


                  // switchery
				  if( typeof btnSwitch != typeof undefined ) {
					  btnSwitch.onchange = function() {
						$chart1.clear();
						if(btnSwitch.checked == true){
						  $scheme = 'light';
						}else{
						  $scheme = 'black';
						}

						$urlParam = <?php echo json_encode($urlParam); ?>;
						if(typeof $urlParam.coin != 'undefined'){
						  $urlCoin = '&coin='+$urlParam.coin;
						}else{
						  $urlCoin ='';
						}
						if(typeof $urlParam.currency != 'undefined'){
						  $urlCurrency = '&currency='+$urlParam.currency;
						}else{
						  $urlCurrency ='';
						}
						if(typeof $urlParam.template != 'undefined'){
						  $urlTemplate = '&template='+$urlParam.template;
						}else{
						  $urlTemplate ='';
						}

						if(typeof $urlParam.c != 'undefined'){
						  $urlC = '&c='+$urlParam.c;
						}else{
						  $urlC ='';
						}


						location.href = "go2.php?template="+$scheme+$urlC+$urlCoin+$urlCurrency;
					  }
                  }


                  // console.log('Cur: ' + $cur);
                  // console.log('Filter: ' + $filter);
                  // console.log('Pair: ' + $currencyPair);
                  // var sock = 0;
                  // $coin = $filter;
                  // $price = 0;
                  // setInterval(function(){
                  //   $.getJSON( "coin_live_price/"+$filter+".json?v=1.3", function( json ) {
                  //     $updated = true;
                  //     $price = json.dollarPrice;
                  //     $btcPrice = $price / $currentBtcPrice;
                  //     $btcPriceConv = json.btcPrice;
                  //     $currentDate = json.timestamp;
                  //     $currentMatchDate = json.matchDate;
                  //     console.log('TS');
                  //     console.log(json)
                  //     if($coin == 'BTC'){
                  //         $currentBtcPrice = $price;
                  //         $btcUsd = $price;
                  //         $pairPrices.BTCUSD = $price;
                  //         $pairPrices.BTCCNY = $btcUsd * $rates['CNY'];
                  //         $pairPrices.BTCEUR = $btcUsd * $rates['EUR'];
                  //         $pairPrices.BTCJPY = $btcUsd * $rates['JPY'];
                  //
                  //     }else if($coin == 'ETH'){
                  //
                  //         $pairPrices.ETHUSD = $price;
                  //         $pairPrices.ETHBTC = $price / $currentBtcPrice;
                  //
                  //     }else if($coin == 'ETC'){
                  //
                  //         $pairPrices.ETCBTC = $price / $currentBtcPrice;
                  //
                  //     }else if($coin == 'LTC'){
                  //
                  //         $pairPrices.LTCUSD = $price;
                  //         $pairPrices.LTCCNY = $price * $rates['CNY'];
                  //         $pairPrices.LTCEUR = $price * $rates['EUR'];
                  //         $pairPrices.LTCJPY = $price * $rates['JPY'];
                  //         $pairPrices.LTCBTC = $price / $currentBtcPrice;
                  //     }else if($coin == 'XRP'){
                  //         $pairPrices.XRPBTC = $price;
                  //     }else if($coin == 'EDO'){
                  //         $pairPrices.EDOUSD = $price;
                  //         $pairPrices.EDOBTC = $price / $currentBtcPrice;
                  //     }else if($coin == 'ETP'){
                  //         $pairPrices.ETPUSD = $price;
                  //         $pairPrices.ETPBTC = $price / $currentBtcPrice;
                  //     }else if($coin == 'NEO'){
                  //         $pairPrices.NEOUSD = $price;
                  //         $pairPrices.NEOBTC = $price / $currentBtcPrice;
                  //     }else if($coin == 'SAN'){
                  //         $pairPrices.SANUSD = $price;
                  //         $pairPrices.SANBTC = $price / $currentBtcPrice;
                  //     }else if($coin == 'ZEC'){
                  //         $pairPrices.ZECUSD = $price;
                  //         $pairPrices.ZECBTC = $btcPrice;
                  //     }else if($coin == 'DASH'){
                  //         $pairPrices.DASHUSD = $price;
                  //         $pairPrices.DASHBTC = $price / $currentBtcPrice;
                  //     }else if($coin == 'BCH'){
                  //         $pairPrices.BCHBTC = $price / $currentBtcPrice;
                  //     }else if($coin == 'EOS'){
                  //         $pairPrices.EOSBTC = $price / $currentBtcPrice;
                  //     }else if($coin == 'IOT'){
                  //         $pairPrices.IOTBTC = $price / $currentBtcPrice;
                  //     }else if($coin == 'OMG'){
                  //         $pairPrices.OMGUSD = $price;
                  //     }else if($coin == 'XMR'){
                  //         $pairPrices.XMRUSD = $price;
                  //     }else if($coin == 'BCH'){
                  //         $pairPrices.BCHUSD = $price;
                  //     }
                  //
                  //
                  //         $beforePrice = parseFloat($('#current_price').val());
                  //         if($filter =='XRP'){
                  //             $dPrice = $price;
                  //         }else{
                  //             $dPrice = ($cur == 'BTC' ? $btcPrice : $price * $rates[$cur]);
                  //         }
                  //
                  //     //    console.log($dPrice);
                  //         if($filter == 'XRP' || $filter == 'ETC' || $filter == 'ETH' || $filter == 'EDO' || $filter == 'SAN'  || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' || $filter == 'EOS'  || $filter == 'OMG'  || $filter == 'XMR'){
                  //           $('#current_price').val(toFixedNew($dPrice, 8));
                  //           $('.current_price').text(toFixedNew($dPrice, 8));
                  //         }else{
                  //           $('#current_price').val(toFixedNew($dPrice, 8));
                  //           $('.current_price').text(toFixedNew($dPrice, 8));
                  //         }
                  //
                  //         if($price > $beforePrice){
                  //
                  //             $('.percent_status').removeClass('text-danger').addClass('text-success');
                  //             $('.percent_status').find('i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
                  //         }else{
                  //
                  //             $('.percent_status').removeClass('text-success').addClass('text-danger');
                  //             $('.percent_status').find('i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
                  //         }
                  //
                  //         // Get Last Trade
                  //         $curentDataLength = $chart1.dataProvider.length;
                  //
                  //         $lastDate = $chart1.dataProvider[$curentDataLength - 1].date;
                  //         $lastDate = $lastDate.split(':');
                  //
                  //         $lastDate = $lastDate[0]+':'+$lastDate[1]+':00';
                  //       //  console.log('Last: ' + $lastDate);
                  //         //   console.log('Current Date: ' + $currentDate + ' | Last Date: ' + $lastDate);
                  //     //    console.log('Last:'+$lastDate);
                  //           $operator = '===';
                  //
                  //           if($mm == 'mm'){
                  //             if($currentMatchDate == $lastDate){
                  //
                  //                 $group.push($dPrice);
                  //
                  //                 $ohlcData = computeOHLC($group);
                  //
                  //                 if($cur == 'BTC'){
                  //
                  //                     if($currencyPair == 'BTC'){
                  //
                  //                         $open = parseFloat($ohlcData[0]);
                  //                         $high = parseFloat($ohlcData[1]);
                  //                         $low = parseFloat($ohlcData[2]);
                  //                         $close = parseFloat($ohlcData[3]);
                  //                     }else{
                  //                         if($filter == 'ETH' || $filter == 'EDO' || $filter == 'ETC' || $filter == 'SAN' || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' || $filter == 'EOS'  || $filter == 'OMG'  || $filter == 'XMR'){
                  //
                  //                           $open = parseFloat($ohlcData[0]);
                  //                           $high = parseFloat($ohlcData[1]);
                  //                           $low = parseFloat($ohlcData[2]);
                  //                           $close = parseFloat($ohlcData[3]);
                  //                         }else{
                  //
                  //                           $open = parseFloat($ohlcData[0] / $currentBtcPrice);
                  //                           $high = parseFloat($ohlcData[1] / $currentBtcPrice);
                  //                           $low = parseFloat($ohlcData[2] / $currentBtcPrice);
                  //                           $close = parseFloat($ohlcData[3] / $currentBtcPrice);
                  //                         }
                  //
                  //                     }
                  //                 }else{
                  //                     if($filter == 'BTC'){
                  //                       $open = parseFloat($ohlcData[0]);
                  //                       $high = parseFloat($ohlcData[1]);
                  //                       $low = parseFloat($ohlcData[2]);
                  //                       $close = parseFloat($ohlcData[3]);
                  //                     }else{
                  //                       if($filter == 'LTC'){
                  //                         if($cur == 'CNY'){
                  //                           $open = parseFloat($ohlcData[0]);
                  //                           $high = parseFloat($ohlcData[1]);
                  //                           $low = parseFloat($ohlcData[2]);
                  //                           $close = parseFloat($ohlcData[3]);
                  //                         }
                  //                       }else{
                  //                         $open = parseFloat($ohlcData[0] * $rates[$cur]);
                  //                         $high = parseFloat($ohlcData[1] * $rates[$cur]);
                  //                         $low = parseFloat($ohlcData[2] * $rates[$cur]);
                  //                         $close = parseFloat($ohlcData[3] * $rates[$cur]);
                  //                       }
                  //
                  //                     }
                  //                 }
                  //
                  //                 $chart1.dataProvider[$curentDataLength - 1] = {
                  //                     "date" : $lastDate,
                  //                     "open" : toFixedNew($open, 8),
                  //                     "high" :  toFixedNew($high, 8),
                  //                     "low"  :  toFixedNew($low, 8),
                  //                     "close" :  toFixedNew($close, 8)
                  //                 }
                  //             }else{
                  //
                  //                 $group = [];
                  //
                  //                 $group.push($dPrice);
                  //                 $ohlcData = computeOHLC($group);
                  //
                  //                 if($cur == 'BTC'){
                  //
                  //                     if($currencyPair == 'BTC'){
                  //
                  //                       $open = parseFloat($ohlcData[0]);
                  //                       $high = parseFloat($ohlcData[1]);
                  //                       $low = parseFloat($ohlcData[2]);
                  //                       $close = parseFloat($ohlcData[3]);
                  //                     }else{
                  //                       if($filter == 'ETH' || $filter == 'EDO' || $filter == 'ETC' || $filter == 'SAN' || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' ||  $filter == 'EOS'  || $filter == 'OMG'  || $filter == 'XMR'){
                  //
                  //                         $open = parseFloat($ohlcData[0]);
                  //                         $high = parseFloat($ohlcData[1]);
                  //                         $low = parseFloat($ohlcData[2]);
                  //                         $close = parseFloat($ohlcData[3]);
                  //                       }else{
                  //
                  //                         $open = parseFloat($ohlcData[0] / $currentBtcPrice);
                  //                         $high = parseFloat($ohlcData[1] / $currentBtcPrice);
                  //                         $low = parseFloat($ohlcData[2] / $currentBtcPrice);
                  //                         $close = parseFloat($ohlcData[3] / $currentBtcPrice);
                  //                       }
                  //                     }
                  //                 }else{
                  //                   if($filter == 'BTC'){
                  //                     $open = parseFloat($ohlcData[0]);
                  //                     $high = parseFloat($ohlcData[1]);
                  //                     $low = parseFloat($ohlcData[2]);
                  //                     $close = parseFloat($ohlcData[3]);
                  //                   }else{
                  //                     if($filter == 'LTC'){
                  //                       if($cur == 'CNY'){
                  //                         $open = parseFloat($ohlcData[0]);
                  //                         $high = parseFloat($ohlcData[1]);
                  //                         $low = parseFloat($ohlcData[2]);
                  //                         $close = parseFloat($ohlcData[3]);
                  //                       }
                  //                     }else{
                  //                       $open = parseFloat($ohlcData[0] * $rates[$cur]);
                  //                       $high = parseFloat($ohlcData[1] * $rates[$cur]);
                  //                       $low = parseFloat($ohlcData[2] * $rates[$cur]);
                  //                       $close = parseFloat($ohlcData[3] * $rates[$cur]);
                  //                     }
                  //
                  //                   }
                  //                 }
                  //
                  //                 $chart1.dataProvider.shift(); // removes first index
                  //                 $chart1.dataProvider.push( {
                  //                     "date" : $currentDate,
                  //                     "open" : toFixedNew($open, 8),
                  //                     "high" :  toFixedNew($high, 8),
                  //                     "low"  :  toFixedNew($low, 8),
                  //                     "close" :  toFixedNew($close, 8)
                  //                 } );
                  //
                  //
                  //
                  //             }
                  //           }else{
                  //             $lastDateMM = getLastMM($lastDate, $mm);
                  //             $lastDate = $lastDate.replace('T', ' ');
                  //             if($currentDate > $lastDateMM && $currentDate <= $lastDate){
                  //
                  //                 $group.push($dPrice);
                  //
                  //                 $ohlcData = computeOHLC($group);
                  //
                  //                 if($cur == 'BTC'){
                  //                     if($currencyPair == 'BTC'){
                  //                         $open = parseFloat($ohlcData[0]);
                  //                         $high = parseFloat($ohlcData[1]);
                  //                         $low = parseFloat($ohlcData[2]);
                  //                         $close = parseFloat($ohlcData[3]);
                  //                         console.log('B');
                  //                     }else{
                  //                         if($filter == 'ETH' || $filter == 'EDO' || $filter == 'ETC' || $filter == 'SAN' || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' ||  $filter == 'EOS'  || $filter == 'OMG'  || $filter == 'XMR'){
                  //                           $open = parseFloat($ohlcData[0]);
                  //                           $high = parseFloat($ohlcData[1]);
                  //                           $low = parseFloat($ohlcData[2]);
                  //                           $close = parseFloat($ohlcData[3]);
                  //                         }else{
                  //                           console.log('A');
                  //                           $open = parseFloat($ohlcData[0] / $currentBtcPrice);
                  //                           $high = parseFloat($ohlcData[1] / $currentBtcPrice);
                  //                           $low = parseFloat($ohlcData[2] / $currentBtcPrice);
                  //                           $close = parseFloat($ohlcData[3] / $currentBtcPrice);
                  //                         }
                  //
                  //                     }
                  //                 }else{
                  //                     if($filter == 'BTC'){
                  //                       $open = parseFloat($ohlcData[0]);
                  //                       $high = parseFloat($ohlcData[1]);
                  //                       $low = parseFloat($ohlcData[2]);
                  //                       $close = parseFloat($ohlcData[3]);
                  //                     }else{
                  //                       if($filter == 'LTC'){
                  //                         if($cur == 'CNY'){
                  //                           $open = parseFloat($ohlcData[0]);
                  //                           $high = parseFloat($ohlcData[1]);
                  //                           $low = parseFloat($ohlcData[2]);
                  //                           $close = parseFloat($ohlcData[3]);
                  //                         }
                  //                       }else{
                  //                         $open = parseFloat($ohlcData[0] * $rates[$cur]);
                  //                         $high = parseFloat($ohlcData[1] * $rates[$cur]);
                  //                         $low = parseFloat($ohlcData[2] * $rates[$cur]);
                  //                         $close = parseFloat($ohlcData[3] * $rates[$cur]);
                  //                       }
                  //
                  //                     }
                  //                 }
                  //
                  //                 $chart1.dataProvider[$curentDataLength - 1] = {
                  //                     "date" : $lastDate,
                  //                     "open" : toFixedNew($open, 8),
                  //                     "high" :  toFixedNew($high, 8),
                  //                     "low"  :  toFixedNew($low, 8),
                  //                     "close" :  toFixedNew($close, 8)
                  //                 }
                  //             }else{
                  //
                  //                 $group = [];
                  //
                  //                 $group.push($dPrice);
                  //                 $ohlcData = computeOHLC($group);
                  //
                  //                 if($cur == 'BTC'){
                  //                     if($currencyPair == 'BTC'){
                  //
                  //                       $open = parseFloat($ohlcData[0]);
                  //                       $high = parseFloat($ohlcData[1]);
                  //                       $low = parseFloat($ohlcData[2]);
                  //                       $close = parseFloat($ohlcData[3]);
                  //                     }else{
                  //                       if($filter == 'ETH' || $filter == 'EDO' || $filter == 'ETC' || $filter == 'SAN' || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' ||  $filter == 'EOS'  || $filter == 'OMG'  || $filter == 'XMR'){
                  //
                  //                         $open = parseFloat($ohlcData[0]);
                  //                         $high = parseFloat($ohlcData[1]);
                  //                         $low = parseFloat($ohlcData[2]);
                  //                         $close = parseFloat($ohlcData[3]);
                  //                       }else{
                  //
                  //                         $open = parseFloat($ohlcData[0] / $currentBtcPrice);
                  //                         $high = parseFloat($ohlcData[1] / $currentBtcPrice);
                  //                         $low = parseFloat($ohlcData[2] / $currentBtcPrice);
                  //                         $close = parseFloat($ohlcData[3] / $currentBtcPrice);
                  //                       }
                  //                     }
                  //                 }else{
                  //                   if($filter == 'BTC'){
                  //                     $open = parseFloat($ohlcData[0]);
                  //                     $high = parseFloat($ohlcData[1]);
                  //                     $low = parseFloat($ohlcData[2]);
                  //                     $close = parseFloat($ohlcData[3]);
                  //                   }else{
                  //                     if($filter == 'LTC'){
                  //                       if($cur == 'CNY'){
                  //                         $open = parseFloat($ohlcData[0]);
                  //                         $high = parseFloat($ohlcData[1]);
                  //                         $low = parseFloat($ohlcData[2]);
                  //                         $close = parseFloat($ohlcData[3]);
                  //                       }
                  //                     }else{
                  //                       $open = parseFloat($ohlcData[0] * $rates[$cur]);
                  //                       $high = parseFloat($ohlcData[1] * $rates[$cur]);
                  //                       $low = parseFloat($ohlcData[2] * $rates[$cur]);
                  //                       $close = parseFloat($ohlcData[3] * $rates[$cur]);
                  //                     }
                  //
                  //                   }
                  //                 }
                  //
                  //                 $currentDate = getCurrentMMDate($lastDate, $mm);
                  //                 console.log('Current Last: ' + $currentDate);
                  //                 $chart1.dataProvider.shift(); // removes first index
                  //                 $chart1.dataProvider.push( {
                  //                     "date" : $currentDate,
                  //                     "open" : toFixedNew($open, 8),
                  //                     "high" :  toFixedNew($high, 8),
                  //                     "low"  :  toFixedNew($low, 8),
                  //                     "close" :  toFixedNew($close, 8)
                  //                 } );
                  //
                  //
                  //
                  //             }
                  //           }
                  //
                  //
                  //
                  //           if($filter == 'XRP' || $filter == 'ETC' || $filter == 'ETH' || $filter == 'LTC' || $filter == 'EDO' || $filter == 'SAN' || $filter == 'BCH' || $filter == 'IOT' || $filter == 'DASH' || $filter == 'LTC' || $filter == 'ETP' || $filter == 'NEO' ||  $filter == 'EOS' || $filter == 'OMG' || $filter == 'XMR'){
                  //             $lPrice = toFixedNew($dPrice, 8);
                  //             $chart1.valueAxes[0].guides[0].label = toFixedNew($dPrice, 8);
                  //             $chart1.valueAxes[0].guides[0].value = toFixedNew($dPrice, 8);
                  //           //  console.log('P: ' + toFixedNew($dPrice, 6));
                  //
                  //           }else{
                  //             $lPrice = toFixedNew($dPrice, 2);
                  //             $chart1.valueAxes[0].guides[0].label = toFixedNew($dPrice, 2);
                  //             $chart1.valueAxes[0].guides[0].value = toFixedNew($dPrice, 2);
                  //           }
                  //
                  //
                  //           $startIndex = $chart1.startIndex;
                  //           $endIndex = $chart1.endIndex - $chart1.dataProvider.length;
                  //           $chart1.validateData();
                  //           $('.amchart1s-chart-div a').css('display', 'none');
                  //           zoomChart($startIndex, $endIndex);
                  //
                  //           addRect($tracks);
                  //
                  //           // check win/lose situation
                  //           for($h = 0; $h < $tradeTransaction.length; $h++){
                  //             $landingAmount = $tradeTransaction[$h].amount;
                  //             $tType = $tradeTransaction[$h].type;
                  //             $tIndex = $tradeTransaction[$h].index;
                  //
                  //             $tStatus = $tradeTransaction[$h].status;
                  //             $tPayout = $tradeTransaction[$h].payout;
                  //
                  //             if($tStatus == 'live'){
                  //               if($tType == 'buy'){
                  //                 if($landingAmount <= $lPrice){
                  //                   $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-danger').addClass('text-success').text($tPayout);
                  //                 }else if($landingAmount == $lPrice){
                  //                   $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-danger').removeClass('text-success').text(0);
                  //                 }else{
                  //                   $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-success').addClass('text-danger').text(0);
                  //                 }
                  //               }else{
                  //                 if($landingAmount >= $lPrice){
                  //                   $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-danger').addClass('text-success').text($tPayout);
                  //                 }else if($landingAmount == $lPrice){
                  //                   $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-danger').removeClass('text-success').text(0);
                  //                 }else{
                  //                   $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-success').addClass('text-danger').text(0);
                  //                 }
                  //               }
                  //             }
                  //           }
                  //   });
                  // }, 1000);


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
                          $amount = parseFloat($('#current_price').val());

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

                          $chart1.valueAxes[0].guides.push(
                              {
                                  "value": toFixedNew($amount, 8),
                                  "label": "Entry Price \n"+ toFixedNew($amount, 8),
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
                          $startIndex = $chart1.startIndex;
                          $endIndex = $chart1.endIndex - $chart1.dataProvider.length;
                          $chart1.validateData();
                          zoomChart($startIndex, $endIndex);


                          $('.amcharts-chart-div a').css('display', 'none');
                          //validate();
                          //insert_order('buy');
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
                          console.log('W');
                          console.log($logTradeData);
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
                            $amount = parseFloat($('#current_price').val());
                            $chart1.valueAxes[0].guides.push(
                                {
                                  "value": toFixedNew($amount, 8),
                                  "label": "Entry Price \n"+toFixedNew($amount, 8),
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
                            $startIndex = $chart1.startIndex;
                            $endIndex = $chart1.endIndex - $chart1.dataProvider.length;
                            $chart1.validateData();
                            zoomChart($startIndex, $endIndex);


                            $('.amcharts-chart-div a').css('display', 'none');

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
                            $tracks[$tIndex] = ['amchart1s-guide-live-guide-sell-'+($tradeIndex+1), '#DB333C'];
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

                          if($minute == 0){
                              $amount = parseFloat($('#current_price').val());
                                  $amountPair = $pairPrices[$pairing];

                              if($type == 'buy'){
                                  if($amount > $entryValue){
                                      $row.find('.trade-status').removeClass('btn-default').addClass('btn-success').text('CLOSE');
                                      $tradeStatus = 'win';
                                      $row.find('.trade-payout').removeClass('text-danger').addClass('text-success').text($payoutt);
                                      $row.find('.exit-price').text($amount);

                                  }else if($amount == $entryValue){
                                    $row.find('.trade-status').removeClass('btn-default').addClass('btn-default').text('CLOSE');
                                    $row.find('.trade-payout').text('0');
                                    $tradeStatus = 'even';
                                    $row.find('.trade-payout').removeClass('text-success').removeClass('text-danger').text(0);
                                    $row.find('.exit-price').text($amount);
                                  }else{
                                      $row.find('.trade-status').removeClass('btn-default').addClass('btn-danger').text('CLOSE');
                                      $row.find('.trade-payout').text('0');
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
                                  winAmount: $winAmount,
                                  leverage: $leveraget,
                                  status: $tradeStatus,
                                  tradeId: $tradeId,
                                  userId: $userId
                              }

                              if($tradeStatus == 'win'){
                                console.log($tradeData);
                              }


                              logTrades($tradeData);
                              $row.find('.timer').text('');
                              $chart1.valueAxes[0].guides[$index + 1].label = 0;
                              $chart1.valueAxes[0].guides[$index + 1].value = 0;
                              $startIndex = $chart1.startIndex;
                              $endIndex = $chart1.endIndex - $chart1.dataProvider.length;

                              $tIndex = 'track_'+($index+1);
                              delete $tracks[$tIndex];
                              localStorage.setItem("tracks", JSON.stringify($tracks));
                              console.log(localStorage.getItem('tracks'));
                              $chart1.validateData();
                              zoomChart($startIndex, $endIndex);

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
                      $chart1.zoomToIndexes( $start, $chart1.dataProvider.length - $end );
                  }
                  catch(err) {
                  //    console.log('');
                  }

              }


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
            },
            data: {param: JSON.stringify({
              userId: $userId,
              filter: '<?php echo $filter; ?>',
              tickInterval: '<?php echo $tickInterval; ?>',
              device: device,
              mm: $mm
            }
          )}
        });


        // on trade history

        $('.date-history').change(function(){
          $('.table-loader').css('display', 'block');
          $('.trade-table').css('display', 'none');
          $.ajax({
              url: 'Controller/history2.php?action=trade-history',
              type: 'post',
              dataType: 'json',
              success: function (data) {
                $('.table-loader').css('display', 'none');
                $('.trade-table').css('display', 'table');
                $tableBody = '';
                  console.log(data);
                  $totalPnl = 0;
                if(data.length == 0){
                  $tableBody += '<tr><td colspan="8" class="text-center">No data available</td></tr>';
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

                $('#tradeHistoryBody').html($tableBody);

              },
              data: {param: JSON.stringify({userId: $userId, from: $('.date-from').val(), to: $('.date-to').val()})}
          });
        });



    });

      setInterval(function(){
        addRect($tracks);
      }, 500);

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

      function getRandomInt(min = -10, max = 10) {
          return Math.floor(Math.random() * (max - min + 1)) + min;
      }

      function getUserById($id){
        $.ajax({
            url: '../Controller/user.php?action=get-user-by-id',
            type: 'post',
            dataType: 'json',
            success: function (rsp) {
                $tradePercentageAmount = rsp.balanceStatus.trade_percentage_amount;
                $balanceId = rsp.balanceStatus.id;
            },
            data: {param: {id: $id, admin: 0}}
        });
      }
</script>



<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-127217195-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-127217195-1');
</script>


</body>
</html>
