<?php
include 'includes/regular/require.php';
require 'Model/History.php';
require 'Model/Users.php';
$users = new Users();
$user = $users->getUserDataById($userSess['info']['id']);
$history = new History();
$urlParam = $_GET;

$dateFrom = date('m/d/Y', strtotime('-7 days'));
$dateTo = date('m/d/Y');

$userSecret = $users->getUserSecret($userSess['info']['id']);
$secretQuestions = $users->getSecretQuestions();
$secretOptions = array();
$i = 1;
foreach($userSecret as $row){
  $userSecretId = $row['secret_question_id'];
  $answer = $row['answer'];
  $qselected = '';
  $option = '';
  foreach($secretQuestions as $a){
      if($a['id'] == $userSecretId){
        $qselected = 'selected';
      }
      $option .= '<option value="'.$a['id'].'" '.$qselected.'>'.$a['question'].'</option>';
  }
  $select = '<div class="col-md-6 spacer"><select class="secretQuestions form-control q'.$i.'"><option value=""></option>'.$option.'</select></div><div class="col-md-6 spacer"><input type="text" class="form-control secretAnswer a'.$i.'" value="'.$answer.'" placeholder="Type answer"></div>';
  $secretOptions[] = $select;
  $i++;
}
if(count($secretOptions) < 3){
  for($x = 0; count($secretOptions) < 3; $x++){
    $option = '';
      foreach($secretQuestions as $a){
        $option .= '<option value="'.$a['id'].'" >'.$a['question'].'</option>';
    }
    $select = '<div class="col-md-6 spacer"><select class="secretQuestions form-control q'.$i.'"><option value="">-- select question --</option>'.$option.'</select></div><div class="col-md-6 spacer"><input type="text" class="form-control secretAnswer a'.$i.'" placeholder="Type answer"></div>';
    $secretOptions[] = $select;
    $i++;
  }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nanopips</title>
        <link rel="shortcut icon" href="https://www.nanopips.com/img/favicon.ico" type="image/x-icon" />
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" media="all" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>

    <link rel="stylesheet" href="assets/css/simple-line-icons.min.css" />
    <link rel="stylesheet" href="assets/css/main.css?v=2.5" />

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
  <nav class="navbar navbar-default navbar-static-top login-navbar">
    <div class="col-md-2 col-sm-3 col-xs-3">
      <div class="logo-header logo-page-header">
        <img src="assets/img/logo.png" style="width: 112px; height:32px;">
      </div>
    </div>
  </nav>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-8 col-sm-12 profile-settings-container">
      <div class="profile-container-body">
        <div class="col-lg-6 col-md-12 col-sm-12">
          <div class="well well-lg profile-well">
            <div class="row">
              <div class="col-md-3">
                <i class="icon-user icon-big"></i>
              </div>
              <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
                <div>
                  <span class="heading">Account Verification</span>
                </div>
                <div class="spacer">
                  <span class="text-bold"><?=$userSess['info']['email']?></span><br>
                  <span>Last login: <?=$userSess['log_history'][count($userSess['log_history'])-1]['date_time']?></span>
                  <br>
                  <span>IP address: <?=$userSess['log_history'][count($userSess['log_history'])-1]['ip_address']?></span>
                </div>
                <div class="spacer submit-information-message">
                  <?php
                    if($user['verified'] == 'yes'){
                      echo '<h4 class="text-success verified">VERIFIED</h4>
                      <small>To update your verification documents please contact admin@nanopips.com</small>';
                    }else if($user['verified'] == 'pending'){
                      echo '<small>Please allow up to 48 hours for your documents to be verified.</small>';
                    }else{
                      echo '<button class="btn outline-primary btn-lg full-width submitInfoBtn">Submit Information</button>';
                    }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
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
                <div class="spacer verification-code-status">
                  <?php
                    if($user['verified_code'] == 'yes'){
                      echo '<h4 class="text-success verified">VERIFIED</h4>
                      <small>To update your verification email please contact admin@nanopips.com</small>';
                    }else{
                      echo '<button class="btn outline-primary btn-lg full-width authenticationBtn">Enable</button>';
                    }
                  ?>

                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
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
                  <button class="btn outline-primary btn-lg full-width changeSecretQuestionBtn" style="font-size: 17px;">Change Secret Questions</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
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
                  <button class="btn outline-primary btn-lg full-width depositWithdrawBtn" data-action="deposit">Deposit Funds</button>
                </div>
                <div class="spacer">
                  <button class="btn outline-primary btn-lg full-width depositWithdrawBtn" data-action="withdraw">Withdraw Funds</button>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="col-md-12 back-button-container">
      <i class="fa fa-arrow-circle-left text-primary back-button display-none"></i>
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
      <div class="col-md-12  col-sm-12 col-xs-12 spacer">
        <p>Please upload: </p>
      </div>
      <div class="col-md-6  col-sm-6 col-xs-12 spacer">
        1 Valid Government Issued Photo ID.
      </div>
      <div class="col-md-6 col-sm-6 spacer">
        <input name="profile_files1" type="file" class="file pull-left">
      </div>
      <div class="col-md-6 col-sm-6 spacer">
        1 Valid Proof of Address within the past 60 days.
      </div>
      <div class="col-md-6  col-sm-6 spacer">
        <input name="profile_files2" type="file" class="file pull-left">
      </div>
      <div class="col-md-12 spacer text-center">
        <button type="submit" class="btn btn-primary btn-lg submit_form_btn">Send</button>
      </div>
    </form>
    </div>


    <!-- authentication -->
    <div class="authentication-container-body profile-sub display-none text-center">
      <div class="authentication-loader">
        <div class="loader" style="width:50px; height: 50px;"></div>
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
        <div class="loader" style="width:50px; height: 50px;"></div>
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
        <div class="loader" style="width:50px; height: 50px;"></div>
      </div>
      <div class="change-secret-container">
        <div class="col-md-12 spacer text-center">
          <h1>Change Secrect Question</h1>
        </div>
        <?php
          for($x = 0; $x < count($secretOptions); $x++){
            echo $secretOptions[$x];
          }
        ?>
        <div class="col-md-12 spacer text-center">
          <button class="btn btn-primary btn-lg confirmChangeSecretBtn">Update</button>
        </div>
      </div>
    </div>

    <!-- deposit-withdraw -->
    <div class="deposit-withdraw-container-body profile-sub display-none text-center">
      <div class="deposit-withdraw-loader display-none">
        <div class="loader" style="width:50px; height: 50px;"></div>
      </div>
      <div class="deposit-withdraw-container">
        <div class="col-md-12" style="margin-left: 62px;">
          <button class="btn btn-primary btn-lg confirmDepositWithdrawBtn" style="float: left;
    width: 200px;
    height: 85px; font-size: 1.5em; font-weight: bold;"></button>
        </div>
        <div class="col-md-12" style="margin-left: 62px; margin-top: 12px">
          <div class="withdrawBalanceMessage" style="text-align:left; font-size: 1.2em; font-weight: bold;"></div>
        </div>
        <div class="col-md-12 spacer text-center">
          <h1 class="deposit-withdraw-title"></h1>
        </div>
        <div class="col-md-12">
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

    <div class="withdraw-container profile-sub">
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

  <div class="col-md-4 col-sm-12 last-login-container">
    <div class="col-md-12 col-sm-12">
      <div class="well well-lg profile-well">
        <div class="row text-center">
          <span class="heading">Last Login</span>
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
  </div>
</div>


<script type="text/javascript" language="JavaScript">
    $(document).ready(function(){
      $userId = <?php echo $userSess['info']['id']; ?>;

      $('.back-button').click(function(){
        $(this).addClass('display-none');
        $('.profile-sub').removeClass('show-margin');
        setTimeout(function(){
          $('.profile-settings-container').removeClass('col-md-12').addClass('col-md-7');
          $('.last-login-container').removeClass('display-none');
          $('.profile-input-container-body').addClass('display-none');
          $('.authentication-container-body').addClass('display-none');
          $('.change-password-container-body').addClass('display-none');
          $('.change-secret-container-body').addClass('display-none');
          $('.deposit-withdraw-container-body').addClass('display-none');
          $('.profile-container-body').removeClass('display-none');
        }, 200);
      });
      $('.depositWithdrawBtn').click(function(){
        $('.profile-settings-container').removeClass('col-md-7').addClass('col-md-12');
        $('.last-login-container').addClass('display-none');
        $('.back-button').removeClass('display-none');
        $action = $(this).attr('data-action');
        $('.profile-container-body').addClass('display-none');
        $('.deposit-withdraw-container-body').removeClass('display-none');
        setTimeout(function(){
          $('.deposit-withdraw-container-body').addClass('show-margin');
        }, 100);
        if($action == 'deposit'){
          $('.deposit-withdraw-title').text('Deposit History');
          
          $('.confirmDepositWithdrawBtn').attr('href', 'https://www.nanopips.com/funding.php').text('Deposit');
           $(".confirmDepositWithdrawBtn").click(function(){
       location.href = 'https://www.nanopips.com/funding.php';
    });
          
          $( '.confirmDepositWithdrawBtn' ).after( '<p align="left"style="padding-top:40px;"> &nbsp;&nbsp;Follow these steps to deposite into your account</p>' );
            $('.withdrawBalanceMessage').html('');
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
          $('.confirmDepositWithdrawBtn').attr('data-action', 'withdraw').text('Withdraw Funds');
          $('.withdrawBalanceMessage').html('To Process your withdrawal request download and fill out the wire request form.<br>Once complete email it back to clientservice@nanopips.com');
          $('.confirmDepositWithdrawBtn').click(function(){
            location.href = 'download.php?form=withdraw';
          });
        }

     //    $('.confirmDepositWithdrawBtn').click(function(){
        //     $action = $(this).attr('data-action');
           //  if($action == 'deposit'){
         //      location.href = 'buy-funds.php';
           //  }else{
             //  $('.profile-container-body').addClass('display-none');
              // $('.withdraw-container').removeClass('display-none');
     //        }
        // });
      })

      $('.changeSecretQuestionBtn').click(function(){
        $('.profile-settings-container').removeClass('col-md-7').addClass('col-md-12');
        $('.last-login-container').addClass('display-none');
        $('.back-button').removeClass('display-none');
        $('.profile-container-body').addClass('display-none');
        $('.change-secret-container-body').removeClass('display-none');
        setTimeout(function(){
          $('.change-secret-container-body').addClass('show-margin');
        }, 100);
        $('.confirmChangeSecretBtn').unbind().click(function(){
          $('.change-secret-loader').removeClass('display-none');
          $secretQA = [];
          if($('.q1').val() != '' && $('.a1').val() != ''){
            $secretQA.push([$('.q1').val(), $('.a1').val()]);
            if($('.q2').val() != '' && $('.a2').val() != ''){
              $secretQA.push([$('.q2').val(), $('.a2').val()]);
            }
            if($('.q3').val() != '' && $('.a3').val() != ''){
              $secretQA.push([$('.q3').val(), $('.a3').val()]);
            }
            $.ajax({
                url: 'Controller/user.php?action=change-secret',
                type: 'post',
                dataType: 'json',
                success: function (rspdata) {
                    $('.change-secret-loader').addClass('display-none');
                    alert('Secret question successfully updated.');
                    $('.back-button').trigger('click');
                },
                data: {
                          param: {
                            userId: $userId,
                            qa: $secretQA
                        }
                      }
            });
          }else{
            alert('Please input secret answer to make an update');
          }
        });
      });

      $('.changePasswordBtn').click(function(){
        $('.profile-settings-container').removeClass('col-md-7').addClass('col-md-12');
        $('.last-login-container').addClass('display-none');
        $('.back-button').removeClass('display-none');
        $('.profile-container-body').addClass('display-none');
        $('.change-password-container-body').removeClass('display-none');
        setTimeout(function(){
          $('.change-password-container-body').addClass('show-margin');
        }, 100);
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

      $('.authenticationBtn').unbind().click(function(){
        $('.profile-settings-container').removeClass('col-md-7').addClass('col-md-12');
        $('.last-login-container').addClass('display-none');
        $('.back-button').removeClass('display-none');
        $('.profile-container-body').addClass('display-none');
        $('.authentication-container-body').removeClass('display-none');
        setTimeout(function(){
          $('.authentication-container-body').addClass('show-margin');
          $('.authentication-resend-button').addClass('display-none');
          $('.authentication-response-body').addClass('display-none');
        }, 100);
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
                        $('.verification-code-status').html('<h4 class="text-success verified">VERIFIED</h4>'+
                        '<small>To update your verification email please contact admin@nanopips.com</small>');
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
                  alert('Information successfully submitted');
                  $('.back-button').trigger('click');
                  $('.submit-info-message').html('<small>Please allow up to 48 hours for your documents to be verified</small>');
                  $('.submit-information-message').html('<small>Please allow up to 48 hours for your documents to be verified.</small>');
                    $('.back-button').trigger('click');
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
        $('.profile-settings-container').removeClass('col-md-7').addClass('col-md-12');
        $('.last-login-container').addClass('display-none');
        $('.back-button').removeClass('display-none');
        $('#profile_form').removeClass('display-none');
        $('.profile_form_message').addClass('display-none');
        $('.profile-container-body').addClass('display-none');
        $('.profile-input-container-body').removeClass('display-none');
        setTimeout(function(){
          $('.profile-input-container-body').addClass('show-margin');
        }, 100);

      });

      $('.date-from').datepicker({
          format: 'mm/dd/yyyy',
      });
      $('.date-to').datepicker({
          format: 'mm/dd/yyyy'
      });

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
            $('.profile-settings-container').removeClass('col-md-7').addClass('col-md-12');
            $('.last-login-container').addClass('display-none');
            $('.profile-container-body').removeClass('display-none');
            $('.profile-sub').addClass('display-none');
            $('.authentication-response-body').addClass('display-none');
            $('.hide-on-profile-init').addClass('display-none');
            $('#profileModal').modal('show');
        });

      });
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
