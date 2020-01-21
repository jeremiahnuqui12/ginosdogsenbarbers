<?php
require '../code/config.php';
if (!isset($_GET['id'])) {
  header("Location: " . $_SERVER['HTTP_REFERER']);
}
try {
  $productDetails = getConnection()->prepare("SELECT * FROM `product` WHERE `id`=:id");
  $productDetails->execute(
    array(
      ":id" => $_GET['id']
    )
  );
  $details = $productDetails->fetch();
  ?>
      <!--div class="form-group col-md-12">
        <label for="editModalId">Product Id: </label>
        <input class="form-control" type="text" readonly required name="productId" value="<?php //echo $details['id']; ?>"/>
      </div-->
      <div class="form-group col-md-12">
        <label for="editModalName">Name: </label>
        <input class="form-control" type="text" required id="editModalName" name="editProductName" value="<?php echo $details['name']; ?>"/>
      </div>
      <div class="form-group col-md-12">
        <label for="editModalDescription">Description: </label>
        <textarea class="form-control" name="editProductDescription" rows="4" cols="30" onblur="checkProductDescription(this)" id="editModalDescription" style="resize:none;"><?php echo $details['description']; ?></textarea>
      </div>
      <div class="form-group col-md-12">
        <label for="editModalPrice">Price: </label>
        <input class="form-control" type="text" name="editProductPrice" required id="editModalPrice" value="<?php echo $details['price']; ?>"/>
      </div>
      <div class="form-group col-md-12">
        <label for="editModalDateAdded">Date Added: </label>
        <input class="form-control" type="text" readonly required id="editModalDateAdded" value="<?php echo date('F d, Y', strtotime($details['date_added'])); ?>"/>
      </div>
  <?php
} catch (Exception $e) {

}

?>
