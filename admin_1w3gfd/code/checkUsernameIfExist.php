<?php
require 'config.php';
function checkIfIsset(){
  if (empty(getUsername())) {
    echo "<span class=\"text-danger\">Required</span>";
  } else if(!preg_match("/^[a-zA-Z0-9]+$/", getUsername())) {
    echo "<span class=\"text-danger\">Invalid Username</span>";
  } else if(strlen(getUsername()) <= 6){
    echo "<span class=\"text-danger\">Username must greater than 6 characters</span>";
  } else if(strlen(getUsername()) >= 20){
    echo "<span class=\"text-danger\">Username must greater than 6 characters</span>";
  } else {
    checkUsernameIfExist();
  }
}
function checkUsernameIfExist(){
  try {
    $checkIfExist = getConnection()->prepare("SELECT COUNT(*) FROM`admin_user` WHERE `username`=:checkUsername");
    $checkIfExist->execute(
      array(
        ":checkUsername" => getUsername()
      )
    );
    $countResult = $checkIfExist->fetch();
    if ($countResult[0] > 0) {
      echo "<span class=\"text-danger\">Username Exist</span>";
    } else if($countResult[0] == 0) {
      echo "<span class=\"text-success\">Username Available</span>";
    }
  } catch (Exception $e) {
    echo "Check If User Exist Error: " . $e->getMessage();
  }
}
function getUsername(){
  return $_GET['id'];
}
checkIfIsset();



?>
