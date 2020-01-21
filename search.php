<?php
	require 'code/config.php';
	// Page Title Here
  if (empty($_GET['search'])) {
		header("Location: index.php");
  }
	$pageTitle = "Search result | Gino's Dogs en Barbers";
  function getResult($id) {
    try {
        $search = implode("|", explode(" ", $_GET['search']));
      $query = getConnection()->prepare("SELECT
        `id`,
        `name`,
        `description`,
        `price`
        FROM `product`
        WHERE (`name`REGEXP :name
        OR `description` REGEXP :descr)
        AND `status`=:status");
      $query->execute(
        array(
          ":name" => $search,
          ":descr" => $search,
          ":status" => "Active"
        )
      );
      if ($id == "tbody") {
          if($query->rowCount() != 0){
                  ?>
                 <thead>
              <tr>
                <th></th>
                <th>Details</th>
                <th>Price</th>
              </tr>
              </thead>
              <tbody>
              <?php
              while ($row = $query->fetch()) {

              ?>
              <tr onclick="window.location.href='<?php echo getWebRoot() . "/products/details.php?id=" . $row['id']; ?>'">
                <td></td>
                <td>
                  <h5 class="productName"><?php echo ucwords(implode("<span class='highlight'>" . ucwords($_GET['search']) . "</span>", explode(strtolower($_GET['search']), strtolower($row['name'])))); ?></h5>
                  <span class="productDescription"><?php echo ucwords(implode("",explode("-",implode("<span class='highlight'>" . ucwords($_GET['search']) . "</span>",explode(strtolower($_GET['search']), strtolower($row['description'])))))); ?></span>
                </td>
                <td><?php echo $row['price']; ?></td>
              </tr>
              <?php
            }
            echo "</tbody>";
          } else{
              echo "<tr><td colspan=3><h3>No result found</h3></td></tr>";
          }
      } elseif ($id == "count") {
        echo $query->rowCount();
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
      tr:hover{
        cursor: pointer;
      }
			.highlight {
			  background-color: #00ff00;
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
              <a href="index.php">Home</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Search result</li>
          </ol>
        </div>
				<div class="col-lg-12 col-sm-12">
					<h3>Search result for: <?php echo $_GET['search'] ?></h3>
          <span><?php getResult("count"); ?> results found</span>
					<table class="table table-hover">
              <?php getResult("tbody"); ?>
          </table>
				</div>
			</div>
		</div>
		<!-- End of Page Content  -->
		<?php getPageFooter(); ?>
		<?php getFooterLinks(); ?>
		<script type="text/javascript">
			/*var searchQuery = "<?php //echo $_GET['search']; ?>";
			var productName = document.getElementsByClassName("productName")[0];
			var productDetails = document.getElementsByClassName("productDescription");
			/*for (var i = 0; i < productName.length; i++) {
				var res = productName[i].innerText.match(/<?php //echo $_GET['search']; ?>/gi);
				if (res.length > 0) {
					//alert(productName[i].innerText);
				}
			}*/
			//-------------------------------------
			var text = "<?php echo $_GET['search']; ?>";
			var inputText = document.getElementsByClassName("productName")[0];
		  var innerHTML = inputText.innerHTML;
		  var index = innerHTML.indexOf(text);
		  if (index >= 0) {
		   innerHTML = innerHTML.substring(0,index) + "<span class='highlight'>" + innerHTML.substring(index,index+text.length) + "</span>" + innerHTML.substring(index + text.length);
		   inputText.innerHTML = innerHTML;
		  }
		</script>
	</body>
</html>
