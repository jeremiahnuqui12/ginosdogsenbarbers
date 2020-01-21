<?php
require '../code/config.php';
//echo $_GET['id'];
if(!isset($_GET['id'])){
  header("Location: " . $_SERVER['HTTP_REFERER']);
}
function getAppointment(){
  try {
    $getAppointment = getConnection()->prepare("SELECT
      `customer_account`.`customer_name` AS `customer_name`,
      `appointment_customer_info`.`contact_number` AS `contact_number`,
      `appointment_details`.`id` AS `id`,
      `appointment_details`.`reserved_at` AS `reserved_at`,
      `appointment_details`.`date` AS `date`,
      `appointment_details`.`time` AS `time`,
      `appointment_details`.`status` AS `status`,
      `appointment_details`.`email_verified` AS `email_verified`
      FROM `appointment_details`
      INNER JOIN `customer_account` ON `appointment_details`.`customer_id`=`customer_account`.`customer_id`
      INNER JOIN `appointment_customer_info` ON `appointment_details`.`id`=`appointment_customer_info`.`appointment_id`
      WHERE `appointment_details`.`id`=:id");
    $getAppointment->execute(
      array(
        ":id" => $_GET['id']
      )
    );
    $data = $getAppointment->fetch();
    ?>
    <?php if ($data['status'] != "Cancelled By Customer" || $data['status'] != "Cancelled By Customer"): ?>
      <?php if ($data['status'] == "Approved"): ?>
        <button type="button" class="btn btn-outline-primary" name="button" onclick="appointmentCompleted(<?php echo $data['id']; ?>)" data-toggle="modal" data-target="#updateStatusModal">Appointment Completed</button>
      <?php endif; ?>
      <?php if ($data['status'] == "Pending"): ?>
        <button type="button" name="button" title="Approved Reservation" class="btn btn-outline-success" onclick="appointmentApprove(<?php echo $data['id']; ?>)" data-toggle="modal" data-target="#updateStatusModal">
          <i class="fas fa-check"></i> Approve
        </button>
      <?php endif; ?>
      <?php if ($data['status'] == "Pending" || $data['status'] == "Approved"): ?>
        <a href="#" title="Cancel Appointment" id="modal-cancel-button2" onclick="appointmentCancelled(<?php echo $data['id']; ?>)" class="btn btn-outline-danger" data-toggle="modal" data-target="#updateStatusModal">
          <i class="fa fa-ban"></i> Cancel
        </a>
      <?php endif; ?>
    <?php endif; ?>
    <div class="form-row">
      <div class="form-group col-md-12">
        <label for="">Customer Name:</label>
        <input type="text" readonly class="form-control" name="customer_name" value="<?php echo $data['customer_name'] ?>">
      </div>
      <div class="form-group col-md-12">
        <label for="">Contact Number:</label>
        <input type="text" readonly class="form-control" name="customer_name" value="<?php echo $data['contact_number'] ?>">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label>Date submitted: </label>
        <input class="form-control" type="text" readonly value="<?php echo date('F d, Y h:i a', strtotime($data['reserved_at'])); ?>"/>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label>Appointment Date: </label>
        <input class="form-control" type="text" readonly value="<?php echo date('F d, Y', strtotime($data['date'])); ?>"/>
      </div>
      <div class="form-group col-md-6">
        <label>Appointment Time: </label>
        <input class="form-control" type="text" readonly value="<?php echo date('h:i a', strtotime($data['time']));; ?>"/>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label>Status: </label>
        <input class="form-control" type="text" readonly value="<?php echo $data['status']; ?>"/>
      </div>
      <div class="form-group col-md-6">
        <label>Email Verified: </label>
        <input class="form-control" type="text" readonly value="<?php echo $data['email_verified']; ?>"/>
      </div>
    </div>

    <?php
  } catch (Exception $e) {

  }
}
function getDetails(){
  try {
    $details = getConnection()->prepare("SELECT * FROM `appointment_customer_info`
      INNER JOIN `pricing_for_grooming` ON `pricing_for_grooming`.`id`=`appointment_customer_info`.`pet_size`
      WHERE `appointment_id`=:id");
    $details->bindParam(":id", $_GET['id']);
    $details->execute();
    $details = $details->fetch();
    ?>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label>Pet Name: </label>
        <input class="form-control" type="text" readonly value="<?php echo $details['pet_name']; ?>"/>
      </div>
      <div class="form-group col-md-6">
        <label>Contact Number: </label>
        <input class="form-control" type="text" readonly value="<?php echo $details['contact_number']; ?>"/>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group col-md-6">
        <label>Pet Size: </label>
        <input class="form-control" type="text" readonly value="<?php echo $details['size'] . "(Price Range:" . $details['price'] . ")"; ?>"/>
      </div>
      <div class="form-group col-md-6">
        <label>Pet Breed: </label>
        <input class="form-control" type="text" readonly value="<?php echo checkBreed($details['pet_breed']); ?>"/>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label>Pet Gender: </label>
        <input class="form-control" type="text" readonly value="<?php echo $details['pet_gender']; ?>"/>
      </div>
      <div class="form-group col-md-6">
        <label>Pet Age: </label>
        <input class="form-control" type="text" readonly value="<?php echo $details['pet_age']; ?>"/>
      </div>
    </div>
      <div class="form-group col-md-12">
        <label>Last Rabies Vaccination Date: </label>
        <input class="form-control" type="text" readonly value="<?php echo date('F d, Y', strtotime($details['last_rabies_vaccination'])); ?>"/>
      </div>
      <div class="form-group col-md-12">
        <label>Last Vaccination Date: </label>
        <input class="form-control" type="text" readonly value="<?php echo date('F d, Y', strtotime($details['last_vaccination'])); ?>"/>
      </div>
    <?php
  } catch (Exception $e) {

  }
}
function checkBreed($breed){
  //return (int)$breed;
  if((int)$breed == 0){
    return $breed;
  } else {
    $query = getConnection()->prepare("SELECT * FROM `dog_breed_list` WHERE `pet_id`=:id");
    $query->execute(
      array(":id"=>$breed)
    );
    return $query->fetch()['dog_breed'];
  }
}
?>
<div class="float-left col-md-6">
  <form method="post">
    <?php getAppointment(); ?>
  </form>
</div>
<div class="float-right col-md-6">
  <?php getDetails(); ?>
</div>
