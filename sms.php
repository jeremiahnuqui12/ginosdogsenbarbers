<?php
require 'vendor/autoload.php';
try {
    $apiKey = 'b20746ae';
    $apiSecret = 'yfBh8URZdITc5UCT';
    $basic  = new \Nexmo\Client\Credentials\Basic($apiKey, $apiSecret);
    $client = new \Nexmo\Client($basic);

    $sentTo = '639081999619';
    $from = "NXSMS";
    $messageDetails = "GinosDogsEnBarbers";
    $message = $client->message()->send([
        'to' => $sentTo,
        'from' => $from,
        'text' => $messageDetails
    ]);
    //header("Location: ../account/myreservation.php?appointment=success");
  } catch (Exception $e) {
    echo "Send SMS Notification Error: " . $e->getMessage();
  }
  /*try {
    ob_start();
	$apiKey = "MsvN0-TuTCWukjIU-Uc8JA==";
    $sentTo = "639478117523";
    $message = "GinosDogsEnBarbers.
Thank you for your choosing us for Product Reservation.
You can view your Reservation in your Email.";
    $message = rawurlencode($message);
    $link = "https://platform.clickatell.com/messages/http/send?apiKey=" . $apiKey . "&to=" . $sentTo . "&content=" . $message;
    $callurl = curl_init();
    curl_setopt($callurl , CURLOPT_URL, $link);
    curl_exec($callurl);
    curl_close($callurl);
  } catch (Exception $e) {
    echo "Send SMS Notification Error: " . $e->getMessage();
  }
  //--------------*/
  /*ob_start();
  $apiKey = "dYrw0-OjRx65dZUYKXJNFQ==";
  $sentTo = "639081999619";
  $message = "GinosDogsEnBarbers.
Thank you for your choosing us for Product Reservation.
You can view your Reservation in your Email.";
  $message = rawurlencode($message);
  $link = "https://platform.clickatell.com/messages/http/send?apiKey=" . $apiKey . "&to=" . $sentTo . "&content=" . $message;
  $callurl = curl_init();
  curl_setopt($callurl , CURLOPT_URL, $link);
  curl_exec($callurl);
  curl_close($callurl);
  ///*/
