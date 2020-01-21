<?php
	require 'code/config.php';
	// Page Title Here
	$pageTitle = "About Us | Gino's Dogs en Barbers";
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<?php getHeadLinks(); ?>
		<?php echo "<title>" . $pageTitle . "</title>"; ?>
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
            <li class="breadcrumb-item active" aria-current="page">About Us</li>
          </ol>
        </div>
				<div class="col-lg-12 col-sm-12" style="text-align:center;">
					<h3>History</h3>
					<p>
						The owner of Gino’s Dogs En’ Barbers is Mrs. Violeta Dizon. It started since 2013,
						 it absolutely was the settled in 145B tenth Ave., Caloocan town.  It is a pet salon
						  that sells accessories and foods and offers grooming service. The pet salon's policy
							 is first to come, first serve.
					</p>
				</div>
				<!--div class="col-lg-12 col-sm-12" style="text-align:justify;">
					<p>&emsp;&emsp; Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
						ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
						 laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in
							voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat
							cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				</div-->
			</div>
		</div>
		<!-- End of Page Content  -->
		<?php getPageFooter(); ?>
		<?php getFooterLinks(); ?>
	</body>
</html>
