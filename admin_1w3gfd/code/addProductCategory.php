<?php
require 'config.php';
if(isset($_POST['newCategory'])){
  checkIfCategoryExist();
} else {
  header("Location: " . getWebRoot());
}
function checkIfCategoryExist() {
  try {
    $query = getConnection()->prepare("SELECT COUNT(*) FROM `product_categories` WHERE `name`=:name");
    $query->bindParam(":name", trim($_POST['newCategory']));
    $query->execute();
    $result = $query->fetch()[0];
    if ($result == 0) {
      addCategory();
    } else {
      header("Location: ../products/?addNewCategory=exist");
    }
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}
function addCategory() {
  try {
    $query = getConnection()->prepare("INSERT INTO `product_categories`(
      `name`,
      `date_added`,
      `added_by`
    ) VALUES (
      :name,
      :dateAdded,
      :addedBy
    )");
    $query->execute(
      array(
        ":name" => $_POST['newCategory'],
        ":dateAdded" => getTimeStamp(),
        ":addedBy" => getSessionAdminId()
      )
    );
    recordActivity("New category has been added. Category name: " . $_POST['newCategory']);
    header("Location: ../products/?addNewCategory=success");
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}
