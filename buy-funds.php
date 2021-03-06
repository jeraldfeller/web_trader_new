<?php
include 'includes/regular/require.php';
require 'Model/History.php';
require 'Model/Users.php';

$history = new History();
$users = new Users();
$data = json_decode($history->getCoinHistory(strtolower('BTC'), 'desktop'), true);
$currentBtcPrice = $data[count($data) - 1]['close'];

$user = $users->getUserDataById($userData['id']);
if($user['dollar_amount'] > 0 ){
  //$funds = number_format($user['dollar_amount'] / $currentBtcPrice * 10000, 2);
  $funds = str_replace(',', '',number_format($currentBtcPrice * 10000, 2));
}else{
  $funds = 0;
}



?>
<!DOCTYPE html>
<html>
<head>
  <title>ZoloTrader</title>
  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  <link href="assets/css/bootstrap.css" type="text/css" rel="stylesheet">
  <link href="assets/css/coinbase.css?v=1.5" type="text/css" rel="stylesheet">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
        type="text/css" media="all"/>

  <style>
    #chartdiv {
      width: 100%;
      height: 500px;
      font-size: 11px;
    }

    .center-block {
      margin-left: auto;
      margin-right: auto;
      display: block;
    }

    .spacer {
      margin-top: 12px; /* define margin as you see fit */
    }

    .text-bold {
      font-weight: bold;
    }

    .amcharts-guide tspan {
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
      text-decoration: overline underline;
      -webkit-text-fill-color: green; /* Will override color (regardless of order) */
      -webkit-text-stroke-width: 20px;
      -webkit-text-stroke-color: black;
      font-weight: bold;
    }

    .amcharts-export-menu {
      display: none;
    }

    body, html {
      margin: 0;
      width: 100%;
      font-family: -apple-system, BlinkMacSystemFont, \.SFNSText-Regular, San Francisco, Roboto, Segoe UI, Helvetica Neue, Lucida Grande, sans-serif;
      -webkit-font-smoothing: antialiased
    }

    #root, #root > [data-reactroot], body, html {
      min-height: 100vh
    }

    * {
      -webkit-box-sizing: border-box;
      box-sizing: border-box
    }

  </style>
  <script>
    var zoomEvents = [];
    if (screen.width <= 768) {
      location.href = 'go_mobile.php';
    }
  </script>
</head>
<body>
<div class="container" style="padding-top:1%;">

  <strong>

  </strong>
  <a href='log.php?v='>Trade History</a> |


