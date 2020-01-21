<?php
require 'code/config.php';
//checkIfAllow("Dashboard Tab");
function allReservationWeek(){
  try {
    $query = getConnection()->prepare("SELECT * FROM `appointment_details` WHERE `status`!='Deleted' ORDER BY `id` DESC LIMIT 10");
    $query->execute();
    while ($row = $query->fetch()) {
      ?>
      <tr onclick="window.location.href='appointment/#groomingReservation'">
        <td>
          <?php echo $row['id'] ?>
        </td>
        <td>
          <?php echo $row['date'] . " " . $row['time']; ?>
        </td>
        <td>
          <?php echo $row['status']; ?>
        </td>
      </tr>
      <?php
    }
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
function latestOrders(){
  try {
    $query = getConnection()->prepare("SELECT
      `customer_order`.`id` AS `orderId`,
      `product`.`name` AS `productName`,
      `customer_order`.`quantity` AS `quantity`,
      `customer_order`.`reservation_date` AS `reservationDate`
      FROM `customer_order`
      INNER JOIN `product` ON `product`.`id`=`customer_order`.`product_id`
      WHERE `customer_order`.`status`!='On-Cart'
      ORDER BY `orderId` DESC LIMIT 0, 10;
    ");
    $query->execute();
    while ($row = $query->fetch()) {
      ?>
      <tr onclick="window.location.href='appointment/#orderReservation'">
        <td>
          <?php echo $row['orderId']; ?>
        </td>
        <td title="<?php echo $row['productName']; ?>">
          <?php echo substr($row['productName'], 0,20); ?>
        </td>
        <td>
          <?php echo $row['quantity']; ?>
        </td>
        <td>
          <?php echo date('M d, Y h:i a', strtotime($row['reservationDate'])); ?>
        </td>
      </tr>
      <?php
    }
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
function reservation($tableName, $status){
  try {
    $query = getConnection()->prepare("SELECT COUNT(*) FROM `$tableName` WHERE `status` LIKE :status");
    $query->bindParam(":status", $status);
    $query->execute();
    echo $query->fetch()[0];
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
function countData($tableName){
  try {
    $query = getConnection()->prepare("SELECT COUNT(*) FROM `$tableName` WHERE `status`='Active'");
    $query->execute();
    echo $query->fetch()[0];
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
function checkStocks($x){
  try {
    if ($x == ">=") {
      $query = getConnection()->prepare("SELECT COUNT(*) FROM `product_stocks` WHERE `stocks_available` >= 15");
    } elseif ($x == "<") {
      $query = getConnection()->prepare("SELECT COUNT(*) FROM `product_stocks` WHERE `stocks_available` < 15");
    }
    $query->execute();
    echo $query->fetch()[0];
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
function reservationToday($tableName, $column){
  try {
    $query = getConnection()->prepare("SELECT COUNT(*) FROM `$tableName` WHERE
    `status`='Approved' AND DATE(`$column`)=CURDATE()");
    $query->execute();
    if ($query->rowCount() == 0) {
      echo 0;
    } else {
      echo $query->fetch()[0];
    }
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
function latestCustomer(){
  try {
    $query = getConnection()->prepare("SELECT * FROM `customer_account` WHERE `status`='Active' ORDER BY `customer_id` DESC LIMIT 0, 10");
    $query->execute();
    while ($row = $query->fetch()) {
      ?>
      <tr>
        <td><?php echo $row['customer_id']; ?></td>
        <td><?php echo $row['customer_name'] ?></td>
        <td><?php echo date('M d, Y h:i a', strtotime($row['created_at'])) ?></td>
      </tr>
      <?php
    }
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
function stocksInDanger(){
  try {
    $query = getConnection()->prepare("SELECT
      COUNT(*)
      FROM `product`
      INNER JOIN `product_stocks` ON `product`.`id`=`product_stocks`.`product_id`
      WHERE `product_stocks`.`stocks_available` < :below
      AND `product`.`status` = :status
    ");
    $query->execute(
      array(
        ":below" => 15,
        ":status" => "Active"
      )
    );
    return $query->fetch()[0];
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
function mostReserved($status) {
  try {
    $dataArray = array();
    $query = getConnection()->prepare("SELECT
          COUNT(`customer_order`.`quantity`) AS `orderSum`
      FROM
          `customer_order`
      WHERE `status` LIKE :status
    ");
    $query->bindParam(":status", $status);
    $query->execute();
    return $query->fetch()[0];
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
//------------------------------------------------------------------------------
function getAppointment(){
    try {
      $maxMonth = 12;
      $monthToday = date("m");
      $monthArray2 = array();
      $monthValue = array();
      if ($monthToday == 1) {
        $monthArray2 = getMonths(11,13,1,4);
      } elseif ($monthToday == 2) {
        $monthArray2 = getMonths(12,13,1,5);
      } elseif ($monthToday == 11) {
        $monthArray2 = getMonths(9,13,1,2);
      } elseif ($monthToday == 12) {
        $monthArray2 = getMonths(10,13,1,3);
      } else {
        $monthArray2 = getMonths($monthToday-2,$monthToday++,--$monthToday,$monthToday+3);
      }
      return horizontalGraphDetails($monthArray2);
    } catch (Exception $e) {

    }
}
function horizontalGraphDetails($monthKey){
  try {
    $appointmentDetails = array(["Month", "Appointment", "Reservation"]);
    $query = getConnection()->prepare("SELECT COUNT(*) FROM `appointment_details` WHERE MONTH(`date`) = :month AND YEAR(`date`) = :year");
    $query2 = getConnection()->prepare("SELECT COUNT(*) FROM `customer_order` WHERE MONTH(`reservation_date`) = :month AND YEAR(`reservation_date`) = :year");
    for ($x=0; $x < count($monthKey); $x++) {
      //Count Appointments in this month
      $query->execute(
        array(
          ":month" => $monthKey[$x][0],
          ":year" => $monthKey[$x][1]
        )
      );
      //Count Reservation in this month
      $query2->execute(
        array(
          ":month" => $monthKey[$x][0],
          ":year" => $monthKey[$x][1]
        )
      );
      array_push($appointmentDetails, [
        getMonthValue($monthKey[$x][0]),
         $query->fetch()[0],
         $query2->fetch()[0]
       ]
     );
    }
    return json_encode($appointmentDetails);
  } catch (Exception $e) {

  }
}
function getMonthValue($x){
  $AllMonthArray = array(1 => "Jan",
    2 => "Feb",
    3 => "March",
    4 => "April",
    5 => "May",
    6 => "June",
    7 => "July",
    8 => "Aug",
    9 => "Sept",
    10 => "Oct",
    11 => "Nov",
    12 => "Dec");
    foreach ($AllMonthArray as $key => $value) {
      if ($x == $key) {
        return $value;
      }
    }
}
function getMonths($a, $b, $c, $d) {
  $monthArray = array();
  $yearToday = date("Y");
  for ($x = $a; $x < $b ; $x++) {
    if ($a == 11 || $a == 12) {
      array_push($monthArray, [$x, (int)$yearToday-1]);
    } elseif ($a == 10 || $a == 9) {
      array_push($monthArray, [$x, (int)$yearToday]);
    } else {
      array_push($monthArray, [$x, (int)$yearToday]);
    }
  }
  for ($y = $c; $y < $d; $y++) {
    if ($a == 10 || $a == 9) {
      array_push($monthArray, [$y, (int)$yearToday+1]);
    } elseif ($a == 11 || $a == 12) {
      array_push($monthArray, [$y, (int)$yearToday]);
    } else {
      array_push($monthArray, [$y, (int)$yearToday]);
    }
  }
  return $monthArray;
}
function countDetails($tableName) {
  try {
    $query = getConnection()->prepare("SELECT COUNT(*) FROM $tableName");
    $query->execute();
    return $query->fetch()[0];
  } catch (Exception $e) {
    echo "Error: " . getMessage()->$e;
  }
}
function countAppointment() {
  try {
    $status = "Pending";
    $query = getConnection()->prepare("SELECT COUNT(*) FROM `appointment_details` WHERE `status`=:status");
    $query->bindParam(":status", $status);
    $query->execute();
    return $query->fetch()[0];
  } catch (Exception $e) {
    return "Error: " . getMessage()->$e;
  }
}
function countReservation() {
  try {
    $status = "Pending";
    $query = getConnection()->prepare("SELECT COUNT(*) FROM `customer_order` WHERE `status`=:status");
    $query->bindParam(":status", $status);
    $query->execute();
    return $query->fetch()[0];
  } catch (Exception $e) {
    echo "Error: " . getMessage()->$e;
  }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php getHeadLinks(); ?>
    <title>Dashboard</title>
    <style media="screen">
      .head-box {
        width: 150px;
        float: left;
        background-color: #fff;
      }
    </style>
    <script type="text/javascript">
    function addStocks(id){
      var stocks = prompt("Enter Amount of stocks to be Added:");
      if(stocks){
        window.location.href="code/addStocks.php?id=" + id + "&stocks=" + stocks;
      }
    }
    </script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(reservationDetails);
      google.charts.setOnLoadCallback(appointmentMonth);
      google.charts.setOnLoadCallback(piechartAppointReservation);
      google.charts.setOnLoadCallback(overallSales);
      function reservationDetails() {
        var data = google.visualization.arrayToDataTable([
        ["Quantity", "Approved", "Cancelled", "Completed", "Pending"],
        ["x",
          <?php echo mostReserved("Approved") ?>,
          <?php echo mostReserved("Cancelled %") ?>,
          <?php echo mostReserved("Completed") ?>,
          <?php //echo mostReserved("On-Cart") ?>
          <?php echo mostReserved("Pending") ?>,
        ]
      ]);
        var options = {
          chart: {
            title: 'Reservation Status (All Time)',
          },
          bars: 'horizontal' // Required for Material Bar Charts.
        };

        var chart = new google.charts.Bar(document.getElementById('piechart_reservationDetails'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
        //---------------------------------------------------
      }
      //------------------------------------------------------------------------
      function appointmentMonth() {
        var data = google.visualization.arrayToDataTable(<?php echo getAppointment(); ?>);
        var options = {
          chart: {
            title: 'No. of Appointment & Reservation',
          },
          bars: 'vertical' // Required for Material Bar Charts.
        };

        var chart = new google.charts.Bar(document.getElementById('barchart_appointment'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
      //------------------------------------------------------------------------
      function piechartAppointReservation() {
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          ['Appointment', <?php echo countDetails("`appointment_details`"); ?>],
          ['Reservation', <?php echo countDetails("`customer_order`"); ?>]
        ]);

        var options = {
          title: 'Appointment and Reservation',
          is3D: true,
          width: 400,
          'chartArea': {'width': '80%', 'height': '80%'},
          'legend': {'position': 'bottom'}
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);
      }
      //------------------------------------------------------------------------
      function overallSales() {
        var data = google.visualization.arrayToDataTable([
          ['Month', 'Sales', 'Reservation', 'Appointment Sales', 'Appointment Sales'],
          ['July', 1000, 400, 1200, 137],
          ['Aug',  1170, 460, 100, 137],
          ['Sept', 660,  1120, 100, 137],
          ['Oct',  1030, 540, 100, 137],
          ['Nov',  1030, 540, 100, 137],
          ['Dec',  1030, 540, 100, 137],
          ['Jan',  1030, 540, 100, 137]
        ]);

        var options = {
          title: 'Company Performance',
          curveType: 'function',
          chartArea: {'width': '80%', 'height': '75%'},
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('overallSales'));

        chart.draw(data, options);
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
        <?php if (isset($_GET['reset'])): ?>
          <?php if ($_GET['reset'] == "success"): ?>
            <div class="alert alert-success" role="alert">
              <span>Password has been Change</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (stocksInDanger() != 0): ?>
          <div class="alert alert-danger">
            <a href="products">
              <span><b>REMINDER: </b> <?php echo stocksInDanger(); ?> products has a stocks 15 and below</span>
            </a>
          </div>
        <?php endif; ?>
        <?php if (countAppointment() != 0): ?>
          <div class="alert alert-danger">
            <a href="appointment/?appointment-dashboard=pending&#appointment-grooming">
              <span><b>NEEDS ACTION:</b> <?php echo countAppointment(); ?> Appointments are still in pending status</span>
            </a>
          </div>
        <?php endif; ?>
        <?php if (countReservation() != 0): ?>
          <div class="alert alert-danger">
            <a href="appointment/?reservation-dashboard=pending&#productReservation">
              <span><b>NEEDS ACTION:</b> <?php echo countReservation(); ?> Reservations are still in pending status</span>
            </a>
          </div>
        <?php endif; ?>
        <div class="graphs float-left mt-1 bg-white p-3" style="margin-right:10px;">
          <div id="piechart_reservationDetails" style="width:570px; height:220px;"></div>
        </div>
        <div class="graphs float-right mt-1 bg-white p-3">
          <div id="barchart_appointment" style="width:440px; height:220px;"></div>
        </div>
        <div class="graphs float-left mt-3 bg-white p-3">
          <div id="piechart_3d" style="width: 500px; height: 300px; padding-right:0px;"></div>
        </div>
        <!--div class="graphs float-right mt-3 bg-white p-3">
          <div id="overallSales" style="width: 500px; height: 300px; padding-right:0px;"></div>
        </div-->
        <div class="clearfix"></div>
        <!--div class="col-md-5 mr-2 p-2 mt-3 float-left">
            <div style="background-color:#ffffff;padding:5px;margin-bottom:10px;">
              <h5>Latest Grooming Appointment</h5>
              <div>
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>Id</th>
                      <th>Date of Reservation</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php //allReservationWeek(); ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div style="background-color:#ffffff;padding:5px;">
              <h5>New Registered Customer this Month</h5>
              <div>
                <table class="table table-striped table-hover">
                  <thead>
                    <th>Id</th>
                    <th style="width:200px;">Customer Name</th>
                    <th>Date Registered</th>
                  </thead>
                  <tbody>
                    <?php //latestCustomer(); ?>
                  </tbody>
                </table>
              </div>
            </div>
        </div>
        <div class="ml-3 p-2 mt-3 float-right" style="width: 610px;">
            <div style="background-color:#ffffff;padding:5px;margin-bottom:10px;">
              <h5>Latest Order Reservation</h5>
              <div>
                <table class="table table-striped table-hover">
                  <thead>
                    <th>Id</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Date Reserved</th>
                  </thead>
                  <tbody>
                    <?php //latestOrders(); ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div style="background-color:#ffffff;padding:5px;">
              <h5>Products with below 15 stocks</h5>
              <?php if (isset($_GET['stocksAdd'])): ?>
                <?php if ($_GET['stocksAdd'] == "success"): ?>
                  <div class="alert alert-success" role="alert">
                    <span>Stocks Added</span>
                  </div>
                <?php endif; ?>
              <?php endif; ?>
              <div>
                <table class="table table-striped table-hover">
                  <thead>
                    <th style="width:400px;;">Product Name</th>
                    <th>Stocks Left</th>
                    <th>Action</th>
                  </thead>
                  <tbody>
                    <?php //belowStocks(); ?>
                  </tbody>
                </table>
              </div>
            </div>
        </div>
      -->

      </div>
    </div>
    <!--Page Content-->
  </body>
</html>
