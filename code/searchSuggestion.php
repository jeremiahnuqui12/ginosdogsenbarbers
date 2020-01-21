<?php
require 'config.php';

try {
    $query = getConnection()->prepare("SELECT * FROM `product` WHERE `name`LIKE :name AND `status`=:status");
    $query->execute(
      array(
        ":name" => $_GET['id'] . "%",
        ":status" => "Active"
      )
    );
    if ($query->rowCount() == 0) {
      echo "<li>No Results Found</li>";
    } else {
      while ($row = $query->fetch()) {
        echo "<li onclick=window.location.href='" . getWebroot() . "/products/details.php?id=" . $row['id'] . "'>" . $row['name'] . "</li>";
      }
    }
} catch (Exception $e) {
  echo "Search Error: " . $e->getMessage();
}
