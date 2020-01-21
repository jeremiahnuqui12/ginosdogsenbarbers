<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
require 'config.php';

function validate(){
  if ($_SERVER['HTTP_REFERER'] != getWebroot() . "/account/account.php") {
    //header("Location:" . $_SERVER['HTTP_REFERER']);
    echo "invalid link";
  }
  if (empty(getSessionCustomerId())) {
    //header("Location:" . $_SERVER['HTTP_REFERER']);
    echo "invalid Id";
  }
  requestChange();
}
function requestChange(){
  try {
    ob_start();
    $mail = new PHPMailer(true);
    SMTPServerSettings($mail);
    $subject = 'Request Change Password | Ginos Dogs En Barbers';
    $message = 'Dear ' . getSessionName() . ',
     <br/><br/>
     Click this <a href="' . getWebroot() . '/account/changePassword.php?id=' . md5(getSessionEmail()) . '">link</a> to change your password.
     <br/><br/>
     In case of any questions, feel free to contact us at <a href="' . getWebroot() .  '/contact.php">Contact Us</a>
     <br/>
     Ginos Dogs En Barbers.';
     $mail->addAddress(getSessionEmail());
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->send();
    header("Location: " . $_SERVER['HTTP_REFERER'] . "?passwordrequest=success");
  } catch (Exception $e) {
    echo "Send Mail Error: " . $e->getMessage();
  }
}
validate();
