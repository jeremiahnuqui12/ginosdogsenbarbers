<?php
require 'config.php';
if (empty(getSessionCustomerId())) {
  echo 'needToSignIn';
} else{
  validateAddToCart();
}
function validateAddToCart(){
  if (isset($_GET['idx'])) {
    checkProductIfExist();
  } else {
    header("Location: " . getWebRoot());
  }
}
function addToCart(){
  try {
    $query = getConnection()->prepare("INSERT INTO `customer_order`(
      `customer_id`,
      `product_id`,
      `quantity`,
      `status`,
      `reserved_at`,
      `verification_code`,
      `email_verified`,
      `verified_at`
    ) VALUES (
      :customerId,
      :productId,
      :quantity,
      :status,
      :reservedAt,
      :verificationCode,
      :email_verified,
      :verified_at
    )");
    $query->execute(
      array(
        ":customerId" => getSessionCustomerId(),
        ":productId" => $_GET['idx'],
        ":quantity" => "1",
        ":status" => "On-Cart",
        ":reservedAt" => getTimeStamp(),
        ":verificationCode" => "",
        ":email_verified" => "No",
        ":verified_at" => ""
      )
    );
    echo "Product added to cart";
  } catch (Exception $e) {
    echo "Add to cart product Error: " . $e->getMessage();
  }
}
function checkProductIfExist(){
  try {
    $query = getConnection()->prepare("SELECT `quantity` FROM `customer_order`
      WHERE `customer_id` = :customerId AND
      `product_id` = :productId AND
      `status` = :status
      ");
      $query->execute(
        array(
          ":customerId" => getSessionCustomerId(),
          ":productId" => $_GET['idx'],
          ":status" => "On-Cart"
        )
      );
      if ($query->rowCount() == 0) {
        addToCart();
      } else {
        updateStocks((int)$query->fetch()[0]);
      }
  } catch (Exception $e) {
    echo "Check if product if exist Error: " . $e->getMessage();
  }
}
/*function cartCountReserved1(){
  global $return;
  try {
    $query = getConnection()->prepare("SELECT count(*) FROM `customer_order`
      WHERE `customer_id`=:customerId AND `status`=:status
    ");
    $query->execute(
      array(
        ":customerId" => getSessionCustomerId(),
        ":status" => "On-Cart"
      )
    );
    array_push($return, $query->fetch()[0]);// Add New Product
  } catch (Exception $e) {
    echo "Item Count in Cart Error: " . $e->getMessage();
  }
}*/
function updateStocks($quantity){
  if (isset($_GET['sign'])) {
    if ($_GET['sign'] == "add") {
      if ($quantity > 9) {
        echo "Max quantity reached";
      } else {
        addStocks();
      }
    } elseif ($_GET['sign'] == "minus") {
      if ($quantity > 1) {
        minusStocks();
      } else {
        //echo "zxczxczxc";
      }
    }
  } else {
    addStocks();
  }
}
function addStocks(){
  try {
    if (stocksOnCart() >= checkStocksLeft()) {
      echo "Out of Stocks";
    } else {
      $query = getConnection()->prepare("UPDATE `customer_order`
        SET `quantity`= `quantity` + 1
        WHERE `customer_id` = :customerId AND
        `product_id` = :productId");
        $query->execute(
          array(
            ":customerId" => getSessionCustomerId(),
            ":productId" => $_GET['idx']
          )
        );
        echo "Add Stocks";//Add Stocks
    }
  } catch (Exception $e) {
    echo "Adding Stocks Error: " . $e->getMessage();
  }
}
function minusStocks(){
  global $return;
  try {
    $query = getConnection()->prepare("UPDATE `customer_order`
      SET `quantity`=`quantity`-1
      WHERE `customer_id` = :customerId AND
      `product_id` = :productId");
      $query->execute(
        array(
          ":customerId" => getSessionCustomerId(),
          ":productId" => $_GET['idx']
        )
      );
      echo "Minus Stocks";//Minus Stocks
  } catch (Exception $e) {
    echo "Minus Stocks Error: " . $e->getMessage();
  }
}
function stocksOnCart() {
  try {
    $query = getConnection()->prepare("SELECT SUM(`quantity`) FROM `customer_order`
      WHERE `product_id`=:productId
      AND `status` =:status
      AND `customer_id` = :customerId"
    );
    $query->execute(
      array(
        ":productId" => $_GET['idx'],
        ":status" => "On-Cart",
        ":customerId" => getSessionCustomerId()
      )
    );
    $stocks = $query->fetch()[0];
    if (empty($stocks)) {
      return '1';
    } else {
      return $stocks;
    }
  } catch (Exception $e) {

  }
}
function checkStocksLeft() {
  try {
    $query = getConnection()->prepare("SELECT `stocks_available` FROM `product_stocks` WHERE `product_id`=:id");
    $query->bindParam(":id", $_GET['idx']);
    $query->execute();
    return $query->fetch()[0];
  } catch (Exception $e) {
    echo "Check Stocks Error: " . $e->getMessage();
  }
}
