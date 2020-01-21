<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
include_once 'config.php';
function validate(){
  $error = array();
  if (explode('?', $_SERVER['HTTP_REFERER'], 2)[0] != getWebroot(). "/account/") {
    header("Location: ../account/?signup=1");
  }
  if (empty(getName())) {
    array_push($error, "name=required");
  } else if(!preg_match("/^[a-zA-Z ]+$/", getName())){
    array_push($error, "name=Invalid Name");
  }
  if (empty(getEmail())) {
    array_push($error, "email=required");
  } elseif(!preg_match("/^[a-zA-Z0-9\@\.]+$/", getEmail())){
      array_push($error, "email=Invalid Email");
  } else {
    $checkEmail = explode('@', getEmail());
    if ($checkEmail[1] != "gmail.com" && $checkEmail[1] != "yahoo.com") {
      array_push($error, "email=Invalid Email(Gmail or Yahoo Email Only)");
    }
  }
  if (empty(getPassword())) {
    array_push($error, "password=required");
  }
  if (empty(getConfirmPassword())) {
    array_push($error, "confirm=required");
  } elseif (getPassword() != getConfirmPassword()) {
    array_push($error, "confirm=Password Not Match");
  }
  if (count($error) > 0) {
    getErrors($error);
  } else {
    checkIfEmailExist();
  }
}
function getErrors($error) {
  $error = implode("&", $error);
  header("Location: ../account/?" . $error);
}
function checkIfEmailExist() {
  try {
    $checkIfExist = getConnection()->prepare("SELECT * FROM`customer_account` WHERE `email`=:checkEmail");
    $checkIfExist->execute(
      array(
        ':checkEmail' => getEmail()
      )
    );
    $countResult = $checkIfExist->fetch();
    if ($checkIfExist->rowCount() > 0) {
      header("Location: ../account/?email=Email Exist");
    } else {
      registerAccount();
    }
  } catch (Exception $e) {
    echo "Check If User Exist Error: " . $e->getMessage();
  }
}
function registerAccount(){
  try {
    $registerAccount = getConnection()->prepare("INSERT INTO `customer_account`(
      `type_of_account`,
      `customer_name`,
      `email`,
      `password`,
      `created_at`,
      `status`
    ) VALUES (
      'Ginos Account',
      :name,
      :email,
      :password,
      :createdAt,
      'Active'
    )");
    $registerAccount->execute(
      array(
        ":name" => getName(),
        ":email" => getEmail(),
        ":password" => getPassword(),
        ":createdAt" => getTimeStamp()
      )
    );
    sendEmail();
  } catch (Exception $e) {
    echo "Register Error: " . $e->getMessage();
  }
}
function sendEmail(){
  ob_start();
  try {
    $mail = new PHPMailer(true);
    SMTPServerSettings($mail);
    $to = getEmail();
    $subject = 'Thank you for Registering in Ginos Dogs En Barbers';
    $message = 'Hi ' . getName() . ',
     <br/><br/>
     Thank you for Registering in  Ginos Dogs En Barbers
     <br/>
     Your account has now been activated!
     <br/>
     You may now log into your account at <a href="' . getWebroot() .  '/account/?signin=1">Login Here</a>
     <br/>
     <br/><br/>
     In case of any questions, feel free to contact us at <a href="' . getWebroot() .  '/contact.php">Contact Us</a>
     <br/>
     Ginos Dogs En Barbers.';
     $mail->addAddress(getEmail());
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->send();
    getAccountName();
  } catch (Exception $e) {
      echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
  }
}
function setSession($id){
  $_SESSION['b8620d1a5676a832c2c9f8fd387f0e8a'] = "Ginos Account";
  $_SESSION['7a13ce2a07525b4fd46ebc0226706fab'] = getName();// Customer Name
  $_SESSION['cb24373bb88538168c8e839069491f18'] = $id;//Customer Id
  $_SESSION['cfecb706488b9b67825b14c6792f0bcc'] = getEmail();// Customer Email
  $_SESSION['e26ad384feaee8a3138677a965f539a8'] = getTimeStamp();
  if (isset($_POST['appDate']) && isset($_POST['appTime'])) {
    header("Location: ../services/grooming.php?date=" . $_POST['appDate'] . "&time=" . $_POST['appTime']);
  } elseif (isset($_POST['product_id'])) {
    header("Location: ../products/details.php?id=" . $_POST['product_id']);
  } else {
    header("Location: ../?register=success");
  }
  //header("Location: ../?register=success");
}
function getAccountName(){
  try {
    $getAccountName = getConnection()->prepare("SELECT * FROM `customer_account` WHERE `email`=:email");
    $getAccountName->execute(
      array(
        ":email" => getEmail()
      )
    );
    $data = $getAccountName->fetch();
    if ($getAccountName->rowCount() > 0) {
      setSession($data['customer_id']);
    } else {
      echo "No Account or Multiple Account Retrive";
    }
  } catch (Exception $e) {
    echo "Get Id Error" . $e->getMessage();
  }
}

//-------------------------------
function getName(){
  return ucfirst(strip_tags($_POST['name']));
}
function getEmail(){
  return strip_tags($_POST['email']);
}
function getPassword(){
  return md5(strip_tags($_POST['password']));
}
function getConfirmPassword(){
  return md5(strip_tags($_POST['confirm-password']));
}
validate();
?>
