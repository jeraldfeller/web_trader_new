<?php
include '../includes/regular/require.php';
require '../Model/History.php';
require '../Model/Users.php';
$users = new Users();

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
</head>
<body>
  <nav class="navbar navbar-default navbar-static-top login-navbar">
    <div class="col-md-2 col-sm-3 col-xs-3">
      <div class="logo-header logo-page-header">
        <img src="../assets/img/logo.png" style="width: 112px; height:32px;">
      </div>
    </div>
  </nav>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
        <div class="well well-lg">
          <table class="table table-stripe">
            <thead>
              <tr>
                <th>#</th>
                <th scope="col">Users</th>
              </tr>
            </thead>
            <tbody id="tableUsers">
              <tr>
                <td colspan="2"><div class="loader" style="width:50px; height: 50px;"></div></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
        <div class="well well-lg">
          <table class="table table-stripe text-center">
            <thead>
              <tr>
                <th scope="col" class="text-center">Initial Trade Balance</th>
                <th scope="col" class="text-center">Total Remaining Balance</th>
                <th scope="col" class="text-center">Total Client Losses</th>
              </tr>
            </thead>
            <tbody id="tableTotalTrades" style="font-size: 2em;">
              <tr>
                <td colspan="3"><div class="loader" style="width:50px; height: 50px;"></div></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
        <div class="well well-lg">
          <table class="table table-stripe text-center">
            <thead>
              <tr>
                <th scope="col" class="text-center" colspan="2">Total Client With Possitive Balance</th>
              </tr>
              <tr>
                <th scope="col" class="text-center">Users</th>
                <th scope="col" class="text-center">+ Amount</th>
              </tr>
            </thead>
            <tbody id="tablePositiveBalance">
              <tr>
                <td colspan="2"><div class="loader" style="width:50px; height: 50px;"></div></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 form-inline">
        <h3>MAX Trade Size Total Per Account: <input class="form-control" style="width: 88px;" type="number" id="maxTradeAmount"> <button class="btn btn-primary" id="maxTradeBtn">OK</button>
      </div>
    </div>
  </div>
</div>

<script src="../assets/js/util.js"></script>
<script type="text/javascript" language="JavaScript">
    $(document).ready(function(){

      $('#maxTradeBtn').click(function(){
        $.ajax({
          url: '../Controller/options.php?action=update',
          type: 'post',
          dataType: 'json',
          data: {param: {name: 'trade_limit', value: $('#maxTradeAmount').val()}}
        }).done((rsp) => {
          alert('MAX Trade Size successfully updated');
        });
      });

      $.ajax({
          url: '../Controller/user.php?action=get-users',
          type: 'get',
          dataType: 'json'
      })
      .done((rsp) => {
        $tbl = '';
        console.log(rsp);
        $.each(rsp, (index, key) => {
          $tbl += '<tr>'
                  +'<td>'+(index+1)+'</td>'
                  +'<td><a href="user.php?id='+key.id+'" data-id="'+key.id+'" class="user-action">'+key.email+'</a></td>'
                  +'</tr>';
        });
        $('#tableUsers').html($tbl);

        $.ajax({
          url: '../Controller/user.php?action=get-trade-stats',
          type: 'get',
          dataType: 'json'
        }).done((rsp) =>{
            console.log(rsp);
            $tbl = '';
            $tbl += '<tr>'
                      +'<td>$'+toFixedNew(rsp.totalBalances, 2)+'</td>'
                      +'<td>$'+toFixedNew(rsp.totalRemainingBalances, 2)+'</td>'
                      +'<td>$'+toFixedNew(rsp.totalClientLosses, 2)+'</td>'
                      +'</tr>';

            $('#tableTotalTrades').html($tbl);


            $.ajax({
              url: '../Controller/user.php?action=get-positive-balance',
              type: 'get',
              dataType: 'json'
            }).done((rsp) => {
              console.log(rsp);
              $tbl = '';
              $.each(rsp, (index, key) => {
                $tbl += '<tr>'
                          +'<td>'+key.email+'</td>'
                          +'<td>+$'+toFixedNew(key.positiveBalance, 2)+'</td>'
                          +'</tr>';
              });

              $('#tablePositiveBalance').html($tbl);


              $.ajax({
                url: '../Controller/options.php?action=get&name=trade_limit',
                type: 'get',
                dataType: 'json'
              }).done((rsp) => {
                console.log(rsp);
                $('#maxTradeAmount').val(rsp);
              });


            })
        })
      });
    });
</script>

</body>
</html>
