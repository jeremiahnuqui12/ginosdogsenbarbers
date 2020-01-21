<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
	require '../code/config.php';
	// Page Title Here
	$pageTitle = "Home | Gino's Dogs en Barbers";
  function forgetPassword(){
      ob_start();
  	try {
  	    $mail = new PHPMailer(true);
      SMTPServerSettings($mail);
  		$subject = 'Request Change Password | Ginos Dogs En Barbers';
  		$message = '
  			Good Day Maam/Sir,
  			<br/><br/>
  			Click this <a href="' . getWebRoot() . '/account/forgetPassword.php?id=' . md5($_POST['email']) . '">Link</a> to change your password
  		 <br/><br/>
  		 In case of any questions, feel free to contact us at <a href="' . getWebroot() .  '/contact.php">Contact Us</a>
  		 <br/>
  		 Ginos Dogs En Barbers.';
  		 $mail->addAddress($_POST['email']);
        $mail->Subject = $subject;
        $mail->Body    = $message;
  		 if($mail->send()) {
  		     ob_get_clean();
  			 return "<div class=\"alert alert-success\" role=\"alert\">We Send you an link in your email to change your password</div>";
  		 }
  	} catch (Exception $e) {
  		echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
  	}
  	
  }
  function verifyId(){
    try {
      $query = getConnection()->prepare("SELECT * FROM `customer_account` WHERE md5(`email`) = :id");
      $query->execute(
        array(
          ":id" => $_GET['id']
        )
      );
      if ($query->rowCount() == 1) {
        changePasswordForm();
      } else {
        throw new EmailNotFoundException("Email Not Found", 1);
      }
    } catch (Exception $e) {
      echo "Error: " . $e->getMessage();
    } catch (EmailNotFoundException $e){
      echo "Error: " . $e->getMessage();
    }

  }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<?php getHeadLinks(); ?>
		<?php echo "<title>" . $pageTitle . "</title>"; ?>
    <script type="text/javascript">
    function ShowPassword(a) {
      var x = document.getElementById('newPassword');
      var y = document.getElementById('confirmNewPassword');
      if (x.type == "password" || y.type == "password") {
        x.type = "text";
        y.type = "text";
        a.innerText = "Hide Password";
        setTimeout(function(){
          x.type = "password";
          y.type = "password";
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
        <?php if (isset($_GET['password'])): ?>
          <div class="alert alert-danger" role="alert">
            Password Not Match
          </div>
        <?php endif; ?>
          <?php if(isset($_POST['email'])) { echo forgetPassword(); }?>
          <?php if (isset($_GET['id'])): verifyId();?>

          <?php endif; ?>
          <?php function changePasswordForm(){ ?>
            <div class="col-md-6">
              <div>
                <h2>Change Password</h2>
              </div>
            </div>
            <form action="../code/forgetChangePassword.php" method="post">
              <input type="hidden" name="email" value="<?php echo$_GET['id']; ?>">
              <div class="form-group col-md-12">
                <label for="newPassword">New Password:</label>
                <input type="password" class="form-control" required min="10" name="newPassword" id="newPassword" placeholder="Password">
                <a href="#" onclick="ShowPassword(this)">Show Password</a>
              </div>
              <div class="form-group col-md-12">
                <label for="confirmNewPassword">Confirm New Password:</label>
                <input type="password" class="form-control" required min="10" name="confirmNewPassword" id="confirmNewPassword" placeholder="Password">
                <a href="#" onclick="ShowPassword(this)">Show Password</a>
              </div>
              <input type="submit" class="btn btn-primary" name="changePassword" value="Change Password">
            </form>
          <?php } ?>
        </div>
      </div>
		</div>
		<!-- End of Page Content  -->
		<?php getPageFooter(); ?>
		<?php getFooterLinks(); ?>
	</body>
</html>
