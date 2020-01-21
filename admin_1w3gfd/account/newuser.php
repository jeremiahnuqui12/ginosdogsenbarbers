<?php
include_once "../code/config.php";
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
    <nav class="col-md-2 sidebar">
      <div>
        <img src="<?php echo getWebroot(); ?>\images\18622390_1507902379228378_3089499811326682571_n.jpg" class="img-fluid" alt="Responsive image">
      </div>
    </nav>
    <!--- Header -->
    <nav class="navbar navbar-expand-lg col-sm-12 col-md-12 col-lg-12 header">
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown ml-auto">
            <a class="nav-link dropdown-toggle" href="#" id="navbarAccount" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span id="session">Sign-in as: <?php echo getSessionAdminUsername(); ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right text-center" aria-labelledby="navbarAccount">
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?php echo getWebroot(); ?>/code/logout.php">Logout</a>
            </div>
          </li>
        </ul>
      </div>
    </nav>
    <!--Page Content -->
    <div class="page-content">
      <div class="container">
        <fieldset>
          <legend>Account Details</legend>
          <form action="../code/passwordReset.php" method="post">
            <table class="table">
              <tr>
                <td>First Name: </td>
                <td>
                  <span><?php echo getSessionAdminFirstName(); ?></span>
                </td>
              </tr>
              <tr>
                <td>Last Name: </td>
                <td>
                  <span><?php echo getSessionAdminLastName(); ?></span>
                </td>
              </tr>
              <tr>
                <td>Email Address: </td>
                <td>
                  <span><?php echo getSessionAdminEmail(); ?></span>
                </td>
              </tr>
              <tr>
                <td>Username: </td>
                <td>
                  <span><?php echo getSessionAdminUsername(); ?></span>
                </td>
              </tr>
              <tr>
                <td>Permission: </td>
                <td>
                  <span><?php echo implode(", ", explode("----", getSessionAdminRoles())); ?></span>
                </td>
              </tr>
              <tr>
                <td>
                  New Password:
                </td>
                <td>
                  <input type="password" class="form-control" placeholder="New Password" name="newPassword" required minlength="10">
                </td>
              </tr>
              <tr>
                <td>
                  Confirm New Password:
                </td>
                <td>
                  <input type="password" class="form-control" placeholder="Confirm New Password" name="confirmPassword" required minlength="10">
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <input type="submit" class="btn btn-success" name="" value="Change Password">
                </td>
              </tr>
            </table>
          </form>
        </fieldset>
      </div>
    </div>
    <!--Page Content-->
  </body>
</html>
