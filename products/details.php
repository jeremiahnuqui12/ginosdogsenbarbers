<?php
	require '../code/config.php';
	// Page Title Here
	if (empty($_GET['id'])) {
		header("Location: index.php");
	}
	function productData(){
		try{
			$productData = getConnection()->prepare("SELECT
				`product`.`id` AS `id`,
				`product`.`image` AS `image`,
				`product`.`name` AS `productName`,
				`product`.`price` AS `price`,
				`product_categories`.`name` AS `categoryName`,
				`product`.`description` AS `description`,
				`product_stocks`.`stocks_available`
				FROM `product`
				INNER JOIN `product_categories` ON `product`.`category`=`product_categories`.`id`
				INNER JOIN `product_stocks` ON `product_stocks`.`product_id`=`product`.`id`
				WHERE `product`.`id`=:id AND `status`='Active'");
			$productData->execute(
				array(
					":id"=>getId()
				)
			);
			$row = $productData->rowCount();
			$data = $productData->fetch();
			if($row == 0){
				echo "<h1>No Product Found</h1>";
			} else {
			?>
				<div class="float-left col-md-6">
					<img src="data:image/jpeg;base64,<?php echo $data['image'] ?>" class="img-fluid"/>
				</div>
				<div class="float-right col-md-6">
					<h2><?php echo $data['productName']; ?></h2>
					<br/><br/>
					<h3><?php echo "&#8369; " . $data['price']; ?></h3>
					<br/>
					<span><b>Category:</b> <?php echo $data['categoryName']; ?></span>
					<br/>
					<br/>
					<?php if ($data['stocks_available'] > 0): ?>
						<span><?php echo $data['stocks_available'] ?> Stocks Available</span>
					<?php else: ?>
						<span class="text-danger">Out of Stocks</span>
					<?php endif; ?>
					<br/><br/>
					<b>Description: </b>
					<textarea name="name" rows="8" cols="80" readonly><?php echo $data['description']; ?></textarea>
					<br/>
					<button type="button" name="button" title="Add to Cart" class="btn btn-outline-primary <?php if($data['stocks_available'] < 1){echo "disabled";}?>" onclick="addToCart(<?php echo $data['id'] ?>)">
						<i class="fas fa-cart-plus"></i>
						<span>Add to Cart</span>
					</button>
				</div>
				<?php
			}

		} catch(Exception $e){
		    echo "Error in Query" . $e->getMessage();
		}
	}
	function getId(){
		if(is_numeric($_GET['id'])){
			return $_GET['id'];
		} else {
			header("Location: " . $_SERVER['HTTP_REFERER']);
		}
	}
	function getProducts(){
		try {
			$productList = getConnection()->prepare("SELECT
				`product`.`id` AS `id`,
				`product`.`image` AS `image`,
				`product`.`price` AS `price`,
				`product`.`name` AS `name`
				 FROM `product`
				INNER JOIN `product_stocks` ON `product_stocks`.`product_id`=`product`.`id`
				WHERE `status` = 'Active' AND `stocks_available` > 0
				LIMIT 0,10;");
			$productList->execute();
			while ($row = $productList->fetch()) {
				?>
				<div class="card product-list">
					<img class="card-img-top product-image" src="data:image/jpeg;base64,<?php echo $row['image']; ?>" alt="<?php echo $row['name'] ?>" onclick="productDetails(<?php echo $row['id'] ?>)"/>
					<div class="card-body">
						<h5 class="card-title product-title" onclick="productDetails(<?php echo $row['id'] ?>)"><?php echo substr($row['name'], 0, 30) . "..."; ?></h5>
						<p class="card-text font-weight-bold" style="text-decoration:none;"><?php echo "&#8369; " . $row['price']; ?></p>
						<div class="clearfix"></div>
					</div>
				</div>
				<?php
			}
		} catch (Exception $e) {
			echo "Error Displaying Products: " . $e->getMessage();
		}
	}
	$pageTitle = "Product Details | Gino's Dogs en Barbers";
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<?php getHeadLinks(); ?>
		<?php echo "<title>" . $pageTitle . "</title>"; ?>
		<style media="screen">
			.card{
				float: left;
				width: 195px;
				height: 280px;
				margin: 10px;
			}
			.card >img {
				width: 190px;
				height: 190px;
				margin-left: auto;
				margin-right: auto;
			}
			.product-title{
				font-size: 15px;
				margin: 0px;
			}
			.product-list .btn{
				width: 130px;
				height: 35px;
				font-size: 15px;
			}
			.product-list:hover{
				box-shadow: 1px 1px #888888;
				cursor: pointer;
			}
			.product-list:hover .product-price{
				text-decoration: none!important;
			}
			#cartNotify{
				z-index: 100;
				width: 200px;
				position: absolute;
				position: fixed;
				top: 200px;
				left: 1280px;
				display:none;
				border-radius: 0px;
				text-align: center;
			}
		</style>
		<script type="text/javascript">
		function productDetails(id){
			window.location.href="details.php?id=" + id;
		}
		</script>
		<script type="text/javascript">
		function addToCart(id){
		    var cartBadge = document.getElementById("cartBadge");
			if (window.XMLHttpRequest) {
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			} else {  // code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function() {
				if (this.readyState==4 && this.status==200) {
					if (this.response == "needToSignIn") {
						window.location.href="../account/?signin=1&reservation=1<?php if(isset($_GET['id'])){echo "&product_id=" . $_GET['id']; }?>"
					} else{
						if (this.response == "Add Stocks") {
							document.getElementById("cartNotify").innerText = "Quantity added";
						} else if (this.response == "Minus Stocks") {
							document.getElementById("cartNotify").innerText = "Minus Stocks";
						} else if(this.response == "Product added to cart"){
						    cartBadge.innerText = parseInt(cartBadge.innerText) + 1;
						    document.getElementById("cartNotify").innerText = this.response;
						} else {
							document.getElementById("cartNotify").innerText = this.response;
						}
						document.getElementById("cartNotify").style.display = "block";
						setTimeout(
							function(){
								document.getElementById("cartNotify").style.display = "none";
							}, 4000
						);
					}
				}
			}
			xmlhttp.open("GET","../code/addToCart.php?idx="+ id  + "&sign=add",true);
			xmlhttp.send();
		}
		</script>
		<style media="screen">
			.product-details{
				border-bottom:1px solid gray;
				padding-bottom:20px;
			}
			.product-details img{
				width: 70%;
				padding-left: 130px;
				height: 70%;
			}
			.product-details textarea{
				border: none;
				resize: none;
				background-color: #fff;
				outline: none;
				width: 500px;
				padding-left: 30px;
			}
		</style>
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
						<li class="breadcrumb-item" aria-current="page">
							<a href="index.php">Products</a>
						</li>
						<li class="breadcrumb-item active" aria-current="page">Details</li>
				  </ol>
				</div>
				<div class="product-details">
					<?php productData(); ?>
					<div class="clearfix"></div>
				</div>
				<div class="mt-4">
					<div>
						<h3>Other Products</h3>
					</div>
					<div class="mt-4">
						<?php getProducts(); ?>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<!-- End of Page Content  -->
		<?php getPageFooter(); ?>
		<?php getFooterLinks(); ?>
		<div class="alert alert-primary" id="cartNotify" role="alert"></div>
	</body>
</html>
