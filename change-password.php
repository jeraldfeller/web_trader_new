<html>
<head>
    <title>Zolotrader Password Reset Request</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" >
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="assets/css/main.css?v=2.0" rel="stylesheet" >

</head>
<body class="login-bg">
  <nav class="navbar navbar-default navbar-static-top login-navbar">
    <div class="logo-header-navbar">
      <img src="assets/img/logo.png" style="width: 168px; height: 48px;">
    </div>
  </nav>
<center>
    <div class="container container-login">
      <div class="row" style="margin-bottom: 36px;">
        <div class="col-md-12">
          <div class="login-wrapper">
            <div class="login-container">
              <div class="row" style="padding: 25px;">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="logo-header">
                    <img src="assets/img/logo.png" style="width: 156px; height: 42px;">
                  </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 48px;">
                  <div class="login-line col-md-2 col-sm-2 col-xs-2"></div>
                  <div class="col-md-8 col-sm-8 col-xs-8" style="margin-top: -12px;">
                    <span class="login-header-text">Password Change Request</span>
                  </div>
                  <div class="login-line col-md-2 col-sm-2 col-xs-2"></div>
                </div>
                <div class="success-container">
                  <?php
                    if(!isset($_GET['token'])){
                  ?>
                  <h2>Invalid token or token not found, please check you email to get the valid token.</h2>
                <?php } else{ ?>
                  <div class="col-md-12" style="margin-bottom: 24px;">
                      <label for="password1" class="sr-only">Type new password</label>
                      <input type="password" style="width: 50%;" class="form-control login-input" id="password1" name="password1" placeholder="Type new password" size='25'>
                  </div>
                  <div class="col-md-12" style="margin-bottom: 48px;">
                      <label for="password2" class="sr-only">Re-enter password</label>
                      <input type="password" style="width: 50%;" class="form-control login-input" id="password2" name="password2" placeholder="Re-enter password" size='25'>
                  </div>
                  <div class="col-md-12" style="margin-bottom: 48px;">
                    <button class="btn btn-success btn-lg submit-btn login-btn"  style='background: #13D384;'>Change Password <i class="fa fa-chevron-right"></i></button><br />
                    <font color='red' class="error-message"></font>
                  </div>
              <?php } ?>

                </div>
                <div class="col-md-12 login-links">
                  <a href="signup.php">Register<a/>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
        <div class="row">
          <div class="col-md-12">
            <span class="footer">
              2018 NanoPips.com | All Rights Reserved
            </span>
          </div>
        </div>
</center>
<script>
    $(document).keypress(function(e) {
      $token = "<?=$_GET['token']?>";
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode == '13') {
            $('.login-btn').trigger('click');
        }
    });
    $(document).ready(function(){
        $('.submit-btn').on('click', function(){
            $password1 = $('#password1').val();
            $password2 = $('#password2').val();
            if($password1 != ''){
              if($password1 == $password2){
                $data = {
                    token: $token,
                    password: $password1
                };
                $.ajax({
                    url: 'Controller/user.php?action=change-password',
                    type: 'post',
                    dataType: 'json',
                    success: function (data) {
                        if(data.success == true){
                            $('.success-container').html('<h2>Password changed successfully, <a href="index.php">login</a> with your new password.</h2>');
                        }else{
                            $('.error-message').text(data.response.message);
                        }
                    },
                    data: {param: JSON.stringify($data)}
                });
              }else{
                $('.error-message').text('Password does not match.');
              }
            }else{
                $('.error-message').text('Please input password.');
            }
        });
    });
</script>
</body>
</html>
