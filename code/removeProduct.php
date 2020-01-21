<?php
require 'config.php';

if (empty($_GET['id'])) {
  header("Location: ../account/cart.php");
}

try {
  $query = getConnection()->prepare("DELETE FROM `customer_order` WHERE `id`=:id");
  $query->execute(
    array(
      ":id" => $_GET['id']
    )
  );
  header("Location: ../account/cart.php?remove=success");
} catch (Exception $e) {
  echo "Error Removing Product:" . $e->getMessage();
}





?>
