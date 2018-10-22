<?php
include '../includes/regular/require.php';
require '../Model/History.php';
require '../Model/Users.php';
$users = new Users();
$dateFrom = date('m/d/Y', strtotime('-7 days'));
$dateTo = date('m/d/Y');
?>
<!DOCTYPE html>
<html>
<head> 
    <title>NanoPips</title>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" media="all" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>

    <link rel="stylesheet" href="../assets/css/simple-line-icons.min.css" />
    <link rel="stylesheet" href="../assets/css/main.css?v=2.3" />
    <link rel="stylesheet" href="../assets/css/admin.css?v=2.3" />
    <link rel="stylesheet" href="../assets/css/switchery.min.css" />
</head>
<body>
  <nav class="navbar navbar-default navbar-static-top login-navbar">
    <div class="col-md-2 col-sm-3 col-xs-3">
      <div class="logo-header logo-page-header">
        <img src="../assets/img/logo.png" style="width: 112px; height:32px;">
      </div>
    </div>
  </nav>
<div class="container-fluid" style="margin-bottom: 48px;">
  <div class="row">
    <div class="col-md-12">
      <div class="loader loader-u"></div>
    </div>
    <div class="col-md-12 user-container display-none">
      <div class="col-md-12 text-center">
        <span class="label-user-title user-header"></span>
      </div>
      <div class="col-lg-6 col-md-12 col-sm-12 spacer-2x">
        <div class="well well-lg">
          <table class="table table-stripe">
            <thead>
              <tr>
                <th>Deposit</th>
                <th scope="col">Date Created</th>
              </tr>
            </thead>
            <tbody id="tableDeposit">
              <tr>
                <td colspan="2"><div class="loader loader-u" style="width:50px; height: 50px;"></div></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-lg-6 col-md-12 col-sm-12 spacer-2x">
        <div class="well well-lg">
          <table class="table table-stripe">
            <thead>
              <tr>
                <th>Withdraw</th>
                <th scope="col">Date Created</th>
              </tr>
            </thead>
            <tbody id="tableWithdraw">
              <tr>
                <td colspan="2"><div class="loader loader-u" style="width:50px; height: 50px;"></div></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-lg-12 col-md-12 col-sm-12 spacer-2x">
        <div class="well well-lg">
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
          </div>
          <table class="table table-stripe spacer-2x">
            <thead>
              <tr>
                <th scope="col">Entry Price</th>
                <th scope="col">Position</th>
                <th scope="col">Time</th>
                <th scope="col">Trade Amount</th>
                <th scope="col">Exit Price</th>
                <th scope="col">Time</th>
                <th scope="col">Pair</th>
                <th scope="col">PnL</th>
              </tr>
            </thead>
            <tbody id="tableTradeHistory">
              <tr>
                <td colspan="8"><div class="loader loader-t" style="width:50px; height: 50px;"></div></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-md-12">
        <span class="label-title"> Verified: </span> <input type="checkbox" class="js-switch" id="verifiedSwitch" />
      </div>
      <div class="col-md-12">
        <span class="label-title"> Total Balance: </span><span class="label-title total-balance text-bold"></span>
      </div>
      <div class="col-md-12">
        <span class="label-title"> Total Balance Current: </span><span class="label-title total-balance-current text-bold"></span>
      </div>
      <div class="col-md-12">
        <span class="label-title"> Total Trade Lost: </span><span class="label-title total-trade-lost text-bold"></span>
      </div>
      <!--
      <div class="col-md-12">
        <span class="label-title"> Total % from Trades: </span><span class="label-title total-percent-trades text-bold"></span>
      </div>
    -->
      <div class="col-lg-1 col-md-2 col-sm-2 spacer">
        <button class="btn btn-primary user-action" data-action="balance">Add Balance</balance>
      </div>
      <div class="col-lg-1 col-md-2 col-sm-2 spacer">
        <button class="btn btn-primary user-action" data-action="withdraw">Withdraw</balance>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12 text-center">
            <h1 class="actionTitle"></h1>
          </div>
          <div class="col-md-12 text-center" style="margin-top: 24px;">
            <input type="text" class="form-control action-value" style="width: 60%; display: inline; text-align: center;">
          </div>
          <div class="col-md-12 text-center" style="margin-top: 24px;">
            <button class="btn btn-primary btn-lg actionSaveBtn"></button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="../assets/js/switchery.min.js"></script>
