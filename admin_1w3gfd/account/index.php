<?php
require '../code/config.php';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php getHeadLinks(); ?>
    <title></title>
    <style media="screen">

    </style>
  </head>
  <body>
    <!--- SideBar -->
    <?php getSidebar(); ?>
    <!--- Header -->
    <?php getHeader(); ?>
    <!--Page Content -->
    <div class="page-content">
      <div class="container">
        <div>
          <?php if (isset($_GET['reset'])): ?>
            <?php if ($_GET['reset'] == "notMatch"): ?>
              <div class="alert alert-danger" role="alert">
                <span>Password not match</span>
              </div>
            <?php endif; ?>
            <?php if ($_GET['reset'] == "success"): ?>
              <div class="alert alert-success" role="alert">
                <span>Password has been changed</span>
              </div>
            <?php endif; ?>
          <?php endif; ?>
          <?php if (isset($_GET['confirm'])): ?>
            <?php if ($_GET['confirm'] == "required"): ?>
              <div class="alert alert-danger" role="alert">
                <span>Confirm Password Required</span>
              </div>
            <?php endif; ?>
          <?php endif; ?>
          <?php if (isset($_GET['password'])): ?>
            <?php if ($_GET['password'] == "required"): ?>
              <div class="alert alert-danger" role="alert">
                <span>Password Required</span>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>
        <div class="mb-3 p-3" style="background-color:#fff;">
          <div>
            <h4>Account Details</h4>
          </div>
          <div class="m-3">
            <a href="../settings/log.php" class="btn btn-primary">View Activity Log</a>
          </div>
          <div>
            <form enctype="multipart/form-data" action="index.html" method="post">
              <!--div class="form-group col-md-6">
                <label for="firstName">Admin Id: </label>
                <input class="form-control" type="text" required readonly value="<?php //echo getSessionAdminId(); ?>" id="adminId"/>
              </div-->
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="firstName">First Name: </label>
                  <input class="form-control" type="text" name="firstName" required value="<?php echo getSessionAdminFirstName(); ?>" onblur="checkFirstname(this)" onkeypress="validateFirstname(this,event)" id="firstName"/>
                  <span class="text-danger" id=""><?php if (isset($firstnameError)) { echo $firstnameError; } ?></span>
                </div>
                <div class="form-group col-md-6">
                  <label for="lastName">Last Name: </label>
                  <input class="form-control" type="text" name="lastName" required value="<?php echo getSessionAdminLastName(); ?>" onblur="checkLastname(this)" onkeypress="validateFirstname(this,event)" id="lastName"/>
                  <span class="text-danger" id=""><?php if (isset($firstnameError)) { echo $firstnameError; } ?></span>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="firstName">Email Address: </label>
                  <input class="form-control" type="text" required value="<?php echo getSessionAdminEmail(); ?>" readonly/>
                  <span class="text-danger" id=""><?php if (isset($firstnameError)) { echo $firstnameError; } ?></span>
                </div>
                <div class="form-group col-md-6">
                  <label for="username">Username: </label>
                  <input class="form-control" type="text" required value="<?php echo getSessionAdminUsername(); ?>" readonly/>
                  <span class="text-danger" id=""><?php if (isset($firstnameError)) { echo $firstnameError; } ?></span>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="Permission">Permission: </label>
                  <input class="form-control" type="text" required value="<?php echo implode(", ",explode("----", getSessionAdminRoles())); ?>" readonly/>
                  <span class="text-danger" id=""><?php if (isset($firstnameError)) { echo $firstnameError; } ?></span>
                </div>
                <div class="form-group col-md-6">
                  <label for="">Change Password</label>
                  <button type="button" name="button" class="form-control btn btn-outline-primary" data-toggle="modal" data-target="#changePasswordModal">Change Password</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!--Page Content-->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form class="" action="../code/passwordReset.php" method="post" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="text" name="newPassword" id="newPassword" minlength=10 class="form-control" required/>
              </div>
              <div class="form-group">
                <label for="confirmNewPassword">Confirm New Password</label>
                <input type="password" name="confirmPassword" id="confirmNewPassword" minlength=10 class="form-control" required/>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
              <input type="submit" name="ChangePassword" class="btn btn-outline-primary" value="Change Password"/>
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
