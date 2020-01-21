<?php function Maintenance() { ?>
  <!DOCTYPE html>
  <html lang="en" dir="ltr">
    <head>
      <meta charset="utf-8">
      <title></title>
    </head>
    <body>
      <h1>Server is Temporarily Down.</h1>
    </body>
  </html>
<?php } ?>
<?php
/*if(getRealIpAddr() != "112.200.35.3"){
    Maintenance();exit();
}
function getRealIpAddr() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip=$_SERVER['HTTP_CLIENT_IP']; //check ip from share internet
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];//to check ip is pass from proxy
    } else {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}*/
session_start();
function getConnection(){
  if ($_SERVER['HTTP_HOST'] == "localhost") {
    $host = "localhost";
    $dbname = "id6697698_ginosdogsenbarbers";
    $user = "id6697698_ginosdogsenbarbers";
    $password = "capstone1234";
  } else {
    $host = "localhost";
    $dbname = "u324490643_capps";
    $user = "u324490643_capps";
    $password = "]wu64NxOz=o5Hel7j$";
  }
  try{
    $connection = new PDO("mysql:host=$host;
      dbname=$dbname",
      $user,
      $password, [
         PDO::ATTR_EMULATE_PREPARES => false,
         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
       ]
      );
      $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $connection;
  } catch(PDOException $e) {
      echo 'Database Connection Error: ' . $e->getMessage();
  }
}
function getTimeStamp() {
  date_default_timezone_set("Asia/Manila");
  return date("Y-m-d H:i:s");
}
//-----------
function getWebRoot(){
  $sitename = $_SERVER['HTTP_HOST'];
  if ($sitename == "localhost") {
    return "http://" . $sitename . "/capstone/admin_1w3gfd";
  } else {
    return "https://" . $sitename . "/admin_1w3gfd";
  }
}
function getCurrentUrl(){
  $protocol = stripos($_SERVER['SERVER_PROTOCOL'],1) === true ? 'https://' : 'http://';
  $link = $protocol . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
  $linkx = explode("?", $link);
  return $linkx[0];
}
//---------------
if (empty($_SESSION['admin_username'])) {
  echo '<script>window.location.href="' . getWebroot() . '/login.php?login=1"</script>';
}
//---Get Session Value
function getSessionAdminId(){
  return $_SESSION['admin_id'];
}
function getSessionAdminFirstName(){
  return $_SESSION['admin_firstname'];
}
function getSessionAdminLastName(){
  return $_SESSION['admin_lastname'];
}
function getSessionAdminEmail(){
  return $_SESSION['admin_email'];
}
function getSessionAdminUsername(){
  return $_SESSION['admin_username'];
}
function getSessionAdminDateAdded(){
  return $_SESSION['admin_dateCreated'];
}
function getSessionAdminRoles(){
  return $_SESSION['admin_roles'];
}
function checkIfAllow($pageName) {
  $permission = explode("----",  getSessionAdminRoles());
  if ($permission[0] == "Super User") {
    return true;
  } else {
    for ($a=0; $a < count($permission); $a++) {
      if ($permission[$a] == $pageName) {
        return true;
      } elseif ($permission == "Super User") {
        return true;
      }
    }
  }
  header("Location:" . getWebRoot() . "/unauthorized.php");
}
function checkIfAllowToAccess($pageName) {
  $permission = explode("----",  getSessionAdminRoles());
  if ($permission[0] == "Super User") {
    return true;
  } else {
    for ($a=0; $a < count($permission); $a++) {
      if ($permission[$a] == $pageName) {
        return true;
      } elseif ($permission == "Super User") {
        return true;
      }
    }
  }
  return false;
}
//------------------------------------------------------------------------------
function recordActivity($description){
  try {
    $query = getConnection()->prepare("INSERT INTO `admin_activity_log`(
        `admin_id`,
        `log_description`,
        `log_time`
      ) VALUES (
        :adminId,
        :logDescription,
        :logTime
      )
    ");
    $query->execute(
      array(
        ":adminId" => getSessionAdminId(),
        ":logDescription" => $description,
        ":logTime" => getTimeStamp()
      )
    );
  } catch (Exception $e) {
    echo "Record Activity Error: " . $e->getMessage();
  }
}
function url(){
  return sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI']
  );
}
//------------------------------------------------------------------------------
function getSidebar(){
  ?>
  <nav class="col-md-2 sidebar">
    <div>
      <img src="<?php echo getWebroot(); ?>\images\18622390_1507902379228378_3089499811326682571_n.jpg" class="img-fluid" alt="Responsive image">
    </div>
    <div class="container sidebar-links" style="margin-top:10px;">
      <ul class="sidebar-links">
        <li <?php if (explode("?", url())[0] == getWebroot() . "/dashboard.php") { ?> style="background-color:#a9a9a9;" <?php } ?>>
          <i class="fa fa-home"></i>
          <a href="<?php echo getWebroot(); ?>/dashboard.php">
            <span>Dashboard</span>
          </a>
        </li>
        <?php if (checkIfAllowToAccess("Calendar Tab")): ?>
          <li <?php if (explode("?", url())[0] == getWebroot() . "/appointment/") { ?> style="background-color:#a9a9a9;" <?php } ?>>
            <i class="fa fa-calendar"></i>
            <a href="<?php echo getWebroot(); ?>/appointment/#calendar">
              <span>Services</span>
            </a>
          </li>
        <?php endif; ?>
        <?php if (checkIfAllowToAccess("Customer List")): ?>
          <li <?php if (explode("?", url())[0] == getWebroot() . "/customer/") { ?> style="background-color:#a9a9a9;" <?php } ?>>
            <i class="fa fa-users"></i>
            <a href="<?php echo getWebroot(); ?>/customer">
              <span>Customer List</span>
            </a>
          </li>
        <?php endif; ?>
        <?php if (checkIfAllowToAccess("Reports")): ?>
          <li <?php if (explode("?", url())[0] == getWebroot() . "/reports/") { ?> style="background-color:#a9a9a9;" <?php } ?>>
            <i class="fas fa-chart-bar"></i>
            <a href="<?php echo getWebroot(); ?>/reports">
              <span>Reports</span>
            </a>
          </li>
        <?php endif; ?>
        <?php if (checkIfAllowToAccess("Product Tab")): ?>
          <li <?php if (explode("?", url())[0] == getWebroot() . "/products/") { ?> style="background-color:#a9a9a9;" <?php } ?>>
            <i class="fab fa-product-hunt"></i>
            <a href="<?php echo getWebroot(); ?>/products">
              Products
            </a>
          </li>
        <?php endif; ?>
        <?php if (checkIfAllowToAccess("Inbox")): ?>
          <li <?php if (explode("?", url())[0] == getWebroot() . "/messages/") { ?> style="background-color:#a9a9a9;" <?php } ?>>
            <i class="fa fa-comments"></i>
            <a href="<?php echo getWebroot(); ?>/messages">
              Inbox
            </a>
          </li>
        <?php endif; ?>
        <?php if (getSessionAdminRoles() == "Super User"): ?>
          <li>
            <i class="fa fa-cogs"></i>
            <a href="#" onclick="setting_onclick(this)">
              <span>Others</span>
            </a>
            <?php if (checkIfOther()): ?>
              <i class="fa fa-caret-down" id="setting-icon"></i>
              <ul class="d-block" id="sub-setting">
            <?php else: ?>
              <i class="fa fa-caret-right" id="setting-icon"></i>
              <ul class="d-none" id="sub-setting">
            <?php endif; ?>
              <li <?php if (explode("?", url())[0] == getWebroot() . "/settings/log.php") { ?> style="background-color:#a9a9a9;" <?php } ?>>
                <i class="fa fa-list-alt"></i>
                <a href="<?php echo getWebRoot(); ?>/settings/log.php">Activity Log</a>
              </li>
              <li <?php if (explode("?", url())[0] == getWebroot() . "/settings/archive.php") { ?> style="background-color:#a9a9a9;" <?php } ?>>
                <i class="fas fa-archive"></i>
                <a href="<?php echo getWebRoot(); ?>/settings/archive.php">Archive</a>
              </li>
              <li <?php if (explode("?", url())[0] == getWebroot() . "/settings/blocklist.php") { ?> style="background-color:#a9a9a9;" <?php } ?>>
                <i class="fa fa-ban"></i>
                <a href="<?php echo getWebRoot(); ?>/settings/blocklist.php">Blocklist</a>
              </li>
              <li <?php if (explode("?", url())[0] == getWebroot() . "/settings/admins.php") { ?> style="background-color:#a9a9a9;" <?php } ?>>
                <i class="fa fa-users"></i>
                <a href="<?php echo getWebRoot(); ?>/settings/admins.php">User Management</a>
              </li>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>
  <?php
}
function checkIfOther(){
  try {
    if (explode("?", url())[0] == getWebroot() . "/settings/admins.php") {
      return true;
    } elseif (explode("?", url())[0] == getWebroot() . "/settings/log.php") {
      return true;
    } elseif (explode("?", url())[0] == getWebroot() . "/settings/blocklist.php") {
      return true;
    } elseif (explode("?", url())[0] == getWebroot() . "/settings/archive.php") {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {

  }
}
function getHeader() {
  ?>
  <nav class="navbar navbar-expand-lg col-sm-12 col-md-12 col-lg-12 header">
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown ml-auto">
          <a class="nav-link dropdown-toggle" href="#" id="navbarAccount" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span id="session">Sign-in as: <?php echo getSessionAdminUsername(); ?></span>
          </a>
          <div class="dropdown-menu dropdown-menu-right text-center" aria-labelledby="navbarAccount">
            <a class="dropdown-item" href="<?php echo getWebRoot() . "/account"; ?>">Account</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="<?php echo getWebroot(); ?>/code/logout.php">Logout</a>
          </div>
        </li>
      </ul>
    </div>
  </nav>
  <?php
}
function getHeadLinks(){
  ?>
  <meta charset="utf-8"/>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
  <meta name="viewport" content="width = device-width, height = device-height, initial-scale = 1.0"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" type="text/css"/>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" type="text/css"/>
  <link rel="shorcut icon" type="icon/image" href="<?php echo getWebRoot() ?>/images/icons/18622390_1507902379228378_3089499811326682571_n.jpg"/>
  <link rel="stylesheet" href="<?php echo getWebroot(); ?>/stylesheet/master.css" type="text/css"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js" charset="utf-8"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" charset="utf-8"></script>
  <script src="<?php echo getWebRoot(); ?>\javascript\master.js" charset="utf-8"></script>
  <script src="https://code.jquery.com/jquery-3.3.1.js" charset="utf-8"></script>
  <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" charset="utf-8"></script>
  <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js" charset="utf-8"></script>
  <link rel="stylesheet" href="<?php echo getWebRoot() ?>/stylesheet/customBootstrap.css" type="text/css"/>
  <?php
}
function getFooter(){

}

?>
