<?php
$template = '<html>
<head>
  <style>
  p {
    font-size: 1.3em;
  }
  * {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}
div {
  display: block;
}
a {
    color: #337ab7;
    text-decoration: none;
}
body {
  font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
  font-size: 14px;
  line-height: 1.42857143;
  color: #333 !important;
  background-color: #fff;
}
.main-container{
  margin-left: 0;
  margin-bottom: 12px;
}
.footer{
    max-width: 720px;
}
.login-container{
  display: flex;
  flex-direction: column;
}
@media (min-width: 992px){
.col-md-12 {
  width: 100%;
}
}
@media (min-width: 992px){
.col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9 {
  float: left;
}
}
.col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
  position: relative;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;
}

* {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}
user agent stylesheet
div {
  display: block;
}
body {
  font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
  font-size: 14px;
  line-height: 1.42857143;
  color: #333;
  background-color: #fff;
}
html {
  font-size: 10px;
  -webkit-tap-highlight-color: rgba(0,0,0,0);
}
  .col-md-12 {
      width: 100%;
      float: left;
      position: relative;
      min-height: 1px;
      padding-right: 15px;
      padding-left: 15px;
  }

  .login-wrapper {
  border: 10px solid rgba(0,162,0, 0.5);
  margin-top: 15%;
}
.login-container {
  background: #fff;
}
.row {
  margin-right: -15px;
  margin-left: -15px;
}

.logo-header {
  float: left;
  margin: 24px;
}
.login-line {
    border-top: 3px solid #BBBBBB;
    border-radius: 4px;
}
.col-md-4 {
    width: 33.33333333%;
}
.login-header-text {
    font-size: 1.5em;
    color: #BBBBBB;
}

.text-center{
  text-align: center;
}
.login-wrapper{
  max-width: 720px;

}
.login-btn {
    width: auto;
    height: auto;
    font-size: auto;
    font-weight: none;
}
.login-header-text{
  line-height: 2 !important;
  font-size: 1.4em !important;
}
.btn-group-lg>.btn, .btn-lg {
    padding: 10px 16px;
    font-size: 18px;
    line-height: 1.3333333;
    border-radius: 6px;
}
.btn-success {
    color: #fff;
    background-color: #5cb85c;
    border-color: #4cae4c;
}
.btn {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
}
@media screen and (max-width: 1330px){
.login-btn {
    /* width: 50%; */

    font-size: 1.2em;
    /* font-weight: bold; */
}
}

.login-header-container{
  margin-top: -18px;
  text-align: center !important;
  border: 2px solid #e3e3e3;
}
.btn-text{
  color: #fff;
}


  </style>
</head>
<body>
<div class="col-md-12 main-container">
        <div class="login-wrapper" style="margin-top: 5% !important;">
          <div class="login-container">
            <div class="row" style="padding: 25px;">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="logo-header">
                  <img src="http://webtrader.nanopips.com/assets/img/logo.png" style="width: 156px; height: 42px;">
                </div>
              </div>
              <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 48px;">

                <div class="col-md-12 col-sm-12 col-xs-12 login-header-container" style="">
                  <span class="login-header-text"><!--$title-->Email Receieved</span>
                </div>

              </div>
              <div class="col-md-12">
                  <!-- $bodyMessage -->
                  <p>Dear Client,</p>
                  <br>
                  <p class="text-center">This is email is to inform you that we have received your inquiry and will response within one business day.</p>
              </div>
              <div class="col-md-12 spacer text-center">
                  <!-- $button -->
                  <a href="http://webtrader.nanopips.com/" class="btn btn-success btn-lg btn-text login-btn"  style="background: #13D384;">Login Here</a>
              </div>
              <div class="col-md-12 spacer text-center">
                  <!-- $footer -->
                  <p class="text-center">In the meantime, you can refer to our  <a href="http://webtrader.nanopips.com/faq.html">FAQ</a> Section for further assistance.</p>
              </div>
              <div class="col-md-12 text-center footer">
                <a href="#">Forgot Password<a/>
                <br>
                <a href="http://webtrader.nanopips.com/signup.php">Register<a/>
              </div>
              </div>

            </div>
          </div>
          <div class="col-md-12 text-center footer">
            <small class="">2018 NanoPips.com | All Rights Reserved</small>
          </div>
        </div>


</body>
</html>
';
?>
<?php
require 'Model/Init.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$email = new PHPMailer();
$email->isSMTP(true);
$email->SMTPDebug = 2;
$email->SMTPAuth = true;
$email->SMTPSecure = 'tls';
$email->Host = "smtp.gmail.com";
$email->Port = 587;
$email->Username = "clientservice@nanopips.com";
$email->Password = "Italian$123";
$email->From = "clientservice@nanopips.com";
$email->FromName = "Nanopips Client Service";
$email->Subject   = 'Account Notification';
$email->Body      = $template;
$email->IsHTML(true);
 $email->AddAddress('jeraldfeller@gmail.com');
// $email->AddAddress('jeraldfeller@gmail.com');
$return = $email->Send();


var_dump($return);
