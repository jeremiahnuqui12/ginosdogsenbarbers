<?php

require '../code/config.php';

if (empty($_GET['id'])) {
  header("Location: ../appointment/");
}
try {
  $reservationDetails = array();
  $query = getConnection()->prepare("SELECT
    `customer_order`.`contact_number` AS `contactNumber`,
    `product`.`name` AS `productName`,
    `customer_order`.`quantity` AS `quantity`,
    `product`.`price` AS `productPrice`,
    `customer_order`.`quantity` * `product`.`price` AS `totalPrice`,
    `customer_order`.`status` AS `orderStatus`,
    `customer_order`.`reserved_at` AS `dateReserved`,
    `customer_order`.`reservation_date` AS `pickUpDate`,
    `customer_order`.`email_verified` AS `orderEmailVerified`
    FROM `customer_order`
    INNER JOIN `product` ON `product`.`id`=`customer_order`.`product_id`
    WHERE `customer_order`.`id` = :id
  ");
  $query->bindParam(":id", $_GET['id']);
  $query->execute();
  $data = $query->fetch();
  //-------------------------------------------
  array_push($reservationDetails, $data['contactNumber']);
  array_push($reservationDetails, date('F d, Y h:i a', strtotime($data['dateReserved'])));
  array_push($reservationDetails, date('F d, Y h:i a', strtotime($data['pickUpDate'])));
  array_push($reservationDetails, $data['productName']);
  array_push($reservationDetails, $data['quantity']);
  array_push($reservationDetails, $data['productPrice']);
  array_push($reservationDetails, $data['totalPrice']);
  array_push($reservationDetails, $data['orderStatus']);
  array_push($reservationDetails, $data['orderEmailVerified']);
  echo json_encode($reservationDetails);
  //------------------------------------------------
} catch (Exception $e) {

}
