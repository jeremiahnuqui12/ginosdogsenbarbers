<?php
	require 'code/config.php';
	// Page Title Here
	$pageTitle = "Contact Us | Gino's Dogs en Barbers";
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<?php getHeadLinks(); ?>
		<?php echo "<title>" . $pageTitle . "</title>"; ?>
		<style media="screen">
			.contact-form{
				border-right: 1px solid#000;
			}
			.location {
			  padding:10px;
				height:400px;
				margin-bottom:100px;
			}
			#map {
        height: 100%;
				width: 100%;
      }
			textarea{
				resize: none;
			}
		</style>
		<script type="text/javascript">
		function validateName(name){
			var spanId = document.getElementById('name-error');
			if (name.value.length == 0) {
				getError("Required", spanId, name, true);
				disabledSignupButton(true);
			} else if (!name.value.match(/^[a-zA-Z ]+$/)) {
				getError("Invalid Name", spanId, name, true);
				disabledSignupButton(true);
			} else {
				getError("", spanId, name, false);
				disabledSignupButton(false);
			}
		}
		function getError(message, spanId, inputBox, border){
			spanId.className = " text-danger";
			spanId.innerText = message;
			if(border){
				inputBox.style.border = "1px solid #ff0000";
			} else {
				inputBox.style.border = "";
			}
		}
		function validateEmail(email){
			var spanId = document.getElementById('email-error');
			if (email.value.length == 0) {
				getError("Required", spanId, email, true);
				disabledSignupButton(true)
			} else if (!email.value.match("@gmail.com") && !email.value.match("@yahoo.com")) {
				getError("Invalid Email(Gmail or Yahoo Email Only)", spanId, email, true);
				disabledSignupButton(true)
			} else {
				getError("", spanId, email, false);
				disabledSignupButton(false)
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
              <a href="index.php">Home</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
          </ol>
        </div>
				<div class="contact-form float-left col-md-6 col-sm-12 col-xs-12">
					<?php if (isset($_GET['send'])): ?>
						<?php if ($_GET['send'] == "success"): ?>
							<div class="alert alert-success" role="alert">
								<span>Message successfully sent</span>
							</div>
						<?php endif; ?>
					<?php endif; ?>
					<div>
						<h3>Send us a message here</h3>
					</div>
					<div>
						<form action="code/contact.php" method="post">
							<div class="form-group col-md-12">
								<label for="fullname">Full Name</label>
								<input type="text" class="form-control" required maxlength="30" id="fullname" name="name" placeholder="Full Name" onblur="validateName(this)"/>
								<span class="text-danger" id="name-error">
									<?php if (isset($_GET['name'])): ?>
										<?php if ($_GET['name'] == "required"): ?>
											<span>Required</span>
										<?php endif; ?>
										<?php if ($_GET['name'] == "invalid"): ?>
											<span>Invalid</span>
										<?php endif; ?>
									<?php endif; ?>
								</span>
							</div>
							<div class="form-group col-md-12">
								<label for="email">Email address</label>
								<input type="email" class="form-control" required maxlength="30" name="email" id="email" placeholder="Email" onblur="validateEmail(this);"/>
								<span class="text-danger col-md-12" id="email-error"></span>
							</div>
							<div class="form-group col-md-12">
								<label for="fullname">Message</label>
								<textarea name="message" maxlength="250" class="form-control" rows="8" cols="80"></textarea>
								<span class="text-danger" id="message-error"></span>
							</div>
							<input type="submit" name="SendMessage" class="btn btn-outline-primary" value="Send Messages"/>
						</form>
					</div>
				</div>
				<div class="location float-right col-md-6 col-sm-12">
					<h5>Store Address: 145B 10th Avenue Caloocan 1400</h5>
					<div id="map" style="width:100%;"></div>
					<br/>
					<iframe src="https://www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FDogsEnBarbers%2F&width=450&layout=standard&action=like&size=small&show_faces=false&share=true&height=35&appId=243215186296081" width="450" height="35" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media"></iframe>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<!-- End of Page Content  -->
		<?php getPageFooter(); ?>
		<!-- Registration Success Modal -->
		<div id="myModal" class="modal fade" role="dialog" style="margin-top:200px;">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-body">
						<h3>Sending Inquiry Message Success!!</h3>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			function initMap() {
				var map = new google.maps.Map(document.getElementById('map'), {
					zoom: 20,
          center: {
						lat: 14.651800,
						 lng: 120.981313
					 }
				 });
        // Define a symbol using a predefined path (an arrow)
        // supplied by the Google Maps JavaScript API.
        var lineSymbol = {
          path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
        };
        // Create the polyline and add the symbol via the 'icons' property.
        var line = new google.maps.Polyline({
          path: [{lat: 22.291, lng: 153.027}, {lat: 18.291, lng: 153.027}],
          icons: [{
            icon: lineSymbol,
            offset: '100%'
          }],
          map: map
        });
      }
		</script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAIPwpen2v5wDLhctwLzp_swliPvh_gGrw&callback=initMap&language=en"
        async defer>
    </script>
		<?php getFooterLinks(); ?>
	</body>
</html>
