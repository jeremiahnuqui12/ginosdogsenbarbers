<?php
require '../code/config.php';
checkIfAllow("blocklist");
function blocklist(){
  try {
    $status=  "Deleted";
    $x = 1;
    $query = getConnection()->prepare("SELECT
      `customer_id`,
      `customer_name`,
      `email`
      FROM `customer_account`
      WHERE `status`=:status
      ");
    $query->bindParam(":status", $status);
    $query->execute();
    if ($query->rowCount() == 0) {
      echo "<tr><td colspan=4>No user found</td></tr>";
    } else {
      while ($row = $query->fetch()) {
        ?>
        <tr>
          <td><?php echo $x++; ?></td>
          <td><?php echo $row['customer_name']; ?></td>
          <td><?php echo $row['email']; ?></td>
          <td>
            <button type="button" title="Unblock" class="btn btn-outline-dark" name="button" data-toggle="modal" data-target="#unblockModal" onclick="unblock(<?php echo $row['customer_id'] . ", '" . $row['customer_name'] . "'"; ?>)">
              <i class="fa fa-ban"></i>
            </button>
          </td>
        </tr>
        <?php
      }
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
    <title>Blocklist</title>
    <style media="screen">
      textarea {
        resize: none;
      }
    </style>
    <script type="text/javascript">
      function unblock(id, name){
        var modalBody = document.getElementById("unblockModalBody");
        var modalLink = document.getElementById("unblockModalLink");
        modalBody.innerHTML = "Are you sure to unblock " + name;
        modalLink.href="../code/restoreCustomer.php?id=" + id;
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
        <?php if (isset($_GET['restore'])): ?>
          <?php if ($_GET['restore'] == "success"): ?>
            <div class="alert alert-success" role="alert">
              <span>Customer has been unblock</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <div  style="background-color:#fff;padding:10px;">
          <div class="m-3">
            <div>
              <h3>Customer blocklist</h3>
            </div>
            <table class="table table-striped">
              <thead>
                <tr>
                  <th></th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php blocklist(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!--Page Content-->
    <div class="modal fade" id="unblockModal" tabindex="-1" role="dialog" aria-labelledby="unblockModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="unblockModalLabel">Unblock Customer</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="unblockModalBody">
            ...
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
            <a href="#" class="btn btn-outline-primary" id="unblockModalLink">Unblock</1>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
