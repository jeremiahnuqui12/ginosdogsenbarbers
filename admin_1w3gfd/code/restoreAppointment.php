<?php
require 'config.php';
if(!isset($_GET['id'])){
  header("Location: " . $_SERVER['HTTP_REFERER']);
}
try {
  $delete = getConnection()->prepare("UPDATE `appointment_details` SET `status`=:status WHERE `id`=:id");
  $delete->execute(
    array(
      ":status" => "Waiting for Approval",
      ":id" => $_GET['id']
    )
  );
  recordActivity("Appointment Id: " . $_GET['id'] . "has been restored");
  header("Location: ../appointment/?restore=success");
} catch (Exception $e) {
  echo $e->getMessage();
}
?>
