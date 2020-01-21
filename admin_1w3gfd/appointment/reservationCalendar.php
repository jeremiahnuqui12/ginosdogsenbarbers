<?php
require '../code/config.php';
function calendarDetails(){
  try {
    $details = getConnection()->prepare("SELECT
      `id`,
      `reservation_date`,
      `status` FROM `customer_order`
      WHERE `status` != :status");
    $details->execute(
      array(
        ":status" => "On-Cart"
      )
    );
    $result = $details->fetchAll();
    foreach($result as $row) {
     $list[] = array(
       "id" => $row["id"],
       "title" => "Reservation",
       "start" => $row["reservation_date"],
       "color" => backgroundColor($row["status"])
     );
    }
    return $list;
  } catch (Exception $e) {
    echo "Calendar Details Error: " . $e->getMessage();
  }
}
function backgroundColor($status){
  if ($status == "Completed") {
    return "#ffff00";
  } elseif ($status == "Pending") {
    return "#5bc0de";
  } elseif ($status == "Approved") {
    return "#5cb85c";
  } elseif ($status == "Cancelled By Admin" || $status == "Cancelled By Customer" || $status == "Expired") {
    return "#d9534f";
  }
}
echo json_encode(calendarDetails());
?>
