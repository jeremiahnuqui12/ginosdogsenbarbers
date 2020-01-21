<?php
require '../code/config.php';
function checkReportType(){
  if ($_REQUEST["report-type"] == "Appointment") {
    AppointmentReports();
  } elseif ($_REQUEST["report-type"] == "Reservation") {
    ReservationReports();
  }
}
function AppointmentReports() {
  try {
    $x=1;
    $query = getConnection()->prepare("SELECT
      `customer_name`,
      `date`,
      `time`,
      `contact_number`,
      `pet_name`,
      `pet_breed`,
      `pet_gender`,
      `pet_age`,
      `pet_size`,
      `last_rabies_vaccination`,
      `last_vaccination`
      FROM `appointment_details`
      INNER JOIN `customer_account` ON `appointment_details`.`customer_id`=`customer_account`.`customer_id`
      INNER JOIN `appointment_customer_info` ON `appointment_customer_info`.`appointment_id`=`appointment_details`.`id`
      WHERE `appointment_details`.`status`=:status");
    $query->bindParam(":status", $_REQUEST['status']);
    $query->execute();
    ?>
    <thead>
      <tr>
        <th></th>
        <th>Customer Name</th>
        <th>Appointment Date</th>
        <th>Contact Number</th>
        <th>Pet Name</th>
        <th>Pet Breed</th>
        <th>Pet Gender</th>
        <th>Pet Age</th>
        <th>Pet Size</th>
        <th>Last Rabies Vaccination</th>
        <th>Last Vaccination</th>
      </tr>
    </thead>
    <tbody>
    <?php
    while ($data = $query->fetch()) {
      ?>
      <tr>
        <td><?php echo $x++; ?></td>
        <td><?php echo $data['customer_name']; ?></td>
        <td><?php echo date('F d, Y - h:i a', strtotime($data['date'] . " " . $data['time'])) ?></td>
        <td><?php echo $data['contact_number'] ?></td>
        <td><?php echo $data['pet_name'] ?></td>
        <td><?php echo $data['pet_breed'] ?></td>
        <td><?php echo $data['pet_gender'] ?></td>
        <td><?php echo $data['pet_age'] ?></td>
        <td><?php echo $data['pet_size'] ?></td>
        <td><?php echo date('F d, Y', strtotime($data['last_rabies_vaccination'])) ?></td>
        <td><?php echo date('F d, Y', strtotime($data['last_vaccination'])) ?></td>
      </tr>
      <?php
      echo "</tbody>";
    }
  } catch (Exception $e) {
    echo "Error:" . $e->getMessage();
  }
}
function ReservationReports() {
  try {
    $x=1;
    $query = getConnection()->prepare("SELECT
      `customer_name`,
      `reservation_date`,
      `product`.`name` AS `ProductName`,
      `contact_number`,
      `customer_order`.`quantity` AS `reserveQuantity`,
      `product`.`price` AS `ProductPrice`
      FROM `customer_order`
      INNER JOIN `customer_account` ON `customer_account`.`customer_id`=`customer_order`.`customer_id`
      INNER JOIN `product` ON `customer_order`.`product_id`=`product`.`id`
      WHERE `customer_order`.`status`=:status
      ");
    $query->bindParam(":status", $_REQUEST['status']);
    $query->execute();
    ?>
    <thead>
      <tr>
        <th></th>
        <th>Customer Name</th>
        <th>Reservation Date</th>
        <th>Contact Number</th>
        <th>Product Name</th>
        <th>Quantity</th>
        <th>Sub-Total</th>
      </tr>
    </thead>
    <tbody>
    <?php
    while ($data = $query->fetch()) {
      ?>
      <tr>
        <td><?php echo $x++; ?></td>
        <td><?php echo $data['customer_name']; ?></td>
        <td><?php echo date('F d, Y - h:i a', strtotime($data['reservation_date'])) ?></td>
        <td><?php echo $data['contact_number'] ?></td>
        <td><?php echo $data['ProductName'] ?></td>
        <td><?php echo $data['reserveQuantity'] ?></td>
        <td><?php echo number_format((float)($data['reserveQuantity'] * $data['ProductPrice']), 2, '.', ''); ?></td>
      </tr>
      <?php
      echo "</tbody>";
    }
  } catch (Exception $e) {
    echo "Error:" . $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <table>
      <?php checkReportType(); ?>
    </table>
  </body>
</html>

<?php
header('Content-Type: application/xls');
header('Content-Disposition: attachment; filename=reports.xls');
?>
