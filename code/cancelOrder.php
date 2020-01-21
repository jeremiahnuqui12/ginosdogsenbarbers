<?php
require 'config.php';
if(!isset($_GET['id'])) {
  header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
  getQuantity();
}
function getQuantity() {
  try {
    $query = getConnection()->prepare("SELECT
      `product_id`,
      `quantity`
      FROM `customer_order`
      WHERE `id`=:id");
    $query->bindParam(":id", $_GET['id']);
    $query->execute();
    $data = $query->fetchAll();
    returnStocks(
      $data[0]['product_id'],
      $data[0]['quantity']
    );
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}
function deleteItem() {
  try {
    $delete = getConnection()->prepare("UPDATE `customer_order` SET `status`=:status WHERE `id`=:id");
    $delete->execute(
      array(
        ":status" => "Cancelled By Customer",
        ":id" => $_GET['id']
      )
    );
    header("Location: ../account/myproductreserve.php?cancelled=success");
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}
function returnStocks($productId, $quantity){
  try {
    $query = getConnection()->prepare("UPDATE `product_stocks`
      SET `stocks_available`= :stocks + `stocks_available`
      WHERE `product_id`=:productId");
    $query->execute(
      array(
        ":stocks" => $quantity,
        ":productId" => $productId
      )
    );
    deleteItem();
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}


?>
