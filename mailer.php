<?php
 
    require("PHPMailer/PHPMailer.php");
    require("PHPMailer/SMTP.php");    
            
  
    $msg="<h1>ho gya</h1>";
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPDebug = 1;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;
    $mail->IsHTML(true);
    $mail->Username = "food.noreply@gmail.com";
    $mail->Password = "food.noreply.PASS";
    $mail->SetFrom("food.noreply@gmail.com","Welcome to FOOD");
    $mail->Subject = "WELCOME";
    $mail->Body = $msg;
    $mail->AddAddress("namanvashistha15@gmail.com");

     if(!$mail->Send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
     } 

?>