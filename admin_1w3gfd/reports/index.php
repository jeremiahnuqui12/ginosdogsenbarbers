<?php
require '../code/config.php';
checkIfAllow("Reports");
function checkReportType(){
  if ($_REQUEST["report-type"] == "Appointment") {
    AppointmentReports();
  } elseif ($_REQUEST["report-type"] == "Reservation") {
    ReservationReports();
  }
}
function countResult(){
  try {
    if ($_REQUEST['report-type'] == "Appointment") {
      $tableName = "`appointment_details`";
    } elseif ($_REQUEST['report-type'] == "Reservation") {
      $tableName = "`customer_order`";
    }
    $query = getConnection()->prepare("SELECT COUNT(*) FROM $tableName WHERE `status`=:status");
    $query->bindParam(":status", $_REQUEST['status']);
    $query->execute();
    return $query->fetch()[0];
  } catch (Exception $e) {
    echo "Error" . $e->getMessage();
  }
}
function checkIfReportTypeSelected($type){
  if(isset($_REQUEST['report-type'])) {
    if ($_REQUEST['report-type'] == $type) {
      echo "selected";
    } elseif ($_REQUEST['report-type'] == $type) {
      echo "selected";
    }
  }
}
function checkIfStatusSelected($status){
  if (isset($_REQUEST['status'])) {
    if ($_REQUEST['status'] == $status) {
      echo "selected";
    } elseif ($_REQUEST['status'] == $status) {
      echo "selected";
    } elseif ($_REQUEST['status'] == $status) {
      echo "selected";
    } elseif ($_REQUEST['status'] == $status) {
      echo "selected";
    }
  }
}
function getStatus(){
  if ($_REQUEST["report-type"] == "Appointment") {
    if ($_REQUEST['status'] == "Approved") {
      return "Approved";
    } elseif ($_REQUEST['status'] == "Pending") {
      return "Pending";
    } elseif ($_REQUEST['status'] == "Completed") {
      return "Completed";
    }
  } elseif ($_REQUEST["report-type"] == "Reservation") {
    if ($_REQUEST['status'] == "Approved") {
      return "Approved";
    } elseif ($_REQUEST['status'] == "Pending") {
      return "Pending";
    } elseif ($_REQUEST['status'] == "Completed") {
      return "Completed";
    }
  }
}
?>
<?php function displayReports() { ?>
  <div class="bg-white p-3 mt-3">
    <div>
      <button type="button" class="btn btn-outline-dark" name="button" onclick="printReport()">
        <i class="fas fa-print"></i>
        <span>Print</span>
      </button>
      <button type="button" class="btn btn-outline-dark" name="button" id="exportToExcel" onclick="exportToExcel()">
        <i class="fas fa-download"></i>
        <span>Export to Excel</span>
      </button>
    </div>
    <div>
      <h5><?php echo countResult() . " " . $_REQUEST['status'] . " " . $_REQUEST['report-type']; ?></h5>
    </div>
    <div style="overflow-x: scroll;" id="reportTable">
      <table class="table table-striped" id="tableDetails" style="width: 150%;">
        <?php checkReportType(); ?>
      </table>
    </div>
  </div>
<?php } ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php getHeadLinks(); ?>
    <title>Reports</title>
    <script type="text/javascript">
      function reportTableType(type) {
        document.getElementById('reportTableTitle').innerText = type.value + " Reservation";
      }
      function printReport(){
        document.getElementById("tableDetails").style.width = "100%";
        var defaultDisplay = document.body.innerHTML;
  			var reportDetails = document.getElementById("reportTable").innerHTML;
        var reportDetails2 = document.getElementById("reportTable");
        document.title = "<?php echo $_REQUEST['status'] . " " . $_REQUEST['report-type']; ?>";
  			document.body.innerHTML = reportDetails;
        if (!window.print()) {
          document.body.innerHTML = defaultDisplay;
          document.getElementById("tableDetails").style.width = "150%";
        } else {
          document.body.innerHTML = defaultDisplay;
          document.getElementById("tableDetails").style.width = "150%";
        }
  		}
      function exportToExcel(){
        window.location.href="exportToExcel.php?report-type=<?php echo $_REQUEST['report-type'] . "&status=" . $_REQUEST['status']; ?>";
      }
    </script>
    <style media="screen">
      .order-product table td, .order-product table tr th{
        padding: 5px;
        width: 10px;
      }
    </style>
    <style media="print">
      @page{
        Size:landscape;
      }
      table{
        font-size: 9px;
      }
      tr td{
        padding:0px;
        margin: 0px;
      }
    </style>
    <script type="text/javascript">
      function showReports(){
        alert(document.getElementById("report-type").value);
      }
    </script>
  </head>
  <body>
    <!--- SideBar -->
    <?php getSidebar(); ?>
    <!--- Header -->
    <?php getHeader(); ?>
    <!--Page Content -->
    <div class="page-content">
      <div class="container">
        <div class="col-md-6 p-3 bg-white">
          <div>
            <form method="post">
              <div class="form-group">
                <label for="report-type">Services:</label>
                <select class="form-control" name="report-type" required id="report-type">
                  <option value="">---Select---</option>
                  <option value="Appointment" <?php checkIfReportTypeSelected("Appointment"); ?>>Appointment</option>
                  <option value="Reservation" <?php checkIfReportTypeSelected("Reservation"); ?>>Reservation</option>
                </select>
              </div>
              <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" name="status" required id="status">
                  <option value="">---Select---</option>
                  <option value="Pending" <?php checkIfStatusSelected("Pending"); ?>>Pending</option>
                  <option value="Approved" <?php checkIfStatusSelected("Approved"); ?>>Approved</option>
                  <option value="Completed" <?php checkIfStatusSelected("Completed"); ?>>Completed</option>
                </select>
              </div>
              <div>
                <input type="submit" class="btn btn-outline-primary" name="button" value="Show Reports"/>
              </div>
            </form>
          </div>
        </div>
        <?php
          if (isset($_REQUEST["report-type"]) && isset($_REQUEST['status'])) {
            displayReports();
          }
        ?>
      </div>
    </div>
    <!--End of Page Content-->
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
  </body>
</html>
<?php
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
    $query->execute(
      array(
        ":status" => getStatus()
      )
    );
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
    $query->execute(
      array(
        ":status" => getStatus()
      )
    );
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
        <td><?php echo "&#8369; " . number_format((float)($data['reserveQuantity'] * $data['ProductPrice']), 2, '.', ''); ?></td>
      </tr>
      <?php
      echo "</tbody>";
    }
  } catch (Exception $e) {
    echo "Error:" . $e->getMessage();
  }
}
