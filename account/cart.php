<?php
	require '../code/config.php';
	// Page Title Here
	$pageTitle = "My Cart | Gino's Dogs en Barbers";
	function cartDetails(){
		try {
			$query = getConnection()->prepare("SELECT
				`customer_order`.`id` AS `prodResId`,
				`product`.`id` AS `productId`,
				`product`.`image` AS `productImage`,
				`product`.`name` AS `productName`,
				`customer_order`.`quantity` AS `quantity`,
				`product`.`price` AS `productPrice`
				FROM `customer_order`
				INNER JOIN `product` ON `product`.`id`=`customer_order`.`product_id`
				WHERE `customer_id`=:customerId AND `customer_order`.`status`=:status");
				$query->execute(
					array(
						":customerId" => getSessionCustomerId(),
						":status" => "On-Cart"
					)
				);
				if ($query->rowCount() == 0) {
					echo "<h3 class=\"text-center\">No Products Found</h3>";
				} else {
					?>
					<table class="table table-striped">
            <thead>
              <tr>
								<th>
									<input type="checkbox" name="all" id="selectAll" <?php echo checkIfCheck(); ?> onclick="selectAllCheck(this)">
								</th>
                <th style="width:500px;">Details</th>
                <th>Item Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
					<?php
					while ($row = $query->fetch()) {
						?>
						<tr for="row-<?php echo $row['prodResId']; ?>">
							<td>
								<input type="hidden" name="selected-product-price[]" value="<?php echo number_format((float)($row['productPrice'] * $row['quantity']), 2, '.', ''); ?>">
								<input type="checkbox" name="selected-product[]" onclick="selectProduct(this, '<?php echo number_format((float)($row['productPrice'] * $row['quantity']), 2, '.', ''); ?>')" <?php echo checkIfCheck(); ?> class="form-control" id="row-<?php echo $row['prodResId']; ?>" value="<?php echo $row['prodResId']; ?>">
							</td>
							<td>
								<div class="">
									<img src="data:image/jpeg;base64,<?php echo $row['productImage']; ?>" alt="Product Image" height="70" width="70">
									<a href="../products/details.php?id=<?php echo $row['productId']; ?>" style="margin-left:15px;float:right;width:82%;"><?php echo $row['productName']; ?></span>
								</div>
							</td>
							<td class="align-middle">
								&#8369; <span id="product-price-<?php echo $row['prodResId']; ?>"><?php echo $row['productPrice']; ?></span>
							</td>
							<td class="align-middle">
								<div class="btn-group quantityButton" role="group" aria-label="First group">
							    <button type="button" class="btn btn-light" name="button" min=1 <?php if($row['quantity'] == 1){echo "disabled";} ?> id="buttonMinus-<?php echo $row['prodResId']; ?>" onclick="minusQuantity(this,<?php echo $row['prodResId'] . "," . $row['productId']; ?>)">
										<i class="fas fa-minus"></i>
									</button>
									<input type="text" name="quantity" value="<?php echo $row['quantity']; ?>" readonly id="quantity-<?php echo $row['prodResId']; ?>"/>
							    <button type="button" class="btn btn-light <?php if($row['quantity'] >= checkStocksLeft($row['productId'])){echo "disabled";} ?>" <?php if($row['quantity'] >= checkStocksLeft($row['productId'])){echo "disabled";} ?> name="button" id="buttonAdd-<?php echo $row['prodResId']; ?>"onclick="addQuantity(this,<?php echo $row['prodResId'] . ", " . $row['productId'] . ", ". checkStocksLeft($row['productId']); ?>)">
										<i class="fas fa-plus"></i>
									</button>
							  </div>
							</td>
							<td class="align-middle" id="total-price-<?php echo $row['prodResId']; ?>">
								&#8369; <span id="product-price-<?php echo $row['prodResId']; ?>"><?php echo number_format((float)($row['productPrice'] * $row['quantity']), 2, '.', ''); ?></span>
							</td>
							<td class="align-middle">
								<a href="#" onclick="deleteCart('<?php echo $row['productName']; ?>',<?php echo $row['prodResId']; ?>)" data-toggle="modal" data-target="#deleteProductModal">Delete</a>
							</td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
				<?php
				}
		} catch (Exception $e) {
			echo "Cart Error: " . $e->getMessage();
		}
	}
	function checkStocksLeft($productId) {
	  try {
	    $query = getConnection()->prepare("SELECT `stocks_available` FROM `product_stocks` WHERE `product_id`=:id");
	    $query->bindParam(":id", $productId);
	    $query->execute();
	    return $query->fetch()[0];
	  } catch (Exception $e) {
	    echo "Check Stocks Error: " . $e->getMessage();
	  }
	}
	function checkIfCheck(){
		if (isset($_GET['select'])) {
			if ($_GET['select'] == "true") {
				return "";
			}
		} else {
			return "checked";
		}
	}
	function TotalPrice(){
		try {
			$query = getConnection()->prepare("SELECT
				SUM(`quantity`*`price`) AS `totalPrice`
			FROM `customer_order`
			INNER JOIN `product` ON `product`.`id`=`customer_order`.`product_id`
			WHERE `customer_id`=:id AND `customer_order`.`status`=:status
			");
			$query->execute(
				array(
					":id" => getSessionCustomerId(),
					":status" => "On-Cart"
				)
			);
			$price = $query->fetch()[0];
			if ($price != null) {
				?>
				<div class="float-right">
					<input type="submit" name="checkout" class="btn btn-outline-primary" value="Proceed to Check-out"/>
				</div>
				<div class="font-weight-bold float-right mr-4">
					<span>Total Price:	&#8369;</span>
					<?php if (isset($_GET['select'])): ?>
						<?php if ($_GET['select'] == "true"): ?>
							<span id="totalPrice">0</span>
						<?php endif; ?>
						<?php else: ?>
							<span id="totalPrice"><?php echo $price; ?></span>
					<?php endif; ?>

				</div>
				<?php
			}

		} catch (Exception $e) {
			echo "Total Price Error: " . $e->getMessage();
		}
	}
	function reserveAnother() {
		try {
			$query = getConnection()->prepare("SELECT
				COUNT(*)
				FROM `customer_order`
				WHERE `customer_id`=:customerId AND `status`=:status");
			$query->execute(
				array(
					":customerId" => getSessionCustomerId(),
					":status" => "On-Cart"
				)
			);
			if ($query->fetch()[0] == 0) {
				echo "Choose products";
			} else {
				echo "Choose another product";
			}
		} catch (Exception $e) {

		}

	}
	if (!isset($_SESSION['7a13ce2a07525b4fd46ebc0226706fab'])) {
		header("Location: ../account/?signin=1&reservation=1");
	}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<?php getHeadLinks(); ?>
		<?php echo "<title>" . $pageTitle . "</title>"; ?>
    <style media="screen">
      .quantityButton{
        margin:0px;
        padding:0px;
        border: 1px solid#a9a9a9;
      }
			.quantityButton button{

			}
      .quantityButton input[type=text]{
        width: 40px;
        text-align: center;
      }
    </style>
    <script type="text/javascript">
			function deleteCart(name, id) {
				document.getElementById("deleteProductTitle").innerText = name;
				document.getElementById("deleteProductLink").href="../code/removeProduct.php?id=" + id;
			}
      function addQuantity(buttonAdd, id, productId, stocksLeft){
				var x = parseInt(document.getElementById("quantity-" + id).value);
				if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safar
	        xmlhttp=new XMLHttpRequest();
	      } else { // code for IE6, IE5
	        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	      }
	      xmlhttp.onreadystatechange=function() {
	        if (this.readyState==4 && this.status==200) {
						if (x > 9) {
							buttonAdd.disabled = true;
						} else if (x+1 < stocksLeft) {
							document.getElementById("quantity-" + id).value = x+1;
							buttonAdd.disabled = false;
							document.getElementById("buttonMinus-" + id).disabled = false;
							computePrice(document.getElementById("quantity-" + id).value = x+1, id, "add");
						} else {
							buttonAdd.disabled = true;
						}
	        }
	      }
	      xmlhttp.open("GET", "../code/addToCart.php?idx=" + productId + "&sign=add",true);
	      xmlhttp.send();
      }
      function minusQuantity(buttonMinus, id, productId){
        var x = parseInt(document.getElementById("quantity-" + id).value);
				//--------------------------
				if (window.XMLHttpRequest) {
	        // code for IE7+, Firefox, Chrome, Opera, Safari
	        xmlhttp=new XMLHttpRequest();
	      } else {  // code for IE6, IE5
	        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	      }
	      xmlhttp.onreadystatechange=function() {
	        if (this.readyState==4 && this.status==200) {
						if (x > 2) {
		          document.getElementById("quantity-" + id).value = x-1;
							buttonMinus.disabled = false;
							document.getElementById("buttonAdd-" + id).disabled = false;
		        } else if (x == 2) {
							buttonMinus.disabled = true;
		        }
						computePrice(document.getElementById("quantity-" + id).value = x-1, id, "minus");
	        }
	      }
	      xmlhttp.open("GET","../code/addToCart.php?idx="+productId+"&sign=minus",true);
	      xmlhttp.send();
				//------------------------------
      }
			function computePrice(quantity, id, sign){
				var x = parseInt(document.getElementById('product-price-'+id).innerText);
				var subtotal = x * parseInt(quantity);
				if(quantity > 1){
					document.getElementById("total-price-"+id).innerHTML = "&#8369; " + subtotal  + ".00";
					totalPrice(x, sign);
				} else {
					document.getElementById("total-price-"+id).innerHTML = "&#8369; " + x  + ".00";
				}
			}
			function totalPrice(price, sign){
				var total = document.getElementById("totalPrice");
				if (sign == "add") {
					total.innerText = parseFloat(total.innerHTML) + price;
				} else if (sign == "minus") {
					total.innerText = parseFloat(total.innerText) - price;
				}
			}
			function selectAllCheck(x) {
				var allPrice = document.getElementsByName("selected-product-price[]");
				var all = document.getElementsByName('selected-product[]');
				var total = document.getElementById("totalPrice");
				var temp =0;
				if (x.checked == false) {
					for (var i = 0; i < all.length; i++){
						if (all[i].checked == true) {
							total.innerText = Number(parseFloat(total.innerText) - parseFloat(allPrice[i].value)).toFixed(2);
							//total.innerText = parseFloat(total.innerText) - parseFloat(allPrice[i].value);
						} else {
							//total.innerText = parseFloat(total.innerText) + parseFloat(all[i].value);
						}
						all[i].checked = false;
					}
				} else if (x.checked == true) {
					for (var i = 0; i < all.length; i++){
						if (all[i].checked == false) {
							total.innerText = Number(parseFloat(total.innerText) + parseFloat(allPrice[i].value)).toFixed(2);
							//total.innerText = parseFloat(total.innerText) + parseFloat(allPrice[i].value);
						} else {
							//total.innerText = parseFloat(total.innerText) - parseFloat(all[i].value);
						}
						all[i].checked = true;
					}
				}
			}
			function selectProduct(x, price) {
				var all = document.getElementsByName('selected-product[]');
				var total = document.getElementById("totalPrice");
				check = true;
				for (var i = 0; i < all.length; i++){
					if (all[i].checked == false) {
						check = false;
					}
				}
				if (x.checked == false) {
					total.innerText = Number(parseFloat(total.innerText) - parseFloat(price)).toFixed(2);
					//total.innerText = parseFloat(total.innerText) - parseFloat(price);
				} else if(x.checked == true) {
					total.innerText = Number(parseFloat(total.innerText) + parseFloat(price)).toFixed(2);
					//total.innerText = parseFloat(total.innerText) + parseFloat(price);
				}
				//--------
				if (check) {
					document.getElementById("selectAll").checked = true;
				} else {
				}
				if (x.checked == false) {
					document.getElementById("selectAll").checked = false;
					//total.innerText = parseFloat(total.innerText) - parseFloat(price);
				}
			}
			function checkIfSelected(){
				/*var all = document.getElementsByName('selected-product[]');
				check = true;
				for (var i = 0; i < all.length; i++) {
					if (all[i].checked == false) {
						check = false;
					}
				}
				if (check) {
					document.getElementById("selectAll").checked = true;
				} else {
					return false;
				}*/
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
						<li class="breadcrumb-item" aria-current="page">
							<a href="account.php">
								My Account
							</a>
						</li>
						<li class="breadcrumb-item active" aria-current="page">My Cart</li>
				  </ol>
				</div>
        <div>
          <h3>My Cart</h3>
        </div>
				<?php if (isset($_GET['remove'])): ?>
					<?php if ($_GET['remove'] == "success"): ?>
						<div class="alert alert-success" role="alert">
						  Product has been remove to your cart.
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<?php if (isset($_GET['select'])): ?>
					<?php if ($_GET['select'] == "true"): ?>
						<div class="alert alert-danger" role="alert">
						  Select first the product you want to reserve.
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<form action="checkout.php" method="post" onsubmit="checkIfSelected()">
	        <div>
							<?php cartDetails(); ?>
	        </div>
	        <div class="float-left">
						<a href="../products" class="btn btn-outline-primary">
							<?php reserveAnother(); ?>
						</a>
					</div>
					<div class="cart-footer col-md-6 float-right text-right">
						<?php TotalPrice();?>
					</div>
				</form>
				<div class="clearfix"></div>
      </div>
		</div>
		<!-- End of Page Content  -->
		<?php getPageFooter(); ?>
		<?php getFooterLinks(); ?>
		<div class="modal fade" id="deleteProductModal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="deleteProductModalLabel">Remove Product from cart</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
						<span>Are you Sure to Remove:</span>
						<span id="deleteProductTitle"></span>
						<span> from your cart?</span>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
		        <a href="#" class="btn btn-outline-primary" id="deleteProductLink">Remove</a>
		      </div>
		    </div>
		  </div>
		</div>
	</body>
</html>
