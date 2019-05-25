function toFixedNew(num, fixed) {
  var re = new RegExp('^-?\\d+(?:\.\\d{0,' + (fixed || -1) + '})?');
  return num.toString().match(re)[0];
}
function getUserById($id){
  $.ajax({
    url: '../Controller/user.php?action=get-user-by-id',
    type: 'post',
    dataType: 'json',
    success: function (rsp) {
      $tradePercentageAmount = rsp.balanceStatus.trade_percentage_amount;
      $balanceId = rsp.balanceStatus.id;
    },
    data: {param: {id: $id, admin: 0}}
  });
}
