<?php
 
         $msg="Your msg";
            
  

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP(); // enable SMTP

    $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587; // or 587
    $mail->IsHTML(true);
    $mail->Username = "EMAIL ID";
    $mail->Password = "PASSWORD";
    $mail->SetFrom("noreplyreckoner@gmail.com","max");
    $mail->Subject = "DISCOUNT ACHIEVED :RECKONER";
    $mail->Body = $msg;
    $mail->AddAddress($row["email"]);

     if(!$mail->Send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
     } 

?>