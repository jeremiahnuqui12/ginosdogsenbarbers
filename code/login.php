<?php
include_once 'config.php';
function getErrors($error) {
  for ($x=0; $x < count($error) ; $x++) {
    echo $error[$x] . "<br/>";
  }
  $x = implode(",", $error);
  header("Location: ../account/?signinError= " . $x);
}
function validate(){
  $error = array();
  if (explode('?', $_SERVER['HTTP_REFERER'], 2)[0] != getWebroot() . "/account/") {
    header("Location: ../account/?signin=1");
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
  if (count($error) > 0) {
    getErrors($error);
  } else {
    checkIfEmailExist();
  }
}
function setSession($name, $id, $createdAt) {
  $_SESSION['7a13ce2a07525b4fd46ebc0226706fab'] = ucfirst($name); //Customer Name
  $_SESSION['cb24373bb88538168c8e839069491f18'] = $id;// Customer Id
  $_SESSION['cfecb706488b9b67825b14c6792f0bcc'] = getEmail();// Customer Email
  $_SESSION['b8620d1a5676a832c2c9f8fd387f0e8a'] = "Ginos Account";// account type
  $_SESSION['e26ad384feaee8a3138677a965f539a8'] = date('F d, Y h:i a', strtotime($createdAt));//Date Registered
  if (isset($_POST['appDate']) && isset($_POST['appTime'])) {
    header("Location: ../services/grooming.php?date=" . $_POST['appDate'] . "&time=" . $_POST['appTime']);
  } elseif (isset($_POST['product_id'])) {
    header("Location: ../products/details.php?id=" . $_POST['product_id']);
  } else {
    header("Location: ../");
  }
}
function checkIfEmailExist() {
  try {
    $checkIfExist = getConnection()->prepare("SELECT * FROM `customer_account`
      WHERE BINARY `email`=:email");
    $checkIfExist->execute(
      array(
        ":email" => getEmail()
      )
    );
    $countResult = $checkIfExist->fetch();
    if ($checkIfExist->rowCount() > 0) {
      checkPasswordIfCorrect();
    } else {
      header("Location: ../account/?email=0");
    }
  } catch (Exception $e) {
    echo "Check If Email Exist Error: " . $e->getMessage();
  }
}
function checkPasswordIfCorrect(){
  try {
    $checkIfExist = getConnection()->prepare("SELECT * FROM `customer_account`
      WHERE BINARY `email`=:email
      AND BINARY `password`=:password
      AND `status`=:status");
    $checkIfExist->execute(
      array(
        ":email" => getEmail(),
        ":password" => getPassword(),
        ":status" => 'Active'
      )
    );
    $countResult = $checkIfExist->fetch();
    if ($checkIfExist->rowCount() > 0) {
      setSession($countResult['customer_name'], $countResult['customer_id'], $countResult['created_at']);
    } else {
      header("Location: ../account/?password=0");
    }
  } catch (Exception $e) {
    echo "Check If Password is Correct Error: " . $e->getMessage();
  }
}
//---get form Value
function getEmail(){
  return strip_tags($_POST['email']);
}
function getPassword(){
  return md5(strip_tags($_POST['password']));
}
validate();
?>
