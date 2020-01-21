<?php
require 'config.php';

if (isset($_POST['date-close']) && isset($_POST['close-details'])) {
  closeStore();
} else {
  header("Location: ../");
}
function closeStore() {
  try {
    $query = getConnection()->prepare("INSERT INTO `date_close`(`value`,`details`) VALUES (:value, :details)");
    $query->bindParam(":value", $_POST['date-close']);
    $query->bindParam(":details", $_POST['close-details']);
    $query->execute();
    recordActivity("Store has been close for " . date('F d, Y', strtotime($_POST['date-close'])));
    header("Location: ../appointment?close=success");
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}
