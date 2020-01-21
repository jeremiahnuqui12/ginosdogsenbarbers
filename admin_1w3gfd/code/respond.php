<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../vendor/autoload.php';
require 'config.php';

function SMTPServerSettings($mail){
  $mail->SMTPDebug = 1;                                 // Enable verbose debug output
  $mail->isSMTP();                                      // Set mailer to use SMTP
  $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
  $mail->SMTPAuth = true;                               // Enable SMTP authentication
  $mail->Username = 'ginosdogsenbarbers@gmail.com';     // SMTP username
  $mail->Password = 'capstone1234';                     // SMTP password
  $mail->Port = 587;
  $mail->setFrom('ginosdogsenbarbers@gmail.com', 'Ginos Dogs En Barbers');
  $mail->isHTML(true);
}

if (isset($_GET['id']) && isset($_POST['sentTo'])) {
  sendMessage();
  insertRespond();
  recordActivity("Responded on `" . getMessageDate() . "` inquiry message");
  header("Location: ../messages?respond=success");
} else {
  header("Location: " . getWebroot());
}
function getMessageDate(){
  try {
    $query = getConnection()->prepare("SELECT `received_at` FROM `contactus_messages` WHERE `id`=:id");
    $query->bindParam(":id", $_GET['id']);
    $query->execute();
    return date('F d, Y h:i a', strtotime($query->fetch()[0]));
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
function insertRespond() {
  try {
    $query = getConnection()->prepare("INSERT INTO `messages_respond`(
      `message_id`,
      `message`,
      `date_sent`)
      VALUES(
        :messageId,
        :messageRespond,
        :dateSent
      )");
    $query->execute(
      array(
        ":messageId" => $_GET['id'],
        ":messageRespond" => $_POST['messageRespond'],
        ":dateSent" => getTimeStamp()
      )
    );
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
function sendMessage() {
  try {
    ob_start();
    $mail = new PHPMailer(true);
    SMTPServerSettings($mail);
    $to = $_POST['sentTo'];
    $subject = 'Respond to your inquiry';
    $message = 'Ginos Dogs En Barbers<br/><br/>'
     . $_POST['messageRespond'] .
    '<br/><br/>In case of any questions, feel free to contact us again at <a href="' . getWebroot() .  '/contact.php">Contact Us</a>
    <br/>
    Ginos Dogs En Barbers.';
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->send();
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
