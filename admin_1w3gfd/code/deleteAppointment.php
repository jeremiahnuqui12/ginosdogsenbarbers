<?php
require 'config.php';
if(!isset($_GET['id'])){
  header("Location: " . $_SERVER['HTTP_REFERER']);
}
try {
  $delete = getConnection()->prepare("UPDATE `appointment_details` SET `status`=:status WHERE `id`=:id");
  $delete->execute(
    array(
      ":status" => "Deleted",
      ":id" => $_GET['id']
    )
  );
  recordActivity("Delete Appointment: Appointment Id: " . $_GET['id']);
  header("Location: " . $_SERVER['HTTP_REFERER']);
} catch (Exception $e) {
  echo $e->getMessage();
}
?>
