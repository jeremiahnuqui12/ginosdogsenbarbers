<?php
	require '../code/config.php';
	// Page Title Here
	$pageTitle = "Products | Gino's Dogs en Barbers";
	//---Get products
	function getProducts(){
		try {
			$query = "SELECT
				`product`.`id` AS `id`,
				`product`.`image` AS `image`,
				`product`.`price` AS `price`,
				`product`.`name` AS `name`
				 FROM `product`
				INNER JOIN `product_stocks` ON `product_stocks`.`product_id`=`product`.`id`
				WHERE `status` = 'Active' AND `stocks_available` > 0";
			if(isset($_GET['id'])) {
				$query .= " AND `category` =" . $_GET['id'];
			}
			//-------Sorting for name
			if (isset($_GET['sort_name'])) {
				if ($_GET['sort_name'] == "asc") {
					$query .= " ORDER BY `name` ASC";
				} elseif ($_GET['sort_name'] == "desc") {
					$query .= " ORDER BY `name` DESC";
				} else {
					$query .= " ORDER BY `name` ASC";
				}
			}
			//-------Sorting for price
			if (isset($_GET['sort_price'])) {
				if ($_GET['sort_price'] == "asc") {
					$query .= " ORDER BY `price` ASC";
				} elseif ($_GET['sort_price'] == "desc") {
					$query .= " ORDER BY `price` DESC";
				} else {
					$query .= " ORDER BY `price` ASC";
				}
			}
			if (isset($_GET['page'])) {
				//0-15, 2=15-30, 3=30-45, 4=45-60
				$min = ($_GET['page'] * 15) - 15;
				$max = $_GET['page'] * 15;
				$query .= " LIMIT $min, $max";
			} else {
				$query .= " LIMIT 0, 15";
			}
			$productList = getConnection()->prepare($query);
			$productList->execute();
			if($productList->rowCount() == 0){
			    echo "<h3>No Product Found.</h3>";
			} else {
			    while ($row = $productList->fetch()) {
				?>
				<div class="card product-list">
					<div>
						<img class="card-img-top product-image" src="data:image/jpeg;base64,<?php echo $row['image']; ?>" alt="<?php echo $row['name'] ?>" onclick="productDetails(<?php echo $row['id'] ?>)"/>
					</div>
					<div class="card-body">
						<h5 class="card-title product-title" onclick="productDetails(<?php echo $row['id'] ?>)"><?php echo substr($row['name'], 0, 30) . "..."; ?></h5>
						<p class="card-text font-weight-bold product-price"><?php echo "&#8369; " . $row['price']; ?></p>
						<div class="clearfix"></div>
					</div>
				</div>
				<?php
			}
			}
		} catch (Exception $e) {
			echo "Error Displaying Products: " . $e->getMessage();
		}
	}
  function getPageNumber(){
		try {
			$productId = "";
			if (isset($_GET['id'])) {
				$productId = "id=" . $_GET['id'] . "&";
				$productPage = getConnection()->prepare("SELECT COUNT(*) FROM `product`
			 INNER JOIN `product_stocks` ON `product_stocks`.`product_id`=`product`.`id`
			 WHERE `status` = 'Active' AND `category`=:id AND `stocks_available` > 0");
				$productPage->bindParam(":id", $_GET['id']);
			} else {
				$productPage = getConnection()->prepare("SELECT COUNT(*) FROM `product`
			 INNER JOIN `product_stocks` ON `product_stocks`.`product_id`=`product`.`id`
			 WHERE `status` = 'Active' AND `stocks_available` > 0");
			}

			$productPage->execute();
			$x = $productPage->fetch()[0];
			$x = $x / 15;
			$maxPage = ceil($x);
			if (isset($_GET['page'])) {
				$page = $_GET['page'];
			} else {
				$page = 1;
			}
			?>
			<?php if ($maxPage > 1): ?>
				<ul class="pagination justify-content-end">
		      <li class="page-item <?php if ($page == 1) {echo "disabled"; } ?>">
		        <a class="page-link" href="?<?php echo $productId; ?>page=<?php echo $page-1; ?>" tabindex="-1">Previous</a>
		      </li>
					<?php
				for ($i=1; $i <= $maxPage; $i++) {
					?>
					<li class="page-item <?php if (isset($_GET['page']) && $_GET['page'] == $i) { echo "disabled"; } ?>">
						<?php if (isset($_GET['sort_name'])): ?>
							<?php if (isset($_GET['sort_name'])): ?>
								<a class="page-link" href="?<?php echo $productId; ?>page=<?php echo $i ?>&sort_name=<?php echo $_GET['sort_name']; ?>"><?php echo $i; ?></a>
							<?php else: ?>
								<a class="page-link" href="?<?php echo $productId; ?>page=<?php echo $i ?>"><?php echo $i; ?></a>
							<?php endif; ?>
							<?php //----------------------------------------------------- ?>
						<?php elseif(isset($_GET['sort_price'])): ?>
							<?php if (isset($_GET['sort_price'])): ?>
								<a class="page-link" href="?<?php echo $productId; ?>page=<?php echo $i ?>&sort_price=<?php echo $_GET['sort_price']; ?>"><?php echo $i; ?></a>
							<?php else: ?>
								<a class="page-link" href="?<?php echo $productId; ?>page=<?php echo $i ?>"><?php echo $i; ?></a>
							<?php endif; ?>
						<?php else: ?>
							<a class="page-link" href="?<?php echo $productId; ?>page=<?php echo $i ?>"><?php echo $i; ?></a>
						<?php endif; ?>
		      </li>
					<?php
				} ?>
				<li class="page-item <?php if ($page == $maxPage) { echo "disabled"; } ?>">
	        <a class="page-link" href="?<?php echo $productId; ?>page=<?php echo $page+1; ?>">Next</a>
	      </li>
	    </ul>
			<?php endif; ?>
		<?php
		} catch (Exception $e) {
			echo "Error Product Page: " . $e->getMessage();
		}
    ?>
    <?php
  }
	function textDisplayTotal(){
		//Displaying 1 to 15 of 30 Products
	}
	function sortName() {
		if (isset($_GET['sort_name'])) {
			if ($_GET['sort_name'] == "asc") {
				return '<i id="arrow-name" class="fa fa-arrow-down"></i>';
			} elseif ($_GET['sort_name'] == "desc") {
				return '<i id="arrow-name" class="fa fa-arrow-up"></i>';
			} else {
				return '<i id="arrow-name" class="fa fa-arrow-down"></i>';
			}
		} else {
			return '<i id="arrow-name" class="fa fa-arrow-down"></i>';
		}
	}

	function sortPrice() {
		if (isset($_GET['sort_price'])) {
			if ($_GET['sort_price'] == "asc") {
				return '<i id="arrow-price" class="fa fa-arrow-down"></i>';
			} elseif ($_GET['sort_price'] == "desc") {
				return '<i id="arrow-price" class="fa fa-arrow-up"></i>';
			} else {
				return '<i id="arrow-price" class="fa fa-arrow-down"></i>';
			}
		} else {
			return '<i id="arrow-price" class="fa fa-arrow-down"></i>';
		}
	}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<?php getHeadLinks(); ?>
		<script type="text/javascript">
			function addToCart(id){
				if (window.XMLHttpRequest) {
	        // code for IE7+, Firefox, Chrome, Opera, Safari
	        xmlhttp=new XMLHttpRequest();
	      } else {  // code for IE6, IE5
	        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	      }
	      xmlhttp.onreadystatechange=function() {
	        if (this.readyState==4 && this.status==200) {
						if (this.response == "needToSignIn0") {
							window.location.href="../account/?signin=1&reservation=1"
						} else{
							if (this.response == "Add Stocks") {
								document.getElementById("cartNotify").innerText = "Stocks Added";
							} else if (this.response == "Minus Stocks") {
								document.getElementById("cartNotify").innerText = "Minus Stocks";
							} else {
								document.getElementById("cartBadge").innerText = this.responseText;
								document.getElementById("cartNotify").innerText = "Added to Cart";
							}
		          //alert(this.responseText);
							document.getElementById("cartNotify").style.display = "block";
						  setTimeout(
						    function(){
						      document.getElementById("cartNotify").style.display = "none";
						    }, 4000
						  );
						}
	        }
	      }
	      xmlhttp.open("GET","../code/addToCart.php?idx="+id,true);
	      xmlhttp.send();
			}
			function productDetails(id){
				window.location.href="details.php?id=" + id;
			}
		</script>
		<?php echo "<title>" . $pageTitle . "</title>"; ?>
		<style media="screen">
			.card{
				float: left;
				width: 195px;
				height: 280px;
				margin: 10px;
				padding-top: 1px;
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
				width: 135px;
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
				position: absolute;
				position: fixed;
				top: 180px;
				left: 1270px;
				background-color: #00ff00;
				display: none;
			}
		</style>
		<script type="text/javascript">
    function sort_name(x){
      var arrow = document.getElementById("arrow-name");
      if (arrow.className == "fa fa-arrow-down") {
        arrow.className = "fa fa-arrow-up";
				x.id="name_descending";
				<?php if (isset($_GET['id'])): ?>
					//----------------------------------
					<?php if (isset($_GET['page'])): ?>
						window.location.href = "?id=<?php echo $_GET['id']; ?>&page=<?php echo $_GET['page']; ?>&sort_name=desc";
					<?php else: ?>
						window.location.href = "?id=<?php echo $_GET['id']; ?>&sort_name=desc";
					<?php endif; ?>
					//-------------------------
				<?php else: ?>
					//------------------------------
					<?php if (isset($_GET['page'])): ?>
						window.location.href = "?page=<?php echo $_GET['page']; ?>&sort_name=desc";
					<?php else: ?>
						window.location.href = "?sort_name=desc";
					<?php endif; ?>
					//----------------------------
				<?php endif; ?>
      } else {
        arrow.className = "fa fa-arrow-down";
				x.id="name_ascending";
				<?php if (isset($_GET['id'])): ?>
					//--------------------
					<?php if (isset($_GET['page'])): ?>
						window.location.href = "?id=<?php echo $_GET['id']; ?>&page=<?php echo $_GET['page']; ?>&sort_name=asc";
					<?php else: ?>
						window.location.href = "?id=<?php echo $_GET['id']; ?>&sort_name=asc";
					<?php endif; ?>
					//--------------------
				<?php else: ?>
					//-------------------
					<?php if (isset($_GET['page'])): ?>
						window.location.href = "?page=<?php echo $_GET['page']; ?>&sort_name=asc";
					<?php else: ?>
						window.location.href = "?sort_name=asc";
					<?php endif; ?>
					//-------------------
				<?php endif; ?>
      }
    }
    function sort_price(x){
      var arrow = document.getElementById("arrow-price");
      if (arrow.className == "fa fa-arrow-down") {
        arrow.className = "fa fa-arrow-up";
				x.id="price_descending";
				<?php if (isset($_GET['id'])): ?>
					//-------------------------------
					<?php if (isset($_GET['page'])): ?>
						window.location.href = "?id=<?php echo $_GET['id']; ?>&page=<?php echo $_GET['page']; ?>&sort_price=desc";
					<?php else: ?>
						window.location.href = "?id=<?php echo $_GET['id']; ?>&sort_price=desc";
					<?php endif; ?>
					//-------------------------------
				<?php else: ?>
					//-------------------------------
					<?php if (isset($_GET['page'])): ?>
						window.location.href = "?page=<?php echo $_GET['page']; ?>&sort_price=desc";
					<?php else: ?>
						window.location.href = "?sort_price=desc";
					<?php endif; ?>
					//-------------------------------
				<?php endif; ?>

      } else {
        arrow.className = "fa fa-arrow-down";
				x.id="price_ascending";
				<?php if (isset($_GET['id'])): ?>
					//----------------------------
					<?php if (isset($_GET['page'])): ?>
						window.location.href = "?id=<?php echo $_GET['id']; ?>&page=<?php echo $_GET['page']; ?>&sort_price=asc";
					<?php else: ?>
						window.location.href = "?id=<?php echo $_GET['id']; ?>&sort_price=asc";
					<?php endif; ?>
					//----------------------------
				<?php else: ?>
					//----------------------------
					<?php if (isset($_GET['page'])): ?>
						window.location.href = "?page=<?php echo $_GET['page']; ?>&sort_price=asc";
					<?php else: ?>
						window.location.href = "?sort_price=asc";
					<?php endif; ?>
					//----------------------------
				<?php endif; ?>
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
							<a href="../">Home</a>
						</li>
						<li class="breadcrumb-item active" aria-current="page">Products</li>
				  </ol>
				</div>
				<div class="clearfix"></div>
    		<div class="col-lg-12 col-sm-12" style="text-align:justify;">
    			<div class="float-sm-left">
    				<div class="btn-group" role="group" aria-label="Basic example">
    				  <button type="button" class="btn btn-secondary disabled">Sort by:</button>
							<button type="button" class="btn btn-secondary" id="name_ascending" onclick="sort_name(this);">Name
								<?php echo sortName(); ?>
							</button>
							<button type="button" class="btn btn-secondary" id="price_ascending" onclick="sort_price(this);">Price
								<?php echo sortPrice(); ?>
							</button>
    				</div>
    			</div>
    			<div class="float-sm-right">
            <?php getPageNumber(); ?>
    			</div>
    			<div class="clearfix"></div>
					<div class="mt-4" id="productsList">
						<?php getProducts(); ?>
					</div>
    			<div class="clearfix"></div>
    			<div class="float-sm-left">
    				<span><?php textDisplayTotal(); ?></span>
    			</div>
    			<div class="float-sm-right">
            <?php getPageNumber(); ?>
    			</div>
    			<div class="clearfix"></div>
    		</div>
    	</div>
		</div>
		<!-- Add Product to Cart notify -->
		<div class="alert alert-success" id="cartNotify" role="alert"></div>
		<!-- End of Page Content  -->
		<?php getPageFooter(); ?>
		<?php getFooterLinks(); ?>
	</body>
</html>
