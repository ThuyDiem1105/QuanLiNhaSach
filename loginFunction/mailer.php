<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

$mail = new PHPMailer(true);
//$mail->SMTPDebug = SMTP::DEBUG_SERVER;

try {
    $mail->isSMTP();                                           
    $mail->SMTPAuth = true;       
    
    $mail->Host = 'smtp.gmail.com';                  
    $mail->Username = 'ngochan6e@gmail.com';                   
    $mail->Password = 'rera zpax czwl iqlz';                             
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           
    $mail->Port = 587;     

    $mail->isHTML(true);  

    return $mail;
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>