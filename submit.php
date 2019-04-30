<?php 
$email=$_POST['email'];

$send_to="clientservice@nanopips.com";
$subject = "Nanopips Registration";

$mailcontent .= "Nanopips Registration <br>";
$mailcontent .= "Email: ".$_POST["email"]."<br>";


// Set content-type header for sending HTML email
$headers = "MIME-Version: 1.0\n" ;
        $headers .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
        $headers .= "X-Priority: 1 (Highest)\n";
        $headers .= "X-MSMail-Priority: High\n";
        $headers .= "Importance: High\n";

// Additional headers
$headers .= 'From: Nanopips Client Service<clientservice@nanopips.com>' . "\r\n";

// Send email
if( mail($send_to,$subject,$mailcontent,$headers,'-fclientservice@nanopips.com') ){
 

					 header("Location:http://webtrader.nanopips.com?confirm=active");
	
}else{
 
echo "<h1 align='center' style='padding-top:40px;'>Email sending fail</h1>";

    
}

                
                
                ?>