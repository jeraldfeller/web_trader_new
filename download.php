<?php
if(isset($_GET)){
    if($_GET['form'] == 'withdraw'){
      $file = 'forms/NanoPipsWire.pdf';
      $fileName = 'NanoPipsWire.pdf';
    }

    header('Content-type: application/pdf');
    header('Content-Disposition: attachment; filename="'.$fileName.'"');
    readfile($file);
}
?>
