<?php
require '../code/config.php';
function getTimeList(){
  try {
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
    for ($x=0; $x < count($time12) ; $x++) {
      $query = getConnection()->prepare("SELECT * FROM `appointment_details` WHERE
        `time` = :timex
        AND `status` IN (:status, :status1)
        AND `date` = :datee");
      $query->execute(
        array(
          ":status" => "Pending",
          ":status1" => "Approved",
          ":timex" => $time24[$x],
          ":datee" => $_GET['date']
        )
      );
      $maxAvailable = getAvailableSlot();
      $countResult = $query->rowCount();
      if($countResult <= 5){
        ?>
        <li class="list-group-item">
          <a href="grooming.php?date=<?php echo $_GET['date'];?>&time=<?php echo $time12[$x];?>" class="text-primary"><?php echo $time12[$x]; ?></a>
          <span class="text-success"><?php echo "Available: " . ($maxAvailable - $countResult); ?></span>
          <span class="text-danger"><?php echo "Occupied: " . $countResult ?></span>
        </li>
        <?php
      } else {
        ?>
        <li class="list-group-item">
          <span class="text-danger"><?php echo $time12[$x]; ?> Fully Occupied</span>
        </li>
        <?php
      }
    }
  } catch (Exception $e) {
    echo "Display Time Error: " . $e->getMessage();
  }
}
function validate(){
  if($_SERVER['HTTP_REFERER'] != getWebroot() . "/services/calendar.php"){
    header("Location: " . $_SERVER['HTTP_REFERER']);
  }
  if (empty($_GET['date'])) {
    header($_SERVER['HTTP_REFERER']);
  }
}

function getAvailableSlot() {
  try {
    $query = getConnection()->prepare("SELECT * FROM `max_appointment_per_day`");
    $query->execute();
    return $query->fetch()[1];
  } catch (Exception $e) {

  }

}

?>
<h4>Choose time for appointment</h4>
<ul class="list-group">
  <?php validate();getTimeList(); ?>
</ul>
