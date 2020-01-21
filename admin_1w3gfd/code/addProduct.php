<?php
require 'config.php';
/*if (!isset($_FILES['ProductImage']) || $_FILES['ProductImage']['error'] == UPLOAD_ERR_NO_FILE) {
  echo '<script>alert("Choose an Image File");window.location.back();</script>';
} else {
  if (getimagesize($_FILES['ProductImage']['tmp_name']) == false){
   echo '<script>alert("Choose an Image File");window.location.back();</script>';
  } else {
    $image = addslashes($_FILES['ProductImage']['tmp_name']);
    $name = addslashes($_FILES['ProductImage']['name']);
    $image = file_get_contents($image);
    $image = base64_encode($image);
    uploadData($name, $image, $connection, $username);
  }
}*/
function validate(){
  if (!isset($_FILES['addProductImage']) || $_FILES['addProductImage']['error'] == UPLOAD_ERR_NO_FILE) {
    header("Location: ../products/?image=invalid");
  } else {
    if (getimagesize($_FILES['addProductImage']['tmp_name']) == false){
     header("Location: ../products/?imagesize=invalid");
   } else {
     addProduct();
   }
  }
}
function addProduct(){
  try {
    $addProduct = getConnection()->prepare("INSERT INTO `product`(
      `image`,
      `name`,
      `category`,
      `description`,
      `price`,
      `added_by`,
      `date_added`,
      `status`
    ) VALUES (
      :image,
      :name,
      :category,
      :description,
      :price,
      :addedBy,
      :dateAdded,
      :status
    )");
    $addProduct->execute(
      array(
        ":image" => getImage(),
        ":name"=> getProductName(),
        ":category" => getProductCategory(),
        ":description"=> getProductDescription(),
        ":price"=> getProductPrice(),
        ":addedBy" => getSessionAdminId(),
        ":dateAdded"=> getTimeStamp(),
        ":status"=> "Active"
      )
    );
    updateStocks();
    recordActivity("New product has been added. Product name: " . getProductName());
    header("Location: ../products?add=success");
  } catch (Exception $e) {

  }
}
function updateStocks(){
  try {
    $query = getConnection()->prepare("INSERT INTO `product_stocks`(
        `product_id`,
        `stocks_available`,
        `stocks_reserve`
      ) VALUES (
        :productId,
        :stocksAvailable,
        :stocksReserve
      )
    ");
    $query->execute(
      array(
        ":productId" => getProductId(),
        ":stocksAvailable" => $_POST['stocksAvailable'],
        ":stocksReserve" => "0"
      )
    );
  } catch (Exception $e) {

  }

}
function getProductId() {
  try {
    $query = getConnection()->prepare("SELECT * FROM `product` WHERE `date_added`=:dateAdded");
    $query->execute(
      array(
        ":dateAdded" => getTimeStamp()
      )
    );
    return $query->fetch()['id'];
  } catch (Exception $e) {

  }
}
function getImage(){
  $image = addslashes($_FILES['addProductImage']['tmp_name']);
  $name = addslashes($_FILES['addProductImage']['name']);
  $image = file_get_contents($image);
  $image = base64_encode($image);
  return $image;
}
function getProductName(){
  return $_POST['productName'];
}
function getProductDescription(){
  return $_POST['description'];
}
function getProductPrice(){
  return $_POST['price'];
}
function getProductCategory(){
  return $_POST['category'];
}
validate();
?>