<script src="../assets/js/util.js"></script>
<script type="text/javascript" language="JavaScript">
    $(document).ready(function(){

      $('.date-from').datepicker({
          format: 'mm/dd/yyyy',
      });
      $('.date-to').datepicker({
          format: 'mm/dd/yyyy'
      });

      var btnSwitch = document.querySelector('.js-switch');
      var verifiedSwitch = new Switchery(btnSwitch);
      $id = <?=$_GET['id']?>;



      $('.date-history').change(function(){
        getUserTradeHistory($id);
      });

      btnSwitch.onchange = function() {
        if(btnSwitch.checked == true){
          verifyUser('yes', $id);
        }else{
          verifyUser('no', $id);
        }
      }

        getUserById($id, btnSwitch);

      $('.user-action').click(function(){
        $action = $(this).attr('data-action');
        if($action == 'balance'){
          $('.actionTitle').html('Add Balance');
          $('.actionSaveBtn').attr('data-action', 'Deposit').html('Deposit');
        }else{
          $('.actionTitle').html('Withdraw Amount');
          $('.actionSaveBtn').attr('data-action', 'Withdraw').html('Withdraw');;
        }
        $('#actionModal').modal('show');
      });

      $('.actionSaveBtn').click(function(){
        $action = $(this).attr('data-action');
        $btn = $(this);
        $amount = $('.action-value').val();
        $btn.html('<i class="fa fa-spinner fa-spin"></i>').attr("disabled", "disabled");

        $.ajax({
            url: '../Controller/user.php?action=update-balance',
            type: 'post',
            dataType: 'json',
            success: function (rsp) {
              alert('Balance sucessfully updated');
              $btn.html($action).attr("disabled", false);
              $('.action-value').val('');
              getUserById($id, btnSwitch);
            },
            error: function (e){
              alert('Ops! Something went wrong, please try again');
              $btn.html($action).attr("disabled", false);
            },
            data: {param: {id: $id, action: $action, amount: $amount}}
        });

      });

    });

    function getUserById($id, btnSwitch){
      $('#tableDeposit').html('<tr><td colspan="2"><div class="loader" style="width:50px; height: 50px;"></div></td></tr>');
      $('#tableWithdraw').html('<tr><td colspan="2"><div class="loader" style="width:50px; height: 50px;"></div></td></tr>');
      $.ajax({
          url: '../Controller/user.php?action=get-user-by-id',
          type: 'post',
          dataType: 'json',
          success: function (rsp) {
            $('.loader-u').addClass('display-none');
            $('.user-container').removeClass('display-none');

            if(rsp.verified == 'yes'){
              if(btnSwitch.checked == false){
                $('#verifiedSwitch').trigger('click');
              }
            }

            $rspTotalBalance = (rsp.totalBalance != null ? rsp.totalBalance : 0);
            $rspDollarAmount = (rsp.dollar_amount != null ? rsp.dollar_amount : 0);
            $rspTotalLost = (rsp.trades.totalLost != null ? rsp.trades.totalLost : 0);
            $rspTotalPercentAmount = (rsp.trades.totalPercentAmount != null ? rsp.trades.totalPercentAmount : 0);
            $('.user-header').html(rsp.email);
            $('.total-balance').html('$'+toFixedNew($rspTotalBalance, 2));
            $('.total-balance-current').html('$'+toFixedNew($rspDollarAmount, 2));
            $('.total-trade-lost').html('$'+toFixedNew($rspTotalLost, 2));
            $('.total-percent-trades').html('$'+toFixedNew($rspTotalPercentAmount, 2));

            $depositTbl = '';
            $withdrawTbl = '';
            $.each(rsp.trades.balanceHistory, (index, key) => {
              $depositTbl += '<tr>'
                              +'<td>$'+key.amount+'</td>'
                              +'<td>$'+key.exec_time+'</td>'
                              +'</tr>';
            });
            $.each(rsp.trades.withdrawHistory, (index, key) => {
              $withdrawTbl += '<tr>'
                              +'<td>$'+key.amount+'</td>'
                              +'<td>$'+key.exec_time+'</td>'
                              +'</tr>';
            });

            $('#tableDeposit').html($depositTbl);
            $('#tableWithdraw').html($withdrawTbl);

            console.log(rsp);

            getUserTradeHistory($id);
          },
          data: {param: {id: $id}}
      });
    }
    function verifyUser($bol, $id){
      $.ajax({
          url: '../Controller/user.php?action=verify-documents',
          type: 'post',
          dataType: 'json',
          success: function (rsp) {

          },
          data: {param: {id: $id, verified: $bol}}
      });
    }
    function getUserTradeHistory($userId){
      $('#tableTradeHistory').html('<tr><td colspan="8"><div class="loader loader-t" style="width:50px; height: 50px;"></div></td></tr>');
      $.ajax({
          url: '../Controller/history2.php?action=trade-history',
          type: 'post',
          dataType: 'json',
          success: function (data) {
            $('.loader-t').remove();
            $tableBody = '';
              console.log(data);
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

            $('#tableTradeHistory').html($tableBody);

          },
          data: {param: JSON.stringify({userId: $userId, from: $('.date-from').val(), to: $('.date-to').val()})}
      });
    }
</script>

</body>
</html>
