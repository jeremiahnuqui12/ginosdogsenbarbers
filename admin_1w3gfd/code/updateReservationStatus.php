<?php
require 'config.php';
require '../../vendor/autoload.php';
if(!isset($_GET['id'])){
  header("Location: ../");
}
if (!isset($_GET['status'])) {
  header("Location: ../");
}
try {
  $delete = getConnection()->prepare("UPDATE `customer_order` SET `status`=:status WHERE `id`=:id");
  $delete->execute(
    array(
      ":status" => $_GET['status'],
      ":id" => $_GET['id']
    )
  );
  sendSMSNotification();
  recordActivity("Reservation for " . getReservationDate() . " has been updated to " . $_GET['status']);
  header("Location: ../appointment/?reservation=" . $_GET['status'] . "&#productReservation");
} catch (Exception $e) {
  echo $e->getMessage();
}
function getReservationDate() {
  try {
    $query = getConnection()->prepare("SELECT `reservation_date` FROM `customer_order` WHERE `id`=:id");
    $query->bindParam(":id", $_GET['id']);
    $query->execute();
    return date('F d, Y h:i a', strtotime($query->fetch()[0]));
  } catch (Exception $e) {

  }
}
function getContactNumber() {
  try {
    $query = getConnection()->prepare("SELECT `contact_number` FROM `customer_order` WHERE `id`=:id");
    $query->bindParam(":id", $_GET['id']);
    $query->execute();
    return $query->fetch()[0];
  } catch (Exception $e) {

  }
}
function sendSMSNotification(){
  try {
    $apiKey = 'b20746ae';
    $apiSecret = 'yfBh8URZdITc5UCT';
    $basic  = new \Nexmo\Client\Credentials\Basic($apiKey, $apiSecret);
    $client = new \Nexmo\Client($basic);

    $sentTo = getContactNumber();
    $from = "GinosDogsEnBarbers";
    $messageDetails = "GinosDogsEnBarbers.
Your reservation for " . getReservationDate() . " has been updated to " . $_GET['status'];
    /*$message = $client->message()->send([
        'to' => $sentTo,
        'from' => $from,
        'text' => $messageDetails
    ]);*/
  } catch (Exception $e) {
    echo "Send SMS Notification Error: " . $e->getMessage();
  }
}
