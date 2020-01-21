<?php
require '../code/config.php';
checkIfAllow("Report Tab");
function monthlyReservation(){
  $months = array(
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
  );
  for ($i=0; $i < count($months); $i++) {
    ?>
    <tr>
      <td><?php echo $months[$i]; ?></td>
      <td><?php echo monthlyResult($i, "Waiting for Approval"); ?></td>
      <td><?php echo monthlyResult($i, "Approved"); ?></td>
      <td><?php echo monthlyCancelled($i); ?></td>
    </tr>
    <?php
  }
}
function monthlyResult($x, $status){
  $x++;
  try {
    $query = getConnection()->prepare("SELECT count(*) FROM `appointment_details`
      WHERE MONTH(`date`) = :month  AND `status` = :status
      GROUP BY MONTH(`date`)
    ");
    $query->execute(
      array(
        ":month" => $x,
        ":status" => $status

      )
    );
    if ($query->rowCount() == 0) {
      return "0";
    } else {
      return $query->fetch()[0];
    }
  } catch (Exception $e) {

  }
}
function monthlyCancelled($x){
  $x++;
  try {
    $query = getConnection()->prepare("SELECT count(*) FROM `appointment_details`
      WHERE MONTH(`date`) = :month  AND `status` LIKE :status
      GROUP BY MONTH(`date`)
    ");
    $query->execute(
      array(
        ":month" => $x,
        ":status" => "Cancelled%"
      )
    );
    if ($query->rowCount() == 0) {
      return "0";
    } else {
      return $query->fetch()[0];
    }
  } catch (Exception $e) {

  }
}
function productReport(){
  $months = array(
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
  );
  $row = 0;
  $x=1;
  while ($row < count($months)) {
    $query = getConnection()->prepare("SELECT
      SUM(`quantity`) AS `totalQuantity`,
      `customer_order`.`quantity` * `product`.`price` AS `sales`
      FROM `customer_order`
      INNER JOIN `product` ON `product`.`id`=`customer_order`.`product_id`
      WHERE MONTH(`reservation_date`)=:month AND `customer_order`.`status`!=:status
      GROUP BY MONTH(`reservation_date`)
    ");
    $query->bindParam(":month", $x);
    $query->execute(
      array(
        ":month" => $x,
        ":status" => "Deleted"
      )
    );
    $data = $query->fetch();
    if ($data == null) {
      ?>
      <tr>
        <td><?php echo $months[$row]; ?></td>
        <td>0</td>
        <td>0</td>
      </tr>
      <?php
    } else {
      ?>
      <tr>
        <td><?php echo $months[$row]; ?></td>
        <td><?php echo $data['totalQuantity']; ?></td>
        <td><?php echo "&#8369; " . $data['sales'] . ".00"; ?></td>
      </tr>
      <?php
    }
    $row++;
    $x++;
  }
}
function groomingReport(){
  $months = array(
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
  );
  $row = 0;
  $x=1;
  while ($row < count($months)) {
    $query = getConnection()->prepare("SELECT
      COUNT(*) AS `serviceCount`
      FROM `appointment_details`
      WHERE MONTH(`date`)=:month
      AND `appointment_details`.`status` = :status
      GROUP BY MONTH(`date`)
    ");
    $query->execute(
      array(
        ":month" => $x,
        ":status" => "Grooming Done"
      )
    );
    $data = $query->fetch();
    if ($data == null) {
      ?>
      <tr>
        <td><?php echo $months[$row]; ?></td>
        <td>0</td>
        <td>0</td>
      </tr>
      <?php
    } else {
      ?>
      <tr>
        <td><?php echo $months[$row]; ?></td>
        <td><?php echo $data['serviceCount']; ?></td>
        <td><?php //echo "&#8369; " . $data['sales'] . ".00"; ?></td>
      </tr>
      <?php
    }
    $row++;
    $x++;
  }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php getHeadLinks(); ?>
    <title></title>
    <script type="text/javascript">
      function reportTableType(type) {
        document.getElementById('reportTableTitle').innerText = type.value + " Reservation";
      }
    </script>
    <style media="screen">
      .order-product table td, .order-product table tr th{
        padding: 5px;
        width: 10px;
      }
    </style>
  </head>
  <body>
    <!--- SideBar -->
    <?php getSidebar(); ?>
    <!--- Header -->
    <?php getHeader(); ?>
    <!--Page Content -->
    <div class="page-content">
      <div class="container">
        <div class="mb-3" style="background-color:#fff;">
          <div class="pl-3 pt-3 pb-3 pr-3">
            <div>
              <h4>Report Filter</h4>
            </div>
            <div>
              <form action="index.html" method="post">
                <div class="">
                  <label for="report-type">Report Type:</label>
                  <select class="" name="report-type">
                    <option value=""> --- Select --- </option>
                    <option value="Appointment">Appointment (Grooming)</option>
                    <option value="Reservation">Reservation (Product)</option>
                  </select>
                </div>
                <div class="">
                  <label for="date-start">Date from:</label>
                  <input type="date" name="date-start">
                </div>
                <div class="">
                  <label for="date-end">To:</label>
                  <input type="date" name="date-end">
                </div>
                <div class="">
                  <label for="product-type">Product Name:</label>
                  <select class="" name="">
                    <option value="">All</option>
                    <option value=" "></option>
                  </select>
                </div>
                <div class="">
                  <label for="product-type">Product Category:</label>
                  <select class="" name="">
                    <option value="">All</option>
                    <option value=" "></option>
                  </select>
                </div>
                <div class="">
                  <label for="">With status:</label>
                  <select class="" name="">
                    <option value=""> --- Select --- </option>
                    <option value="">Complete</option>
                    <option value="">Cancelled</option>
                    <option value="">Order Received</option>
                    <option value="">Pending</option>
                    <option value="">Approved</option>
                  </select>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="order-product col-md-12 mb-3" style="background-color:#fff;">
          <div class="pl-3 pt-3 pb-3 pr-3">
            <span>Summary of product Selled</span>
            <table class="m-3 table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Product Name</th>
                  <th>Quantity Selled</th>
                  <th>Total Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>asdasd</td>
                  <td>123</td>
                  <td>123</td>
                </tr>
              </tbody>
            </table>
            <br/>
            <span>Summary of all Appointment</span>
            <table class="m-3 table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Customer Name</th>
                  <th>Date & Time Appointment</th>
                  <th>Status</th>
                  <th>Payment</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>asdasd</td>
                  <td>123</td>
                  <td>123</td>
                  <td>123</td>
                </tr>
              </tbody>
            </table>
            <br/>
            <span>Summary of All Reservation</span>
            <table class="m-3 table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Customer Name</th>
                  <th>Product Name</th>
                  <th>Product Quantity</th>
                  <th>Date & Time of Pick-up</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>asdasd</td>
                  <td>123</td>
                  <td>123</td>
                  <td>123</td>
                  <td>123</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="order-product float-left" style="width: 540px;background-color:#fff;">
          <div class="pl-3 pt-3">
            <h4>Reservation Sales</h4>
          </div>
          <table class="m-3 table-bordered table-striped">
            <tr>
              <th style="width:200px;">Month</th>
              <th style="width:200px;">Item Quantity</th>
              <th style="width:200px;">Sales</th>
            </tr>
            <?php productReport(); ?>
          </table>
        </div>
        <div class="order-product float-right" style="width: 540px;background-color:#fff;">
          <div class="pl-3 pt-3">
            <h4>Appointment Sales</h4>
          </div>
          <table class="m-3 table-bordered table-striped">
            <tr>
              <th style="width:200px;">Month</th>
              <th style="width:200px;"># of Grooming</th>
              <th style="width:200px;">Sales</th>
            </tr>
            <?php groomingReport(); ?>
          </table>
        </div>
        <div class="clearfix">

        </div>
        <div class="col-md-12" style="background-color:#fff;">
          <div class="col-md-12 mt-0"></div>
        </div>
      </div>
    </div>
    <!--Page Content-->
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
  </body>
</html>
