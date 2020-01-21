<?php
require '../code/config.php';
function getAppId(){
  try {
    $x = 1;
    $appointmentDetails = getConnection()->prepare("SELECT * FROM `appointment_details`
    WHERE `customer_id`=:customerId");
    $appointmentDetails->execute(
      array(
        ":customerId" => $_GET['id']
      )
    );
    if ($appointmentDetails->rowCount() == 0) {
      throw new Exception("No Appointment Found");
    } else {
      echo "<table class=\"table\"><tr class=\"font-weight-bold\">";
      echo "<td>Id</td>";
      echo "<td>Date</td>";
      echo "<td>Time</td>";
      echo "<td>Reserved At</td>";
      echo "<td>Action</td>";
      echo "</tr>";
      while ($row = $appointmentDetails->fetch()) {
        ?>
        <tr>
          <td><?php echo $x++; ?></td>
          <td><?php echo date('F d, Y', strtotime($row[3])); ?></td>
          <td><?php echo date('h:i a', strtotime($row[4])); ?></td>
          <td><?php echo date('F d, Y - h:i a', strtotime($row[5])); ?></td>
          <td>
            <button type="button" class="btn btn-outline-primary" onclick="viewAppointmentInfo(<?php echo $row[0]; ?>)"title="View Appointment" data-toggle="modal" data-target="#customer-appointment-info">
              <i class="fa fa-calendar"></i>
            </button>
          </td>
        </tr>
        <?php

      }
      echo "</table>";
    }
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}
getAppId();
?>
