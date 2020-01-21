<?php
if (isset($_GET['error'])) {
		$x = explode(",", $_GET['error']);
		for ($a=0; $a < count($x); $a++) {
			if ($x[$a] == 1) {
				$usernameError = "<br/><span>Username Is Required</span>";
			}
			else if ($x[$a] == 2) {
				$passwordError = "<br/><span>Password Is Required</span>";
			}
		}
}
if (isset($_GET['exist'])) {
  if ($_GET['exist'] == 0) {
    $usernameError = "<br/><span>Username Not Exist</span>";
  }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="stylesheet/login.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <title>Admin Login</title>
  </head>
  <body>
		<div class="page-background">
		</div>
      <div class="login-form">
        <div class="form-header">
          <div class="header-image">
            <img src="images/login/white+dog+icon.png" alt="">
          </div>
          <div class="header-text">
            <h1>Admin Login</h1>
          </div>
        </div>
        <form class="" action="code/login.php" method="post" enctype="multipart/form-data">
          <div class="username">
            <div class="username-textbox">
              <input type="text" name="username" id="username" minlength="5" maxlength="20" class="form-textbox" placeholder="Username"/>
              <?php
                if (isset($usernameError)) {
                  echo $usernameError;
                }
              ?>
            </div>
          </div>
          <div class="password">
            <div class="password-textbox">
              <input type="password" name="password" id="password" minlength="5" class="form-textbox" placeholder="Password"/>
              <?php
              if (isset($passwordError)) {
                echo $passwordError;
              }
              ?>
            </div>
          </div>
          <div class="submit-button">
            <input type="submit" name="login" value="Log-in">
            <br/>
          </div>
          <div class="error">
            <span></span>
          </div>
        </form>
      </div>
  </body>
</html>
