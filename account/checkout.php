<?php
	require '../code/config.php';
	// Page Title Here
	if (empty($_POST['selected-product'])) {
		header("Location: cart.php?select=true");
	}
	$pageTitle = "Checkout | Gino's Dogs en Barbers";
	function app_time(){
		$time12Hour = array(
			"08:00 AM",
			"08:30 AM",
			"09:00 AM",
			"09:30 AM",
			"10:00 AM",
			"10:30 AM",
			"11:00 AM",
			"11:30 AM",
			"12:00 PM",
			"12:30 PM",
			"01:00 PM",
			"01:30 PM",
			"02:00 PM",
			"02:30 PM",
			"03:00 PM",
			"03:30 PM",
			"04:00 PM",
			"04:30 PM",
			"05:00 PM",
			"05:30 PM",
			"06:00 PM",
			"06:30 PM",
		);
		$time24Hour = array(
			"08:00",
			"08:30",
			"09:00",
			"09:30",
			"10:00",
			"10:30",
			"11:00",
			"11:30",
			"12:00",
			"12:30",
			"13:00",
			"13:30",
			"14:00",
			"14:30",
			"15:00",
			"15:30",
			"16:00",
			"16:30",
			"17:00",
			"17:30",
			"18:00",
			"18:30"
		);
		for ($aa=0; $aa < count($time12Hour); $aa++) {
			echo "<option value=\"" . $time24Hour[$aa] ."\">" . $time12Hour[$aa] . "</option>";
		}
	}
  function cartDetails(){
		try {
			$selected = $_POST['selected-product'];
			for ($x=0; $x < count($selected); $x++) {
				$query = getConnection()->prepare("SELECT
					`customer_order`.`id` AS `prodResId`,
					`product`.`id` AS `productId`,
					`product`.`image` AS `productImage`,
					`product`.`name` AS `productName`,
					`customer_order`.`quantity` AS `quantity`,
					`product`.`price` AS `productPrice`
					FROM `customer_order`
					INNER JOIN `product` ON `product`.`id`=`customer_order`.`product_id`
					WHERE `customer_id`=:customerId
					AND `customer_order`.`status`=:status
					AND `customer_order`.`id` = :id");
					$query->execute(
						array(
							":customerId" => getSessionCustomerId(),
							":status" => "On-Cart",
							":id" => $selected[$x]
						)
					);
					if ($query->rowCount() == 0) {
						echo "<script>window.location.href=\"cart.php\"</script>";
					} else {
						while ($row = $query->fetch()) {
							?>
							<tr>
								<td>
										<img src="data:image/jpeg;base64,<?php echo $row['productImage']; ?>" alt="Product Image" height="50" width="50">
								</td>
								<td style="width:400px;padding-top:2%;padding-bottom:2%;word-wrap: break-word;">
									<span style="margin-left:10px;"><?php echo $row['productName']; ?></span>
								</td>
								<td style="text-align: center;padding-top:2%;padding-bottom:2%;">
									&#8369; <span id="product-price-<?php echo $row['prodResId']; ?>"><?php echo $row['productPrice']; ?></span>
								</td>
								<td style="text-align: center;padding-top:2%;padding-bottom:2%;">
									<span><?php echo $row['quantity']; ?></span>
								</td>
								<td style="text-align: center;padding-top:2%;padding-bottom:2%;" id="total-price-<?php echo $row['prodResId']; ?>">
									&#8369; <span id="product-price-<?php echo $row['prodResId']; ?>"><?php echo number_format((float)($row['productPrice'] * $row['quantity']), 2, '.', ''); ?></span>
								</td>
							</tr>
							<?php
						}
					}
			}
		} catch (Exception $e) {
			echo "Cart Error: " . $e->getMessage();
		}
	}
  function TotalPrice(){
		try {
			$selected = $_POST['selected-product'];
			$total=0;
			for ($x=0; $x < count($selected); $x++) {
				$query = getConnection()->prepare("SELECT
					SUM(`quantity`*`price`) AS `totalPrice`
					FROM `customer_order`
					INNER JOIN `product` ON `product`.`id`=`customer_order`.`product_id`
					WHERE `customer_id`=:id
					AND `customer_order`.`status`=:status
					AND `customer_order`.`id` = :idx
				");
				$query->execute(
					array(
						":id" => getSessionCustomerId(),
						":status" => "On-Cart",
						":idx" => $selected[$x]
					)
				);
				$total =  $total + $query->fetch()[0];
			}
			return number_format((float)($total), 2, '.', '');
		} catch (Exception $e) {
			echo "Total Price Error: " . $e->getMessage();
		}
	}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<?php getHeadLinks(); ?>
		<?php echo "<title>" . $pageTitle . "</title>"; ?>
    <style media="screen">
      .cart-details th, .cart-details td{
        padding: 5px;
      }
    </style>
		<script type="text/javascript">
			function checkContactNumber(x) {
				var spanId = document.getElementById('contactError');
				if (x.value.length == 11) {
					x.type = "text";
					x.value = "+63 (" + x.value.substring(1, 4) + ")-" + x.value.substring(4, 7) + "-" + x.value.substring(7, 11);
					removeErrorMessage(spanId, x);
				} else if(x.value.length == 0) {
					getErrorMessage("Required", spanId, x);
				} else if(x.value.length < 11) {
					getErrorMessage("Incomplete Number", spanId, x);
				} else if(x.value.substring(0, 3) == "+63") {
					removeErrorMessage(spanId, x);
				} else if(x.value.length > 11) {
					getErrorMessage("Max number is 11 digit", spanId, x);
				}  else if (!x.value.match(/^[0-9\-\)\(\ \+]+$/)) {
					getErrorMessage("Invalid Characters", spanId, x);
				} else if (x.value.substring(0, 1) != 09 && x.value.substring(0, 6) != "+63 (9") {
					getErrorMessage("Invalid Number", spanId, x);
				}
			}
			function getErrorMessage(message, spanId, x) {
				spanId.innerText = message;
				x.style.border = "1px solid#ff0000";
			}
			function removeErrorMessage(spanId, x){
				spanId.innerText = "";
				x.style.border = "1px solid#a9a9a9";
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
              <a href="account.php">My Account</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">
              <a href="cart.php">My Cart</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Check Out</li>
          </ol>
        </div>
        <div class="col-md-12">
          <div>
            <h3>Checkout </h3>
          </div>
          <div>
						<div class="col-md-12 mb-3">
							<form class="" action="../code/customerReservation.php" method="post" enctype="multipart/form-data">
								<?php
									$selected = $_POST['selected-product'];
									for ($x=0; $x < count($selected); $x++) {
									?>
									<input type="hidden" name="selected-product[]" value="<?php echo $selected[$x]; ?>">
									<?php
									}
								?>
								<div class="form-row">
									<div class="form-group col-md-4">
										<label for="date">Reservation Date:</label>
										<input type="date" class="form-control" min="<?php echo date("Y-m-d", strtotime(date("Y-m-d") . '+3 day')); ?>" max="<?php echo date("Y-m-d", strtotime(date("Y-m-d") . '+1 year')); ?>" required name="reservationDate"/>
										<?php if (isset($_GET['invalidDate'])): ?>
											<?php if ($_GET['invalidDate'] == "tooHigh"): ?>
												<span class="text-danger">Maximum year is <?php echo date("Y") + 1; ?></span>
											<?php endif; ?>
										<?php endif; ?>
									</div>
									<div class="form-group col-md-4">
										<label for="date">Reservation Time:</label>
										<select class="form-control" required name="reservationTime">
											<option value="">--Select--</option>
											<?php app_time(); ?>
										</select>
									</div>
									<div class="form-group col-md-4">
										<label for="owner-contact-number">Contact Number: (Mobile Number Only)</label>
										<input class="form-control" id="owner-contact-number" required type="text" maxlength="11" placeholder="Contact Number" onblur="checkContactNumber(this);" name="contact_number" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode === 8"/>
										<span class="text-danger" id="contactError">
											<?php if (isset($_GET['contact'])): ?>
												<?php if ($_GET['contact'] == "invalid"): ?>
													Invalid Contact Number
												<?php endif; ?>
											<?php endif; ?>
										</span>
									</div>
								</div>
								<input type="submit" name="submit" class="btn btn-primary" value="Reserve Items Now"/>
							</form>
						</div>

            <table class="table table-striped cart-details">
              <thead>
                <tr class="text-center">
                  <th colspan="2">Details</th>
                  <th>Item Price</th>
                  <th>Quantity</th>
                  <th>Total Price</th>
                </tr>
              </thead>
              <tbody>
  							<?php cartDetails(); ?>
              </tbody>
            </table>
            <div class="cart-footer col-md-12 text-right">
    					<div class="font-weight-bold float-right mr-4">
    						<span>Total Price:</span>
    						<span id="totalPrice"><?php echo TotalPrice(); ?></span>
    					</div>
    				</div>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
		</div>
		<!-- End of Page Content  -->
		<?php getPageFooter(); ?>
		<?php getFooterLinks(); ?>
	</body>
</html>
