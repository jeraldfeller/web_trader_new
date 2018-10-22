<html>
<head>
    <title>Zolotrader Login</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" >
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="assets/css/main.css?v=1.5" rel="stylesheet" >

</head>
<body class="login-bg">
<center>
    <div class="container">
      <div class="row" style="margin-bottom: 36px;">
        <div class="col-md-12">
          <div class="login-wrapper">
            <div class="login-container">
              <div class="row" style="padding: 25px;">
                <div class="col-md-12">
                  <div class="logo-header">
                    <img src="assets/img/logo.png" style="width: 224px; height: 64px;">
                  </div>
                </div>
                <div class="col-md-12" style="margin-bottom: 36px;">
                  <div class="login-line col-md-5 col-sm-5 col-xs-5"></div>
                  <div class="col-md-2 col-sm-2 col-xs-2" style="margin-top: -12px;">
                    <span class="login-header-text">Login</span>
                  </div>
                  <div class="login-line col-md-5 col-sm-5 col-xs-5"></div>
                </div>
                <div class="col-md-12">
                    <label for="lg_username" class="sr-only">Username</label>
                    <input type="text" style="width: 80%;" class="form-control" id="lg_username" name="lg_username" placeholder="Username" size='25'>
                </div>
                <div class="col-md-12 spacer"  style="margin-bottom: 36px;">
                    <label for="lg_password" class="sr-only">Password</label>
                    <input type="password" style="width: 80%;" class="form-control" id="lg_password" name="lg_password" placeholder="Password" size='25'>
                </div>
                <div class="col-md-12" style="margin-bottom: 36px;">
                  <button class="btn btn-success btn-lg submit-btn"  style='background: #13D384;'>Login <i class="fa fa-chevron-right"></i></button><br />
                  <font color='red' class="error-message"></font>
                </div>
                <div class="col-md-12">
                  <a href="#">Forgot Password<a/>
                  <br>
                  <a href="#">Register<a/>
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
    $(document).ready(function(){
        $('.submit-btn').on('click', function(){
            $email = $('#lg_username').val();
            $password = $('#lg_password').val();
            if($email != '' && $password != ''){
                $data = {
                    email: $email,
                    password: $password,
                    dateNow: Date.now()
                };
                $.ajax({
                    url: 'Controller/user.php?action=login',
                    type: 'post',
                    dataType: 'json',
                    success: function (data) {
                        if(data.success == true){
                            $('.error-message').html('');
                            if(data.funds == 0){
                              location.href = 'buy-funds.php';
                            }else{
                              location.href = 'go.php';
                            }


                        }else{

                            $('.error-message').text(data.response.message);

                        }
                    },
                    data: {param: JSON.stringify($data)}
                });
            }else{
                $('.error-message').text('Please input email and password');
            }
        });
    });
</script>
</body>
</html>
