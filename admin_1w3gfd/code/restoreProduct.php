<?php
require 'config.php';
if(!isset($_GET['id'])){
  header("Location: " . $_SERVER['HTTP_REFERER']);
}
try {
  $deleteProduct = getConnection()->prepare("UPDATE `product` SET `status`=:status WHERE `id`=:id");
  $deleteProduct->execute(
    array(
      ":status" => "Active",
      ":id" => $_GET['id']
    )
  );
  recordActivity("Product `" . getProductName() . "` has been restored");
  header("Location: ../products/?restore=success");
} catch (Exception $e) {

}

function getProductName() {
  try {
    $query = getConnection()->prepare("SELECT `name` FROM `product` WHERE `id`=:id");
    $query->bindParam(":id", $_GET['id']);
    $query->execute();
    return $query->fetch()[0];
  } catch (Exception $e) {

  }
}
?>
