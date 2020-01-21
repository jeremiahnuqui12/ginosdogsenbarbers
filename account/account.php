<?php
	include_once '../code/config.php';
	// Page Title Here
	$pageTitle = "Home | Gino's Dogs en Barbers";

	if (!isset($_SESSION['7a13ce2a07525b4fd46ebc0226706fab'])) {
		header("Location: ../account/?signin=1&reservation=1");
	}
	function checkAccountType(){
		if (getAccountType() == "Ginos Account") {
			echo"";
		} elseif (getAccountType() == "Google Account" || getAccountType() == "Facebook Account") {
			echo "disabled";
		}
	}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<?php getHeadLinks(); ?>
		<?php echo "<title>" . $pageTitle . "</title>"; ?>
		<style media="screen">
			#ChangePassword{
				padding-top: 150px;
			}
			#changePasswordForm{
				display: none;
			}
		</style>
		<script type="text/javascript">
			function ShowPassword(a){
				var x = document.getElementById('password-register');
				if (x.type == "password") {
					x.type = "text";
					a.innerText = "Hide Password";
					setTimeout(function(){
						x.type = "password";
						a.innerText = "Show Password";
					}, 500);
				}
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
            <li class="breadcrumb-item active" aria-current="page">My Account</li>
          </ol>
        </div>
				<?php if (isset($_GET['passwordrequest'])): ?>
					<?php if ($_GET['passwordrequest'] == "success"): ?>
						<div class="alert alert-success" role="alert">
							Check your email to change your password.
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<?php if (isset($_GET['passwordchange'])): ?>
					<?php if ($_GET['passwordchange'] == "success"): ?>
						<div class="alert alert-success" role="alert">
							Password Successfully Changed!
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<div class="col-lg-12 col-sm-12" style="text-align:justify;">
					<div class="mb-3">
						<button type="button" class="btn btn-outline-secondary" onclick="window.location.href='myappointment.php'">My Appointment</button>
						<button type="button" class="btn btn-outline-secondary" onclick="window.location.href='myproductreserve.php'">My Reservation</button>
						<button type="button" class="btn btn-outline-secondary" onclick="window.location.href='cart.php'">My Cart</button>
					</div>
					<div>
						<h3>Account Details</h3>
					</div>
					<form>
						<div class="form-group col-md-6">
							<label for="customer-name">Customer Name: </label>
							<input type="text" class="form-control" required name="customer-name" placeholder="Customer Name" value="<?php echo getSessionName(); ?>" onblur="validateEmailSignup(this)"/>
						</div>
						<?php if (!empty(getSessionEmail())): ?>
							<div class="form-group col-md-6">
								<label for="customer-email">Email address</label>
								<input type="email" class="form-control" required value="<?php echo getSessionEmail(); ?>" id="customer-email" name="email" placeholder="Email Address" onblur="validateEmailSignup(this)"/>
							</div>
						<?php endif; ?>
						<div class="form-group col-md-6">
							<label for="customer-registerAt">Register At: </label>
							<input type="text" class="form-control" id="customer-registerAt" readonly required value="<?php echo date('F d, Y h:i a', strtotime(getSessionRegisterAt()));; ?>"/>
						</div>
						<div class="form-group col-md-6">
							<label for="customer-registerAt">Password: </label>
							<button type="button" class="btn btn-primary" <?php checkAccountType(); ?> data-toggle="modal" data-target="#changePasswordModal">
								Change Password
							</button>
						</div>
					</form>
				</div>
				<div class="col-md-12" id="changePasswordForm">
					<form class="" action="index.html" method="post">
						<fieldset>
							<legend>Change Password</legend>
							<div class="form-group col-md-3">
								<label for="new-password">New Password</label>
								<input type="password" class="form-control" name="newPassword" min="10" id="new-password"  placeholder="New Password"/>
								<a href="#" onclick="ShowPassword(this)">Show Password</a>
								<br>
							</div>
							<div class="form-group col-md-3">
								<label for="confirm-password">Confirm Password</label>
								<input type="text" class="form-control" name="confirmPassword" min="10" id="confirm-password" placeholder="Confirm Password"/>
								<a href="#" onclick="ShowPassword(this)">Show Password</a>
								<br>
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
		<!-- End of Page Content  -->
		<?php getPageFooter(); ?>
		<?php getFooterLinks(); ?>
		<script type="text/javascript">
			function changePassword(){
				if(document.getElementById("customer-email")){
					window.location.href="../code/requestChangePassword.php";
				} else {
					$(document).ready(function() {
						$("#changePasswordModal").modal('toggle');
						$("#changePasswordModal").css("display", "none");
						$('#changePasswordForm').css("display", "block");
					});
				}
			}
		</script>
		<!-- Modal Change Password Request -->
		<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
						<span>Are you sure to change your password?</span>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
		        <button type="button" class="btn btn-primary" onclick="changePassword();">Change</button>
		      </div>
		    </div>
		  </div>
		</div>
		<!-- Change Password Modal -->
		<div class="modal fade" id="ChangePassword" tabindex="-1" role="dialog" aria-labelledby="ChangePasswordLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="ChangePasswordLabel">Modal title</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
					<form>
			      <div class="modal-body">
							<input type="text" name="sd" value="">

			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
							<input type="submit"  class="btn btn-primary" value="Change Password"/>
			      </div>
					</form>
		    </div>
		  </div>
		</div>
	</body>
</html>
