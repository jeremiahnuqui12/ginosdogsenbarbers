<?php
require '../code/config.php';
checkIfAllow("Inbox");
function contactUsMessages(){
  try {
    $query = getConnection()->prepare("SELECT * FROM `contactus_messages` WHERE `status`=:status");
    $query->execute(array(
      ":status" => "Active"
    ));
    $count = $query->rowCount();
    if ($count == 0) {
      echo "<tr><td colspan=4>No Messages Found</td></tr>";
    } else {
      $x = 1;
      while ($row = $query->fetch()) {
        ?>
        <tr>
          <td><?php echo $x++; ?></td>
          <td><?php echo $row['name']; ?></td>
          <td><?php echo $row['email']; ?></td>
          <td><?php echo date('F d, Y - h:i a', strtotime($row['received_at'])); ?></td>
          <td>
            <button type="button" class="btn btn-outline-info" title="View Message" data-toggle="modal" data-target="#viewMessageModal" onclick="viewMessage(<?php echo $row['id']; ?>)">
              <i class="fa fa-edit"></i>
            </button>
            <!--button type="button" class="btn btn-outline-danger" onclick="deleteMessage(<?php //echo $row['id']; ?>)" title="Delete Message" data-toggle="modal" data-target="#deleteMessageModal">
              <i class="fa fa-trash"></i>
            </button-->
          </td>
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
    <title>Inbox</title>
    <style media="screen">
      textarea {
        resize: none;
      }
    </style>
    <script type="text/javascript">
      function deleteMessage(id){
        document.getElementById("deleteMessageModalLink").href="../code/deleteInquiry.php?id=" + id;
      }
      function viewMessage(id){
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
        } else {  // code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function() {
          if (this.readyState==4 && this.status==200) {
            details = JSON.parse(this.responseText);
            document.getElementById("sender-datesent").value = details[4];
            document.getElementById("sender-name").value = details[1];
            document.getElementById("sender-email").value = details[2];
            document.getElementById("sender-message").value = details[3];
            if (details[6] == "Not Responded") {
              document.getElementById("sentTo").value = details[2];
              document.getElementById("respondForm").action = "../code/respond.php?id=" + details[0];
            }
            else if (details[6] == "Responded") {
              document.getElementById("sentTo").value = details[2];
              document.getElementById("messageRespond").value = details[7];
              document.getElementById("messageRespond").disabled = true;
              document.getElementById("respondButton").style.display="none";
            }
          }
        }
        xmlhttp.open("GET", "details.php?idx="+id, true);
        xmlhttp.send();
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
        <div class="m-3">
          <?php if (isset($_GET['respond'])): ?>
            <?php if ($_GET['respond'] == "success"): ?>
              <div class="alert alert-success" role="alert">
                <span>Your respond has been sent.</span>
              </div>
            <?php endif; ?>
          <?php endif; ?>
          <!--button type="button" class="btn btn-outline-dark" name="button">Archive</button-->
        </div>
        <div style="background-color:#fff;padding:10px;">
          <div class="m-3">
            <h3>Inbox</h3>
          </div>
          <table class="table table-striped">
            <thead>
              <tr>
                <th></th>
                <th>Name</th>
                <th>Email</th>
                <th>Received At</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php contactUsMessages(); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!--Page Content-->
    <!-- View Message -->
    <div class="modal fade" id="viewMessageModal" tabindex="-1" role="dialog" aria-labelledby="viewMessageModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="viewMessageModalLabel">Message Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="#" method="post">
              <div>
                <button type="button" class="btn btn-outline-dark" name="button" data-toggle="modal" data-target="#respondMessage">Send Feedback</button>
              </div>
              <div class="form-row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="sender-datesent">Date Sent:</label>
                    <input type="text" name="sender-datesent" id="sender-datesent" class="form-control" disabled/>
                  </div>
                  <div class="form-group">
                    <label for="sender-name">Sender Name:</label>
                    <input type="text" name="sender-name" id="sender-name" class="form-control" disabled/>
                  </div>
                  <div class="form-group">
                    <label for="sender-email">Sender Email:</label>
                    <input type="text" name="sender-email" id="sender-email" class="form-control" disabled/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="sender-message">Feedback:</label>
                    <textarea name=sender-message rows="8" cols="80" id="sender-message" class="form-control" disabled></textarea>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" onclick="location.reload();">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Delete Message-->
    <div class="modal fade" id="deleteMessageModal" tabindex="-1" role="dialog" aria-labelledby="deleteMessageModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteMessageModalLabel">Delete Message</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Are you sure to delete this message?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
            <a href="#" id="deleteMessageModalLink" class="btn btn-outline-primary">Delete Message</a>
          </div>
        </div>
      </div>
    </div>
    <!-- Respond Message Modal-->
    <div class="modal fade" style="background-color:rgba(0, 0, 0, 0.5);" id="respondMessage" tabindex="-1" role="dialog" aria-labelledby="respondMessageLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="respondMessageLabel">Send Feedback</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form class="" action="#" id="respondForm" method="post" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="form-group">
                <label for="sentTo">Sent to:</label>
                <input type="text" name="sentTo" id="sentTo" readonly class="form-control">
              </div>
              <div class="form-group">
                <label for="message">Feedback:</label>
                <br/>
                <textarea id="messageRespond" name="messageRespond" rows="8" cols="80" minlength="20" maxlength="300" class="form-control" required></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
              <input type="submit" id="respondButton" class="btn btn-outline-primary" value="Send"/>
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
