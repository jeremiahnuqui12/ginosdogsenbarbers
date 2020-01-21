<?php
require '../code/config.php';
$output="";
$query = getConnection()->prepare("SELECT
  `product`.`id` AS `ProductId`,
  `product`.`name` AS `ProductName`,
  `product_categories`.`name` AS `categoryName`,
  `stocks_available`,
  `stocks_reserve`,
  `price`,
  `description`
  FROM `product`
  INNER JOIN `product_categories` ON `product_categories`.`id` = `product`.`category`
  INNER JOIN `product_stocks` ON `product_stocks`.`product_id`=`product`.`id`
  WHERE `status`='Active'");
$query->execute();
$output .= '
   <table class="table" bordered="1">
    <tr>
      <th>Id</th>
      <th>Name</th>
      <th>Category</th>
      <th>Available Stocks</th>
      <th>Reserved Stocks</th>
      <th>Price</th>
      <th>Product Description</th>
    </tr>
  ';
while($row = $query->fetch()) {
  $output .= '
       <tr>
         <td>'.$row["ProductId"].'</td>
         <td>'.$row["ProductName"].'</td>
         <td>'.$row["categoryName"].'</td>
         <td>'.$row["stocks_available"].'</td>
         <td>'.$row["stocks_reserve"].'</td>
         <td>'.$row["price"].'</td>
         <td>'.$row["description"].'</td>
       </tr>';
}
$output .= '</table>';
  header('Content-Type: application/xls');
  header('Content-Disposition: attachment; filename=ProductMasterlist.xls');
  echo $output;
?>
