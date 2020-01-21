<?php
require 'config.php';


function changePassword(){
  try {
    $query = getConnection()->prepare("UPDATE `customer_account` SET `password`=:newPassword WHERE `customer_id`=:id");
    $query->execute(
      array(
        ":newPassword" => getNewPassword(),
        ":id" => getSessionCustomerId()
      )
    );
    header("Location: ../account/account.php?passwordchange=success");
  } catch (Exception $e) {
    
  }

}
function validate(){
  if (empty(getNewPassword())) {
    header("Location: " . $_SERVER['HTTP_REFERER'] . "newpassword=required");
  }
  if (empty(getConfirmNewPassword())) {
    header("Location: " . $_SERVER['HTTP_REFERER'] . "confirmnewpassword=required");
  }
  if (getNewPassword() != getConfirmNewPassword()) {
    header("Location: " . $_SERVER['HTTP_REFERER'] . "?password=not match");
  }
  changePassword();
}
function getNewPassword(){
  return md5($_POST['newPassword']);
}
function getConfirmNewPassword(){
  return md5($_POST['confirmNewPassword']);
}

validate();
?>
