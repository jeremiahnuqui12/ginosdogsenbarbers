<?php

require "config.php";

if (isset($_GET['value'])) {
  if (is_nan($_GET['value'])) {
    // code...
  } else {
    UpdateMaxPerTime();
  }
} else {
  // code...
}

function UpdateMaxPerTime() {
  try {
    $query = getConnection()->prepare("UPDATE `max_appointment_per_day` SET `value`=:value");
    $query->bindParam(":value", $_GET['value']);
    $query->execute();
    header("Location: ../appointment?availableslot=1");
  } catch (Exception $e) {

  }
}
