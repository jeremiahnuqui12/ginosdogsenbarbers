<?php
require 'config.php';
if(!isset($_GET['id'])){
  header("Location: ../");
}
if(!isset($_GET['price'])){
  header("Location: ../");
}
try {
  $delete = getConnection()->prepare("UPDATE `appointment_details`
    SET `status`=:status,
    `payment`=:price
    WHERE `id`=:id");
  $delete->execute(
    array(
      ":status" => "Grooming Done",
      ":price" => $_GET['price'],
      ":id" => $_GET['id']
    )
  );
  header("Location: " . $_SERVER['HTTP_REFERER']);
} catch (Exception $e) {
  echo $e->getMessage();
}
?>
