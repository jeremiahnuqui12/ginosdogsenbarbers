<?php
require 'config.php';
function validateData(){
  $error = array();
  if (!empty(getFirstName())) {
    if(!preg_match("/^[a-zA-Z\ ]+$/", getFirstName())){
      array_push($error, "1"); // 1="Invalid Character First Name";
    }
  }else {
      array_push($error, "1.1");// 1.1="First Name is required";
  }
  if (!empty(getLastName())) {
    if(!preg_match("/^[a-zA-Z\ ]+$/", getLastName())){
      array_push($error, "2"); // 2="Invalid Last Name";
    }
  }else {
      array_push($error, "2.1");
  }
  if (!empty(getEmailAddress())) {
    if (!filter_var(getEmailAddress(), FILTER_VALIDATE_EMAIL)) {
      array_push($error, "3"); // 3="Invalid Email";
    }
  } else {
    array_push($error, "3.1"); //3.1 == "EMail is required"
  }
  if (!empty(getUsername())) {
    if(!preg_match("/^[a-zA-Z0-9]+$/", getUsername())) {
      array_push($error, "4"); //4="Invalid Username";
    }else {
      checkUsernameIfExist();
    }
  } else {
    array_push($error,"4.1");
  }
  if (empty(getTemporaryPassword())) {
    array_push($error, "5"); //5="Password is Required"
  }
  if (empty(getRoles())) {
    array_push($error, "6");//6=Permission is required
  }
  if(count($error)>0){
    getError($error);
  }else {
    addUser();
  }
}
function getError($error){
  $x = implode(",", $error);
  header("Location: ../settings/admins.php?modal=1&error=$x");
}
//----------------------------------------------------//
//----------------------------------------------------//
function addUser() {
  try {
    $queryAddUser = getConnection()->prepare("INSERT INTO `admin_user`(
      `first_name`,
      `last_name`,
      `email_address`,
      `username`,
      `temp_password`,
      `password`,
      `role_value`,
      `created_at`,
      `status`
    ) VALUES (
      :firstname,
      :lastname,
      :email,
      :username,
      :temp_password,
      :password,
      :role_value,
      :createdAt,
      :status
    );");
      $queryAddUser->execute(
        array(
          ":firstname" => getFirstName(),
          ":lastname" => getLastName(),
          ":email" => getEmailAddress(),
          ":username" => getUsername(),
          ":temp_password" => getTemporaryPassword(),
          ":password" => getTemporaryPassword(),
          ":role_value" => getRoles(),
          ":createdAt" => getTimeStamp(),
          ":status" => "Active"
        )
      );
      recordLog();
      header("Location: ../settings/admins.php?success");
  } catch (Exception $e) {
    echo "Registering New Admin Error: " . $e->getMessage();
  }
}
//----------------------------------------------------//
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
      header("Location: ../settings/admins.php?modal=1&exist=1");
    }
  } catch (Exception $e) {
    echo "Check If User Exist Error: " . $e->getMessage();
  }
}
//----------------------------------------------------//
function recordLog(){
  $logDescription = "Add New User:" . getUsername();
  try {
    $query = getConnection()->prepare("INSERT INTO `admin_log`(
        `admin_id`,
        `log_description`,
        `log_time`
      ) VALUES (
        :adminId,
        :logDescription,
        :logTime
      )
    ");
    $query->execute(
      array(
        ":adminId" => getSessionAdminId(),
        ":logDescription" => $logDescription,
        "logTime" => getTimeStamp()
      )
    );
  } catch (Exception $e) {
    echo "Record Activity Error: " . $e->getMessage();
  }
}
//----------------------------------------------------//
//----------------------------------------------------//
validateData();

function getFirstName(){
  return strip_tags($_POST['firstName']);
}
function getLastName(){
  return strip_tags($_POST['lastName']);
}
function getEmailAddress(){
  return strip_tags($_POST['emailAddress']);
}
function getUsername(){
  return strip_tags($_POST['username']);
}
function getTemporaryPassword(){
  return md5(strip_tags($_POST['autogen-password']));
}
function getRoles(){
  return implode("----", $_POST['roles']);
}
?>
