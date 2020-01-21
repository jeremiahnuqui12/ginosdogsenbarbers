<?php
include_once "config.php";
function validatePassword(){
  if (empty($_POST['newPassword'])) {
    header("Location: ../account/?password=required");
  }
  if (empty($_POST['confirmPassword'])) {
    header("Location: ../account/?confirm=required");
  }
  if ($_POST['newPassword'] != $_POST['confirmPassword']) {
    header("Location: ../account/?reset=notMatch");
    //echo "Password Doesn't Match";
  }
  resetPassword();
}
function resetPassword() {
  try {
    $query = getConnection()->prepare("UPDATE `admin_user` SET `password`=:newPassword where `username`=:username");
    $query->execute(
      array(
        ":newPassword" => getNewPassword(),
        ":username" => getSessionAdminUsername()
      )
    );
    recordActivity(getSessionAdminUsername() . " password has been updated");
    header("Location: ../account/?reset=success");
  } catch (Exception $e) {
    echo "Password Reset Error: " . $e->getMessage();;
  }
}
function getNewPassword(){
  return md5($_POST['newPassword']);
}
validatePassword();
