<?php
require 'config.php';

if (empty($_GET['stocks'])) {
  header("Location: ../products/");
} if (empty($_GET['id'])) {
  header("Location: ../products/");
}

try {
  $query = getConnection()->prepare("UPDATE `product_stocks`
    SET `stocks_available`=`stocks_available`+:stocks
    WHERE `product_id`=:id");
    $query->execute(
      array(
        ":stocks" => $_GET['stocks'],
        ":id" => $_GET['id']
      )
    );
    header("Location: " . explode("?", $_SERVER['HTTP_REFERER'])[0] . "?stocksAdd=success");
} catch (Exception $e) {
  echo "Adding Stocks: " . $e->getMessage();
}
?>
