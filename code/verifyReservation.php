<?php
require 'config.php';
function checkVerificationIfExist(){
  try {
    $query = getConnection()->prepare("SELECT * FROM `appointment_details` WHERE `verification_code`=:id");
    $query->execute(
      array(
        ":id" => getId()
      )
    );
    if ($query->rowCount() == 0) {
      throw new IdNotFoundException("No Verification Id Found", 1);
    } else {
      verifyReservation();
    }
  } catch (Exception $e) {
    header("Location: " . getWebRoot() . "/account/myappointment.php?verify=failed&message=" . $e->getMessage());
  } catch(IdNotFoundException $e){
    header("Location: " . getWebRoot() . "/account/myappointment.php?verify=failed&message=" . $e->getMessage());
  }
}
function verifyReservation() {
  try {
    $query = getConnection()->prepare("UPDATE `appointment_details`
      SET `email_verified`=:verified,
      `verified_at`=:verifiedAt
      WHERE `id`=:appId");
    $query->execute(
      array(
        ":verified" => "Yes",
        ":verifiedAt" => getTimeStamp(),
        ":appId" => getAppId()
      )
    );
    header("Location: " . getWebRoot() . "/account/myappointment.php?verify=success");
  } catch (Exception $e) {
    header("Location: " . getWebRoot() . "/account/myappointment.php?verify=failed&message=" . $e->getMessage());
  }

}
function validate(){
  if (empty(getId())) {
    header("Location: " . getWebRoot());
  } else {
    checkVerificationIfExist();
  }
}
function getId(){
  return $_GET['code'];
}
function getAppId(){
  return $_GET['app-id'];
}
validate();
?>
