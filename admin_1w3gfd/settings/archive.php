<?php
require '../code/config.php';
checkIfAllow("archive");
function productArchive() {
  try {
    $status = "Deleted";
    $x = 1;
    $query = getConnection()->prepare("SELECT `name` FROM `product` WHERE `status`=:status");
    $query->bindParam(":status", $status);
    $query->execute();
    while ($row = $query->fetch()) {
      ?>
      <tr>
        <td><?php echo $x++; ?></td>
        <td title="<?php echo $row['name']; ?>"><?php echo  $row['name'];//substr($row['name'], 0, 20) . "..."; ?></td>
      </tr>
      <?php
    }
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php getHeadLinks(); ?>
    <title>Inbox</title>
    <style media="screen">
      textarea {
        resize: none;
      }
    </style>
  </head>
  <body>
    <!--- SideBar -->
    <?php getSidebar(); ?>
    <!--- Header -->
    <?php getHeader(); ?>
    <!--Page Content -->
    <div class="page-content">
      <div class="container">
        <div style="background-color:#fff;padding:10px;">
          <div class="m-3">
            <div>
              <h4>Archive</h4>
            </div>
            <div class="ml-5">
              <div>
                <h5>Product archive</h5>
              </div>
              <div>
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th></th>
                      <th>Product Name</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php productArchive(); ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--Page Content-->
  </body>
</html>
