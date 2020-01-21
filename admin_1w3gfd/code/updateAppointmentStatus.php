<?php
require 'config.php';
require '../../vendor/autoload.php';
if(!isset($_GET['id']) && !isset($_GET['status'])){
  header("Location: ../appointment");
}
try {
  $delete = getConnection()->prepare("UPDATE `appointment_details` SET `status`=:status WHERE `id`=:id");
  $delete->execute(
    array(
      ":status" => $_GET['status'],
      ":id" => $_GET['id']
    )
  );
  sendSMSNotification();
  recordActivity("Appointment for `" . getAppointmentDate() . "` has been updated to " . $_GET['status']);
  header("Location: ../appointment/?appointment=" . $_GET['status'] . "&#appointment-grooming");
} catch (Exception $e) {
  echo $e->getMessage();
}

function getAppointmentDate() {
  try {
    $query = getConnection()->prepare("SELECT `date`, `time` FROM `appointment_details` WHERE `id`=:id");
    $query->bindParam(":id", $_GET['id']);
    $query->execute();
    $result = $query->fetchAll();
    return date('F d, Y h:i a', strtotime($result[0][0] . " " . $result[0][1]));
  } catch (Exception $e) {

  }
}
function getContactNumber() {
  try {
    $query = getConnection()->prepare("SELECT `contact_number` FROM `appointment_customer_info` WHERE `appointment_id`=:id");
    $query->bindParam(":id", $_GET['id']);
    $query->execute();
    return $query->fetch()[0];
  } catch (Exception $e) {

  }
}
function sendSMSNotification(){
  /*try {
    ob_start();
    $apiKey = "dYrw0-OjRx65dZUYKXJNFQ==";
    $message = "GinosDogsEnBarbers.
Your appointment for `" . getAppointmentDate() . "` has been updated to " . $_GET['status'];
    $message = rawurlencode($message);
    $link = "https://platform.clickatell.com/messages/http/send?apiKey=" . $apiKey . "&to=" . getContactNumber() . "&content=" . $message;
    $callurl = curl_init();
    curl_setopt($callurl , CURLOPT_URL, $link);
    curl_exec($callurl);
    curl_close($callurl);
    end();
  } catch (Exception $e) {
    echo "Send SMS Notification Error: " . $e->getMessage();
  }
  //-----*/
  try {
    $apiKey = 'b20746ae';
    $apiSecret = 'yfBh8URZdITc5UCT';
    $basic  = new \Nexmo\Client\Credentials\Basic($apiKey, $apiSecret);
    $client = new \Nexmo\Client($basic);

    $sentTo = getContactNumber();
    $from = "GinosDogsEnBarbers";
    $messageDetails = "GinosDogsEnBarbers.
Your appointment for `" . getAppointmentDate() . "` has been updated to " . $_GET['status'];
    $message = $client->message()->send([
        'to' => $sentTo,
        'from' => $from,
        'text' => $messageDetails
    ]);
  } catch (Exception $e) {
    echo "Send SMS Notification Error: " . $e->getMessage();
  }
  //----------------------------------------------------------------------------
  /**/
}
