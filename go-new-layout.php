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

}else{
  $tickInterval = 'mm';
  $mm1Class = 'btn-primary';

}


//$data = json_decode(file_get_contents('https://api.hitbtc.com/api/2/public/candles/'.$filter.$currencyPair.'?period=M1&limit=120'), true);


$urlParam = $_GET;

$dateFrom = date('m/d/Y', strtotime('-7 days'));
$dateTo = date('m/d/Y');

?>
<!DOCTYPE html>
<html>
<head>
    <title>ZoloTrader</title>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="//cdn.rawgit.com/noelboss/featherlight/1.7.9/release/featherlight.min.css" type="text/css" rel="stylesheet" />
    <script src="//cdn.rawgit.com/noelboss/featherlight/1.7.9/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
    <script src="https://www.amcharts.com/lib/3/serial.js"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" media="all" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>
    <link rel="stylesheet" href="assets/css/switchery.min.css" />
    <link rel="stylesheet" href="assets/css/simple-line-icons.min.css" />
    <link rel="stylesheet" href="assets/css/main.css?v=1.3" />
    <script src="assets/js/switchery.min.js"></script>

    <script>
        var zoomEvents = [];
        if (screen.width <= 768) {
            var device = 'mobile';
            var zoomValue = '60';
        }else{
          var device = 'desktop';
          var zoomValue = '190';
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
    <div class="col-md-12" style="text-align: right;">
      <i class="fa fa-question-circle popOverTrigger-btcSwitch"></i>
    </div>
    <div class="col-md-12" style="text-align: right;">
      BTC <input type="checkbox" class="js-switch  pull-right" id="btcSwitch" checked /> <span class="text-success">dm</span>BTC
    </div>
  </div>
    <div class="row">
      <div class="col-md-10 col-sm-10 col-xs-10 nopadding">

        <div id="chartdiv" style="height: 860px;"><img src="assets/img/tenor.gif"></div>
      </div>
      <div class="col-md-2 col-md-2-mod col-sm-2 col-xs-2 nopadding">
        <div class="col-md-12 col-sm-12 col-xs-12 spacer-2x text-center">
          Balance <i class="fa fa-question-circle popOverTrigger"></i>
          <br>
          <div class="balance-loader text-center">
              <img src="assets/img/tenor.gif" width="100" height="100">
          </div>
          <span  type="button" class=" balance-loader-display btn outline-primary full-width btn-small-text balance" data-value="nbtc" style="display: none; padding-left: 2px; padding-right: 2px;"><span class="userFunds">0.00</span></span>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12"></div>
        <div class="col-md-6 col-sm-12 col-xs-12 spacer-3-4">
            <button  class="btn btn-primary btn-sm full-width-sm full-width-md" id="getHistoryTrigger" title="Trade History">Trade History</button>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12 spacer-3-4">
            <button  class="btn btn-primary btn-sm full-width btn-small-text" title="Profile" id="profileBtn">Profile</button>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 spacer-2x text-center">
          Timeframe
          <br>
          <button class="btn <?=$mm1Class?> btn-xs candle-select-range" data-value="1mm">1M</button>
          <button class="btn <?=$mm5Class?> btn-xs candle-select-range" data-value="5mm">5M</button>
          <button class="btn <?=$mm30Class?> btn-xs candle-select-range" data-value="30mm">30M</button>
          <button class="btn <?=$mm60Class?> btn-xs candle-select-range" data-value="60mm">60M</button>
          <button class="btn <?=$mm240Class?> btn-xs candle-select-range" data-value="240mm">240M</button>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12 spacer-2x text-center">
          Market
          <br>
          <div class="btn-group full-width">
            <button type="button" class="btn btn-sm outline-primary dropdown-toggle full-width dropdown-filter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
        <div class="col-md-6 col-sm-12 col-xs-12 spacer-2x text-center">
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
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 spacer-2x text-center">
          <span class="leverage-display">Leverage = <span class=""><img src="assets/img/btc-logo.ico" style="width: 25px; height: 25px; margin-bottom: 5px;"></span><span class="current-exchange-price text-bold"></span> <i class="fa fa-question-circle popOverTrigger-leverage"></i></span>
          <br>
          <div class="form-inline">
            <input type="hidden" id="current_price">
            <input type="hidden" class="max-quadpips">
            <input type="hidden" class="max-dollar-price">
              <input id="leverage" type="number" value='0.5' min='0.1' class="form-control" aria-label="3.5" style="width: 80%;"> <i class="fa fa-question-circle popOverTrigger-leverage-input"></i>

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
            <div class="col-md-6 col-md-offset-2 col-sm-12 table-border">
              <div class="live-trade-loader text-center">
                  <img src="assets/img/tenor.gif">
              </div>
              <span><i class="fa fa-question-circle popOverTrigger-table pull-right"></i></span>
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

<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-90" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="profile-container-body">
              <div class="col-md-6 col-sm-12">
                <div class="well well-lg profile-well">
                  <div class="row">
                    <div class="col-md-3">
                      <i class="icon-user icon-big"></i>
                    </div>
                    <div class="col-md-9">
                      <div>
                        <span class="heading"><?=$userSess['info']['email']?></span>
                      </div>
                      <div class="spacer">
                        <span>Last login: <?=$userSess['log_history'][count($userSess['log_history'])-1]['date_time']?></span>
                        <br>
                        <span>IP address: <?=$userSess['log_history'][count($userSess['log_history'])-1]['ip_address']?></span>
                      </div>
                      <div class="spacer">
                        <button class="btn outline-primary btn-lg full-width submitInfoBtn">Submit Information</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="well well-lg profile-well">
                  <div class="row">
                    <div class="col-md-3">
                      <i class="icon-envelope icon-big"></i>
                    </div>
                    <div class="col-md-9">
                      <div>
                        <span class="heading">Email Authentication</span>
                      </div>
                      <div class="spacer">
                        <span>use for security validation.</span>
                        <br>
                        <span>&nbsp;</span>
                      </div>
                      <div class="spacer">
                        <button class="btn outline-primary btn-lg full-width authenticationBtn">Enable</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="well well-lg profile-well">
                  <div class="row">
                    <div class="col-md-3">
                      <i class="icon-lock icon-big"></i>
                    </div>
                    <div class="col-md-9">
                      <div>
                        <span class="heading">Security</span>
                      </div>
                      <div class="spacer">
                        <button class="btn outline-primary btn-lg full-width changePasswordBtn">Change Password</button>
                      </div>
                      <div class="spacer">
                        <button class="btn outline-primary btn-lg full-width changeSecretQuestionBtn">Change Secret Questions</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="well well-lg profile-well">
                  <div class="row">
                    <div class="col-md-3">
                      <i class="icon-user-following icon-big"></i>
                    </div>
                    <div class="col-md-9">
                      <div>
                        <span class="heading">Account</span>
                      </div>
                      <div class="spacer">
                        <button class="btn outline-primary btn-lg full-width depositWithdrawBtn" data-action="deposit">Deposit Crypto</button>
                      </div>
                      <div class="spacer">
                        <button class="btn outline-primary btn-lg full-width depositWithdrawBtn" data-action="withdraw">Withdraw Crypto</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="spacer col-md-12"><span class="heading">Last Login</span></div>
              <div class="col-md-12 col-sm-12">
                <div class="well well-lg profile-well">
                  <div class="row">
                    <div class="col-md-12">
                      <table class="table">
                        <thead>
                          <tr>
                            <th class="text-center">Date</th>
                            <th class="text-center">Ip Address</th>
                            <th class="text-center">Location</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            for($x = 0; $x < count($userSess['log_history']); $x++){
                              echo '<tr class="text-center">
                                      <td>'.$userSess['log_history'][$x]['date_time'].'</td>
                                      <td>'.$userSess['log_history'][$x]['ip_address'].'</td>
                                      <td>'.$userSess['log_history'][$x]['region'].', '.$userSess['log_history'][$x]['location'].'</td>
                                    </tr>';
                            }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="profile-input-container-body profile-sub display-none">
              <form enctype="multipart/form-data" id="profile_form">
              <div class="col-md-12 spacer">
                <input type="text" class="form-control" id="profile_first_name" name="profile_first_name" value="<?=$userSess['info']['first_name']?>" placeholder="First Name *">
              </div>
              <div class="col-md-12 spacer">
                <input type="text" class="form-control" id="profile_last_name" name="profile_last_name" value="<?=$userSess['info']['last_name']?>" placeholder="Last Name">
              </div>
              <div class="col-md-12 spacer">
                <input type="text" class="form-control" id="profile_address" name="profile_address" value="<?=$userSess['info']['address']?>" placeholder="Address">
              </div>
              <div class="col-md-12 spacer">
                <p>Please upload:</p>
                <p>1 Valid Government Issued Photo ID</p>
                <p>1 Valid Proof of Address within the past 60 days.</p>
              </div>
              <div class="col-md-12">
                <input name="profile_files[]" type="file" class="file" multiple data-show-upload="true" data-show-caption="true">
                <p class="help-block">Try selecting more than one file when browsing for files.</p>
              </div>
              <div class="col-md-12 spacer text-center">
                <button type="submit" class="btn btn-primary btn-lg submit_form_btn">Send</button>
              </div>
            </form>
            </div>


            <!-- authentication -->
            <div class="authentication-container-body profile-sub display-none text-center">
              <div class="authentication-loader">
                <img src="assets/img/tenor.gif">
              </div>
              <div class="authentication-input-body display-none">
                <div class="col-md-4 col-md-offset-4 spacer">
                  <h1>Enter the 6 digit code</h1>
                  <input type="text" class="form-control authentication-code" placeholder="CODE">
                </div>
                <div class="col-md-12 spacer text-center">
                  <button type="button" class="btn btn-primary btn-lg authentication-code-send">Send</button>
                </div>
              </div>
              <div class="authentication-response-body display-none">
                <div class="col-md-4- col-md-offset-0 spacer">
                </div>
                <div class="col-md-12 spacer text-center authentication-resend-button hide-on-profile-init display-none">
                  <button class="btn btn-primary authenticationBtn">Resend</button>
                </div>
                <div class="col-md-12 spacer text-center authentication-retry-button hide-on-profile-init display-none">
                  <button class="btn btn-primary authenticationRetryBtn">Retry</button>
                </div>
              </div>
            </div>


            <!-- change password -->
            <div class="change-password-container-body profile-sub display-none text-center">
              <div class="change-security-loader display-none">
                <img src="assets/img/tenor.gif">
              </div>
              <div class="change-security-container">
                <div class="col-md-12 spacer text-center">
                  <h1>Change Password</h1>
                </div>
                <div class="col-md-12 spacer">
                  <input type="password" class="form-control" id="oldPassword" placeholder="Type old password">
                </div>
                <div class="col-md-12 spacer">
                  <input type="password" class="form-control" id="password1" placeholder="Type new password">
                </div>
                <div class="col-md-12 spacer">
                  <input type="password" class="form-control" id="password2" placeholder="Retype new password">
                </div>
                <div class="col-md-12 spacer text-center">
                  <button class="btn btn-primary btn-lg confirmChangePasswordBtn">Update</button>
                </div>
              </div>
            </div>

            <!-- change secret -->
            <div class="change-secret-container-body profile-sub display-none text-center">
              <div class="change-secret-loader display-none">
                <img src="assets/img/tenor.gif">
              </div>
              <div class="change-secret-container">
                <div class="col-md-12 spacer text-center">
                  <h1>Change Secrect Question</h1>
                </div>
                <div class="col-md-6 spacer">
                  <select id="secretQuestions" class="form-control">
                    <?php
                      $userSecret = $users->getUserSecret($userSess['info']['id']);
                      $secretQuestions = $users->getSecretQuestions();
                      $qselected = '';
                      foreach($secretQuestions as $a){
                        if($a['id'] == $userSecret){
                          $qselected = 'selected';
                        }
                        echo '<option value="'.$a['id'].'" '.$qselected.'>'.$a['question'].'</option>';
                      }
                    ?>
                  </select>
                </div>
                <div class="col-md-6 spacer">
                  <input type="text" class="form-control" id="secretAnswer" placeholder="Type answer">
                </div>
                <div class="col-md-12 spacer text-center">
                  <button class="btn btn-primary btn-lg confirmChangeSecretBtn">Update</button>
                </div>
              </div>
            </div>

            <!-- deposit-withdraw -->
            <div class="deposit-withdraw-container-body profile-sub display-none text-center">
              <div class="deposit-withdraw-loader display-none">
                <img src="assets/img/tenor.gif">
              </div>
              <div class="deposit-withdraw-container">
                <div class="col-md-12 spacer text-center">
                  <h1 class="deposit-withdraw-title"></h1>
                </div>
                <div class="col-md-3">
                  <button class="btn btn-primary btn-lg confirmDepositWithdrawBtn"></button>
                </div>
                <div class="col-md-9">
                  <div class="well well-lg profile-well">
                    <div class="row">
                      <div class="col-md-12">
                        <table class="table">
                          <thead>
                            <tr>
                              <th class="text-center">Date</th>
                              <th class="text-center">Amount</th>
                              <th class="text-center">ID</th>
                              <th class="text-center">Notes</th>
                            </tr>
                          </thead>
                          <tbody class="deposit-withdraw-history">

                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="withdraw-container profile-sub display-none">
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
          <div class="col-md-6 form-inline">
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
          </div>

        </div>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="table-loader text-center">
                <img src="assets/img/tenor.gif">
            </div>
            <table class="table trade-table" style="display: none;">
              <thead>
                <tr>
                  <th scope="col">Entry Price</th>
                  <th scope="col">Position</th>
                  <th scope="col">Time</th>
                  <th scope="col">Leverage</th>
                  <th scope="col">Exit Price</th>
                  <th scope="col">Time</th>
                  <th scope="col">Pair</th>
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
        $userId = <?php echo $userSess['info']['id']; ?>;

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
      var elem = document.querySelector('.js-switch');
      var init = new Switchery(elem);
      init.disable();

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
            $('.profile-container-body').removeClass('display-none');
            $('.profile-sub').addClass('display-none');
            $('.authentication-response-body').addClass('display-none');
            $('.hide-on-profile-init').addClass('display-none');
            $('#profileModal').modal('show');
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
        $tracks['track_0'] = ['amcharts-guide-live-guide', 'black'];
        localStorage.setItem("tracks", JSON.stringify($tracks));
        $tradeIndex = 0;

        // call History



        $.ajax({
            url: 'Controller/history.php?action=load-history',
            type: 'post',
            dataType: 'json',
            success: function (rspdata) {
              init.enable();
              $('.balance-loader').css('display', 'none');
              $('.balance-loader-display').css('display', 'block');
              $('.userFunds').html(rspdata.funds);
              $isLoadedUpdate = false;
              $isLoadedAdd = false;
              $btcUsd = rspdata.currentBtcPrice;
              $currentBtcPrice = rspdata.currentBtcPrice;
              $currentCoinPrice = toFixedNew(rspdata.currentCoinPrice, 6);
              $cur = '<?php echo $currency; ?>';
              $currencyPair = '<?php echo $currencyPair; ?>';
              $userId = <?php echo $userSess['info']['id']; ?>;
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
                    $tickValue = ($cur == 'BTC' ? $currentCoinPrice / $currentBtcPrice : $currentCoinPrice * $rates[$cur]);

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

                              $trade = '<tr class="trade-index-'+$tradeIndex+'" data-type="'+$typeText.toLowerCase()+'" data-index="'+$tradeIndex+'"><td class="'+$tradeText+'">'+$typeText+'</td><td>'+$amount+'</td><td>'+data[i].pair+'</td><td><span class="timer active-timer"></span></td><td></td><td><button class="btn btn-xs '+$tradeClass+' trade-status">LIVE</button></td><td><span class="trade-investment">'+data[i].leverage+'</span></td><td><span class="trade-payout">'+data[i].payout+'</span></td></tr>';
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
                    zoomChart(zoomValue, 1);

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
                          if(elem.checked == true){
                            $maxQuadPrice = parseFloat(250 / $currentBtcPrice * 10000);
                            $('.max-quadpips').val(toFixedNew($maxQuadPrice, 2));
                            $curExcPrice = $('#leverage').val() / 10000;
                          }else{
                            $maxQuadPrice = parseFloat(250 / $currentBtcPrice / 10000);
                            $('.max-quadpips').val(toFixedNew($maxQuadPrice, 2));
                            $curExcPrice = $('#leverage').val() * 10000;
                          }

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

                              $tStatus = $tradeTransaction[$h].status;
                              if(elem.checked == true){
                                $tPayout = $tradeTransaction[$h].payout;
                              }else{
                                $tPayout = $tradeTransaction[$h].payout / 10000;
                              }
                              if($tStatus == 'live'){
                                if($tType == 'buy'){
                                  if($landingAmount <= $lPrice){
                                    $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-danger').addClass('text-success').text($tPayout.toPrecise(6));
                                  }else if($landingAmount == $lPrice){
                                    $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-danger').removeClass('text-success').text(0);
                                  }else{
                                    $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-success').addClass('text-danger').text(0);
                                  }
                                }else{
                                  if($landingAmount >= $lPrice){
                                    $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-danger').addClass('text-success').text($tPayout.toPrecise(6));
                                  }else if($landingAmount == $lPrice){
                                    $('.trade-index-'+$tIndex).find('.trade-payout').removeClass('text-danger').removeClass('text-success').text(0);
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
                  $funds = parseFloat($('.userFunds').text());
                  if($funds > 0){
                    $amount = parseFloat($('#current_price').val());
                    if(elem.checked == true){
                      $leverage = parseFloat($('#leverage').val());
                      $payout = ($leverage * .70) + $leverage;
                      $leverageDisplay = parseFloat($('#leverage').val());
                      $payoutDisplay = ($leverage * .70) + $leverage;
                      $winAmount = $leverage * .70;
                    }else{
                      $leverage = parseFloat($('#leverage').val()) * 10000;
                      $payout = ($leverage * .70) + $leverage;
                      $leverageDisplay = parseFloat($('#leverage').val());
                      $payoutDisplay = ($leverageDisplay * .70) + $leverageDisplay;
                      $winAmount = $leverageDisplay * .70;
                    }



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
                    $trade = '<tr class="trade-index-'+$tradeIndex+'" data-type="buy" data-index="'+$tradeIndex+'"><td class="text-success">Buy</td><td>'+$amount+'</td><td>'+$cryptoCoin+$cur+'</td><td><span class="timer active-timer"></span></td><td class="exit-price"></td><td><button class="btn btn-xs btn-success trade-status">LIVE</button></td><td><span class="trade-investment">'+$leverageDisplay+'</span></td><td><span class="trade-payout">'+$payoutDisplay+'</span></td></tr>';
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
                        remainder: 5
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
                                    leverageDisplay: $leverageDisplay,
                                    payoutDisplay: $payoutDisplay,
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
                  $funds =  parseFloat($('.userFunds').text());
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

                    if(elem.checked == true){
                      $leverage = parseFloat($('#leverage').val());
                      $payout = ($leverage * .70) + $leverage;
                      $leverageDisplay = parseFloat($('#leverage').val());
                      $payoutDisplay = ($leverage * .70) + $leverage;
                      $winAmount = $leverage * .70;
                    }else{
                      $leverage = parseFloat($('#leverage').val()) * 10000;
                      $payout = ($leverage * .70) + $leverage;
                      $leverageDisplay = parseFloat($('#leverage').val());
                      $payoutDisplay = ($leverageDisplay * .70) + $leverageDisplay;
                      $winAmount = $leverageDisplay * .70;
                    }

                    $trade = '<tr class="trade-index-'+$tradeIndex+'" data-type="sell" data-index="'+$tradeIndex+'"><td class="text-danger">Sell</td><td>'+$amount+'</td><td>'+$cryptoCoin+$cur+'</td><td><span class="timer active-timer"></span></td><td class="exit-price"></td><td><button class="btn btn-xs btn-success trade-status">LIVE</button></td><td><span class="trade-investment">'+$leverageDisplay+'</span></td><td><span class="trade-payout">'+$payoutDisplay+'</span></td></tr>';
                    $('.trade-log').prepend($trade);
                    $logTradeData = {
                        expires: parseInt($('.selected-expire').attr('data-value')),
                        type: 'sell',
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
                                    leverageDisplay: $leverageDisplay,
                                    payoutDisplay: $payoutDisplay,
                                    winAmount: $winAmount,
                                    type: 'sell',
                                    status: 'live',
                                    tradeId: data.tradeId,
                                    remainder: 5
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
                      $row = $('.trade-index-'+$index);
                      $minute = $tradeTransaction[$a]['time'];
                      $status = $tradeTransaction[$a]['status'];
                      $leverage = $tradeTransaction[$a]['leverage'];
                      $winAmount = $tradeTransaction[$a]['winAmount'];
                      $tradeId = $tradeTransaction[$a]['tradeId'];
                      $remainder = parseInt($tradeTransaction[$a]['remainder']);
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
                                      $row.find('.trade-status').removeClass('btn-default').addClass('btn-success').text('CLOSE');
                                      $tradeStatus = 'win';
                                      $row.find('.trade-payout').removeClass('text-danger').addClass('text-success').text($payout);
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
                                      $row.find('.trade-payout').removeClass('text-danger').addClass('text-success').text($payout);
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
                $('.trade-table').css('display', 'none');
                $('.table-loader').css('display', 'block');
                $('#tradeHistoryBody').html('<tr><td colspan="7 text-center">Loading data....</td></tr>');
                $.ajax({
                    url: 'Controller/history.php?action=trade-history',
                    type: 'post',
                    dataType: 'json',
                    success: function (data) {
                      $('.table-loader').css('display', 'none');
                      $('.trade-table').css('display', 'table');
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
                          $pair = data[$z].pair;
                          if($status == 'win'){
                            $textClass = 'text-success';
                            $pnl = '+'+$pnl;
                          }else if($status == 'even'){
                            $textClass = 'text-default';
                            $pnl = 0;
                          }
                          else{
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
                                        +'<td>'+ $pair + '</td>'
                                        +'<td class="'+$textClass+'">'+ $pnl + '</td>'
                                      +'</tr>';
                        }

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
              url: 'Controller/history.php?action=trade-history',
              type: 'post',
              dataType: 'json',
              success: function (data) {
                $('.table-loader').css('display', 'none');
                $('.trade-table').css('display', 'table');
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
                    $pair = data[$z].pair;
                    if($status == 'win'){
                      $textClass = 'text-success';
                      $pnl = '+'+$pnl;
                    }else if($status == 'even'){
                      $textClass = 'text-default';
                      $pnl = 0;
                    }
                    else{
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
                                  +'<td>'+ $pair + '</td>'
                                  +'<td class="'+$textClass+'">'+ $pnl + '</td>'
                                +'</tr>';
                  }

                }

                $('#tradeHistoryBody').html($tableBody);

              },
              data: {param: JSON.stringify({userId: $userId, from: $('.date-from').val(), to: $('.date-to').val()})}
          });
        });


        // switch

        elem.onchange = function() {
          $c = parseFloat($('.current_price').text());
          $balance = parseFloat($('.userFunds').text());
          $tradeAmount = parseFloat($('#leverage').val());
          if(elem.checked == true){
            $('.leverage-display').css('display', 'block');
            $b = $balance * 10000;
            $t = $tradeAmount * 10000;
          }else{
            $('.leverage-display').css('display', 'none');
            $b = $balance / 10000;
            $t = $tradeAmount / 10000;
          }
          $('.userFunds').text($b);
          $('#leverage').val($t);


          $('.trade-investment').each(function(index, key){
            $v = parseFloat($(this).text());
            if(elem.checked == true){
              $(this).text($v * 10000);
            }else{
              $(this).text($v / 10000);
            }
          });

          $('.trade-payout').each(function(index, key){
            $v = parseFloat($(this).text());
            if(elem.checked == true){
              $(this).text($v * 10000);
            }else{
              $p = $v / 10000;
              $(this).text($p.toPrecise(6));
            }
          });


        };

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
