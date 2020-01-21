<?php
require 'config.php';
if(!isset($_GET['id'])){
  header("Location: " . $_SERVER['HTTP_REFERER']);
}
try {
  $delete = getConnection()->prepare("UPDATE `contactus_messages` SET `status`=:status WHERE `id`=:id");
  $delete->execute(
    array(
      ":status" => "Deleted",
      ":id" => $_GET['id']
    )
  );
  recordActivity("Message with id of " . $_GET['id'] . " has been deleted");
  header("Location: " . $_SERVER['HTTP_REFERER']);
} catch (Exception $e) {
  echo $e->getMessage();
}
?>
