<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
require 'config.php';
//--------------------------------------
/*list($year, $month, $day) = explode('-', $_POST['lastRabiesVaccDate']);
echo checkdate($month, $day, $year);*/

function validate(){
  $error = array();
  /*if ($_SERVER['HTTP_REFERER'] != getWebroot() . "/services/grooming.php") {
    header("Location: " . getWebroot());
  }*/
  if (empty($_SESSION['7a13ce2a07525b4fd46ebc0226706fab'])) {
    header("Location: " . getWebroot());
  }
  //--------------------------------------------
  if (empty(getAppointmentDate())) {
    array_push($error, "date=required");//
  } elseif (!strtotime(getAppointmentDate())) {
    array_push($error, "date=Invalid Date. Format(YYYY-MM-DD)");//
  }
  if (empty(getAppointmentTime())) {
    array_push($error, "time=required");//
  } elseif (!strtotime(getAppointmentDate())) {
    array_push($error, "time=Invalid Time. Format(HH:MM AM/PM)");//
  }
  //----------------------------------------
  if (empty(getContactNumber())) {
    array_push($error, "contact=required");
  } else if (strlen(getContactNumber()) != 12) {
    array_push($error, "contact=invalid");
  }
  if (empty(getPetBreed())) {
    array_push($error, "breed=required"); //1 = Pet Breed is Required
  }
  if (empty(getPetName())) {
    array_push($error, "petname=required"); //1 = Pet Breed is Required
  }
  if (empty(getPetSize())) {
    array_push($error, "petsize=required");
  }
  if (empty(getPetGender())) {
    array_push($error, "petgender=required"); //1 = Pet Breed is Required
  }
  if (empty(getPetAge())) {
    array_push($error, "petage=required");
  }
  //------------------------------------------
  if (empty(getLastRabiesVaccinationDate())) {
    array_push($error, "lastRabiesVaccDate=required");
  } elseif (!strtotime(getLastRabiesVaccinationDate())) {
    array_push($error, "lastRabiesVaccDate=Invalid Date. Format(YYYY-MM-DD)");
  }
  if (empty(getLastVaccinationDate())) {
    array_push($error, "lastVaccDate=required");
  } elseif (!strtotime(getLastRabiesVaccinationDate())) {
    array_push($error, "lastVaccDate=Invalid Date. Format(YYYY-MM-DD)");
  }

  //-----------------------------------
  if (count($error) > 0) {
    getErrors($error);
  } else {
    getReservation();
  }
}
function getReservation() {
  try {
    $insertApp = getConnection()->prepare("INSERT INTO `appointment_details`(
      `customer_id`,
      `date`,
      `time`,
      `reserved_at`,
      `status`,
      `verification_code`,
      `email_verified`
    ) VALUES (
      :customerId,
      :app_date,
      :app_time,
      :reservedAt,
      :status,
      :verification_code,
      :email_verified
    )");
      $insertApp->execute(
        array(
          ":customerId" => $_SESSION['cb24373bb88538168c8e839069491f18'],
          //":app_type" => getAppointmentType(),
          ":app_date" => getAppointmentDate(),
          ":app_time" => getAppointmentTime(),
          ":reservedAt"=> getTimeStamp(),
          ":status" => "Pending",
          ":verification_code" => verificationCode(),
          ":email_verified" => "No"
        )
      );
      getAppDetails();
  } catch (Exception $e) {
    echo "getReservation Function Error: " . $e->getMessage();
  }
}
function getAppDetails(){
  try {
    $appId = getConnection()->prepare("INSERT INTO `appointment_customer_info`(
      `appointment_id`,
      `contact_number`,
      `pet_name`,
      `pet_breed`,
      `pet_gender`,
      `pet_age`,
      `pet_size`,
      `last_rabies_vaccination`,
      `last_vaccination`
    ) VALUES (
      :app_id,
      :contactNumber,
      :petName,
      :petBreed,
      :petGender,
      :petAge,
      :petSize,
      :lastRabies,
      :lastVaccination
    )");
    $appId->execute(
      array(
        ":app_id" => getAppId(),
        ":contactNumber"=>getContactNumber(),
        ":petName" => getPetName(),
        ":petBreed" => getPetBreed(),
        ":petGender" => getPetGender(),
        ":petAge" => getPetAge(),
        ":petSize" => getPetSize(),
        ":lastRabies" => getLastRabiesVaccinationDate(),
        ":lastVaccination" => getLastVaccinationDate()
      )
    );
    sendEmailNotification();
    sendSMSNotification();
    header("Location: ../account/myappointment.php?appointment=success");
  } catch (Exception $e) {
    echo "getAppId Error: " . $e->getMessage();
  }
}
function sendEmailNotification(){
  try {
    ob_start();
    $mail = new PHPMailer(true);
    SMTPServerSettings($mail);
    $to = $_SESSION['cfecb706488b9b67825b14c6792f0bcc'];
    $subject = 'Appointment in Ginos Dogs En Barbers';
    $message = 'Hi ' .   $_SESSION['7a13ce2a07525b4fd46ebc0226706fab'] . ',
     <div style="padding-bottom:10px;">
       Thank you for Choosing Ginos Dogs En Barbers for your Appointment
       <br/>
       Click this <a href="' . getWebRoot() . '/code/verifyReservation.php?code=' . verificationCode() . '&app-id=' . getAppId() . '">link</a> to verify your your appointment
       <br/>
       Your Appointment Details:
       <br/>
       <div style="margin-top:10px;">
         <div style="border-bottom:1px solid gray;padding-top:2%;padding-bottom:2%;word-wrap: break-word;">
            <span><b>Appointment Date: </b>' . getAppointmentDate() . '</span>
         </div>
         <div style="border-bottom:1px solid gray;padding-top:2%;padding-bottom:2%;word-wrap: break-word;">
            <span><b>Appointment Time: </b>' . getAppointmentTime() . '</span>
         </div>
         <div style="border-bottom:1px solid gray;padding-top:2%;padding-bottom:2%;word-wrap: break-word;">
            <span><b>Owner\'s Contact Number: </b>' . getContactNumber() . '</span>
         </div>
         <div style="border-bottom:1px solid gray;padding-top:2%;padding-bottom:2%;word-wrap: break-word;">
            <span><b>Pet Name: </b>' . getPetName() . '</span>
         </div>
         <div style="border-bottom:1px solid gray;padding-top:2%;padding-bottom:2%;word-wrap: break-word;">
            <span><b>Pet Breed: </b>' . getPetBreed() . '</span>
         </div>
         <div style="border-bottom:1px solid gray;padding-top:2%;padding-bottom:2%;word-wrap: break-word;">
            <span><b>Pet Gender: </b>' . getPetGender() . '</span>
         </div>
         <div style="border-bottom:1px solid gray;padding-top:2%;padding-bottom:2%;word-wrap: break-word;">
            <span><b>Pet Age: </b>' . getPetAge() . '</span>
         </div>
         <div style="border-bottom:1px solid gray;padding-top:2%;padding-bottom:2%;word-wrap: break-word;">
            <span><b>Last Rabies Vaccination Date: </b>' . getLastRabiesVaccinationDate() . '</span>
         </div>
         <div style="border-bottom:1px solid gray;padding-top:2%;padding-bottom:2%;word-wrap: break-word;">
            <span><b>Last Vaccination Date: </b>' . getLastVaccinationDate() . '</span>
         </div>
       </div>
       <div style="margin-top:10px;">
         In case of any questions, feel free to contact us at <a href="' . getWebroot() .  '/contact.php">Contact Us</a>
         <br/>
         Ginos Dogs En Barbers.
       </div>
     </div>';
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->send();
    end();
  } catch (Exception $e) {
      echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
  }
}
function verificationCode(){
  return md5(getAppointmentDate().getAppointmentTime().getSessionEmail().getPetName());
}
function sendSMSNotification(){
  /*try {
    ob_start();
    $apiKey = "dYrw0-OjRx65dZUYKXJNFQ==";
    $sentTo = getContactNumber();
    $message = "GinosDogsEnBarbers.
Thank you for choosing us in your appointment .
You can view your appointment in your email.";
    $message = rawurlencode($message);
    $link = "https://platform.clickatell.com/messages/http/send?apiKey=" . $apiKey . "&to=" . $sentTo . "&content=" . $message;
    $callurl = curl_init();
    curl_setopt($callurl , CURLOPT_URL, $link);
    curl_exec($callurl);
    curl_close($callurl);
  } catch (Exception $e) {
    echo "Send SMS Notification Error: " . $e->getMessage();
  }
  //----------------------------------------------------------*/
  try {
    $apiKey = 'b20746ae';
    $apiSecret = 'yfBh8URZdITc5UCT';
    $basic  = new \Nexmo\Client\Credentials\Basic($apiKey, $apiSecret);
    $client = new \Nexmo\Client($basic);

    $sentTo = getContactNumber();
    $from = "GinosDogsEnBarbers";
    $messageDetails = "GinosDogsEnBarbers.
You have appointment on " . date('F d, Y - h:i a', strtotime(getAppointmentDate() . " " . getAppointmentTime())) . ".
Thank you for choosing us in your appointment .
You can view your appointment in your email.";
    $message = $client->message()->send([
        'to' => $sentTo,
        'from' => $from,
        'text' => $messageDetails
    ]);
    header("Location: ../account/myreservation.php?appointment=success");
  } catch (Exception $e) {
    echo "Send SMS Notification Error: " . $e->getMessage();
  }
  //---------------------------------------*/
}
function getErrors($error){
  $error = implode("&", $error);
  header("Location: ../services/grooming.php?date=" . getAppointmentDate() . "&time=" . getAppointmentTime() . "&" . $error);
}
function getAppId(){
  try {
    $appId = getConnection()->prepare("SELECT * FROM `appointment_details`
    WHERE `customer_id`=:customerId AND `reserved_at`=:reservedAt");
    $appId->execute(
      array(
        ":customerId" => $_SESSION['cb24373bb88538168c8e839069491f18'],
        ":reservedAt" => getTimeStamp()
      )
    );
    return $appId->fetch()['id'];
  } catch (Exception $e) {

  }

}
//---Get and Validate Form Data -------
/*function getAppointmentType() {
  return strip_tags($_POST['app_type']);
}*/
function getAppointmentDate() {
  return strip_tags($_POST['app_date']);
}
function getAppointmentTime() {
  $time12 = array(
    "9:00 AM",
    "10:30 AM",
    "12:00 AM",
    "01:30 PM",
    "03:00 PM",
    "04:30 PM",
    "06:00 PM"
  );
  $time24 = array(
    "09:00:00",
    "10:30:00",
    "12:00:00",
    "13:30:00",
    "15:00:00",
    "16:30:00",
    "18:00:00"
  );
  for ($x=0; $x < $time12; $x++) {
    if ($_POST['app_time'] == $time12[$x]) {
      return $time24[$x];
    }
  }
}
function getContactNumber(){
  return "63" . substr($_POST['contact_number'],5,3) . substr($_POST['contact_number'],10,3) . substr($_POST['contact_number'],14,4);
}
function getPetSize(){
  return strip_tags($_POST['pet_size']);
}
function getPetName(){
  return strip_tags($_POST['pet_name']);
}
function getPetBreed(){
  return strip_tags($_POST['pet_breed']);
}
function getPetGender(){
  return strip_tags($_POST['pet_gender']);
}
function getPetAge(){
  return strip_tags($_POST['pet_age']);
}
function getLastRabiesVaccinationDate(){
  return strip_tags($_POST['lastRabiesVaccDate']);
}
function getLastVaccinationDate(){
  return strip_tags($_POST['lastVaccDate']);
}
validate();
