<?php
require '../code/config.php';
checkIfAllow("Admin Tab");
function admins() {
  try {
    $x = 1;
    $list = getConnection()->prepare("SELECT * FROM `admin_user` WHERE `status`='Active'");
    $list->execute();
    while ($row = $list->fetch()) {
      ?>
      <tr>
        <td><?php echo $x++; ?></td>
        <td><?php echo strip_tags($row['first_name']) . ' ' . strip_tags($row['last_name']); ?></td>
        <td><?php echo strip_tags($row['email_address']) ?></td>
        <td><?php echo strip_tags($row['username']) ?></td>
        <td>
          <button type="button" class="btn btn-outline-info" title="Edit Admin" onclick="viewUserDetails(<?php echo $row['admin_id']; ?>)"data-toggle="modal" data-target="#modalEditAdmin">
            <i class="fa fa-edit"></i>
          </button>
          <button type="button" name="button" title="Delete Admin" onclick="getId('<?php echo strip_tags($row['username']) ?>', <?php echo strip_tags($row['admin_id']) ?>)" class="btn btn-outline-danger" data-toggle="modal" data-target="#modalDeleteAdmin">
            <i class="fa fa-trash"></i>
          </button>
        </td>
      </tr>
      <?php
    }
  } catch (Exception $e) {
    echo "<tr colspan=\"6\"><td>No Data Found. Error: " .$e->getMessage() . "</td></tr>";
  }
}
function permissionList() {
  $query = getConnection()->prepare("SELECT * FROM `admin_permission`");
  $query->execute();
  while($row = $query->fetch()){
  echo '
    <li>
      <input type="checkbox" id="permission-' .$row[0] . '" name="permission[]" value="' . $row[0]. '"/>
      <label for="permission-' . $row[0] . '">' . $row[1] . '</label>
    </li>';}
}
if (isset($_GET['error'])) {
  $x = explode(",", $_GET['error']);
  for ($i=0; $i < count($x); $i++) {
    if ($x[$i] == 1) {
      $firstnameError = "Invalid Character";
    } else if ($x[$i] == 1.1) {
      $firstnameError = "First Name is Required";
    } else if ($x[$i] == 2) {
      $lastnameError = "Invalid Character";
    } else if ($x[$i] == 2.1) {
      $lastnameError = "Last Name is Required";
    } else if ($x[$i] == 3) {
      $emailError = "Invalid Email";
    } else if ($x[$i] == 3.1) {
      $emailError = "Email is Required";
    } else if ($x[$i] == 4) {
      $usernameError = "Invalid Character";
    } else if ($x[$i] == 4.1) {
      $usernameError = "Username is Required";
    } else if ($x[$i] == 5) {
      $passwordError = "Password is Required";
    } else if ($x[$i] == 6) {
      $permissionError = "Permission is Required";
    }
  }
}
if (isset($_GET['exist'])) {
  if ($_GET['exist'] == 1) {
    $usernameError = "Username Existed</span>";
  }
}
if (isset($_GET['modal'])) {
  if ($_GET['modal'] == 1) {
    $showModal = 'show" style="padding-right: 17px; display: block;background-color:rgba(0,0,0,0.5);';
    $buttonModal = '<button type="button" class="close" name="button" onclick="modalClose()"><span aria-hidden="true">&times;</span></button>';
  }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php getHeadLinks(); ?>
    <title>User Management</title>
    <script type="text/javascript">
      function getId(username, id) {
        document.getElementById('AdminUserName').innerText = "Are you sure to delete admin: " + username + "?";
        document.getElementById('deleteAdmin').href = "../code/deleteadmin.php?id=" + id + "&username=" + username;
      }
    </script>
    <script>
      $(document).ready(function() {
        $('#adminList').DataTable();
      });
    </script>
    <script type="text/javascript">
      document.getElementById("autogen-password").value = Math.random().toString(36).slice(-10);
      function validateUsername(a, event) {
        var x = event.keyCode;
        var invalidChar =  "<span id=\"invalidChar\">Invalid Character. Allowed Character(a-z, A-Z, 0-9)</span>";
        var noSpaceAllowed = "<span id=\"noSpaceAllowed\">No Space Allowed!!</span>";
        if(!(x >= 48 && x <= 57) && !(x >= 65 && x <= 90) && !(x >= 97 && x <= 122)) {
          if (!document.getElementById('invalidChar')) {
            document.getElementById('errorList').innerHTML += invalidChar;
            a.style.background = "rgba(255,255,0,0.3)";
          }
        }
        if(x == 32) {
          if (!document.getElementById('noSpaceAllowed')) {
            document.getElementById('errorList').innerHTML += "<br/>" +  noSpaceAllowed;
          }
        }

      }
      function checkUsername(x) {
        if(x.value.match(/^[a-zA-Z0-9]+$/)){
          document.getElementById('errorList').innerHTML = "";
          x.style.background = "#fff";
        }
      }
      function generatePassword(x) {
        document.getElementById("autogen-password").value = Math.random().toString(36).slice(-10);
        document.getElementById("autogen-password").style.background = "#fff";
        x.innerText = "Generate Another Password";
      }
      function validateForm(x) {
          if (!x["username"].value.match(/^[a-zA-Z0-9]+$/)) {
            return false
          }
          if (x["autogen-password"].value=="") {
            x["autogen-password"].style.background = "rgba(255,255,0,0.3)";
            return false;
          }
          if (x["permission"].checked == false) {
            return false;
          }
      }
      function checkifExist(x) {
        if (window.XMLHttpRequest) {
          // code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
        } else {  // code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function() {
          if (this.readyState==4 && this.status==200) {
            document.getElementById("username-error").innerHTML=this.responseText;
          }
        }
        xmlhttp.open("GET","../code/checkUsernameIfExist.php?id="+x.value,true);
        xmlhttp.send();
      }
    </script>
  </head>
  <style media="screen">
    .main li{
      padding-right: 25px;
    }
    .sub li{
      display:table-row;
    }
    .sub{
      padding-left: 15px;
    }
    .admin-list{
      background-color: #fff;
    }
  </style>
  <script type="text/javascript">
    function checkAccountIfCheck(x){
      if(x.checked == true){
        document.getElementById('customerAccount').checked = true;
      }
    }
    function checkProductIfCheck(x){
      if(x.checked == true){
        document.getElementById('product').checked = true;
      }
    }
    function checkSettingIfCheck(x){
      if(x.checked == true){
        document.getElementById('setting').checked = true;
      }
    }
    function viewUserDetails(x){
      if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
      } else {  // code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
          details = JSON.parse(this.responseText);
          document.getElementById("editLastName").value = details[1];
          document.getElementById("editFirstName").value = details[0];
          document.getElementById("editUsername").value = details[3];
          document.getElementById("editEmail").value = details[2];
          document.getElementById("user-privilegde").value = details[4];
        }
      }
      xmlhttp.open("GET","adminDetails.php?id="+x,true);
      xmlhttp.send();
      //--------------------------------------
    }
  </script>
  <body>
    <!--- SideBar -->
    <?php getSidebar(); ?>
    <!--- Header -->
    <?php getHeader(); ?>
    <!--Page Content -->
    <div class="page-content">
      <div class="container">
        <?php if (isset($_GET['success'])) { ?>
          <div class="alert alert-primary" role="alert">
            New admin account has been added
          </div>
        <?php } ?>
        <div>
          <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#addAdminModal">
            <i class="fas fa-plus"></i>
            <span>Add User</span>
          </button>
        </div>

        <div class="admin-list col-md-12 mt-3 p-3">
          <h3>User Management</h3>
          <table id="adminList" class="table table-striped" style="width:100%">
            <thead>
              <tr>
                <th></th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Username</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php admins(); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!--Page Content-->
    <!-- Modal Add Admin -->
    <div class="modal fade <?php if(isset($showModal)){echo $showModal;} ?>" id="addAdminModal" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="width:900px;">
          <div class="modal-header">
            <h5 class="modal-title" id="addAdminLabel">Add Admin</h5>
            <?php if (isset($buttonModal)) {
              echo $buttonModal;
            } else { ?>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <?php } ?>
          </div>
          <form name="adduser" action="../code/newuser.php" onsubmit="return validateForm(this)" method="post">
            <div class="modal-body">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="firstName" class="font-weight-bold">First Name: </label>
                  <input class="form-control" type="text" name="firstName" required onblur="checkFirtsname(this)" onkeypress="validateFirstname(this,event)" id="firstName"/>
                  <span class="text-danger" id=""><?php if (isset($firstnameError)) { echo $firstnameError; } ?></span>
                </div>
                <div class="form-group col-md-6">
                  <label for="lastName" class="font-weight-bold">Last Name: </label>
                  <input class="form-control" type="text" name="lastName" required onblur="checkLastName(this)" onkeypress="validateLastName(this,event)" id="lastName"/>
                  <span class="text-danger" id=""><?php if (isset($lastnameError)) { echo $lastnameError; } ?></span>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="emailAddress" class="font-weight-bold">Email Address: </label>
                  <input class="form-control" type="email" name="emailAddress" required onblur="checkEmail(this)" onkeypress="validateEmail(this,event)" id="emailAddress"/>
                  <span class="text-danger"><?php if (isset($emailError)) { echo $emailError; } ?></span>
                </div>
                <div class="form-group col-md-6" id="">
                  <label for="username" class="font-weight-bold">Username: </label>
                  <input class="form-control" type="text" name="username" onchange="checkifExist(this)" onblur="checkUsername(this)" onkeyup="checkIfExist(this.value)" required onkeypress="validateUsername(this, event)" maxlength="20" minlength="6" id="username"/>
                  <span class="text-danger"><?php if (isset($usernameError)) { echo $usernameError; } ?></span>
                  <span id="username-error"></span>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="autogen-password" class="font-weight-bold">Password: </label>
                  <input class="form-control" type="text" name="autogen-password" required readonly id="autogen-password"/>
                  <a href="#" onclick="generatePassword(this)">Generate Password</a>
                  <span class="text-danger"><?php if (isset($passwordError)) { echo $passwordError; } ?></span>
                </div>
              </div>
                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                  <label for="Permission" class="font-weight-bold">Permission: </label>
                  <ul class="list-inline main">
                    <li class="list-inline-item">
                      <input type="checkbox"  name="roles[]" value="Calendar Tab" id="calendar"/>
                      <label for="calendar">Calendar</label>
                    </li>
                    <li class="list-inline-item">
                      <input type="checkbox" name="roles[]" value="Customer List" id="customerList"/>
                      <label for="customerList">Customer List</label>
                    </li>
                    <li class="list-inline-item">
                      <input type="checkbox"  name="roles[]" value="Reports" id="reports"/>
                      <label for="reports">Reports</label>
                    </li>
                    <li class="list-inline-item">
                      <input type="checkbox" name="roles[]" value="Product Tab" id="product"/>
                      <label for="product">Product & Services Tab</label>
                    </li>
                    <li class="list-inline-item">
                      <input type="checkbox"  name="roles[]" value="Inbox" id="inbox"/>
                      <label for="inbox">Inbox</label>
                    </li>
                  </ul>
                  <div class="clearfix"></div>
                  <span class="text-danger"><?php if(isset($permissionError)){ echo $permissionError; } ?></span>
                </div>
          </div>
          <div class="modal-footer">
						<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
						<input type="submit" class="btn btn-outline-primary" name="AddAccount" onclick="validateNewUser(this)" value="Add Admin">
					</div>
					</form>
        </div>
      </div>
    </div>
    <!-- Modal Delete Admin-->
    <div class="modal fade" id="modalDeleteAdmin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Delete</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <span id="AdminUserName"></span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
            <a class="btn btn-outline-primary" id="deleteAdmin">Delete User</a>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal Edit Admin-->
    <div class="modal fade" id="modalEditAdmin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Update User Data</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form method="post">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="firstName">First Name</label>
                        <input type="text" readonly id="editFirstName" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="lastName">Last Name</label>
                        <input type="text" readonly id="editLastName" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="email">Email Address:</label>
                        <input type="text" readonly id="editEmail" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="username">Username:</label>
                        <input type="text" readonly id="editUsername" class="form-control">
                    </div>
                </div>
                <!--div class="form-row">
                    <div class="col-md-6">
                        <button class="btn btn-outline-primary">Change Password</button>
                    </div>
                </div-->
                <div class="form-group">
                  <label for="user-privilegde">User Privilegde</label>
                  <input type="text" readonly name="user-privilegde" id="user-privilegde" class="form-control"value=""/>
                </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-outline-primary">Update User Data</button>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      function modalClose(){
        window.location.href = "<?php echo getWebRoot(); ?>" + "/settings/admins.php";
      }
    </script>
  </body>
</html>
