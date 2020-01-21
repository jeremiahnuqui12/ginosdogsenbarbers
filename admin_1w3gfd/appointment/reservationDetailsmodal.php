<?php
require '../code/config.php';

if (empty($_GET['id'])) {
  header("Location: ../appointment/");
}
try {
  $query = getConnection()->prepare("SELECT
    `customer_order`.`id` AS `orderId`,
    `customer_order`.`contact_number` AS `contactNumber`,
    `customer_account`.`customer_name` AS `customerName`,
    `product`.`name` AS `productName`,
    `customer_order`.`quantity`,
    `product`.`price` AS `productPrice`,
    `customer_order`.`quantity` * `product`.`price` AS `totalPrice`,
    `customer_order`.`status` AS `orderStatus`,
    `customer_order`.`reserved_at` AS `dateReserved`,
    `customer_order`.`reservation_date` AS `pickUpDate`,
    `customer_order`.`email_verified` AS `orderEmailVerified`,
    `customer_order`.`reservation_date` AS `reservationDate`
    FROM `customer_order`
    INNER JOIN `customer_account` ON `customer_account`.`customer_id`=`customer_order`.`customer_id`
    INNER JOIN `product` ON `product`.`id`=`customer_order`.`product_id`
    WHERE `customer_order`.`id` = :id
  ");
  $query->bindParam(":id", $_GET['id']);
  $query->execute();
  $data = $query->fetch();
  ?>
  <div class="mb-3">
    <?php if ($data['orderStatus'] == "Cancelled By Admin" || $data['orderStatus'] == "Cancelled By Customer"): ?>
      <span class="alert alert-danger">Reservation Cancelled</span>
    <?php elseif(strtotime($data['reservationDate']) < strtotime(getTimeStamp())): ?>
      <span class="alert alert-info">Reservation Expired</span>
    <?php else: ?>
      <?php if ($data['orderStatus'] == "Approved"){ ?>
        <button type="button" class="btn btn-outline-primary" onclick="reservationReceived(<?php echo $data['orderId'] ?>)" name="button" data-toggle="modal" data-target="#updateStatusModal">Reservation Complete</button>
      <?php } elseif($data['orderStatus'] != "Completed") { ?>
        <button type="button" class="btn btn-outline-success" onclick="approveReservation(<?php echo $data['orderId'] ?>)" name="button" data-toggle="modal" data-target="#updateStatusModal">Approve</button>
      <?php } if($data['orderStatus'] != "Completed") { ?>
        <button type="button" class="btn btn-outline-danger" onclick="cancelReservation(<?php echo $data['orderId'] ?>)" name="button" data-toggle="modal" data-target="#updateStatusModal"><i class="fas fa-ban"></i> Cancel</button>
      <?php } ?>
    <?php endif; ?>
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="">Customer Name</label>
      <input type="text" class="form-control" readonly value="<?php echo $data['customerName'] ?>">
    </div>
    <div class="form-group col-md-6">
      <label>Contact Number : </label>
      <input class="form-control" type="text" readonly value="<?php echo $data['contactNumber']; ?>"/>
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label>Reservation Date : </label>
      <input class="form-control" type="text" readonly value="<?php echo date('F d, Y h:i a', strtotime($data['pickUpDate'])); ?>"/>
    </div>
    <div class="form-group col-md-6">
      <label>Date submitted : </label>
      <input class="form-control" type="text" readonly value="<?php echo date('F d, Y h:i a', strtotime($data['dateReserved'])); ?>"/>
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-md-10">
      <label for="">Product Name</label>
      <input type="text" readonly class="form-control" value="<?php echo $data['productName'] ?>">
    </div>
    <div class="form-group col-md-2">
      <label for="">Quantity</label>
      <input type="text" readonly class="form-control" value="<?php echo $data['quantity'] ?>">
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="">Product Price</label>
      <input type="text" readonly class="form-control" value="<?php echo $data['productPrice'] ?>">
    </div>
    <div class="form-group col-md-6">
      <label for="">Sub-total</label>
      <input type="text" readonly class="form-control" value="<?php echo $data['totalPrice'] ?>">
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="">Status</label>
      <input type="text" readonly class="form-control" value="<?php echo $data['orderStatus'] ?>">
    </div>
    <div class="form-group col-md-6">
      <label for="">Email Verified</label>
      <input type="text" readonly class="form-control" value="<?php echo $data['orderEmailVerified'] ?>">
    </div>
  </div>
  <?php
} catch (Exception $e) {

}


 ?>
