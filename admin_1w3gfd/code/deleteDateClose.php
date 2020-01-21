<?php
require 'config.php';

if (isset($_GET['id']) && isset($_GET['date'])) {
  closeStore();
} else {
  header("Location: ../");
}
function closeStore() {
  try {
    $query = getConnection()->prepare("DELETE FROM `date_close` WHERE `id`=:value");
    $query->bindParam(":value", $_GET['id']);
    $query->execute();
    recordActivity("Closing for " . date('F d, Y', strtotime($_GET['date'])) . " has been removed.");
    header("Location: ../appointment?close=remove");
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}
