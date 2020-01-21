<?php
require '../code/config.php';
function getAppDetails($status){
  try {
    $query = getConnection()->prepare("SELECT
      `customer_order`.`id` AS `prodResId`,
      `product`.`id` AS `productId`,
      `product`.`image` AS `productImage`,
      `product`.`name` AS `productName`,
      `customer_order`.`quantity` AS `quantity`,
      `product`.`price` AS `productPrice`,
      `customer_order`.`status` AS `productStatus`,
      `customer_order`.`email_verified` AS `emailVerified`,
      `customer_order`.`reservation_date` AS `reservation_date`
      FROM `customer_order`
      INNER JOIN `product` ON `product`.`id`=`customer_order`.`product_id`
      WHERE `customer_id`= :customerId AND `customer_order`.`status` LIKE :status");
      $query->execute(
        array(
          ":customerId" => getSessionCustomerId(),
          ":status" => $status
        )
      );
      if ($query->rowCount() == 0) {
        noOrderFound();
      } else {
        while ($row = $query->fetch()) {
          ?>
          <tr>
            <td>
                <img src="data:image/jpeg;base64,<?php echo $row['productImage']; ?>" alt="Product Image" height="50" width="50">
            </td>
            <td style="width:300px;padding-top:2%;padding-bottom:2%;word-wrap: break-word;">
              <a href="../products/details.php?id=<?php echo $row['productId']; ?>" style="margin-left:10px;" title="<?php echo $row['productName']; ?>"><?php echo substr($row['productName'], 0, 25) . "... "; ?></a><?php echo " x " . $row['quantity'] ?>
            </td>
            <td style="text-align: center;padding-top:2%;padding-bottom:2%;" id="total-price-<?php echo $row['prodResId']; ?>">
              &#8369; <span id="product-price-<?php echo $row['prodResId']; ?>"><?php echo ($row['productPrice'] * $row['quantity']) . ".00"; ?></span>
            </td>
            <td style="text-align: center;padding-top:2%;padding-bottom:2%;">
              <?php echo date('F d, Y h:i a', strtotime($row['reservation_date'])); ?>
            </td>
            <?php if ($row['productStatus'] != "Cancelled By Customer" && $row['productStatus'] != "Cancelled By Admin" && $row['productStatus'] != "Product Received" && $row['productStatus'] != "Expired"): ?>
              <td>
                <button type="button" name="button" class="btn btn-outline-dark" data-toggle="modal" onclick="reservationDetails(<?php echo $row['prodResId']; ?>)"data-target="#reservationDetailsModal">
                  <i class="fa fa-calendar"></i>
                </button>
                <button type="button" name="button" <?php echo checkStatus($row['productStatus']); ?> onclick="cancelAppointment(<?php echo $row['prodResId']; ?>,'<?php echo $row['productName']; ?>')" title="Cancel Reservation" class="btn btn-outline-danger" data-toggle="modal" data-target="#cancelOrderModal">
                  <i class="fa fa-ban"></i>
                </button>
              </td>
            <?php endif; ?>
          </tr>
          <?php
        }
      }
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}
function checkStatus($status){
  if($status =="Order Received"){
    return "disabled";
  } else if ($status == "Deleted") {
    return "disabled";
  } else if ($status == "Cancelled by Admin" || $status == "Cancelled By Customer") {
    return "disabled";
  }
}
function noOrderFound(){
  ?>
  <tr>
    <td colspan="10">No Reservation Found <a href="../products/" class="btn btn-primary">Reserve Now</a></td>
  </tr>
  <?php
}
	$pageTitle = "My Reservation | Gino's Dogs en Barbers";

	if (!isset($_SESSION['7a13ce2a07525b4fd46ebc0226706fab'])) {
		header("Location: ../account/?signin=1&reservation=1");
	}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<?php getHeadLinks(); ?>
		<title><?php echo $pageTitle; ?></title>
    <script type="text/javascript">
      function cancelAppointment(id, name) {
        document.getElementById("cancelOrderBody").innerHTML = "Are you Sure to cancel your order: " + name + "?";
        document.getElementById("cancelOrderModalButton").href="../code/cancelOrder.php?id=" + id;
      }
    </script>
    <style media="screen">
      .card-body{
        padding:5px;
      }
    </style>
    <script type="text/javascript">
      function reservationDetails(id) {
        if (window.XMLHttpRequest) {
          // code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
        } else {  // code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function() {
          if (this.readyState==4 && this.status==200) {
            var reservationDetails = JSON.parse(this.responseText);
            document.getElementById("reservation-contact").value = reservationDetails[0];
            document.getElementById("reservation-dateSubmitted").value = reservationDetails[1];
            document.getElementById("reservation-date").value = reservationDetails[2];
            document.getElementById("reservation-productName").value = reservationDetails[3];
            document.getElementById("reservation-quantity").value = reservationDetails[4];
            document.getElementById("reservation-price").value = reservationDetails[5];
            document.getElementById("reservation-subTotal").value = reservationDetails[6];
            document.getElementById("reservation-status").value = reservationDetails[7];
            document.getElementById("reservation-emailVerified").value = reservationDetails[8];
          }
        }
        xmlhttp.open("GET","reservationDetails.php?id="+id,true);
        xmlhttp.send();
      }
    </script>
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
  								My Account
  							</a>
  						</li>
  						<li class="breadcrumb-item active" aria-current="page">My Reservation</li>
  				  </ol>
  				</div>
          <?php if (isset($_GET['reservation'])): ?>
            <?php if ($_GET['reservation'] == "success"): ?>
              <div class="alert alert-success" role="alert">
                <span>Reservation Success. Go to your email to verify your reservation</span>
              </div>
            <?php endif; ?>
          <?php endif; ?>
          <?php if (isset($_GET['cancelled'])): ?>
            <?php if ($_GET['cancelled'] == "success"): ?>
              <div class="alert alert-success" role="alert">
                <span>Reservation has been cancelled.</span>
              </div>
            <?php endif; ?>
          <?php endif; ?>
          <?php if (isset($_GET['verify'])): ?>
            <?php if ($_GET['verify']== "success"): ?>
              <div class="alert alert-success" role="alert">
                <span>Reservation Verified</span>
              </div>
            <?php endif; ?>
            <?php if ($_GET['verify'] == "failed"): ?>
              <div class="alert alert-danger" role="alert">
                <span>Reservation Verification Failed: <?php echo $_GET['message']; ?></span>
              </div>
            <?php endif; ?>
          <?php endif; ?>
  			<div class="col-lg-12 col-sm-12" style="text-align:center;">
            <h3>My Reservation</h3>
  			</div>
        <br/>
  			<div class="col-md-12 com-sm-12 col-xs-12 col-lg-12">
          <div>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <a class="nav-item nav-link active col-md-2" id="nav-pending-tab" data-toggle="tab" href="#nav-pending" role="tab" aria-controls="nav-pending" aria-selected="true">Pending</a>
              <a class="nav-item nav-link col-md-3" id="nav-approved-tab" data-toggle="tab" href="#nav-approved" role="tab" aria-controls="nav-approved" aria-selected="false">Approved</a>
              <a class="nav-item nav-link col-md-3" id="nav-done-tab" data-toggle="tab" href="#nav-done" role="tab" aria-controls="nav-done" aria-selected="false">Completed</a>
              <a class="nav-item nav-link col-md-2" id="nav-cancelled-tab" data-toggle="tab" href="#nav-cancelled" role="tab" aria-controls="nav-cancelled" aria-selected="false">Cancelled</a>
              <a class="nav-item nav-link col-md-2" id="nav-expired-tab" data-toggle="tab" href="#nav-expired" role="tab" aria-controls="nav-expired" aria-selected="false">Expired</a>
            </div>
            <div class="tab-content p-3" id="nav-tabContent">
              <div class="tab-pane fade show active" id="nav-pending" role="tabpanel" aria-labelledby="nav-pending-tab">
                <table class="table table-striped cart-details">
                  <thead>
                    <tr class="text-center">
                      <th colspan="2">Details</th>
                      <th>Price</th>
                      <th>Reservation Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php getAppDetails("Pending"); ?>
                  </tbody>
                </table>

              </div>
              <div class="tab-pane fade" id="nav-approved" role="tabpanel" aria-labelledby="nav-approved-tab">
                <table class="table table-striped cart-details">
                  <thead>
                    <tr class="text-center">
                      <th colspan="2">Details</th>
                      <th>Price</th>
                      <th>Reservation Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php getAppDetails("Approved"); ?>
                  </tbody>
                </table>
              </div>
              <div class="tab-pane fade" id="nav-done" role="tabpanel" aria-labelledby="nav-done-tab">
                <table class="table table-striped cart-details">
                  <thead>
                    <tr class="text-center">
                      <th colspan="2">Details</th>
                      <th>Price</th>
                      <th>Reservation Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php getAppDetails("Completed"); ?>
                  </tbody>
                </table>
              </div>
              <div class="tab-pane fade" id="nav-cancelled" role="tabpanel" aria-labelledby="nav-cancelled-tab">
                <table class="table table-striped cart-details">
                  <thead>
                    <tr class="text-center">
                      <th colspan="2">Details</th>
                      <th>Price</th>
                      <th>Reservation Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php getAppDetails("Cancelled%"); ?>
                  </tbody>
                </table>
              </div>
              <div class="tab-pane fade" id="nav-expired" role="tabpanel" aria-labelledby="nav-expired-tab">
                <table class="table table-striped cart-details">
                  <thead>
                    <tr class="text-center">
                      <th colspan="2">Details</th>
                      <th>Price</th>
                      <th>Reservation Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php getAppDetails("Expired"); ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
		  </div>
		</div>
		<!-- End of Page Content  -->
		<?php getPageFooter(); ?>
		<?php getFooterLinks(); ?>
    <!-- --------------------------->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" role="dialog" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Reservation</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="cancelOrderBody">
            <span>Are you sure to cancel this ?</span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <a href="#" class="btn btn-primary" id="cancelOrderModalButton">Cancel Reservation</a>
          </div>
        </div>
      </div>
    </div>
    <!-- ------------------------------------->
    <div class="modal fade" id="detailsOrderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Reservation Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="reservationDetailsModal" tabindex="-1" role="dialog" aria-labelledby="reservationDetailsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="reservationDetailsModalLabel">Reservation Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="#" method="post">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Contact Number : </label>
                  <input class="form-control" type="text" id="reservation-contact" readonly value=""/>
                </div>
                <div class="form-group col-md-6">
                  <label>Date submitted : </label>
                  <input class="form-control" type="text" id="reservation-dateSubmitted" readonly value=""/>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Reservation Date : </label>
                  <input class="form-control" type="text" id="reservation-date" readonly value=""/>
                </div>

              </div>
              <div class="form-row">
                <div class="form-group col-md-10">
                  <label for="">Product Name</label>
                  <input type="text" readonly class="form-control" id="reservation-productName" value="">
                </div>
                <div class="form-group col-md-2">
                  <label for="">Quantity</label>
                  <input type="text" readonly class="form-control" id="reservation-quantity" value="">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="">Product Price</label>
                  <input type="text" readonly class="form-control" id="reservation-price" value="">
                </div>
                <div class="form-group col-md-6">
                  <label for="">Sub-total</label>
                  <input type="text" readonly class="form-control" id="reservation-subTotal" value="">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="">Status</label>
                  <input type="text" readonly class="form-control" id="reservation-status" value="">
                </div>
                <div class="form-group col-md-6">
                  <label for="">Email Verified</label>
                  <input type="text" readonly class="form-control" id="reservation-emailVerified" value="">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
	</body>
</html>
