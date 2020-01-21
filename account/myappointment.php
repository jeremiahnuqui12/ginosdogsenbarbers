<?php
require '../code/config.php';
function getAppDetails($status){
  try {
    $appointmentDetails = getConnection()->prepare("SELECT * FROM `appointment_details`
    WHERE `customer_id` = :customerId AND
    `status` LIKE :status");
    $appointmentDetails->execute(
      array(
        ":customerId" => getSessionCustomerId(),
        ":status" => $status
      )
    );
    if ($appointmentDetails->rowCount() == 0) {
      throw new Exception("No Appointment Found");
    } else {
      ?>
      <table class="table table-striped">
        <thead>
          <tr class="font-weight-bold">
            <td>Appointment Date</td>
            <td>Appointment Time</td>
            <td>Date Submitted</td>
            <td>Action</td>
          </tr>
        </thead>
        <tbody>
          <?php
          while ($row = $appointmentDetails->fetch()) {
        ?>
        <tr>
          <td><?php echo date('F d, Y', strtotime($row[2])); ?></td>
          <td><?php echo date('h:i a', strtotime($row[3])); ?></td>
          <td><?php echo date('F d, Y ', strtotime($row[4])); ?></td>
          <td>
            <button type="button" class="btn btn-primary" onclick="viewAppointmentInfo(<?php echo $row[0]; ?>)" title="View Appointment" data-toggle="modal" data-target="#customer-appointment-info">
              <i class="fa fa-calendar"></i>
            </button>
            <?php if ($row[5] == "Approved" || $row[5] == "Pending"): ?>
              <button type="button" name="button" <?php echo checkStatus($row[6]); ?> onclick="cancelAppointment(<?php echo $row[0]; ?>)" title="Cancel Appointment" class="btn btn-outline-danger" data-toggle="modal" data-target="#cancelAppointmentModal">
                <i class="fa fa-ban"></i>
              </button>
            <?php endif; ?>
          </td>
        </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php
    }
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}
function checkStatus($status){
  if ($status == "Deleted") {
    return "disabled";
  } elseif ($status == "Cancelled by Admin" || $status == "Cancelled By Customer") {
    return "disabled";
  }
}
	$pageTitle = "My Appointment | Gino's Dogs en Barbers";

	if (!isset($_SESSION['7a13ce2a07525b4fd46ebc0226706fab'])) {
		header("Location: ../account/?signin=1&reservation=1");
	}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<?php getHeadLinks(); ?>
		<?php echo "<title>" . $pageTitle . "</title>"; ?>
    <script type="text/javascript">
      function cancelAppointment(id){
        document.getElementById('cancelAppointmentModalButton').href="../code/cancelAppointment.php?id=" + id;
      }
      function viewAppointmentInfo(x){
        if (window.XMLHttpRequest) {
          // code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
        } else {  // code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function() {
          if (this.readyState==4 && this.status==200) {
            document.getElementById("appointment-info").innerHTML=this.responseText;
          }
        }
        xmlhttp.open("GET","viewinfo.php?id="+x,true);
        xmlhttp.send();
      }
    </script>
    <style media="screen">
      .card-body{
        padding:5px;
      }
      .tab-content{
        padding: 10px;
      }
    </style>
	</head>
	<body>
		<!-- Page Header -->
		<?php getPageHeader(); ?>
		<!-- Start of Page Content Here -->
		<div class="page-content">
		    <div class="container">
          <div aria-label="breadcrumb">
  				  <ol class="breadcrumb">
  				    <li class="breadcrumb-item" aria-current="page">
  							<a href="../">Home</a>
  						</li>
  						<li class="breadcrumb-item" aria-current="page">
  							<a href="account.php">
  								Account
  							</a>
  						</li>
  						<li class="breadcrumb-item active" aria-current="page">My Appointment</li>
  				  </ol>
  				</div>
          <?php if (isset($_GET['appointment'])): ?>
            <?php if ($_GET['appointment'] == "success"): ?>
              <div class="alert alert-success" role="alert">
                <span>Appointment Success. Go to your email to verify your Appointment</span>
              </div>
            <?php endif; ?>
            <?php if ($_GET['appointment'] == "cancel"): ?>
              <div class="alert alert-success" role="alert">
                <span>Your appointment has been cancelled.</span>
              </div>
            <?php endif; ?>
          <?php endif; ?>

          <?php if (isset($_GET['verify'])): ?>
            <?php if ($_GET['verify']== "success"): ?>
              <div class="alert alert-success" role="alert">
                <span>Appointment Verified</span>
              </div>
            <?php endif; ?>
            <?php if ($_GET['verify'] == "failed"): ?>
              <div class="alert alert-danger" role="alert">
                <span>Appointment Verification Failed: <?php echo $_GET['message']; ?></span>
              </div>
            <?php endif; ?>
          <?php endif; ?>
  			<div class="col-lg-12 col-sm-12" style="text-align:center;">
            <h3>My Appointments</h3>
  			</div>
  			<div class="col-lg-12 col-sm-12" style="text-align:justify;">
  				<a href="../services/calendar.php" class="btn btn-primary">Make Another Appointment</a>
  			</div>
        <br/>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active col-md-2" id="nav-pending-tab" data-toggle="tab" href="#nav-pending" role="tab" aria-controls="nav-pending" aria-selected="true">Pending</a>
          <a class="nav-item nav-link col-md-3" id="nav-approved-tab" data-toggle="tab" href="#nav-approved" role="tab" aria-controls="nav-approved" aria-selected="false">Approved</a>
          <a class="nav-item nav-link col-md-3" id="nav-done-tab" data-toggle="tab" href="#nav-done" role="tab" aria-controls="nav-done" aria-selected="false">Completed</a>
          <a class="nav-item nav-link col-md-2" id="nav-cancelled-tab" data-toggle="tab" href="#nav-cancelled" role="tab" aria-controls="nav-cancelled" aria-selected="false">Cancelled</a>
          <a class="nav-item nav-link col-md-2" id="nav-expired-tab" data-toggle="tab" href="#nav-expired" role="tab" aria-controls="nav-expired" aria-selected="false">Expired</a>
        </div>
        <div class="tab-content" id="nav-tabContent">
          <div class="tab-pane fade show active" id="nav-pending" role="tabpanel" aria-labelledby="nav-pending-tab">
            <?php getAppDetails("Pending"); ?>
          </div>
          <div class="tab-pane fade" id="nav-approved" role="tabpanel" aria-labelledby="nav-approved-tab">
            <?php getAppDetails("Approved"); ?>
          </div>
          <div class="tab-pane fade" id="nav-done" role="tabpanel" aria-labelledby="nav-done-tab">
            <?php getAppDetails("Completed"); ?>
          </div>
          <div class="tab-pane fade" id="nav-cancelled" role="tabpanel" aria-labelledby="nav-cancelled-tab">
            <?php getAppDetails("Cancelled%"); ?>
          </div>
          <div class="tab-pane fade" id="nav-expired" role="tabpanel" aria-labelledby="nav-expired-tab">
            <?php getAppDetails("Expired"); ?>
          </div>
        </div>
		  </div>
		</div>
		<!-- End of Page Content  -->
		<?php getPageFooter(); ?>
		<?php getFooterLinks(); ?>
    <div class="modal fade" tabindex="-1" id="customer-appointment-info" role="dialog" aria-labelledby="appointmentLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg"  role="document">
        <div class="modal-content"style="min-width:800px;">
          <div class="modal-header">
            <h5 class="modal-title" id="appointmentLabel">Appointment Info</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body" id="appointment-info">
            <div class="loader"></div>
          </div>
        </div>
      </div>
    </div>
    <!-- --------------------------->
    <div class="modal fade" id="cancelAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="cancelAppointmentModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="cancelAppointmentModalLabel">Cancel Appointment</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <span>Are you sure to cancel this appointment?</span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <a href="#" class="btn btn-primary" id="cancelAppointmentModalButton">Cancel Appointment</a>
          </div>
        </div>
      </div>
    </div>
	</body>
</html>
