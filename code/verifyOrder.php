<?php
require 'config.php';
function checkVerificationIfExist(){
  try {
    $query = getConnection()->prepare("SELECT * FROM `customer_order` WHERE `verification_code`=:id");
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
    header("Location: " . getWebRoot() . "/account/myproductreserve.php?verify=failed&message=" . $e->getMessage());
  } catch(IdNotFoundException $e){
    header("Location: " . getWebRoot() . "/account/myproductreserve.php?verify=failed&message=" . $e->getMessage());
  }
}
function verifyReservation() {
  try {
    $query = getConnection()->prepare("UPDATE `customer_order`
      SET `email_verified`=:verified,
      `verified_at`=:verifiedAt
      WHERE `verification_code`=:verificationCode");
    $query->execute(
      array(
        ":verified" => "Yes",
        ":verifiedAt" => getTimeStamp(),
        ":verificationCode" => getId()
      )
    );
    header("Location: " . getWebRoot() . "/account/myproductreserve.php?verify=success");
  } catch (Exception $e) {
    header("Location: " . getWebRoot() . "/account/myproductreserve.php?verify=failed&message=" . $e->getMessage());
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
  return $_GET['id'];
}
validate();
?>
