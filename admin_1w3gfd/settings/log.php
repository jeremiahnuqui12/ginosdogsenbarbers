<?php
require '../code/config.php';
checkIfAllow("log");
function activityLog(){
  try {
    $query = getConnection()->prepare("SELECT * FROM `admin_activity_log`
      INNER JOIN `admin_user` ON `admin_activity_log`.`admin_id`=`admin_user`.`admin_id`
      ORDER BY `log_time` DESC");
    $query->execute();
    $count = $query->rowCount();
    if ($count == 0) {
      echo "<tr><td colspan=4>No Activity Found</td></tr>";
    } else {
      $x = 1;
      while ($row = $query->fetch()) {
        ?>
        <tr>
          <td><?php echo $x++; ?></td>
          <td><?php echo date('F d, Y - h:i a', strtotime($row['log_time'])); ?></td>
          <td><?php echo $row['username']; ?></td>
          <td><?php echo $row['log_description']; ?></td>
        </tr>
        <?php
      }
    }
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php getHeadLinks(); ?>
    <title>Activity Log</title>
    <script type="text/javascript">
      $(document).ready(function() {
        $('#logTable').DataTable();
      });
    </script>
    <style media="screen">
      .pagination{
        margin-left: -150px;
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
            <h3>Activity Log</h3>
          </div>
          <table class="table table-striped" id="logTable">
            <thead>
              <tr>
                <th></th>
                <th style="width:280px;">Time executed</th>
                <th>Actor</th>
                <th>Details</th>
              </tr>
            </thead>
            <tbody>
              <?php activityLog(); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!--Page Content-->
  </body>
</html>
