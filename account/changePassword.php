<?php
require '../code/config.php';
if (!isset($_GET['id'])) {
  header("Location: account.php");
}
if ($_GET['id'] != md5(getSessionEmail())) {
  header("Location: account.php");
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <?php getHeadLinks(); ?>
  <title>Change Password | Ginos Dogs En Barbers</title>
  <script type="text/javascript">
    function checkPassword(){
      var newPassword = document.forms["changePasswordForm"]["newPassword"];
      var confirmNewPassword = document.forms["changePasswordForm"]["confirmNewPassword"];
      var passwordError = document.getElementById('password-alert');
      if (newPassword.value == "") {
        passwordError.innerText = "New password required";
        passwordError.style.display = "block";
        return false;
      } else if (confirmNewPassword.value == "") {
        passwordError.innerText = "Confirm new password required";
        passwordError.style.display = "block";
        return false;
      } else if (newPassword.value != confirmNewPassword.value) {
        passwordError.innerText = "Confirm new password not match";
        passwordError.style.display = "block";
        return false;
      }
    }
  </script>
  <style media="screen">
    #password-alert {
      display: none;
    }
  </style>
</head>
<body>
  <!-- Page Header -->
  <?php getPageHeader(); ?>
  <!-- Start of Page Content Here -->
  <div class="page-content">
    <div class="container">
      <div class="alert alert-danger" role="alert" id="password-alert">
      </div>
      <form class="" action="../code/changePassword.php" name="changePasswordForm" onsubmit="return checkPassword()" method="post">
        <fieldset>
          <legend>Change Password</legend>
          <div class="form-group col-md-6">
            <label for="newPassword">New Password</label>
            <input type="password" class="form-control" required id="newPassword" minlength="5" name="newPassword" placeholder="New Password"/>
          </div>
          <div class="form-group col-md-6">
            <label for="confirmNewPassword">New Password</label>
            <input type="password" class="form-control" required minlength="5" id="confirmNewPassword" name="confirmNewPassword" placeholder="New Password"/>
          </div>
          <input type="submit" name="changePassword" value="Change Password" class="btn btn-primary">
        </fieldset>
      </form>
    </div>
  </div>
  <!-- End of Page Content  -->
  <?php getPageFooter(); ?>
  <?php getFooterLinks(); ?>
</body>
</html>
