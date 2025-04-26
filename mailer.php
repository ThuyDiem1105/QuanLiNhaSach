<?php
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function sendMail($email, $subject, $htmlBody, $plainText = '') {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = 'smtp@gmail.com';
    $mail->Username = 'ngochan2005blislife@gmail.com';
    $mail->Password = 'xfgo smta nett fhth';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->setFrom('ngochan2005blislife@gmail.com', 'Bookstore');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $htmlBody;
    $mail->AltBody = $plainText ?:  strip_tags($htmlBody);
    if (!$mail->send()) {
        echo 'Gửi email chưa thành công. Vui lòng thử lại!';
    }
    else {
        echo 'Gửi email thành công!';
    }
    
}

?>