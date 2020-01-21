<?php
  require '../code/config.php';
  checkIfAllow("Customer List");
  function getCustomerDetails() {
    $customerDetails = getConnection()->prepare("SELECT * FROM `customer_account` WHERE `status`='Active';");
    $customerDetails->execute();
    $x = 1;
    while ($row = $customerDetails->fetch()) {
    ?>
      <tr>
        <td><?php echo $x++; ?></td>
        <td id="row-created-<?php echo $row['customer_id']; ?>"><?php echo date('F d, Y - h:i a', strtotime($row['created_at'])); ?></td>
        <td id="row-name-<?php echo $row['customer_id']; ?>"><?php echo $row['customer_name']; ?></td>
        <td id="row-email-<?php echo $row['customer_id']; ?>"><?php echo $row['email']; ?></td>
        <td>
          <!--button type="button" class="btn btn-outline-info" onclick="viewAppointment(<?php //echo $row['customer_id']; ?>)"title="View Appointment" data-toggle="modal" data-target="#customer-appointment">
            <i class="fa fa-calendar"></i>
          </button-->
          <button type="button" name="button" onclick="edit(<?php echo $row['customer_id']; ?>)" title="Edit User" class="btn btn-outline-info" data-toggle="modal" data-target="#customer-edit">
            <i class="fa fa-edit"></i>
          </button>
          <button type="button" name="button" title="Ban User" onclick="deleteCustomer(<?php echo $row['customer_id']; ?>)" class="btn btn-outline-danger" data-toggle="modal" data-target="#modalDeleteCustomer">
            <i class="fa fa-ban"></i>
          </button>
        </td>
      </tr>
    <?php
    }
  }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <?php getHeadLinks(); ?>
    <title>Customer | Dashboard | Ginos Dogs En Barbers</title>
    <script type="text/javascript">
      function restoreArchive(id, customername){
        document.getElementById('restoreCustomer').innerHTML = "<span>Are you sure to unblock " + customername + "</span>";
        document.getElementById("restoreButton").href = "../code/restoreCustomer.php?id=" + id;
      }
      function deleteCustomer(id) {
        var name = document.getElementById('row-name-' + id).innerText;
        document.getElementById('deleteModalFullName').innerHTML = "<span>Are you sure to block " + name + "?</span>";
        document.getElementById('deleteModalCustomer').href = "../code/deleteCustomer.php?id=" + id;
      }
      function edit(id){
        var name = document.getElementById('row-name-' + id).innerText;
        var email = document.getElementById('row-email-' + id).innerText;
        var created = document.getElementById('row-created-' + id).innerText;
        document.getElementById('editModalId').value = id;
        document.getElementById('editModalFullName').value = name;
        document.getElementById('editModalEmail').value = email;
        document.getElementById('editModalCreated').value = created;
      }
      function viewAppointment(x) {
        if (window.XMLHttpRequest) {
          xmlhttp=new XMLHttpRequest();
        } else {
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function() {
          if (this.readyState==4 && this.status==200) {
            document.getElementById("appointment-details").innerHTML=this.responseText;
          }
        }
        xmlhttp.open("GET","viewappointment.php?id="+x,true);
        xmlhttp.send();
      }
      function viewAppointmentInfo(x){
        if (window.XMLHttpRequest) {
          // code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
        } else {  // code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function() {
          if (this.readyState==4 && this.status==200) {
            document.getElementById("appointment-info").innerHTML=this.responseText;
          }
        }
        xmlhttp.open("GET","viewinfo.php?id="+x,true);
        xmlhttp.send();
      }
    </script>
    <style media="screen">
      .customer-list {
        width: 100%;
        background-color: #fff;
        padding: 10px;
      }
      .customerButtons{
        background-color: #fff;
        margin-bottom: 10px;
        padding: 5px;
      }
      #restoreCustomerModal {
        background-color: rgba(0, 0, 0, 0.5);
      }
    </style>
    <script type="text/javascript">
      $(document).ready(function() {
        $('#customerTable').DataTable();
      });
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
        <?php if (isset($_GET['delete'])): ?>
          <?php if ($_GET['delete'] == "success"): ?>
            <div class="alert alert-success" role="alert">
              <span>Customer has been block</span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <div class="mb-3 col-md-12">
          <a href="exporttocsv.php" class="btn btn-outline-dark">
            <i class="fas fa-download"></i>
            <span>Export to Excel</span>
          </a>
        </div>
        <div class="">
          <div class="customer-list">
            <table class="table table-striped" id="customerTable">
              <thead>
                <tr>
                  <th></th>
                  <th>
                    <span>Registered At</span>
                  </th>
                  <th>
                    <span>Name</span>
                  </th>
                  <th>
                    <span>Email</span>
                  </th>
                  <th>
                    <span>Action</span>
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php getCustomerDetails(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!--Page Content-->
    <div class="modal fade" id="customer-appointment" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addAdminLabel">Appointment</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body" id="appointment-details">
            <div class="loader"></div>
          </div>
        </div>
      </div>
    </div>
    <!-- Appointment Info -->
    <div class="modal fade" style="background-color:rgba(0,0,0,0.5);margin-left:-14px;padding-top:80px;;" id="customer-appointment-info" role="dialog">
      <div class="modal-dialog modal-lg"  role="document">
        <div class="modal-content"style="min-width:800px;">
          <div class="modal-header">
            <h5 class="modal-title" id="addAdminLabel">Appointment Info</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body" id="appointment-info">
            <div class="loader"></div>
          </div>
        </div>
      </div>
    </div>
    <!-- Edit User -->
    <div class="modal fade" id="customer-edit" role="dialog">
      <div class="modal-dialog modal-lg"  role="document">
        <div class="modal-content"style="min-width:800px;">
          <div class="modal-header">
            <h5 class="modal-title" id="addAdminLabel">Customer Details</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body" id="appointment-info">
            <form class="" action="index.html" method="post">
              <div class="form-group col-md-6">
                <label for="ecitModalId">Customer Id: </label>
                <input class="form-control" type="text" readonly required id="editModalId"/>
              </div>
              <div class="form-group col-md-6">
                <label for="editModalFullName">Full Name: </label>
                <input class="form-control" type="text" name="firstName" required id="editModalFullName"/>
              </div>
              <div class="form-group col-md-6">
                <label for="editModalFullName">Email: </label>
                <input class="form-control" type="text" name="EditEmail" required id="editModalEmail"/>
              </div>
              <div class="form-group col-md-6">
                <label for="editModalFullName">Created at: </label>
                <input class="form-control" type="text" readonly required id="editModalCreated"/>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Delete Customer Modal-->
    <div class="modal fade" id="modalDeleteCustomer" tabindex="-1" role="dialog" aria-labelledby="deleteCustomerModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteCustomerModalLabel">Block Customer</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <span id="deleteModalFullName"></span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <a class="btn btn-primary" id="deleteModalCustomer">Block Customer</a>
          </div>
        </div>
      </div>
    </div>
    <!-- Archive Product Modal -->
    <div class="modal fade" id="archiveCustomerModal" tabindex="-1" role="dialog" aria-labelledby="archiveCustomerModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="archiveCustomerModalLabel">Blocklist</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="container">
              <?php customerArchive(); ?>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Restore Customer Modal -->
    <div class="modal fade" id="restoreCustomerModal" tabindex="-1" role="dialog" aria-labelledby="restoreCustomerModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="restoreCustomerModalLabel">Unblock Customer</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="restoreCustomer">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <a href="#" class="btn btn-primary" id="restoreButton">Unblock</a>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
