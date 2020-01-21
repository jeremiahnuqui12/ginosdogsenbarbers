<?php
require '../code/config.php';
checkIfAllow("Product Tab");
function getProducts() {
  try {
    $getProducts = getConnection()->prepare("SELECT * FROM `product`
      INNER JOIN `product_stocks` ON `product_stocks`.`product_id`=`product`.`id`
      WHERE `status`='Active'");
    $getProducts->execute();
    $column = $getProducts->rowCount();
    if($column == 0){
      echo "<tr><td colspan=6 style=\"text-align:center;\">No Products Found</td></tr>";
    }
    $x = 1;
    while ($row = $getProducts->fetch()) {
      ?>
      <tr>
        <td><?php echo $x++; ?></td>
        <td title="<?php echo $row['name']; ?>"><?php echo substr($row['name'], 0, 20) . "..."; ?></td>
        <td><?php echo checkStocks($row['stocks_available']); ?></td>
        <td><?php echo "&#8369; " . $row['price']; ?></td>
        <td><?php echo date('F d, Y - h:i a', strtotime($row['date_added'])); ?></td>
        <td>
          <button type="button" name="button" onclick="addStocks(<?php echo $row[0]; ?>)" title="Add Stocks" class="btn btn-outline-info">
            <i class="fa fa-plus"></i>
          </button>
          <button type="button" name="button" onclick="editProduct(<?php echo $row[0]; ?>)" title="Edit Details" class="btn btn-outline-info" data-toggle="modal" data-target="#product-edit-modal">
            <i class="fa fa-edit"></i>
          </button>
          <button type="button" name="button" title="Delete" onclick="deleteProduct(<?php echo $row[0]; ?>,'<?php echo $row['name']; ?>')" class="btn btn-outline-danger" data-toggle="modal" data-target="#product-delete-modal">
            <i class="fa fa-trash"></i>
          </button>
        </td>
      </tr>
      <?php
    }
  } catch (Exception $e) {
    echo "Error in Displaying Products: " . $e->getMessage();
  }
}
function checkStocks($stocks){
  if ($stocks >= 15) {
    return "<span class=\"text-success\">" . $stocks . "</span>";
  } else if ($stocks < 15) {
    return "<span class=\"text-danger\">" . $stocks . "</span>";
  } elseif ($stocks < 0) {
      return "<span class=\"text-danger\">Out of Stocks</span>";
  }
}
function productCategory() {
  try {
    $query = getConnection()->prepare("SELECT * FROM `product_categories`");
    $query->execute();
    while ($row = $query->fetch()) {
      echo "<option value=\"" . $row['id'] . "\">" . $row['name'] . "</option>";
    }
  } catch (\Exception $e) {
    echo "No Category Found Error: " . $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php getHeadLinks(); ?>
    <script type="text/javascript">
      function deleteProduct(id, name) {
        document.getElementById('modal-deleteProduct').innerHTML="Are you sure to delete product: <b>" + name + "</b>?";
        document.getElementById('modal-delete-button').href = "../code/deleteProduct.php?id=" + id;
      }
      function restoreArchive(id, productname){
        document.getElementById('restoreProduct').innerHTML = "<span>Are you sure to restore Product: " + productname + "</span>";
        document.getElementById("restoreButton").href = "../code/restoreProduct.php?id=" + id;
      }
    </script>
    <title>Products</title>
    <script type="text/javascript">
    $(document).ready(function() {
      var brand = document.getElementById('addProductImage');
      brand.className = 'attachment_upload';
      brand.onchange = function() {
          document.getElementById('addProductImage').value = this.value.substring(12);
      };
      // Source: http://stackoverflow.com/a/4459419/6396981
      function readURL(input) {
          if (input.files && input.files[0]) {
              var reader = new FileReader();
              reader.onload = function(e) {
                  $('.img-preview').attr('src', e.target.result);
                  $('.img-preview').css("width","40%");
                  $('.img-preview').css("height","40%");
              };
              reader.readAsDataURL(input.files[0]);
          }
      }
      $("#addProductImage").change(function() {
          readURL(this);
      });
    });
    </script>
    <script type="text/javascript">
      $(document).ready(function() {
        var brand = document.getElementById('editProductImage');
        brand.className = 'edit-attachment_upload';
        brand.onchange = function() {
            document.getElementById('editProductImage').value = this.value.substring(12);
        };
        // Source: http://stackoverflow.com/a/4459419/6396981
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.edit-img-preview').attr('src', e.target.result);
                    $('.edit-img-preview').css("width","40%");
                    $('.edit-img-preview').css("height","40%");
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#editProductImage").change(function() {
            readURL(this);
        });
      });
    </script>
    <script type="text/javascript">
      $(document).ready(function() {
        $('#productTable').DataTable();
      });
    </script>
    <script type="text/javascript">
    function editProduct(id){
      if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
      } else {  // code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
          document.getElementById("modal-productEdit").innerHTML=this.responseText;
        }
      }
      xmlhttp.open("GET","productDetails.php?id="+id,true);
      xmlhttp.send();
    }
    </script>
    <style media="screen">
      #restoreProductModal{
        background-color: rgba(0,0,0,0.5);
      }
      #productList{
        padding: 10px;
        background-color: #fff;
        margin-bottom: 10px;
        margin-top: 10px;
      }
      #serviceButton{

      }
    </style>
    <script type="text/javascript">
      function addStocks(id){
        var stocks = prompt("Enter Amount of stocks to be Added:");
        if (stocks) {
          if(isNaN(stocks)){
            alert("Must be a valid number!!!");
          } else if (stocks < 1) {
            alert("Must be greater than 1");
          } else {
            window.location.href="../code/addStocks.php?id=" + id + "&stocks=" + stocks;
          }
        }
      }
      function deleteCategory(id, name) {
        document.getElementById("deleteCategoryModalBody").innerHTML = "Are you sure to delete category '" + name + "'";
        document.getElementById("deleteCategoryModalLink").href = "../code/deleteCategory.php?id=" + id;
      }
    </script>
  </head>
  <body>
    <!--- SideBar -->
    <?php getSidebar(); ?>
    <!--- Header -->
    <?php getHeader(); ?>
    <!--Page Content -->
    <div class="page-content">
      <div class="container">
        <?php if (isset($_GET['deleteCategory'])): ?>
          <?php if ($_GET['deleteCategory'] == "failed"): ?>
            <div class="alert alert-danger" role="alert">
              <span>Category has still active products. Delete those products first to delete this category.</span>
            </div>
          <?php endif; ?>
          <?php if ($_GET['deleteCategory'] == "success"): ?>
            <div class="alert alert-success" role="alert">
              <span>Category Deleted</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($_GET['add'])): ?>
          <?php if ($_GET['add'] == "success"): ?>
            <div class="alert alert-success" role="alert">
              <span>Product Added</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($_GET['restore'])): ?>
          <?php if ($_GET['restore'] == "success"): ?>
            <div class="alert alert-success" role="alert">
              <span>Product Restored</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($_GET['delete'])): ?>
          <?php if ($_GET['delete'] == "success"): ?>
            <div class="alert alert-success" role="alert">
              <span>Product Deleted</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($_GET['addNewCategory'])): ?>
          <?php if ($_GET['addNewCategory'] == "success"): ?>
            <div class="alert alert-success" role="alert">
              <span>New Product Category Added</span>
            </div>
          <?php endif; ?>
          <?php if ($_GET['addNewCategory'] == "exist"): ?>
            <div class="alert alert-danger" role="alert">
              <span>Category already exist</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($_GET['edit'])): ?>
          <?php if ($_GET['edit'] == "success"): ?>
            <div class="alert alert-success" role="alert">
              <span>Product details updated successfully</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($_GET['stocksAdd'])): ?>
          <?php if ($_GET['stocksAdd'] == "success"): ?>
            <div class="alert alert-success" role="alert">
              <span>Stocks Added</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($_GET['image'])): ?>
          <?php if ($_GET['image'] == "invalid"): ?>
            <div class="alert alert-danger" role="alert">
              <span>Invalid Image</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <div id="serviceButton">
          <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#addProductModal">
            <i class="fas fa-plus"></i>
            <span>Add Products</span>
          </button>
          <a href="exporttoexcel.php" class="btn btn-outline-dark">
            <i class="fas fa-download"></i>
            <span>Export to Excel</span>
          </a>
          <button type="button" name="newCategoryButton" class="btn btn-outline-dark" data-toggle="modal" data-target="#addCategoryModal">
            <i class="fas fa-plus"></i>
            <span>Add Category</span>
          </button>
        </div>
        <div id="productList">
          <table class="table text-center" id="productTable">
            <thead>
              <tr>
                <th class="align-middle">#</th>
                <th class="align-middle">Name</th>
                <th class="align-middle">Available Stocks</th>
                <th class="align-middle">Price</th>
                <th class="align-middle">Date Added</th>
                <th class="align-middle">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php getProducts(); ?>
            </tbody>
          </table>
        </div>
        <!--div style="border-top: 1px solid gray;">
          <div class="float-left col-md-6" style="border-right:1px solid gray;">
            <h4>Pricing for Grooming</h4>
          </div>
          <div class="float-right col-md-6" style="border-left:1px solid gray;">

          </div>
        </div-->
      </div>
    </div>
    <!--Page Content-->
    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="../code/addProduct.php" enctype="multipart/form-data" method="post">
              <div class="form-group col-md-12">
                <label for="productName">Product Image: </label>
                <input id="addProductImage" type="file" name="addProductImage" required class="attachment_upload"/>
                <div class="main-img-preview">
                  <img class="thumbnail img-preview"/>
                </div>
              </div>
              <div class="form-group col-md-12">
                <label for="productName">Name: </label>
                <input class="form-control" type="text" name="productName" required onblur="checkProductName(this)" id="addProductName"/>
              </div>
              <div class="form-group col-md-12">
                <label for="productCategory">Category</label>
                <select class="form-control" required name="category" onchange="checkCategory(this)">
                  <option value="">---Select---</option>
                  <?php productCategory(); ?>
                </select>
              </div>
              <div class="form-group col-md-12">
                <label for="stocksAvailable">Stocks: </label>
                <input class="form-control" type="number" name="stocksAvailable" min="1" required  id="stocksAvailable"/>
              </div>
              <div class="form-group col-md-12">
                <label for="firstName">Description: </label>
                <textarea class="form-control" name="description" rows="4" cols="30" onblur="checkProductDescription(this)"id="addProductDescription" style="resize:none;"></textarea>
              </div>
              <div class="form-group col-md-12">
                <label for="firstName">Price: </label>
                <input class="form-control" type="number" name="price" min="1" max="10000" id="addProductPrice"/>
                <span class="text-danger" id="errorPrice"></span>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
            <input type="submit" class="btn btn-outline-primary" value="Add Product"/>
          </div>
        </div>
        </form>
      </div>
    </div>
    <!-- Modal Edit Products -->
    <div class="modal fade" id="product-edit-modal" tabindex="-1" role="dialog" aria-labelledby="product-edit-modal-label" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="product-edit-modal-label">Edit Product</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <!-- action="../code/editProduct.php" -->
          <form class=""  method="post" action="../code/editProduct.php">
            <div class="modal-body" id="modal-productEdit">

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
              <input type="submit" name="editProduct" class="btn btn-outline-primary" value="Edit"/>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Modal Delete Products -->
    <div class="modal fade" id="product-delete-modal" tabindex="-1" role="dialog" aria-labelledby="product-delete-modal-label" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="product-delete-modal-label">Delete product</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="modal-deleteProduct">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
            <a href="#" class="btn btn-outline-primary" id="modal-delete-button">Delete</a>
          </div>
        </div>
      </div>
    </div>
    <!-- ------------------------------------------------------------------------->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addCategoryModalLabel">Category</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
            <div class="modal-body">
              <form class="mb-3" action="../code/addProductCategory.php" method="post">
                <div class="form-group">
                  <label for="newcategoryName">Category Name:</label>
                  <input type="text" name="newCategory" class="form-control" required maxlength="30" min="4" id="newcategoryName">
                </div>
                <input type="submit" name="addCategory" value="Add Category" class="btn btn-outline-primary">
              </form>
              <div>
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th></th>
                      <th>Category</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php getCategoryList(); ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" style="background:rgba(0,0,0,0.5);" id="deleteCategoryModal" tabindex="-1" role="dialog" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteCategoryModalLabel">Delete Category</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="deleteCategoryModalBody">

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
            <a class="btn btn-outline-primary" href="#" id="deleteCategoryModalLink">Delete</a>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
<?php
function getCategoryList() {
  try {
    $x=1;
    $query = getConnection()->prepare("SELECT * FROM `product_categories` ORDER BY `name` ASC");
    $query->execute();
    while ($a = $query->fetch()) {
      ?>
      <tr>
        <td><?php echo $x++ ?></td>
        <td><?php echo $a['name']; ?></td>
        <td>
          <button type="button" name="button" class="btn btn-outline-dark" onclick="deleteCategory(<?php echo $a['id'] . ", '" . $a['name'] . "'"; ?>)"title="delete" data-toggle="modal" data-target="#deleteCategoryModal">
            <i class="fas fa-times"></i>
          </button>
        </td>
      </tr>
      <?php
    }
  } catch (Exception $e) {

  }
}
