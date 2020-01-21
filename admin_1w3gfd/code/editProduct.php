<?php
require 'config.php';
function validate(){
  if (empty(getProductName())) {
    header("Location: ../products");
  }
  if (empty(getProductDescription())) {
    // code...
  }
  if (empty(getProductPrice())) {
    // code...
  }
  updateProduct();
}

function updateProduct(){
  try {
    $query = getConnection()->prepare("UPDATE `product`
      SET `name`=:name,
      `description`=:description,
      `price`=:price
      WHERE `id`=:id");
      $query->execute(
        array(
          ":name" => getProductName(),
          ":description" => getProductDescription(),
          ":price" => getProductPrice(),
          ":id" => getProductId()
        )
      );
      recordActivity("Product " . getProductName() . " has been updated");
      header("Location: ../products/?edit=success");
  } catch (Exception $e) {
    echo "Updating Product Failed: " . $e->getMessage();
  }

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
validate();
function getProductName(){
  return $_POST['editProductName'];
}
function getProductDescription(){
  return $_POST['editProductDescription'];
}
function getProductPrice(){
  return $_POST['editProductPrice'];
}
function getProductId(){
  return $_POST['productId'];
}
?>
