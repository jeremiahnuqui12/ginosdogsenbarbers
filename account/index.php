<?php
include_once '../code/config.php';
require '../code/error.php';
	// Page Title Here
	$pageTitle = "Account | Gino's Dogs en Barbers";
	//-------------------------------------
	if (isset($_GET['email'])) {
		if ($_GET['email'] == "0") {
			$signinEmailError = "<span class=\"text-danger\">Email Not Exist</span>";
		}
		if ($_GET['email'] == "exist") {
			$emailError = "<span class=\"text-danger\">Email Already Exist</span>";
		}
		if ($_GET['email'] == "block") {
			$signinEmailError = "<span class=\"text-danger\">Email has been block</span>";
		}
	}
	//------------------------------------------------
	if (isset($_GET['password'])) {
		if ($_GET['password'] == 0) {
			$signinPasswordError = "<span class=\"text-danger\">Incorrect Password</span>";
		}
	}
	//---------------------------------------------------
	if (isset($_SESSION['7a13ce2a07525b4fd46ebc0226706fab'])) {
		header("Location: ../");
	}
	if (isset($_GET['reservation'])) {
		if ($_GET['reservation'] == 1) {
			$reservationFirst = "<span class=\"text-danger alert\">You Need to Sign-In First</span>";
		}
	}
	function checkIfAppisSet(){
		if (isset($_GET['date']) && isset($_GET['time'])) {?>
			<input type="hidden" name="appDate" value="<?php echo $_GET['date']; ?>"/>
			<input type="hidden" name="appTime" value="<?php echo $_GET['time']; ?>">
		<?php }
		elseif (isset($_GET['product_id'])) { ?>
			<input type="hidden" name="product_id" value="<?php echo $_GET['product_id']; ?>">
		<?php }
	}
	function checkAppTime(){
		if (isset($_GET['date']) && isset($_GET['time'])) {
			echo "&appDate=" . $_GET['date'] . "&appTime=" . $_GET['time'];
		} elseif (isset($_GET['product_id'])) {
			echo "&product_id=" . $_GET['product_id'];
		}
	}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<?php getHeadLinks(); ?>
		<?php echo "<title>" . $pageTitle . "</title>"; ?>
		<script type="text/javascript">
		    function onSignIn(googleUser) {
            // Useful data for your client-side scripts:
            var profile = googleUser.getBasicProfile();
            console.log("ID: " + profile.getId()); // Don't send this directly to your server!
            console.log('Full Name: ' + profile.getName());
            console.log('Given Name: ' + profile.getGivenName());
            console.log('Family Name: ' + profile.getFamilyName());
            console.log("Image URL: " + profile.getImageUrl());
            console.log("Email: " + profile.getEmail());

            // The ID token you need to pass to your backend:
            var id_token = googleUser.getAuthResponse().id_token;
            console.log("ID Token: " + id_token);
            window.location.href="../code/google_signin.php?name=" + profile.getName() + "&email=" + profile.getEmail() + "&img_url=" + profile.getImageUrl() + "&token=" +  googleUser.getAuthResponse().id_token + "<?php checkAppTime();?>";
        }
        //sign out
        function signOut() {
            gapi.auth2.getAuthInstance().signOut();
        }

		</script>
		<style media="screen">
			.login-form{
				border-bottom: 1px solid#a0a0a0;
				padding-bottom: 10px;
			}
			.social-account{
        border-top: 1px solid#a0a0a0;
      }
			.register-form{
				border-left: 2px solid #a0a0a0;
				height: auto;
				padding: 20px;
      }
			@media screen and (max-width: 800px) {
				.login-form{
					padding: 20px;
					margin: 0px;
					border-bottom: 1px solid#a0a0a0;
				}
				.social-account{
					padding: 20px;
					margin: 0px;
	        border-bottom: 1px solid#a0a0a0;
					border-top: 0px;
	      }
				.register-form{
					border-left: 0px;
					padding: 0px;
					margin: 0px;
					border-bottom: 1px solid #a0a0a0;
	      }
			}
		</style>
	  <style type="text/css">
	  #google-button{
	    background-color: transparent;
	    border: 2px solid#4285f4;
	    padding: 0px;
			outline: none;
	  }
	  #google-button:hover{
	    cursor: pointer;
	  }
	  #google-button>img{
	    width: 250px;
	    height: 50px;
	  }
	  #facebook-button{
	    background-color: transparent;
	    border: 2px solid#3b579d;
	    padding: 0px;
	  }
		/*-----------*/
		#email{
			width: 400px;
			margin: 0px;
		}
		#password, #loginPassword {
			width: 400px;
		}
		.text-danger {
			margin: 0px;
			padding: 0px;
		}
	  </style>
		<script type="text/javascript">
			function validateEmail(email) {
				var spanId = document.getElementById('signin-email-error');
				if (email.value.length == 0) {
					getError("Required", spanId, email, true);
					disabledSigninButton(true);
					return false;
				} else if (!email.value.match("@gmail.com") && !email.value.match("@yahoo.com")) {
					getError("Invalid Email(Gmail or Yahoo Email Only)", spanId, email, true);
					disabledSigninButton(true);
					return false;
				} else {
					getError("", spanId, email, false);
					disabledSigninButton(false);
					return true;
				}
			}
			function validatePassword(password){
				var spanId = document.getElementById('signin-password-error');
				if (password.value.length == 0) {
					getError("Required", spanId, password, true);
					disabledSigninButton(true);
					return false;
				} else {
					getError("", spanId, password, false);
					disabledSigninButton(false);
					return true;
				}
			}
			function validateEmailSignup(email){
				var spanId = document.getElementById('signup-email-error');
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
			function validateName(name){
				var spanId = document.getElementById('signup-name-error');
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
			function validateSignupPassword(password){
				var spanId = document.getElementById('signup-password-error');
				if (password.value.length == 0) {
					getError("Required", spanId, password, true);
					disabledSignupButton(true);
				} else {
					getError("", spanId, password, false);
					disabledSignupButton(false);
				}
			}
			function validateSignupConfirmPassword(password) {
				var x = document.getElementById("password-register");
				var spanId = document.getElementById('signup-confirm-password-error');
				if (password.value.length == 0) {
					getError("Required", spanId, password, true);
					disabledSignupButton(true);
				} else if (x.value != password.value) {
					getError("Password Not Match", spanId, password, true);
					disabledSignupButton(true);
				} else if (x.value === password.value) {
					spanId.className = " text-success";
					spanId.innerText = "Password Match";
					password.style.border = "";
					//getError("Password Match", spanId, password, false);
					disabledSignupButton(false);
				} else {
					getError("", spanId, password, false);
					disabledSignupButton(false);
				}
			}
			function disabledSigninButton(x){
				document.getElementById('signinButton').disabled = x;
			}
			function disabledSignupButton(x){
				document.getElementById('signupButton').disabled = x;
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
			function validateSigninForm(){
				var email = document.forms["signinForm"]["email"];
				var password = document.forms["signinForm"]["password"];
				validateEmail(email);
				validatePassword(password);
			}
		</script>
		<script>
		  // This is called with the results from from FB.getLoginStatus().
		  function statusChangeCallback(response) {
		    console.log('statusChangeCallback');
		    console.log(response);
		    if (response.status === 'connected') {
		      testAPI();
		    } else {
		      document.getElementById('status').innerHTML = 'Please log ' +
		        'into this app.';
		    }
		  }

		  function checkLoginState() {
		    FB.getLoginStatus(function(response) {
		      statusChangeCallback(response);
		    });
		  }

		  window.fbAsyncInit = function() {
		    FB.init({
		      //appId      : '544437452646519',
		      appId      : '243215186296081',
		      cookie     : true,
		      xfbml      : true,
		      version    : 'v3.1'
		    });
		  };

		  // Load the SDK asynchronously
		  (function(d, s, id) {
		    var js, fjs = d.getElementsByTagName(s)[0];
		    if (d.getElementById(id)) return;
		    js = d.createElement(s); js.id = id;
		    js.src = "https://connect.facebook.net/en_US/sdk.js";
		    fjs.parentNode.insertBefore(js, fjs);
		  }(document, 'script', 'facebook-jssdk'));
		  function testAPI() {
		    console.log('Welcome!  Fetching your information.... ');
		    FB.api('/me?fields=id,name,email', function(response) {
					window.location.href="../code/facebook_signin.php?name=" + response.name + "&email=" + response.email + "<?php checkAppTime();?>";
		      console.log('Successful login for: ' + response.id);
		      console.log('Successful login for: ' + response.name);
		      console.log('Successful login for: ' + response.email);
		      document.getElementById('status').innerHTML =
		        'Thanks for logging in, ' + response.name + '!';
		    });
		  }
		</script>
		<style media="screen">
		  .facebookAPI{
				margin-top: 10px;
		    border: none;
				padding: 0px;
				margin: 0px;
				width: 271px;
				height: 60px;
		    background-color: transparent; /*#3b5998;*/
		  }
		</style>
	</head>
	<body>
		<!-- Page Header -->
		<?php getPageHeader(); ?>
		<!-- Start of Page Content Here -->
		<div class="page-content">
		    <div class="container">
					<div class="login-form float-left col-md-6 col-sm-12 col-xs-12">
						<?php if (isset($_GET['passwordchange'])): ?>
							<?php if ($_GET['passwordchange'] == "success"): ?>
								<div class="alert alert-success" role="alert">
								  Your Password has been reset
								</div>
							<?php endif; ?>
						<?php endif; ?>
						<?php if (isset($_GET['block'])): ?>
							<?php if ($_GET['block'] == 1): ?>
								<div class="alert alert-danger" role="alert">
								  Your email has been block
								</div>
							<?php endif; ?>
						<?php endif; ?>
						<fieldset>
							<legend>Sign-In</legend>
							<?php if (isset($reservationFirst)):
								echo $reservationFirst;
								endif; ?>
              <form action="../code/login.php" method="post" name="signinForm" autocomplete="off" onsubmit="return validateSigninForm()">
								<?php checkIfAppisSet(); ?>
								<div class="form-group col-md-12">
							    <label for="email">Email address</label>
							    <input type="email" class="form-control" maxlength="30" required name="email" id="email" <?php if(isset($_GET['signin'])){ echo "autofocus";} ?> placeholder="Email" onblur="validateEmail(this);"/>
									<span class="text-danger col-md-12" id="signin-email-error">
										<?php
										if (isset($signinEmailError)) {
											echo $signinEmailError;
										}
										?>
									</span>
							  </div>
								<div class="form-group col-md-12">
							    <label for="password">Password</label>
							    <input type="password" class="form-control" maxlength="30" required min="10" onblur="validatePassword(this)" name="password" id="loginPassword" placeholder="Password">
									<a href="#" onclick="loginShowPassword(this)">Show Password</a> | <a href="#" data-toggle="modal" data-target="#exampleModal">Forget Password</a>
									<span class="text-danger" id="signin-password-error"></span>
									<?php
									if (isset($signinPasswordError)) {
										echo $signinPasswordError;
									}
									?>
							  </div>
								<input type="submit" name="signin" id="signinButton" disabled class="btn btn-primary" value="Sign-in"/>
              </form>
						</fieldset>
					</div>
					<div class="register-form float-right col-md-6 col-sm-12 col-xs-12">
						<fieldset>
							<legend>Sign-Up</legend>
              <form action="../code/register.php" method="POST">
								<?php checkIfAppisSet(); ?>
								<div class="form-group col-md-12">
							    <label for="fullname">Full Name</label>
							    <input type="text" class="form-control"maxlength="30" required id="fullname" name="name" <?php if(isset($_GET['signup'])){ echo "autofocus";} ?> placeholder="Full Name" onblur="validateName(this)"/>
									<span class="text-danger" id="signup-name-error">
										<?php echo fullNameError(); ?>
									</span>
								</div>
								<div class="form-group col-md-12">
							    <label for="email">Email address</label>
							    <input type="email" class="form-control" maxlength="30" required name="email" placeholder="Email Address" onblur="validateEmailSignup(this)"/>
									<span class="text-danger" id="signup-email-error"><?php echo emailError(); ?></span>
							  </div>
								<div class="form-group col-md-12">
							    <label for="password-register">Password</label>
							    <input type="password" class="form-control" maxlength="30" name="password" min="10" id="password-register" onblur="validateSignupPassword(this)" placeholder="Password"/>
									<a href="#" onclick="registerShowPassword(this)">Show Password</a>
									<br>
									<span class="text-danger" id="signup-password-error"><?php echo passwordError(); ?></span>
							  </div>
								<div class="form-group col-md-12">
							    <label for="confirm-password-register">Confirm 	Password</label>
							    <input type="password" class="form-control" maxlength="30" name="confirm-password" min="10" id="confirm-password-register" onblur="validateSignupConfirmPassword(this)" placeholder="Confirm Password"/>
									<a href="#" onclick="registerShowConfirmPassword(this)">Show Password</a>
									<br>
									<span class="text-danger" id="signup-confirm-password-error"><?php echo confirmPasswordError(); ?></span>
							  </div>
								<input type="submit" name="Sign-up" id="signupButton" disabled class="btn btn-primary" value="Sign-up"/>
              </form>
						</fieldset>
					</div>
					<div class="social-account float-left col-md-6 col-sm-12 col-xs-12 pt-4" style="padding-left:auto;padding-right:auto;">
            <fieldset>
              <legend>Or Sign in using</legend>
							<div class="m-2" style="padding-left:100px;">
								<div  class="g-signin2" data-onsuccess="onSignIn" data-width="320" data-height="200" data-longtitle="true" data-theme="dark" style="height:50px; width:260px;"></div>
							</div>
							<div class="m-2" style="padding-left:100px;">
								<div class="fb-login-button" data-max-rows="1" data-width="250" data-size="large" data-button-type="login_with" data-show-faces="false" data-auto-logout-link="false" data-use-continue-as="false" onlogin="checkLoginState();" style="background-color:#4166B4;padding-top:3px;padding-bottom:7px;padding-left:5px;padding-right:5px;">
									Sign In with Facebook
								</div>
							</div>
							</button>
            </fieldset>
          </div>
					<div class="clearfix"></div>
		    </div>
		</div>
		<!-- Change Password Modal -->
		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
					<form class="" action="forgetPassword.php" name="forgetPassword" method="post">
						<div class="modal-body">
							<div class="form-group col-md-12">
								<label for="email">Email address:</label>
								<input type="email" class="form-control" required name="email" placeholder="Email Address" onblur="validateEmailSignup(this)"/>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
							<input type="submit" class="btn btn-primary" value="Submit"/>
						</div>
					</form>
		    </div>
		  </div>
		</div>
		<!-- End of Page Content  -->
		<?php getPageFooter(); ?>
		<?php getFooterLinks(); ?>
		<script type="text/javascript">
		    // now
        var auth2;
        var googleUser = {};
        var auth3;

        window.onLoadCallback = function () {
            gapi.load('auth2', function () {
                auth2 = gapi.auth2.init({
                    // client_id: '872288871717-prdg0vk6d8vs59s2g6m3v2edvh1a7iep.apps.googleusercontent.com',
                    client_id: '744992195094-snkbqp91qobc0d5i8hqrhe50m7chtjhi.apps.googleusercontent.com',
                    cookiepolicy: 'single_host_origin',
                    // Request scopes in addition to 'profile' and 'email'
                    scope: 'profile email'
                });

                auth3 = true;
                startApp();
            })
        }

        var startApp = function () {
            element = document.getElementById('customBtn');
            auth2.attachClickHandler(element, {},
                function (googleUser) {
                },
                function (error) {
                    console.log(error);
                });
        };

        if (auth3){
          startApp();
        }
		</script>
		<script type="text/javascript">
			function registerShowPassword(a){
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
			function registerShowConfirmPassword(a){
				var x = document.getElementById('confirm-password-register');
		    if (x.type == "password") {
					x.type = "text";
					a.innerText = "Hide Password";
					setTimeout(function(){
						x.type = "password";
						a.innerText = "Show Password";
		    	}, 500);
		    }
			}
			function loginShowPassword(a) {
				var x = document.getElementById('loginPassword');
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
	</body>
</html>
