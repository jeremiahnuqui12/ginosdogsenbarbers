<?php
	include_once 'code/config.php';
	// Page Title Here
	$pageTitle = "Home | Gino's Dogs en Barbers";

	//----Product list
	function getProductList($x, $y){
		try {
			$query = getConnection()->prepare("SELECT * FROM `product` WHERE `status`='Active' LIMIT :x OFFSET :y");
			$query->execute(
				array(
					":x" => $x,
					":y" => $y
				)
			);
			while ($row = $query->fetch()) {
				?>
				<div class="col-xs-3 col-sm-3 col-md-3">
					<img src="data:image/jpeg;base64,<?php echo $row['image']; ?>" alt="<?php echo $row['name'] ?>" onclick="window.location.href='products/details.php?id=<?php echo $row['id'] ?>'">
					<span><?php echo substr($row['name'], 0, 40) . "..." ?></span>
				</div>
				<?php
			}
		} catch (Exception $e) {

		}

	}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<?php getHeadLinks(); ?>
		<?php echo "<title>" . $pageTitle . "</title>"; ?>
		<style media="screen">
			.content-header {
				padding-top: 20px;
				padding-bottom: 20px;
				overflow: auto;
			}
			.main-content {
				float: left;
				width: 650px;
			}
			.facebook-reviews {
				float: right;
				width: 440px;
				border-left: 2px solid#f0f0f0;
				height: 500px!important;
				overflow: auto;
			}
			/*--------------*/
			div.sk-ww-fb-page-reviews {

			}
		</style>
		<style media="screen">
			.col-md-3{
				display: inline-block;
				margin-left:none;
			}
			.col-md-3 img{
				width:180px;
				min-height: 190px;
				height:auto;
			}
			body .carousel-indicators{
			bottom: 0;
			}
			body .no-padding{
			padding-left: 0;
			padding-right: 0;
			 }
			 .product-list div{
				 width: 210px;
			 }
			 .product-list div:hover{
 				box-shadow: 1px 1px 1px 1px #888888;
				border-radius: 5px;
 				cursor: pointer;
 			}
			 .product-list-control{
				 width: 50px;
			 }
			 #productList{
				 min-height: 220px;
				 border-top: 1px solid gray;
				 border-bottom: 1px solid gray;
			 }
			 #productList .carousel-indicators li{
				 background-color: #000;
				 height: 4px;
			 }
		</style>
	</head>
	<body>
		<!-- Page Header -->
		<?php getPageHeader(); ?>
		<!-- Page Slider Carousel Start -->
		<div class="page-content">
			<?php if (isset($_GET['register'])): ?>
				<?php if ($_GET['register'] == "success"): ?>
					<div class="alert alert-success" style="margin-bottom:0px;;"role="alert">
					  Registered Successfully. <!--span class="font-weight-bold">Please go to your email to verify your account.</span-->
					</div>
				<?php endif; ?>
			<?php endif; ?>
    		<div id="carouselslideShow" class="carousel slide" data-ride="carousel">
    		  <ol class="carousel-indicators">
    		    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
    		    <li data-target="#carouselslideShow" data-slide-to="1"></li>
    		    <li data-target="#carouselslideShow" data-slide-to="2"></li>
    				<li data-target="#carouselslideShow" data-slide-to="3"></li>
    		  </ol>
    		  <div class="carousel-inner">
    		    <div class="carousel-item active">
    		      <img class="d-block w-100" src="images\slideshow\43548837_272831560019528_3989114965385543680_n.jpg" alt="First SlideShow"/>
							<div>
								<a href="services/reservationCalendar.php" style="height:65px;width:325px; position:absolute;top:393px;left:110px;"></a>
							</div>
    		    </div>
    		    <div class="carousel-item">
    		      <img class="d-block w-100" src="images\slideshow\43417535_163651844569812_7246396129489715200_n.png" alt="Second SlideShow">
							<div>
								<a href="products/?id=1" style="height:32px;width:130px; position:absolute;top:357px;left:490px;"></a>
							</div>
    		    </div>
    		    <div class="carousel-item">
    		      <img class="d-block w-100" src="images\slideshow\43490605_299602020644236_3664415296074022912_n.png" alt="Third SlideShow">
							<div>
								<a href="products/?id=2" style="height:32px;width:35px; position:absolute;top:330px;left:560px;border-radius:50% 50% 50% 50%;"></a>
							</div>
    		    </div>
						<div class="carousel-item">
    		      <img class="d-block w-100" src="images\slideshow\43586903_2149611291944057_4031762255468036096_n.jpg" alt="Fourth SlideShow">
    		    </div>
    		  </div>
    		  <a class="carousel-control-prev" href="#carouselslideShow" role="button" data-slide="prev">
    		    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    		    <span class="sr-only">Previous</span>
    		  </a>
    		  <a class="carousel-control-next" href="#carouselslideShow" role="button" data-slide="next">
    		    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    		    <span class="sr-only">Next</span>
    		  </a>
    		</div>
    		<!-- Page Slider Carousel End -->

    		<!-- Page Content Here -->
    		<div class="container pt-4 border-bottom-1">
					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
						<div class="product-list-title">
							<h3>
								<a href="products/">Products</a>
							</h3>
						</div>
						<div id="productList" class="carousel slide" data-ride="carousel">
						  <div class="container-fluid carousel-inner no-padding">
						    <div class="carousel-item active product-list">
									<?php getProductList(5,0); ?>
						    </div>
						    <div class="carousel-item product-list">
									<?php getProductList(5,5); ?>
						    </div>
						    <div class="carousel-item product-list">
									<?php getProductList(5,10); ?>
						    </div>
						  </div>
						  <!-- Left and right controls -->
						  <a class="carousel-control-prev product-list-control" href="#productList" data-slide="prev">
						    <span class="carousel-control-prev-icon"></span>
						  </a>
						  <a class="carousel-control-next product-list-control" href="#productList" data-slide="next">
						    <span class="carousel-control-next-icon"></span>
						  </a>
						</div>
					</div>
    			<div>
						<div class="mt-3">
							<div class="col-lg-12 col-sm-12" style="text-align:justify;">
								<p>&emsp;&emsp; A full service of dog grooming salon.
									A happy grooming experience.
									A new destination to pamper your pets.
									Always a gentle and loving touch.
									Bathe your pet in luxury.
									First class care for the pampered pet.
									Gentle care for your best friend.
									Grooming is our middle name.
									Making the world beautiful one pet at a time.
									One dog at a time.
									Pet grooming with love.
									Rock star treatment for your dog.
									Tails are wagging and pets are bragging.
									We groom with gentle, loving care.
									We love what we do so we love your pet.
									Where “love” is in our name and pampering is our game.
									Your dog comes first.
									Welcome to Gino’s dogs en barbers!
								</p>
							</div>
						</div>
						<!--div class="col-lg-12 col-sm-12 mt-3 facebook-reviews">
							<div class='sk-ww-fb-page-reviews' id="facebook-reviews" data-embed-id='13814'></div>
						</div-->
						<div style="clear:both;"></div>
    			</div>
    		</div>
		</div>
		<!-- Registration Success Modal -->
		<div id="myModal" class="modal" role="dialog" style="margin-top:200px;" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-body">
						<h3>Registeration Success!!</h3>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<!-- Footer -->
		<?php getPageFooter(); ?>
		<?php getFooterLinks(); ?>
		<script src='https://www.sociablekit.com/app/embed/facebook-page-reviews/widget.js'></script>
	</body>
</html>
