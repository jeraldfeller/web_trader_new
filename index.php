<html>
<head>
    <title>NanoPips</title>
   <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <link rel="shortcut icon" href="https://www.nanopips.com/img/favicon.ico" type="image/x-icon" />
        
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" >
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="assets/css/main.css?v=2.0" rel="stylesheet" >
    <link href="custom.css" rel="stylesheet" >

</head>
<body class="login-bg">
    
    <header>
			
			<!-- Top navbar -->
			
<nav class="navbar navbar-default">

	<div class="container">

		<div class="col-xs-4 col-sm-4">
		    <div class="logo pull-left">
			<a href="https://www.nanopips.com/index.php"><img src="https://www.nanopips.com/img/logo1.png" alt="NanoPips" /></a>
			</div> 
		</div>

		<div class="col-xs-8 col-sm-8">
		<!-- <ul class="nav navbar-nav pull-right wow fadeInRight">
	      <li class="active"><a href="index.php">Home</a></li>
	      <li><a href="help.php">Help</a></li>
	      <a href="http://webtrader.nanopips.com/">  <button type="button" class="btn btn-mine">Sign In </button> </a>
	    
	      </ul> -->
		</div>

	</div>
	
</nav>
    </header>
    
    
  <!--<nav class="navbar navbar-default navbar-static-top login-navbar">
    <div class="logo-header-navbar">
      <img src="assets/img/logo.png" style="width: 250px; height: 60px;">
    </div>
  </nav> -->
<center>
    <div class="container container-login">
      <div class="row" style="margin-bottom: 36px;">
        <div class="col-md-12">
          <div class="login-wrapper">
            <div class="login-container">
              <div class="row" style="padding: 25px;">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="logo-header logo">
                    <img src="https://www.nanopips.com/img/logo1.png" class="widchange">
                  </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 48px;">
                  <div class="login-line col-md-5 col-sm-5 col-xs-4"></div>
                  <div class="col-md-2 col-sm-2 col-xs-4 " style=" text-align:center;margin:0 auto; margin-top: -12px;">
                    <span class="login-header-text" style="text-align:center;margin:0 auto;">Login</span>
                  </div>
                  <div class="login-line col-md-5 col-sm-5 col-xs-4"></div>
                </div>
                <div class="logform">
                <div class="col-md-12 ">
                    <label for="lg_username" class="sr-only">Email</label>
                    <input type="text" class="form-control " id="lg_username" name="lg_username" placeholder="Email">
                </div>
                <div class="col-md-12 spacer"  style="margin-bottom: 48px;">
                    <label for="lg_password" class="sr-only">Password</label>
                    <input type="password" class="form-control" id="lg_password" name="lg_password" placeholder="Password">
                </div>
                <div class="col-md-12" style="margin-bottom: 48px;">
                  <button class="btn btn-success btn-lg submit-btn loginbtn"  style='background: #13D384;'>Login <i class="fa fa-chevron-right"></i></button><br />
                  <font color='red' class="error-message"></font>
                </div>
                <div class="col-md-12 ">
                  <a href="request-password-reset.php">Forgot Password<a/>
                  <br>
                  <a href="signup.php">Register<a/>
                </div>
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
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode == '13') {
            $('.loginbtn').trigger('click');
        }
    });
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
                              location.href = 'go2.php';
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
