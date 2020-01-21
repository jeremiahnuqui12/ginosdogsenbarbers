<?php

require 'config.php';

if (isset($_GET['id'])) {
  checkIfHasActiveProduct();
} else {
  header("Location: " . getWebRoot());
}
function deleteCategory() {
  try {
    $categoryName = getCategoryName();
    $query = getConnection()->prepare("DELETE FROM `product_categories` WHERE `id`=:id");
    $query->bindParam(":id", $_GET['id']);
    $query->execute();
    recordActivity("Product category `" . $categoryName . "` has been deleted");
    header("Location: ../products/?deleteCategory=success");
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
function getCategoryName() {
  try {
    $query = getConnection()->prepare("SELECT `name` FROM `product_categories` WHERE `id`=:id");
    $query->bindParam(":id", $_GET['id']);
    $query->execute();
    return $query->fetch()['name'];
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
function checkIfHasActiveProduct(){
  try {
    $query = getConnection()->prepare("SELECT COUNT(*) FROM `product` WHERE `category`=:id");
    $query->bindParam(":id", $_GET['id']);
    $query->execute();
    $result = $query->fetch()[0];
    if ($result == 0) {
      deleteCategory();
    } else {
      header("Location: ../products/?deleteCategory=failed");
    }
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
