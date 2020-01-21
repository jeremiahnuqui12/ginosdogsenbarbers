<?php
require 'config.php';
if(!isset($_GET['id'])){
  header("Location: " . $_SERVER['HTTP_REFERER']);
}
try {
  $delete = getConnection()->prepare("UPDATE `appointment_details` SET `status`=:status WHERE `id`=:id");
  $delete->execute(
    array(
      ":status" => "Cancelled By Customer",
      ":id" => $_GET['id']
    )
  );
  header("Location: ../account/myappointment.php?appointment=cancel");
} catch (Exception $e) {
  echo $e->getMessage();
}


?>
