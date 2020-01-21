<?php
require '../code/config.php';
$output="";
$query = getConnection()->prepare("SELECT
  `customer_id`,
  `customer_name`,
  `email`,
  `created_at`,
  `status`
   FROM `customer_account` ORDER BY `customer_id` ASC");
$query->execute();
$output .= '
   <table class="table" bordered="1">
    <tr>
      <th>ID</th>
      <th>Customer Name</th>
      <th>Email Address</th>
      <th>Date Registered</th>
      <th>Status</th>
    </tr>
  ';
while($row = $query->fetch()) {
  $output .= '
       <tr>
         <td>'.$row["customer_id"].'</td>
         <td>'.$row["customer_name"].'</td>
         <td>'.$row["email"].'</td>
         <td>'.$row["created_at"].'</td>
         <td>'.$row["status"].'</td>
       </tr>';
}
$output .= '</table>';
  header('Content-Type: application/xls');
  header('Content-Disposition: attachment; filename=customerMasterlist.xls');
  echo $output;
?>
