<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
require 'config.php';

if (empty($_POST['selected-product'])) {
  header("Location: ../account/cart.php");
}

if (empty($_POST['reservationDate'])) {
  header("Location: ../account/cart.php");
}
if (empty($_POST['reservationTime'])) {
  header("Location: ../account/cart.php");
}
function getProductDetails(){
  try {//echo implode(", ", $_POST['selected-product']);
    $selected = $_POST['selected-product'];
    $arrayMessage = array();
    $code = rand();
    for ($x=0; $x < count($_POST['selected-product']); $x++) {
      $query = getConnection()->prepare("SELECT
        `customer_order`.`id` AS `OrderId`,
        `product`.`id` AS `productId`,
        `product`.`image` AS `productImage`,
        `product`.`name` AS `productName`,
        `customer_order`.`quantity` AS `quantity`,
        `product`.`price` AS `productPrice`
        FROM `customer_order`
        INNER JOIN `product` ON `product`.`id` = `customer_order`.`product_id`
        WHERE `customer_order`.`status`= :status
        AND `customer_id`= :customerId
        AND `customer_order`.`id` = :idx"
      );
      $query->execute(
        array(
          ":status" => "On-Cart",
          ":customerId" => getSessionCustomerId(),
          ":idx" => $selected[$x]
        )
      );
      while ($row = $query->fetch()) {
        updateStatus($row['OrderId'], $code);
        updateStocks($row['productId'], $row['quantity']);
        array_push($arrayMessage, "<tr>
          <td style=\"border-bottom:1px solid gray;padding-top:2%;padding-bottom:2%;word-wrap: break-word;\">
            <span style=\"margin-left:10px;\">" . $row['productName'] . "</span>
          </td>
          <td style=\"border-bottom:1px solid gray;text-align: center;padding-top:2%;padding-bottom:2%;\">
            &#8369; <span>" . $row['productPrice'] . "</span>
          </td>
          <td style=\"border-bottom:1px solid gray;text-align: center;padding-top:2%;padding-bottom:2%;\">
            <span>" . $row['quantity'] . "</span>
          </td>
          <td style=\"border-bottom:1px solid gray;text-align: center;padding-top:2%;padding-bottom:2%;\">
            &#8369; <span>" . number_format((float)($row['productPrice'] * $row['quantity']), 2, '.', '') . "</span>
          </td>
        </tr>");
      }
    }
    getVerificationCode($code);
    emailMessage($arrayMessage, $code);
    sendSMSNotification();
    header("Location: ../account/myproductreserve.php?verify=no");
  } catch (Exception $e) {
    echo "Error" . $e->getMessage();
  }
}
function updateStocks($id, $quantity){
  try {
    $query = getConnection()->prepare("UPDATE `product_stocks`
      SET `stocks_available`=`stocks_available`-$quantity,
      `stocks_reserve`=`stocks_reserve`+$quantity
      WHERE `product_id`=:id
      ");
    $query->bindParam(":id", $id);
    $query->execute();
  } catch (Exception $e) {
    echo "Error in Updating Stocks: " . $e->getMessage();
  }
}
function updateStatus($id, $code){
  try {
    $query = getConnection()->prepare("UPDATE `customer_order`
      SET `status` = :status,
      `verification_code` = :code,
      `reservation_date` = :reservationDate,
      `contact_number` = :contactNumber
      WHERE `id` = :id
    ");
    $query->execute(
      array(
        ":reservationDate" => $_POST['reservationDate'] . " " . $_POST['reservationTime'],
        ":status" => "Pending",
        ":code" => getVerificationCode($code),
        ":contactNumber" => getContactNumber(),
        ":id" => $id
      )
    );
  } catch (Exception $e) {
    echo "Error in Updating Status: " . $e->getMessage();
  }
}
function getVerificationCode($code){
    return md5($code . getSessionEmail());
}
function totalPrice(){
  try {
    $query = getConnection()->prepare("SELECT
      SUM(`quantity`*`price`) AS `totalPrice`
    FROM `customer_order`
    INNER JOIN `product` ON `product`.`id`=`customer_order`.`product_id`
    WHERE `customer_id`=:id AND
    `customer_order`.`reservation_date` = :reservationDate
    ");
    $query->execute(
      array(
        ":id" => getSessionCustomerId(),
        ":reservationDate" => $_POST['reservationDate'] . " " . $_POST['reservationTime']
      )
    );
    return number_format((float)($query->fetch()[0]), 2, '.', '');
  } catch (Exception $e) {
    echo "Error in Total Price: " . $e->getMessage();
  }
}
function emailMessage($arrayMessage, $code){
  try {
    ob_start();
    $mail = new PHPMailer(true);
    SMTPServerSettings($mail);
    $message = "<div style=\"background-color:#fff; border:1px solid #a9a9a9;\">
      <div style=\"margin:10px;\">
        <span>Thank you for reservation</span>
      </div>
      <div style=\"margin:10px;\">
        <span><b>Billed To: </b>" . getSessionName() . "</span>
        <br/>
        <span><b>Pickup and payment date: </b>" . date('F d, Y', strtotime($_POST['reservationDate'])) . "</span>
        <br/>
        <span><b>Total amount payment: </b>&#8369; " . totalPrice() . "</span>
        <br/><br/>
        <span>Click this <a href=\"" . getWebroot() . "/code/verifyOrder.php?id=" . getVerificationCode($code) . "\">Link</a> to verify your reservation.</span>
      </div>
      <div style=\"margin:10px;\">
        <h2>Your reservation details</h2>
      </div>
      <div style=\"margin:10px;\">
        <table cellspacing=0 cellpadding=5>
          <thead style=\"border-collapse: collapse;\">
            <tr class=\"text-center\" style=\"border-bottom:1px solid gray;\">
              <th style=\"min-width:450px;\">Description</th>
              <th style=\"min-width:100px;\">Item Price</th>
              <th style=\"min-width:100px;\">Quantity</th>
              <th style=\"min-width:100px;\">Total Price</th>
            </tr>
          </thead>
          <tbody>";
          $message .= implode(" " ,$arrayMessage);
      $message .= "</tbody>
      </table>
      </div>
      <div style=\"margin:10px;\">
      In case of any questions, feel free to contact us at <a href=\"" . getWebroot() .  "/contact.php\">Contact Us</a>
      <br/>
      Ginos Dogs En Barbers.';
      </div>
    </div>";
    $subject = 'Product Reservation in Ginos Dogs En Barbers';
    $mail->addAddress(getSessionEmail());
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->send();
  } catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
  }
}
function sendSMSNotification(){
  /*try {
    ob_start();
    $apiKey = "dYrw0-OjRx65dZUYKXJNFQ==";
    $sentTo = getContactNumber();
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
  //*/
  try {
    $apiKey = 'b20746ae';
    $apiSecret = 'yfBh8URZdITc5UCT';
    $basic  = new \Nexmo\Client\Credentials\Basic($apiKey, $apiSecret);
    $client = new \Nexmo\Client($basic);

    $sentTo = getContactNumber();
    $from = "GinosDogsEnBarbers";
    $messageDetails = "GinosDogsEnBarbers.
You have reservation on " . date('F d, Y - h:i a', strtotime($_POST['reservationDate'] . " " . $_POST['reservationTime'])) . ".
Thank you for your choosing us for Product Reservation.
You can view your Reservation in your Email.";
    $message = $client->message()->send([
        'to' => $sentTo,
        'from' => $from,
        'text' => $messageDetails
    ]);
    header("Location: ../account/myproductreserve.php?verify=no");
  } catch (Exception $e) {
    echo "Send SMS Notification Error: " . $e->getMessage();
  }
  //----------------------------------------------------*/
}
if (strtotime($_POST['reservationDate']) < date("Y-m-d", strtotime(date("Y-m-d") . '+1 year'))) {
  header("Location: ../account/checkout.php?invalidDate=tooHigh");
} else {
  getProductDetails();
}

function getContactNumber(){
  $x = "63" . substr($_POST['contact_number'],5,3) . substr($_POST['contact_number'],10,3) . substr($_POST['contact_number'],14,4);
  if (strlen($x) == 12) {
    return $x;
  } else {
    header("Location: ../account/checkout.php?contact=invalid");
  }
}