</div>
<div class="container-fluid">
    <?php if ($funds == 0) { ?>
      <div class="row">
        <div class="col-md-12 spacer text-center">
          <h1>You have no nanopips available, please click the button to purchase nanopips.</h1>
        </div>
      </div>
    <?php } ?>
  <div class="row">
    <div class="col-md-12 spacer text-center">
      <h3> Nanopips: <?php echo $funds; ?></h3>
      <div>
        <!--
        <a class="buy-with-crypto"
           href="https://commerce.coinbase.com/checkout/8b1229b9-62cc-47ac-a5c8-752ea13f79cd">
          <span>Buy with Crypto</span>
        </a>
        <script src="https://commerce.coinbase.com/v1/checkout.js">
        </script>
      -->
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#nanopipsModal">
        Deposit with Crypto
      </button>

      </div>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="nanopipsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" style="width: 130%;">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Purchase Nanopips</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="selectCurrencyContainer">
                <div class="sc-iBEsjs gxLxCO sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                  <div class="sc-hzNEM jqIWxI sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                    <div class="sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                      <div class="sc-gmeYpB lnleVr sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">Select currency</div>
                      <div class="sc-fQejPQ bQCrKx sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS selectCurrency" data-currency="bitcoin">
                        <img
                            class="sc-bMvGRv fEsGFv sc-ksYbfQ ijgyGm sc-cJSrbW dLwzxs"
                            src="data:image/png;base64,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"
                            alt="Bitcoin">
                        <div class="sc-clNaTc eyPACQ sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">Bitcoin</div>
                        <div class="sc-cpmLhU golEd sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS"><i class="fa fa-angle-right"></i></div>
                      </div>

                      <!--
                      <div class="sc-fQejPQ bQCrKx sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS selectCurrency" data-currency="bitcoin-cash">
                        <img
                            class="sc-bMvGRv fEsGFv sc-ksYbfQ ijgyGm sc-cJSrbW dLwzxs"
                            src="data:image/png;base64,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"
                            alt="Bitcoin Cash">
                        <div class="sc-clNaTc eyPACQ sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">Bitcoin Cash</div>
                        <div class="sc-cpmLhU golEd sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS"><i class="fa fa-angle-right"></i></div>
                      </div>
                      -->
                      <!--
                      <div class="sc-fQejPQ bQCrKx sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS selectCurrency" data-currency="ethereum">
                        <img
                            class="sc-bMvGRv fEsGFv sc-ksYbfQ ijgyGm sc-cJSrbW dLwzxs"
                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8CAYAAAA6/NlyAAAABGdBTUEAALGPC/xhBQAACKpJREFUaAXlW2mMFEUU/npmD1jlFhY8ABcR8AgokQUCyy54gnjGAzXRoAkBJP4Q1Gi8jRqvPyqIB0pESYxiVAQVEXAJl3dEAaNIjAeCBBAFWXan/b7q6d3emdmZ7ZlqQX3Zma6ufvXV+7pevXrdU+sgIpk93i2r34YRLjAYLvq5Dvo5QHeW27Gunbrl+R5+7eH5VsfFJpY3se6Tom5YOektZ28UphHfnswa7nZraMAVJHARiVUSuSRP9DqSX0vjFsTjeHnyKmdbnjhpzawQfmK4W4V6zOAonk2iRWm9FFLhoJ6j/w5RH75+lfNhIVBqWxDhJyvdGo7mPa6LEYUa0pr2joOVvKm3T1vjLG+NfiadvAjPHun2qKvDoxzNCZlAI69zML+kBDdOqnV+CdtXaMIzh7rnJBKYz5HtELYzm/o0fHcshglT1jiLw+DGwijThW9sSGDhwSYrm2WDbJFNYTi0aoQXneOWbt6B2ezk6jDg/5QuScyt6IJJYxc7+3P1mZPwnJFu17378QbJDssFdjCvk8jqslKcP7HW2Z7NjqwurZH9N5AVQQ2IbJXNeRNOurH1ke05HOhpHdUjLZvzIqxgEMWcjfP+n3KF94nnm4dlYSSbswWyjC5tlh4XD2XBzfvSwMuBw7rwcwSgchSSoO3ikAk7jbCSCq2zVE67lgkgTF37o4G+Y+h6HAbJcacD7Y/yypa/Y+IgLqm4aaSUQdGeSJKKIdcB8WKawJAq0nFm3addm2qSnXNxMNlgClwzwo8PdaujShd7VwFdj0+OLq1hXmyk2wCg98gUq2ydMvU1nAJ4zQjzqeTewDVrxSIFqgmEI0mfqMBNmeQH8ZqCWRSSyqmR8Mxh7qionnoGMiq34STRoMqVRVRH/hlp28kjnTy1ehAncfNBGwknGjDdr7R57HAMA9VoD1EEDdnkUTfAH/HjqNOeulFIkJshrDcV5uE9gt4UlJzkKwF/hDW0Im9GmQXVK5gNiSqA8cWEOIqeIazXMrQgaZaq7UhFNdCtH7FESm6chFVZJP057bu3dI8dlVSyeSA3w5GYhjANucgmvrCK2jCxuMwbRY8dKwPERVKkzdEUvPJABjC1tS0+R0dvF+t+xU52YDXRkysrsTAjSXARk/hz1pzqiwpJvo263ywBPp5j1G1+1ZWUo1NMr1KJapVsp15ARTVRSci4q0+MzFT0R7eRrKls0u1TA3TsRUW7UiKuMfY12C4us6eJXhZlCJGkP2d9oiWHAcVl7JWdmzrqBHVNBkYM2yKuRexUYcWaaHnp0jcJJ0I+MklpLdbHpJesbzgA7NsF1P3Bk0RAl6fKyiqqgc3LeWJLyDWmXwRs4WnUTr6UaCQnohrZWBwo6wzIzfWE5JNVnyof3pXu2xNQ8iFdM8fZTqKgV9zWK9v4FtcYsbvbABPGoCubMiq5ZRkfA4NkWurHvynSLeNNibGtXL1tR2Je1VKr8PXiGuNQmN95wjdv3qJzb6BPFZeUUo5aOR9VmDWZdNIsfM11WzpzqNumPW8S2wpD+XWfUfQOYlsRclXQskJ41C0kyRHqwOdbBSXjmnlaqbalh3tYunHVN+cJlNJMXEPc/5TWKadfv8EgVJdSaeFUmF8R25Y4TwxxfyNzzjY7UnMr0H8c3bHARFURfOPbwLIH7NglFDrOjhi/99iA7DnUSwmX3Q+8eCF/8P3KCzxhsRWstq4nBpNdkVWaKWwrQq6aw1ttgO3cAlzyvJdO/sFfc1+dCCyaAezd0Xp06S6azrZMS4Wh1FSYwrYh4hofd9RdIwk2qFBAJQ919JWz7gOOPBXYvhH4+XPgs5e8rKprf2+dzdRPPefp5/OBN28Adv3A+dUHOJueciqXpJWPAb98kalV+Dq69NL42KPv4lsljAnfPL3Fju+85aiCy9OJF3jL0tYvgS21wPrXgPITGSa7N0VwNwH89KnnDd9Tp4SRefg0YPRtXoTe9A7w0XPp/eRbw+j/isOX1mfyPe67+YKktivmkjRhHt9eHOld2cfnsFUzgQ1veuca/TPu9MpL7qYXkLCiyQnj+VvJFC/j0tXffwbmc4QP/Onp2vhmlnVWJI+HPQYCF85q7sK/fg2seBjYxmNQNOpVnOvl8rOk8JUMXp9sz5WTsObxkPcWeLzS/ZAZl+ayNamc5D01BQEVgTe8xZ/5OOKSYVOBAec2ubhX67nx2qf9M0tHB7XT1jpVZrUk6wW0xSrhdc9yOan05q1vsjKoE85juljDGpaVTaWKlqR1Fuetjy+OKptMS1uDaEC9f9HG0aVbvncHI/fedLTSdpnJSncJ57faWhVyMxwJaghrHxRfWDMm2pXdPwK1j7Ues/ZRbtxgG9sibv5eL0PYdMB9ULY7Ep7m7Lcf5Eb+dil1F+bWy0sjwK2RsDZ9cY6tzAswRyOliMqcWhJdW/ZgS1cLqxen4Ia2RsKC5cTmrLMv+38H3r87c26tyK31WDpRCN9y3B7EbUZ46lpnGVkzybMvP37M9PHldNzP5jHb4rVIhFxSd+01I6xOtcONI707CgO0/m7/pgl5+yZgzVNN5zZL4iAuqZhphLWdTzvcqMhM164kuPBpqTrwF1DPz7t0NtVFIAlxyLQ1kTcis2hjCHPsRzJfLaz2pIvZnnN3vUkFCsPK1Jo583ROTy5y6dIiYanybcgLtOvq9GaHbg0Jzb1+nXNNSxamuXRQUdv5CLA6WHcol2WrbM5mY1bC2ruo7Xz/BtKyUbbm2m+ZlbDulPYu8q7VEHButjt3MK/JNtmYa5+lbKRu6yUZyB5ii5w3qvWoBWkmGKBuailAZUIORVgA/6sN4iKsHejFpRgQVUamPnIKMyjZEHY3vHBDj3DQGG360j4o5sMjgvVRlfUgoNw4NV0M019BhP2OtA9KW4NoTGT/xsNfGB+ZstpZ4feZ79EKYb/zZyrd8v18aclk5b/9j1o+4eDxUP1XvL8B50OwwTuf6cYAAAAASUVORK5CYII="
                            alt="Ethereum">
                        <div class="sc-clNaTc eyPACQ sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">Ethereum</div>
                        <div class="sc-cpmLhU golEd sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS"><i class="fa fa-angle-right"></i></div>
                      </div>
                      -->
                      <!--
                      <div class="sc-fQejPQ bQCrKx sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS selectCurrency" data-currency="litecoin">
                        <img
                            class="sc-bMvGRv fEsGFv sc-ksYbfQ ijgyGm sc-cJSrbW dLwzxs"
                            src="data:image/png;base64,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"
                            alt="Litecoin">
                        <div class="sc-clNaTc eyPACQ sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">Litecoin</div>
                        <div class="sc-cpmLhU golEd sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS"><i class="fa fa-angle-right"></i></div>
                      </div>
                      -->
                    </div>

                  </div>
                  <div class="sc-chbbiW jkfpbM sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS"></div>
                </div>
              </div>


              <!--- Transaction -->
              <div class="transactionContainer" style="display: none;">
                <div class="sc-iBEsjs gxLxCO sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                  <div class="sc-hzNEM jqIWxI sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                    <div class="sc-likbZx csjLbb sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                      <div class="sc-gmeYpB lnleVr sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                        To pay send <span class="selectedCoin"></span> to the address below
                      </div>
                      <div class="sc-eKZiaR fEvEMo sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                        <div class="sc-eXNvrr dBDltv sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                          <label class="sc-cpmKsF lazhiw sc-hzDkRC fsyfQm sc-bRBYWo fDApnR">Amount</label>
                        </div>
                        <div class="sc-gVyKpa dPTfnR sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                          <div class="sc-kQsIoO ktnYNL sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                            <span contenteditable="true" class="amount" id="amount" style="width: 70%;">1</span> USD
                            <span class="sc-jrIrqw cTQTpB">(<span class="coinValue">1.00</span>)</span>
                          </div>
                        </div>
                        <div class="text-center" style="margin-top: 12px;">

                            <button class="btn btn-primary order-btn">ORDER</button>

                        </div>
                      </div>
                      <div class="coin-transaction-address" style="display:none;">
                        <div class="sc-drMfKT khFLnI sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                          <div class="sc-eXNvrr dBDltv sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                            <label class="sc-cpmKsF lazhiw sc-hzDkRC fsyfQm sc-bRBYWo fDApnR"><span class="selectedCoinText">Bitcoin</span> address</label>
                          </div>
                          <div class="sc-gVyKpa dPTfnR sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                               <span class="coinAddress"></span>
                          </div>
                        </div>
                        <div class="sc-fgfRvd hCzwYa sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS" style="margin-top: 12px;">
                          <div class="fa fa-spin sc-ePZHVD gQuqEO sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                            <svg class="CircularProgressbar  " viewBox="0 0 100 100">
                              <path class="CircularProgressbar-trail" d="
                              M 50,50
                              m 0,-46
                              a 46,46 0 1 1 0,92
                              a 46,46 0 1 1 0,-92
                            " stroke-width="8" fill-opacity="0"></path>
                            <path class="CircularProgressbar-path" d="
                              M 50,50
                              m 0,-46
                              a 46,46 0 1 1 0,92
                              a 46,46 0 1 1 0,-92
                            " stroke-width="8" fill-opacity="0" style="stroke-dasharray: 289.027px, 289.027px; stroke-dashoffset: 236.837px;"></path>
                          </svg>
                        </div>
                        <div class="sc-gpHHfC cFEulg sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS countdown-timer"></div>
                        <div class="sc-hIVACf clmSIA sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">Awaiting payment...</div>
                      </div>

                      <div class="sc-hjRWVT eJsbVm sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS scan-qrcode">
                         <i class="fa fa-qrcode"></i> &nbsp;&nbsp;&nbsp;Scan QR code instead
                      </div>
                      <div class="sc-iQtOjA kDpZjR sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS qr-code-container">
                        <div class="sc-fHxwqH jhgeDH sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                          <div id="qrcode"></div>
                        </div>
                      </div>
                      </div>

                  </div>
                </div>
                <div class="sc-chbbiW jkfpbM sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                  <div class="sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                    <a class="sc-iSDuPN dTraGu sc-kAzzGY jHprQs sc-cSHVUG emdAGG back-arrow pull-left">
                      <i class="fa fa-long-arrow-left"></i>
                    </a>
                  </div>
                </div>
              </div>
              </div>


              <!--- Message -->
              <div class="messageContainer" style="display: none;">
                <div class="sc-iBEsjs gxLxCO sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                  <div class="sc-hzNEM jqIWxI sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                    <div class="sc-likbZx csjLbb sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                      <div class="sc-eKZiaR fEvEMo sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS">
                        <div class="sc-eXNvrr dBDltv sc-ifAKCX cFlEyZ sc-bdVaJa iHZvIS text-center">
                          <h1 class="messageText"></h1>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>


            </div>
          </div>
        </div>
      </div>


    </div>
  </div>
  <script src="assets/js/qrcode.js"></script>
  <script type="text/javascript">
    $(document).ready(function(){
        var root = "https://blockchain.info/";
      var qrcode = new QRCode("qrcode", {
          text: "btc",
          width: 128,
          height: 128,
          colorDark : "#000000",
          colorLight : "#ffffff",
          correctLevel : QRCode.CorrectLevel.H
      });
      $step = 1;

      $('.selectCurrency').on('click', function(){
        $step = 2;
        $currency = $(this).attr('data-currency');
        console.log($currency);
        if($currency == 'bitcoin'){
          $coin = 'BTC';
          $coinText = 'Bitcoin';

          $qrCode = 'https://webtrader.nanopips.com/assets/img/btc-qr.png';

        }else if($currency == 'ethereum'){
          $coin = 'ETH';
          $coinText = 'Ethereum';

          $qrCode = 'https://webtrader.nanopips.com/assets/img/btc-qr.png';

        }else if($currency == 'bitcoin-cash'){
          $coin = 'BCH';
          $coinText = 'Bitcoin Cash';
          $qrCode = 'https://webtrader.nanopips.com/assets/img/btc-qr.png';
        }else if($currency == 'litecoin'){
          $coin = 'LTC';
          $coinText = 'Litecoin';
          $qrCode = 'https://webtrader.nanopips.com/assets/img/btc-qr.png';
        }

        $.getJSON('https://coincap.io/page/'+$coin).done(function(response){
          $coinPrice = response.price;
          $amount = parseFloat($('.amount').text());
          $coinValue = $amount / $coinPrice;
          $('.coinValue').text($coinValue + ' ' + $coin);


          var editable = document.getElementById('amount');
          editable.addEventListener('input', function() {
            $amount = parseFloat($('.amount').text());
            $coinValue = $amount / $coinPrice;
            $('.coinValue').text($coinValue + ' ' + $coin);
          });



          $('.selectedCoin').text($coin);
          $('.selectedCoinText').text($coinText);


          $('.qr-code').attr('src', $qrCode);

          $('.selectCurrencyContainer').css('display', 'none');
          $('.transactionContainer').css('display', 'block');


          $('.order-btn').click(function(){
            $(this).html('<i class="fa fa-spinner fa-spin"></i>');

            $.ajax({
                url: 'Controller/blockchain.php?action=create-invoice',
                type: 'post',
                dataType: 'json',
                success: function (data) {

                    $('.order-btn').css('display', 'none');
                    console.log(data);

                    $code = data.input_address;
                    //timer
                    //initCountdown();
                    // get charge Status
                    /*
                    setInterval(function(){
                      $.ajax({
                        url: 'Controller/coinbase.php?action=get-charge',
                        type: 'post',
                        dataType: 'json',
                        success: function (response) {
                          console.log(response);
                          $timeline = response.data.timeline;
                          $amountTransaction = response.data.pricing.local.amount;
                          for(var i = 0; i < $timeline.length; i++){
                            $status = $timeline[i].status;
                            console.log($status);
                            if($status == 'COMPLETED'){
                                $message = 'Transaction Successfully Completed.';
                                $('.transactionContainer').css('display', 'none');
                                $('.messageContainer').css('display', 'block');
                                $('.messageText').text($message);

                                $.ajax({
                                  url: 'Controller/coinbase.php?action=complete-charge',
                                  type: 'post',
                                  dataType: 'json',
                                  success: function (response) {
                                  },
                                  data: {param: JSON.stringify({amount: $amountTransaction})}
                                });

                            }else if($status == 'EXPIRED'){
                                $message = 'Transaction Expired.';
                                $('.transactionContainer').css('display', 'none');
                                $('.messageContainer').css('display', 'block');
                                $('.messageText').text($message);
                            }
                          }



                        },
                        data: {param: JSON.stringify({code: $code})}
                      })
                    }, 3000);
                    */

                    /*
                    $address = data.data.addresses;
                    if($currency == 'bitcoin'){
                      $coinAddress = $address.bitcoin;
                    }else if($currency == 'ethereum'){
                      $coinAddress = $address.ethereum;
                    }else if($currency == 'bticoin-cash'){
                        $coinAddress = $address.bitcoincash;
                    }else if($currency == 'litecoin'){
                        $coinAddress = $address.litecoin;
                    }
                    */

                    $coinAddress = $code;



                    $('.coinAddress').text($coinAddress);
                    qrcode.clear(); // clear the code.
                    qrcode.makeCode($coinAddress); // make another code.

                    $('.coin-transaction-address').css('display', 'block');
                    $('.scan-qrcode').click(function(){
                      $('.qr-code-container').css('visibility', 'visible');
                      $('.qr-code-container').css('opacity', 1);
                    });

                    $('.qr-code-container').click(function(){
                      $('.qr-code-container').css('visibility', 'hidden');
                      $('.qr-code-container').css('opacity', 0);
                    });


                    function checkBalance() {
                        $.ajax({
                            type: "GET",
                            url: root + 'q/getreceivedbyaddress/'+$coinAddress,
                            data : {format : 'plain'},
                            success: function(response) {
                              console.log(response);
                                if (!response) return;

                                var value = parseInt(response);

                                if (value > 0) {
                                  $message = 'Transaction Successfully Completed.';
                                  $('.transactionContainer').css('display', 'none');
                                  $('.messageContainer').css('display', 'block');
                                  $('.messageText').text($message);

                                  setTimeout(function(){
                                    location.href = 'buy-funds.php';
                                  }, 3000)


                                } else {
                                    setTimeout(checkBalance, 5000);
                                }
                            }
                        });
                    }

                    try {
                        ws = new WebSocket('wss://ws.blockchain.info/inv');

                        if (!ws) return;

                        ws.onmessage = function(e) {
                            try {
                                var obj = $.parseJSON(e.data);

                                if (obj.op == 'utx') {
                                    var tx = obj.x;

                                    var result = 0;
                                    for (var i = 0; i < tx.out.length; i++) {
                                        var output = tx.out[i];

                                        if (output.addr == response.input_address) {
                                            result += parseInt(output.value);
                                        }
                                    }
                                }

                                button.find('.blockchain').hide();
                                button.find('.stage-paid').trigger('show').show().html(button.find('.stage-paid').html().replace('[[value]]', result / 100000000));

                                ws.close();
                            } catch(e) {
                                console.log(e);

                                console.log(e.data);
                            }
                        };

                        ws.onopen = function() {
                            ws.send('{"op":"addr_sub", "addr":"'+ response.input_address +'"}');
                        };
                    } catch (e) {
                        console.log(e);
                    }


                    ///Check for incoming payment
                    setTimeout(checkBalance, 5000);

                },
                data: {param: JSON.stringify({amount: $amount})}
            });




              $('.selectCurrency').click(function(){
                $currency = $(this).attr('data-currency');

                if($currency == 'bitcoin'){
                  $coinAddress = $address.bitcoin;
                }else if($currency == 'ethereum'){
                  $coinAddress = $address.ethereum;
                }else if($currency == 'bticoin-cash'){
                    $coinAddress = $address.bitcoincash;
                }else if($currency == 'litecoin'){
                    $coinAddress = $address.litecoin;
                }




                $('.coinAddress').text($coinAddress);
                qrcode.clear(); // clear the code.
                qrcode.makeCode($coinAddress); // make another code.
              });


          });


        });




        // Back button
        $('.back-arrow').click(function(){
          $('.selectCurrencyContainer').css('display', 'block');
          $('.transactionContainer').css('display', 'none');
        })



      });
    });

    function initCountdown(){
      $minute = 900;
      var interval = setInterval(function(){

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
            $('.countdown-timer').text($fullMinDisplay+':'+$minSecsDisplay);
        }
        else if($minute < 10){
            $('.countdown-timer').text('00:0'+$minute);
        }else{
            $('.countdown-timer').text('00:'+$minute);
        }

        if($minute == 0 ){
          clearInterval(interval);
        }

        $minute--;
      }, 1000);

    }
  </script>
</body>
</html>
