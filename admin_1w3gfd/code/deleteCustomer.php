<?php
require 'config.php';


function checkUrl(){
  $x = explode("?", $_SERVER['HTTP_REFERER']);
  if ($x[0] == getWebRoot() . "/customer/") {
    deleteUser();
  } else {
    header("Location: " . getWebRoot() . "/unauthorized.php");
  }
}
function deleteUser(){
  try {
    $deleteQuery = getConnection()->prepare("UPDATE `customer_account`
      SET `status`='Deleted'
      WHERE `customer_id`=:id"
    );
    $deleteQuery->execute(
      array(
        ":id" => getCustomerId()
      )
    );
    recordActivity("Account of " . getCustomerName() . " has been blocked");
    header("Location: ../customer/?delete=success");
  } catch (Exception $e) {

  }
}
function getCustomerId(){
  if(isset($_GET['id'])){
    return $_GET['id'];
  } else {
    header("Location: " . getWebRoot() . "/unauthorized.php");
  }
}
function getCustomerName(){
  try {
    $query = getConnection()->prepare("SELECT `customer_name` FROM `customer_account` WHERE `customer_id`=:id");
    $query->bindParam(":id", getCustomerId());
    $query->execute();
    return $query->fetch()[0];
  } catch (Exception $e) {

  }
}
checkUrl();

?>
