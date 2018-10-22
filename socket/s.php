<html>
<head>
  <script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
</head>
<body>
<script type="text/javascript" language="JavaScript">
var settings = {
"async": true,
"crossDomain": true,
"url": "https://api.coincap.io/v2/exchanges",
"method": "GET",
"headers": {}
}

$.ajax(settings).done(function (response) {
console.log(response);
});
</script>
</body>
  </html>
