<?php
require '../code/config.php';
if (isset($_GET['id'])) {
  getDetails();
} else {
  header("Location: admins.php");
}

function getDetails() {
  try {
    $details = array();
    $query = getConnection()->prepare("SELECT
      `first_name`,
      `last_name`,
      `email_address`,
      `username`,
      `role_value`
      FROM `admin_user` WHERE `admin_id`=:id");
    $query->bindParam(":id", $_GET['id']);
    $query->execute();
    while ($row = $query->fetch()) {
      array_push($details, $row[0]);
      array_push($details, $row[1]);
      array_push($details, $row[2]);
      array_push($details, $row[3]);
      array_push($details,
        implode(", ",
          explode("----",
            $row[4]
          )
        )
      );
    }
    echo json_encode($details);
  } catch (Exception $e) {

  }
}



?>
