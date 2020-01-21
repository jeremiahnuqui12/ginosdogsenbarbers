<?php
require 'code/config.php';
function x(){
  try {
    $query = getConnection()->prepare("SELECT SUM(`quantity`) FROM `customer_order`
      WHERE `product_id`=:productId
      AND `status` NOT IN (
        :status,
        :status2
      )"
    );
    $query->execute(
      array(
        ":productId" => 31,
        ":status" => "Approved",
        ":status2" => "On-Cart"
      )
    );
    $stocks = $query->fetch()[0];
    if (empty($stocks)) {
      return 99;
    } else {
      return $stocks;
    }
  } catch (Exception $e) {

  }
}
echo x();

/*function mostReserved() {
  try {
    $dataArray = array();
    $query = getConnection()->prepare("SELECT
          COUNT(`customer_order`.`quantity`) AS `orderSum`
      FROM
          `product`
      INNER JOIN `customer_order` ON `customer_order`.`product_id` = `product`.`id`
      GROUP BY
          `customer_order`.`status`
      ORDER BY `customer_order`.`status` ASC
    ");
    $query->execute();
    $data = $query->fetchAll();
    array_push($dataArray, ["Quantity", "Approved", "Cancelled", "On-Cart", "Pending", "Product Received"]);
    array_push($dataArray, ["x",
      $data[0][0],
      $data[1][0],
      $data[2][0],
      $data[3][0],
      $data[4][0]
    ]);
    return json_encode($dataArray);
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
echo mostReserved();*/
/*function topOrder() {
  try {
    $dataArray = array();
    $query = getConnection()->prepare("SELECT
    `product`.`name` AS `ProductName`,
    SUM(`customer_order`.`quantity`) `orderQuantity`
    FROM `product`
    INNER JOIN `customer_order` ON `customer_order`.`product_id`=`product`.`id`
    WHERE `customer_order`.`status` = 'Order Received'
    GROUP BY `ProductName`
    ORDER BY `orderQuantity` DESC
    LIMIT 0,5
    ");
    $query->execute();
    $data = $query->fetchAll();
    array_push($dataArray, ["Products", "Quantity"]);
    foreach ($data as $x) {
      array_push($dataArray, [substr($x[0],0,20) . "...", (int)$x[1]]);
    }
    return $dataArray;
  } catch (Exception $e) {

  }
}*/
?>
<html lang="en" dir="ltr">
<head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(piechartAppointReservation);
      function piechartAppointReservation() {
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          ['Work',     11],
          ['Eat',      2],
          ['Commute',  2],
          ['Watch TV', 2],
          ['Sleep',    7]
        ]);

        var options = {
          title: 'asdasd',
          is3D: true,
          width: 400,
          'chartArea': {'width': '80%', 'height': '80%'},
          'legend': {'position': 'bottom'}
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);
      }
    </script>
    <style media="screen">
      body{
        background-color: #f0f0f0;
      }
    </style>
  </head>
  <body>
    <div id="piechart_3d" style="width: 500px; height: 300px; padding-right:0px;"></div>
  </body>
</html>
