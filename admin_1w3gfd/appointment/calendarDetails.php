<?php
require '../code/config.php';
function calendarDetails(){
  try {
    $details = getConnection()->prepare("SELECT * FROM `appointment_details` WHERE `status`!='Deleted'");
    $details->execute();
    $result = $details->fetchAll();
    foreach($result as $row) {
     $list[] = array(
       "id" => $row["id"],
       "title" => "Appointment",
       "start" => $row["date"] . " " . $row["time"],
       "color" => backgroundColor($row["status"])
     );
    }
    return $list;
  } catch (Exception $e) {
    echo "Calendar Details Error: " . $e->getMessage();
  }
}
echo json_encode(calendarDetails());
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
/*title: 'All Day Event',
start: '2018-03-01'
$list[] = array(
  "title" => "All Day Event",
  "start" => "2018-09-30"
);
*/

?>
