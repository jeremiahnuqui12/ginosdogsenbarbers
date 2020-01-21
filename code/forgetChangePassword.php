<?php
require 'config.php';
function changePassword(){
  try {
    $query = getConnection()->prepare("UPDATE `customer_account` SET `password`=:newPassword WHERE md5(`email`)=:email");
    $query->execute(
      array(
        ":newPassword" => getNewPassword(),
        ":email" => $_POST['email']
      )
    );
    header("Location: ../account/?passwordchange=success");
  } catch (Exception $e) {

  }
}
function validate(){
  if (empty(getNewPassword())) {
    header("Location: " . $_SERVER['HTTP_REFERER'] . "&newpassword=required");
  }
  if (empty(getConfirmNewPassword())) {
    header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmnewpassword=required");
  }
  if (getNewPassword() != getConfirmNewPassword()) {
    echo "Not Match";
    header("Location: " . $_SERVER['HTTP_REFERER'] . "&password=not match");
  } else {
    changePassword();
  }
}
function getNewPassword(){
  return md5($_POST['newPassword']);
}
function getConfirmNewPassword(){
  return md5($_POST['confirmNewPassword']);
}
validate();
/*echo $_POST['email'] . "<br/>";
echo $_POST['newPassword'] . "<br/>";
echo $_POST['confirmNewPassword'];*/
?>
