<?php
  require "../code/config.php";
  if (isset($_GET['idx'])) {
    getMessageDetails();
  } else {
    header("Location: ../");
  }
  function getMessageDetails() {
    try {
      $messageDetails = array();
      $query = getConnection()->prepare("SELECT * FROM `contactus_messages` WHERE `id`=:id");
      $query->bindParam(":id", $_GET['idx']);
      $query->execute();
      while ($row = $query->fetch()) {
        array_push($messageDetails, $row[0]);
        array_push($messageDetails, $row[1]);
        array_push($messageDetails, $row[2]);
        array_push($messageDetails, $row[3]);
        array_push($messageDetails, date('F d, Y - h:i a', strtotime($row[4])));
        array_push($messageDetails, $row[5]);
        checkIfAlreadyRespond($messageDetails, $row[0]);
      }
    } catch (Exception $e) {
      echo "Error: " . $e->getMessage();;
    }
  }
  function checkIfAlreadyRespond($details, $id) {
    try {
      $query = getConnection()->prepare("SELECT COUNT(*), `message` FROM `messages_respond` WHERE `message_id`=:id");
      $query->bindParam(":id", $id);
      $query->execute();
      $data = $query->fetch();
      if ($data[0] == 0) {
        array_push($details, "Not Responded");
      } else {
        array_push($details, "Responded");
        array_push($details, $data[1]);
      }
      echo json_encode($details);
    } catch (Exception $e) {
      echo "Error: " . $e->getMessage();;
    }
  }
