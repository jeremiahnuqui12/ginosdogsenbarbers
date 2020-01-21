<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
include_once 'config.php';
function validate() {
  if (explode('?', $_SERVER['HTTP_REFERER'], 2)[0] != getWebroot(). "/account/") {
    header("Location: ../account/?signin=1");
  }
  if (empty($_GET['name'])) {
    return false;
  }
  if (empty($_GET['img_url'])) {

  }
  if (empty($_GET['token'])) {
    return false;
  }
  if (empty($_GET['email'])) {
    return false;
  } else {
    checkIfEmailExist($_GET['email']);
  }
}
function setSession($id) {
  $_SESSION['7a13ce2a07525b4fd46ebc0226706fab'] = getName();//Customer Name
  $_SESSION['cb24373bb88538168c8e839069491f18'] = $id; // Customer Id
  $_SESSION['cfecb706488b9b67825b14c6792f0bcc'] = getEmail();// Customer Email
  $_SESSION['b8620d1a5676a832c2c9f8fd387f0e8a'] = "Google Account";// account type
  if (isset($_GET['appDate']) && isset($_GET['appTime'])) {
    header("Location: ../services/grooming.php?date=" . $_GET['appDate'] . "&time=" . $_GET['appTime']);
  } elseif (isset($_GET['product_id'])) {
    header("Location: ../products/details.php?id=" . $_GET['product_id']);
  } else {
    header("Location: ../");
  }
}
function checkIfEmailExist($email) {
  try {
    $checkIfExist = getConnection()->prepare("SELECT * FROM`customer_account` WHERE `email`=:checkEmail");
    $checkIfExist->bindParam(':checkEmail', $email);
    $checkIfExist->execute();
    $countResult = $checkIfExist->fetch();
    if ($checkIfExist->rowCount() > 0) {
      $_SESSION['e26ad384feaee8a3138677a965f539a8'] = date('F d, Y h:i a', strtotime($countResult['created_at']));
      addToken($countResult['customer_id'], $countResult['customer_name']);
    } else {
      registerAccount();
    }
  } catch (Exception $e) {
    echo "Check If User Exist Error: " . $e->getMessage();
  }
}
function addToken($accountId) {
  $date = date("Y-m-d H:i:s");
  try {
    $googleToken = getConnection()->prepare("INSERT INTO `google_token`(
      `customer_id`,
      `id_token`,
      `signin_at`
    ) VALUES (
      :customerId,
      :idToken,
      :signinAt
    )");
    $googleToken->execute(
      array(
        ":customerId" => $accountId,
        ":idToken" => getToken(),
        ":signinAt" => getTimeStamp()
      )
    );
    if (checkIfActive() > 0) {
      setSession($accountId);
    } else {
      header("Location: ../account/?block=1");
    }
  } catch (Exception $e) {
    echo "Add Token Error: " . $e->getMessage();
  }
}
function checkIfActive(){
  try {
    $query = getConnection()->prepare("SELECT COUNT(`email`) FROM `customer_account`
      WHERE `status`=:status AND `email`=:email");
    $query->execute(
      array(
        ":status" => "Active",
        ":email" => getEmail()
      )
    );
    return $query->fetch()[0];
  } catch (Exception $e) {

  }

}
function registerAccount() {
  try {
    $registerAccount = getConnection()->prepare("INSERT INTO `customer_account`(
      `type_of_account`,
      `customer_name`,
      `img_url`,
      `email`,
      `created_at`,
      `status`
    ) VALUES (
      'Google Account',
      :name,
      :imgUrl,
      :email,
      :createdAt,
      :status
    )");
    $registerAccount->execute(
      array(
        ":name" => getName(),
        ":imgUrl" => getImgUrl(),
        ":email" => getEmail(),
        ":createdAt" => getTimeStamp(),
        ":status" => "Active"
      )
    );
    $_SESSION['e26ad384feaee8a3138677a965f539a8'] = getTimeStamp();
    sendEmail();
  } catch (Exception $e) {
    echo "Add Google Data Error: " . $e->getMessage();
  }
}
function getAccountId() {
  try {
    $getAccountId = getConnection()->prepare("SELECT * FROM `customer_account` WHERE `email`=:email");
    $getAccountId->execute(
      array(
        ":email" => getEmail()
      )
    );
    $data = $getAccountId->fetch();
    $count = $getAccountId->rowCount();
    if ($count == 1) {
      addToken($data['customer_id']);
    } else {
      echo "Multiple Account Retrive";
    }
  } catch (Exception $e) {
    echo "Get Id Error" . $e->getMessage();
  }
}
function sendEmail(){
  ob_start();
  try {
    $mail = new PHPMailer(true);
    SMTPServerSettings($mail);
    $subject = 'Thank you for Registering in Ginos Dogs En Barbers';
    $message = 'Hi ' . getName() . ',
     <br/><br/>
     Thank you for registering in  Ginos Dogs En Barbers
     <br/>
     Your account has now been activated!
     <br/>
     You may now log in to your account at <a href="' . getWebroot() .  '/account/?signin=1">Login Here</a>
     <br/>
     <br/><br/>
     In case of any questions, feel free to contact us at <a href="' . getWebroot() .  '/contact.php">Contact Us</a>
     <br/>
     Ginos Dogs En Barbers.';
     $mail->addAddress(getEmail());
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->send();
    getAccountId();
  } catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
  }
  ob_end();
}

function getName(){
  return $_GET['name'];
}
function getImgUrl(){
  return $_GET['img_url'];
}
function getToken(){
  return $_GET['token'];
}
function getEmail(){
  return $_GET['email'];
}
validate();
?>
