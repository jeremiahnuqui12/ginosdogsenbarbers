<?php
require '../code/config.php';
checkIfAllow("Calendar Tab");
function getAppointments(){
  try {
    $x=1;
    $query = "SELECT
      `appointment_details`.`id` AS `AppId`,
      `customer_account`.`customer_name` AS `customerName`,
      `appointment_customer_info`.`contact_number` AS `contactNumber`,
      `appointment_details`.`date` AS `appDate`,
      `appointment_details`.`time` AS `appTime`,
      `appointment_details`.`status` AS `appStatus`,
      `appointment_details`.`email_verified` AS `emailVerified`
      FROM `appointment_details`
      INNER JOIN `customer_account` ON `appointment_details`.`customer_id`=`customer_account`.`customer_id`
      INNER JOIN `appointment_customer_info` ON `appointment_details`.`id`=`appointment_customer_info`.`appointment_id`";
      if (isset($_GET['appointment-dashboard'])) {
        $query .= "WHERE `appointment_details`.`status`='Pending'";
      } else {
        $query .= "WHERE `appointment_details`.`status` NOT IN ('Deleted', 'Cancelled By Customer', 'Cancelled By Admin')";
      }
      $query .= "ORDER BY `AppId` DESC";
    $appointment = getConnection()->prepare($query);
      $appointment->execute();
      $count = $appointment->rowCount();
      if ($count == 0) {
        echo "<tr><td colspan=6 style=\"text-align:center;\">No Appointment Found</td></tr>";
      } else {
        while ($row = $appointment->fetch()) {
          ?>
          <tr>
            <td><?php echo $x++; ?></td>
            <td><?php echo $row['customerName']; ?></td>
            <td><?php echo "+" . $row['contactNumber']; ?></td>
            <td><?php echo  date('F d, Y h:i a', strtotime($row['appDate'] . $row['appTime']));  ?></td>
            <td><?php echo checkStatus("Appointment", $row['AppId'], $row['appDate'], $row['appStatus']); ?></td>
            <td>
              <button type="button" name="button" title="Approved Appointment" class="btn btn-outline-success" <?php echo checkApprovedStatus($row['appStatus']); ?> onclick="appointmentApprove(<?php echo $row['AppId']; ?>)" data-toggle="modal" data-target="#updateStatusModal">
                <i class="fas fa-check"></i>
              </button>
              <button type="button" name="button" onclick="updateAppointment(<?php echo $row['AppId']; ?>)" title="View Appointment" class="btn btn-outline-info" data-toggle="modal" data-target="#appointment-update-modal">
                <i class="fa fa-edit"></i>
              </button>
              <button type="button" name="button" <?php echo checkCancelStatus($row['appStatus']); ?> title="Cancel Appointment" onclick="appointmentCancelled(<?php echo $row['AppId']; ?>)" class="btn btn-outline-danger" data-toggle="modal" data-target="#updateStatusModal">
                <i class="fa fa-ban"></i>
              </button>
            </td>
          </tr>
          <?php
        }
      }
  } catch (Exception $e) {

  }
}
function checkApprovedStatus($status){
  if ($status == "Approved") {
    return "disabled";
  } elseif ($status == "Completed") {
    return "disabled";
  } elseif ($status == "Expired") {
    return "disabled";
  }
}
function checkCancelStatus($status) {
  if ($status == "Cancelled By Admin" || $status == "Cancelled By Customer") {
    return "disabled";
  } elseif ($status == "Completed") {
    return "disabled";
  } elseif ($status == "Expired") {
    return "disabled";
  }
}
function dayDetails() {
  $x = 1;
  $query = "SELECT
    `customer_order`.`id` AS `reservationId`,
    `customer_account`.`customer_name` AS `customerName`,
    `product`.`name` AS `productName`,
    `customer_order`.`quantity`,
    `customer_order`.`reservation_date` AS `reservationDate`,
    `customer_order`.`quantity` * `product`.`price` AS `totalPrice`,
    `customer_order`.`status` AS `reservationStatus`
    FROM `customer_order`
    INNER JOIN `customer_account` ON `customer_account`.`customer_id`=`customer_order`.`customer_id`
    INNER JOIN `product` ON `product`.`id`=`customer_order`.`product_id`";
    if (isset($_GET['reservation-dashboard'])) {
      $query .= "WHERE `customer_order`.`status` = 'Pending'";
    } else {
      /*
      $query .= "WHERE `customer_order`.`status` NOT IN (:status, :status2, :status3, :status4)";
      array(
        ":status" => "Deleted",
        ":status2" => "On-Cart",
        ":status3" => "Cancelled By Customer",
        ":status4" => "Cancelled By Admin"
      )
      */
      $query .= "WHERE `customer_order`.`status` NOT IN ('Deleted', 'On-Cart', 'Cancelled By Customer', 'Cancelled By Admin')";
    }
  $query .= "ORDER BY `customer_order`.`id` DESC";
  $details = getConnection()->prepare($query);
  $details->execute();
  while ($row = $details->fetch()) {
    ?>
    <tr>
      <td><?php echo $x++; ?></td>
      <td><?php echo $row['customerName']; ?></td>
      <td title="<?php echo $row['productName']; ?>"><?php echo substr($row['productName'], 0, 30) . "..."; ?></td>
      <td><?php echo date('F d, Y h:i a', strtotime($row['reservationDate'])); ?></td>
      <td><?php echo checkStatus("Reservation", $row['reservationId'], $row['reservationDate'], $row['reservationStatus']); ?></td>
      <td>
        <button type="button" name="button" onclick="orderDetails(<?php echo $row['reservationId']; ?>)" title="View Reservation" class="btn btn-outline-info" data-toggle="modal" data-target="#appointment-update-modal">
          <i class="fa fa-edit"></i>
        </button>
      </td>
    </tr>
  <?php }
}
function getAvailableSlot() {
  try {
    $query = getConnection()->prepare("SELECT * FROM `max_appointment_per_day`");
    $query->execute();
    return $query->fetch()[1];
  } catch (Exception $e) {

  }
}
function checkStatus($serviceType, $id, $appointmentDate, $status) {
    try{
        /*$start = "2019-02-10";
        if(date('Y-m-d', strtotime(getTimeStamp())) > date('Y-m-d',strtotime('+1 day',strtotime($appointmentDate)))){
            return "Expired";
            //return date('Y-m-d', strtotime(getTimeStamp()));;
        } else{
            return "asd";
        }*/
        if($status == "Expired"){
            return "<span class='text-danger'>" . $status . "</span>";
        } else if (date('Y-m-d', strtotime(getTimeStamp())) > date('Y-m-d',strtotime('+1 day',strtotime($appointmentDate)))) {
          updateStatus($serviceType, $id);
          return "Expired";
        } elseif ($status == "Approved") {
          return "<span class='text-success'>" . $status . "</span>";
        } elseif ($status == "Pending") {
          return "<span class='text-info'>" . $status . "</span>";
        } elseif ($status == "Completed") {
          return "<span class='text-warning'>" . $status . "</span>";
        }
    } catch (Exception $e) {

    }
}
function updateStatus($serviceType, $id) {
    try{
        $status = "Expired";
        if ($serviceType == "Appointment") {
          $query = getConnection()->prepare("UPDATE `appointment_details` SET `status`=:status WHERE `id`=:id");
        } elseif ($serviceType == "Reservation") {
          $query = getConnection()->prepare("UPDATE `customer_order` SET `status`=:status WHERE `id`=:id");
        }
        $query->bindParam(":status", $status);
        $query->bindParam(":id", $id);
        $query->execute();
        header("Refresh");
    } catch (Exception $e) {

    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php getHeadLinks(); ?>
    <title>Appointment | Admin | Gino's Dogs en Barbers</title>
    <script type="text/javascript">
      function approvedReservation(id) {
        document.getElementById('approvedReservationLink').href="../code/approvedReservation.php?id=" + id;
      }
      function restoreAppointment(id) {
        document.getElementById('restoreReservation').innerHTML = "<span>Are you sure to restore this appointment: " + id + "</span>";
        document.getElementById('restoreReservationLink').href = "../code/restoreAppointment.php?id=" + id;
      }
      function deleteAppointment(id) {
        document.getElementById('modal-delete-button').href = "../code/deleteAppointment.php?id=" + id;
      }
    </script>
    <style media="screen">
      thead{
        font-weight: bold;
      }
    </style>
    <script type="text/javascript">
      //modal-appointmentUpdate
      function updateAppointment(id){
        if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
        } else {  // code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function() {
          if (this.readyState==4 && this.status==200) {
            document.getElementById("appointment-update-modal-label").innerHTML = "Appointment Details";
            document.getElementById("modal-appointmentUpdate").innerHTML = this.responseText;
          }
        }
        xmlhttp.open("GET","details.php?id="+id,true);
        xmlhttp.send();
      }
      function orderDetails(id){
        if (window.XMLHttpRequest) {
          // code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
        } else {  // code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function() {
          if (this.readyState==4 && this.status==200) {
            document.getElementById("appointment-update-modal-label").innerHTML="Reservation Details";
            document.getElementById("modal-appointmentUpdate").innerHTML=this.responseText;
          }
        }
        xmlhttp.open("GET","reservationDetailsmodal.php?id="+id,true);
        xmlhttp.send();
      }
    </script>
    <style media="screen">
      .app_batch{
        border: 1px solid#000;
        border-radius: 5px 5px 5px 5px;
        margin: 10px;
        padding: 15px;
      }
      .pagination {
        padding-left: 50px;
      }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css"  media="screen,projection" charset="utf-8"/>
    <script type="text/javascript">
      var calendarAPI = jQuery.noConflict();
      calendarAPI(document).ready(function() {
        calendarAPI('#calendar').fullCalendar({
          header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,basicWeek,basicDay,listDay,listWeek'
          },
          views: {
            month: {
              buttonText: 'Month'
            },
            basicWeek: {
              buttonText: 'Week'
            },
            basicDay: {
              buttonText: 'Day'
            },
            listDay: {
              buttonText: 'List Day'
            },
            listWeek: {
              buttonText: 'List Week'
            }
          },
          navLinks: true, // can click day/week names to navigate views
          editable: true,
          height: 600,
          eventLimit: true, // allow "more" link when too many events
          eventSources: [
            {
              url: 'calendarDetails.php'
            },
            {
              url: 'reservationCalendar.php'
            }
          ],
          selectable: true,
          selectHelper: true,
          eventClick: function(event) {
            if (event.title == "Reservation") {
              var calendarDay = calendarAPI.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
              var longDate = calendarAPI.fullCalendar.formatDate(event.start, "MMMM DD, YYYY");
              calendarAPI.ajax({
                url: "reservationDetailsmodal.php?id=" + event.id,
                success: function(result){
                  calendarAPI("#reservationDayModalBody").html(result);
                  calendarAPI("#reservationDayModalLabel").text(longDate);
                }
              });
              //calendarAPI('#appointmentCalendarDetails').css("display", "block");
              calendarAPI('#reservationDayModal').css("background-color", "rgba(0,0,0,0.5)");
              calendarAPI("#reservationDayModal").fadeIn(90);
            } else if (event.title == "Appointment") {
              var calendarDay = calendarAPI.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
              var longDate = calendarAPI.fullCalendar.formatDate(event.start, "MMMM DD, YYYY");
              calendarAPI.ajax({
                url: "details.php?id=" + event.id,
                success: function(result){
                  calendarAPI("#appointmentCalendarDetailsBody").html(result);
                  calendarAPI("#appointmentCalendarDetailsLabel").text(longDate);
                }
              });
              //calendarAPI('#appointmentCalendarDetails').css("display", "block");
              calendarAPI('#appointmentCalendarDetails').css("background-color", "rgba(0,0,0,0.5)");
              calendarAPI("#appointmentCalendarDetails").fadeIn(90);
            }
          }
        });
      });
    </script>
    <style media="screen">
      .fc-today-button {
        text-transform: capitalize;
      }
      #calendar{
        margin-bottom: 20px;
        z-index: -1;
      }
      #calendarDayTable{
        z-index: -100;
      }
      #approvedReservationModal{
        z-index: 10000;
        background-color: rgba(0, 0, 0, 0.5);
      }
      #appointment-update-modal{
        z-index: 10000;
      }
      #appointment-cancel-modal{
        z-index: 10000;
        background-color: rgba(0,0,0,0.5);
      }
      #reservationFinishModal{
        z-index: 10000;
        background-color: rgba(0,0,0,0.5);
      }
      #updateStatusModal{
        z-index: 10000;
        background-color: rgba(0, 0, 0, 0.5);
      }
      #restoreReservationModal{
        background-color: rgba(0,0,0,0.5);
      }
      #colorSymbol{
        height: 10px;
        width: 10px;
      }
      #productReservation{
        background-color: #fff;
      }
      #appointment-grooming{
        background-color: #fff;
        margin-bottom: 10px;
      }
      #calendarDiv{
        padding: 10px;
        background-color: #fff;
        margin-bottom: 10px;
      }
      #appointmentButton{
        padding: 10px;
        margin-bottom: 0px;
      }
    </style>
    <script type="text/javascript">
      $(document).ready(function() {
        $('#appointmentTable').DataTable({
          "order": [[ 0, "asc" ]]
        });
        $('#productReservationTable').DataTable({
          "order": [[ 0, "asc" ]]
        });
      });
    </script>
    <script type="text/javascript">
      function appointmentCompleted(id) {
        document.getElementById("updateStatusModalLabel").innerHTML = "Complete Appointment";
        document.getElementById("updateStatusModalBody").innerHTML = "Update status to `Appointment Completed`";
        document.getElementById("updateStatusModalLink").href = "../code/updateAppointmentStatus.php?id=" + id + "&status=Completed";
      }
      function appointmentApprove(id) {
        document.getElementById("updateStatusModalLabel").innerHTML = "Approve Appointment";
        document.getElementById("updateStatusModalBody").innerHTML = "Are you sure to approve this appointment";
        document.getElementById("updateStatusModalLink").href = "../code/updateAppointmentStatus.php?id=" + id + "&status=Approved";
      }
      function appointmentCancelled(id) {
        document.getElementById("updateStatusModalLabel").innerHTML = "Cancel Appointment";
        document.getElementById("updateStatusModalBody").innerHTML = "Are you sure to cancel this appointment?`";
        document.getElementById("updateStatusModalLink").href = "../code/updateAppointmentStatus.php?id=" + id + "&status=Cancelled By Admin";
      }
      ///----------------------------------------------------------------
      function approveReservation(id) {
        document.getElementById("updateStatusModalLabel").innerHTML = "Approve Reservation";
        document.getElementById("updateStatusModalBody").innerHTML = "Are you sure to approve this reservation?";
        document.getElementById("updateStatusModalLink").href = "../code/updateReservationStatus.php?id=" + id + "&status=Approved";
      }
      function reservationReceived(id) {
        document.getElementById("updateStatusModalLabel").innerHTML = "Reservation Complete";
        document.getElementById("updateStatusModalBody").innerHTML = "Update status to `Completed`";
        document.getElementById("updateStatusModalLink").href = "../code/updateReservationStatus.php?id=" + id + "&status=Completed";
      }
      function cancelReservation(id) {
        document.getElementById("updateStatusModalLabel").innerHTML = "Cancel Reservation";
        document.getElementById("updateStatusModalBody").innerHTML = "Are you sure to cancel this reservation?";
        document.getElementById("updateStatusModalLink").href = "../code/updateReservationStatus.php?id=" + id + "&status=Cancelled By Admin";
      }
      function maxAppointment() {
        var max = prompt("Update slot");
        if (isNaN(max)) {
          alert("Must be a number");
        } else if (max >= 6) {
          alert("Must be less than 6");
        } else if (max <= 1) {
          alert("Must be greater than 1");
        } else {
          window.location = "../code/maxappointment.php?value=" + max;
        }
      }
      function addPetBreed() {
        var breed = prompt("New Breed Name:");
        if (!isNaN(breed)) {
          alert("Invalid Characters");
        } else if (breed.length >= 100) {
          alert("Maximum characters is 100");
        } else if (breed.length <= 3) {
          alert("Minimum characters is 3");
        } else {
          window.location.href = "../code/addPetBreed.php?name=" + breed;
        }
      }
    </script>
  </head>
  <body onload="calendar();">
    <!--- SideBar -->
    <?php getSidebar(); ?>
    <!--- Header -->
    <?php getHeader(); ?>
    <!--Page Content -->
    <div class="page-content">
      <div class="container">
        <?php if (isset($_GET['newBreed'])): ?>
          <?php if ($_GET['newBreed'] == "failed"): ?>
            <div class="alert alert-success" role="alert">
              <span>Please Try again</span>
            </div>
          <?php endif; ?>
          <?php if ($_GET['newBreed'] == "success"): ?>
            <div class="alert alert-success" role="alert">
              <span>New Breed has been added</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($_GET['close'])): ?>
          <?php if ($_GET['close'] == "success"): ?>
            <div class="alert alert-success" role="alert">
              <span>Store closed</span>
            </div>
          <?php endif; ?>
          <?php if ($_GET['close'] == "remove"): ?>
            <div class="alert alert-success" role="alert">
              <span>Store close remove</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($_GET['restore'])): ?>
          <?php if ($_GET['restore'] == "success"): ?>
            <div class="alert alert-success" role="alert">
              <span>Appointment Restored</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($_GET['availableslot'])): ?>
          <?php if ($_GET['availableslot'] == 1): ?>
            <div class="alert alert-success" role="alert">
              <span>Available slot has been updated</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($_GET['delete'])): ?>
          <?php if ($_GET['delete'] == "success"): ?>
            <div class="alert alert-success" role="alert">
              <span>Reservation Deleted</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <div id="appointmentButton">
          <a href="#appointment-grooming" class="btn btn-outline-info">Appointment</a>
          <a href="#productReservation" class="btn btn-outline-info">Reservation</a>
          <a href="#" class="btn btn-outline-dark" onclick="maxAppointment()">Update Available slot (<?php echo getAvailableSlot(); ?>)</a>
          <a href="#" class="btn btn-outline-dark" data-toggle="modal" data-target="#storeCloseModal">Make schedule for closing</a>
          <a href="#" class="btn btn-outline-dark" onclick="addPetBreed()">Add Pet Breed</a>
        </div>
        <br/>
        <div id="calendarDiv">
          <div id='calendar'></div>
          <div class="p-2">
            <span>Legend:</span>
          </div>
          <div class="pl-5">
            <table>
              <tr>
                <td>
                  <div id="colorSymbol" style="background-color:#ffff00;"></div>
                </td>
                <td>
                  <span>Reservation or Appointment Completed</span>
                </td>
              </tr>
              <tr>
                <td>
                  <div id="colorSymbol"style="background-color:#5cb85c;"></div>
                </td>
                <td>
                  <span> Approved</span>
                </td>
              </tr>
              <tr>
                <td>
                  <div id="colorSymbol"style="background-color:#d9534f;"></div>
                </td>
                <td>
                  <span> Expired or Cancelled by Admin or User</span>
                </td>
              </tr>
              <tr>
                <td>
                  <div id="colorSymbol"style="background-color:	#5bc0de;"></div>
                </td>
                <td>
                  <span> Pending</span>
                </td>
              </tr>
            </table>
          </div>
        </div>
        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12" id="appointment-grooming">
          <div>
            <h4>Appointments</h4>
            <?php if (isset($_GET['appointment-dashboard'])): ?>
              <a href="../appointment/#appointment-grooming" class="btn btn-outline-secondary">Show All</a>
            <?php endif; ?>
          </div>
          <div>
            <?php if (isset($_GET['appointment'])): ?>
              <div class="alert alert-success" role="alert">
                <span>Appointment <?php echo ucfirst($_GET['appointment']); ?></span>
              </div>
            <?php endif; ?>
            <table class="table table-striped" id="appointmentTable">
              <thead>
                <tr>
                  <td></td>
                  <td>Customer Name</td>
                  <td>Contact Number</td>
                  <td>Appointment Date</td>
                  <td>Status</td>
                  <td>Action</td>
                </tr>
              </thead>
              <tbody>
                <?php getAppointments(); ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12" id="productReservation">
          <div>
            <h4>Reservation</h4>
            <?php if (isset($_GET['reservation-dashboard'])): ?>
              <a href="../appointment/#productReservation" class="btn btn-outline-secondary">Show All</a>
            <?php endif; ?>
          </div>
          <div>
            <?php if (isset($_GET['reservation'])): ?>
              <div class="alert alert-success" role="alert">
                <span>Reservation <?php echo ucfirst($_GET['reservation']); ?></span>
              </div>
            <?php endif; ?>
            <table id="productReservationTable" class="table table-striped">
              <thead>
                <tr>
                  <th></th>
                  <th>Customer Name</th>
                  <th>Product Name</th>
                  <th>Reservation Date</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php dayDetails();?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!--Page Content-->
    <!--  Appointment Details Modal -->
    <div class="modal fade" id="appointment-update-modal" tabindex="-1" role="dialog" aria-labelledby="appointment-update-modal-label" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="appointment-update-modal-label"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="modal-appointmentUpdate">
            <div class="loader"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Calendar Day Modal -->
    <div class="modal" id="appointmentCalendarDetails" tabindex="10" role="dialog" aria-labelledby="appointmentCalendarDetailsLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="appointmentCalendarDetailsLabel">Calendar Day </h5>
            <button type="button" class="close" onclick="closeDay(this)" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="appointmentCalendarDetailsBody">
            <div class="loader"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" onclick="closeDay(this)">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Restore Reservation Modal -->
    <div class="modal fade" id="restoreReservationModal" tabindex="-1" role="dialog" aria-labelledby="restoreReservationModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="restoreReservationModalLabel">Restore Appointment</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="restoreReservation">
            ...
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
            <a href="#" class="btn btn-outline-primary" id="restoreReservationLink">Restore Appointment</a>
          </div>
        </div>
      </div>
    </div>
    <!-- Reservation Day Modal -->
    <div class="modal" id="reservationDayModal" tabindex="10" role="dialog" aria-labelledby="reservationDayModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="reservationDayModalLabel"></h5>
            <button type="button" class="close" onclick="closeOrder(this)" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="reservationDayModalBody">
            <div class="loader"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" onclick="closeOrder(this)">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Update Status -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="updateStatusModalLabel">Update Status</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="updateStatusModalBody">
            ...
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
            <a href="#" class="btn btn-outline-primary" id="updateStatusModalLink">Update Status</a>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="storeCloseModal" tabindex="-1" role="dialog" aria-labelledby="storeCloseModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="storeCloseModalLabel">Choose a day to close the store</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form class="" action="../code/storeClose.php" method="post" onsubmit="return validateForm()" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="form-group">
                <label for="date-close">Date</label>
                <input type="date" name="date-close" min="<?php echo date("Y-m-d", strtotime(date("Y-m-d") . '+3 day')); ?>" max="<?php echo date("Y-m-d", strtotime(date("Y-m-d") . '+1 year')); ?>" class="form-control" required/>
              </div>
              <div class="form-group">
                <label for="date-details">Details</label>
                <input type="text" maxlength="30" required name="close-details" class="form-control"/>
              </div>
              <div class="">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Details</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php getStoreCloseDate(); ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
              <input type="submit" name="submit" class="btn btn-outline-primary" value="Set">
            </div>
          </form>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      function closeDay(aa){
        document.getElementById('appointmentCalendarDetails').style.display = "none";
      }
      function closeOrder(aa){
        document.getElementById('reservationDayModal').style.display = "none";
      }
    </script>
  </body>
</html>
<?php
  function getStoreCloseDate() {
    try {
      $query = getConnection()->prepare("SELECT * FROM `date_close` ORDER BY `value` ASC");
      $query->execute();
      while ($row = $query->fetch()) {
        ?>
        <tr>
          <td><?php echo date('F d, Y', strtotime($row['value'])); ?></td>
          <td><?php echo $row['details']; ?></td>
          <td>
            <a href="../code/deleteDateClose.php?id=<?php echo $row['id'] . "&date=" .$row['value'] ?>" class="btn btn-outline-dark">
              <i class="fas fa-times"></i>
            </a>
          </td>
        </tr>
        <?php
      }
    } catch (Exception $e) {

    }

  }
?>
