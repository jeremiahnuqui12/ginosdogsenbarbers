<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
require 'config.php';
function validate() {
  $error = array();
  if(empty($_POST['name'])) {
    array_push($error,"name=required");
  }
  if(empty($_POST['email'])) {
    array_push($error,"email=required");
  }
  if(empty($_POST['message'])) {
    array_push($error,"message=required");
  }
  sendMessage();
}
function sendMessage(){
  try {
    $sendMessage = getConnection()->prepare("INSERT INTO `contactus_messages`(
      `name`,
      `email`,
      `message`,
      `received_at`,
      `status`
    )VALUES(
      :name,
      :email,
      :message,
      :receivedAt,
      :status
    )");
    $sendMessage->execute(
      array(
        ":name" => getName(),
        ":email" => getEmail(),
        ":message" => getContactMessage(),
        ":receivedAt" => getTimeStamp(),
        ":status" => "Active"
      )
    );
    sendEmailNotification();
    header("Location: ../contact.php?send=success");
  } catch (Exception $e) {
    echo "Send Message Error: " . $e->getMessage();
  }
}
function sendEmailNotification(){
  try {
      $mail = new PHPMailer(true);
      SMTPServerSettings($mail);
      $subject = 'Inquiry Message in Ginos Dogs En Barbers';
    $message = 'Dear ' .   getName() . ',
     <br/><br/>
     Thank you for sending us your Inquiry
     <br/>
     Your Inquiry Message Details:
     <br/>
     <b>Name: </b><span>' . getName() . '</span>
     <br/>
     <b>Message: </b><span>' . getContactMessage() . '</span>
     <br/>
     <b>Sent At: </b><span>' . getTimeStamp() . '</span>
     <br/>
     <br/><br/>
     In case of any questions, feel free to contact us again at <a href="' . getWebroot() .  '/contact.php">Contact Us</a>
     <br/>
     Ginos Dogs En Barbers.';
      $mail->addAddress(getEmail());
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->send();
  } catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
  }
}

function getName(){
  if (empty($_POST['name'])) {
    header("Location: ../contact.php?name=required");
  }
  if (preg_match("/^[a-zA-Z\ ]+$/", $_POST['name'])) {
    return strip_tags($_POST['name']);
  } else {
    header("Location: ../contact.php?name=invalid");
  }
}
function getEmail(){
  return strip_tags($_POST['email']);
}
function getContactMessage(){
  return strip_tags($_POST['message']);
}
validate();
?>
