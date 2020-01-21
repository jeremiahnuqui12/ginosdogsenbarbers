<?php function Maintenance() { ?>
  <!DOCTYPE html>
  <html lang="en" dir="ltr">
    <head>
      <meta charset="utf-8">
      <title></title>
    </head>
    <body>
      <h1>Server is temporarily down.</h1>
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
//---Session Start------------------------
session_start();
//---Date Timezone-----------------------
function getTimeStamp(){
  date_default_timezone_set("Asia/Manila");
  return date("Y-m-d H:i:s");
}
//---Set Database Connection----------------------------------
function getConnection(){
  /**/
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
//---Set Webroot---------------------------
function getWebroot(){
  $sitename = $_SERVER['HTTP_HOST'];
  if ($sitename == "localhost") {
    return "http://" . $sitename . "/capstone";
  } else {
    return "https://" . $sitename;
  }
}
//---Session Details---------------------
function getSessionCustomerId() {
  if (isset($_SESSION['cb24373bb88538168c8e839069491f18'])) {
    return $_SESSION['cb24373bb88538168c8e839069491f18'];
  }
}
function getAccountType(){
  return $_SESSION['b8620d1a5676a832c2c9f8fd387f0e8a'];
}
function getSessionName(){
  return $_SESSION['7a13ce2a07525b4fd46ebc0226706fab'];
}
function getSessionEmail(){
  if (isset($_SESSION['cfecb706488b9b67825b14c6792f0bcc'])) {
    return $_SESSION['cfecb706488b9b67825b14c6792f0bcc'];
  }
}
function getSessionRegisterAt(){
  return $_SESSION['e26ad384feaee8a3138677a965f539a8'];
}
//---Other Files----------------------------------------------------------------------------------------------
function cartCountReserved(){
  try {
    $query = getConnection()->prepare("SELECT count(*) FROM `customer_order`
      WHERE `customer_id`=:customerId AND `status`=:status
    ");
    $query->execute(
      array(
        ":customerId" => getSessionCustomerId(),
        ":status" => "On-Cart"
      )
    );
    return $query->fetch()[0];
  } catch (Exception $e) {

  }
}
function getProductCategories(){
    try {
    $query = getConnection()->prepare("SELECT * FROM `product_categories` ORDER BY `name` ASC");
    $query->execute();
    while($row = $query->fetch()){
      echo "<a class=\"dropdown-item\" href=\"" .  getWebroot() . "/products/?id=" . $row['id'] . "\">" . $row['name'] . "</a>";
    }
  } catch (Exception $e) {
    echo "Error In categories: " . $e->getMessage();
  }
}
function SMTPServerSettings($mail){
  $mail->SMTPDebug = 1;                                 // Enable verbose debug output
  $mail->isSMTP();                                      // Set mailer to use SMTP
  $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
  $mail->SMTPAuth = true;                               // Enable SMTP authentication
  $mail->Username = 'ginosdogsenbarbers@gmail.com';     // SMTP username
  $mail->Password = 'capstone1234';                     // SMTP password
  $mail->Port = 587;
  $mail->setFrom('ginosdogsenbarbers@gmail.com', 'Ginos Dogs En Barbers');
  $mail->isHTML(true);
}
//---Page Header-------------------
function getPageHeader() { ?>
<!-- Page Header -->
<div style="position:fixed;z-index:100;min-width:100%; box-shadow:0px 0px 10px 0px grey;">
    <!--div>
      <marquee style="background:#fff000;position:absolute;position: fixed;top:0;left:0;z-index:101;width:100%;">
        <b>ATTENTION:</b> This Website is for Educational Purposes (Capstone Project) only.
        <b>ATTENTION:</b> This Website is for Educational Purposes (Capstone Project) only.
        <b>ATTENTION:</b> This Website is for Educational Purposes (Capstone Project) only.
      </marquee>
    </div-->
    <div class="page-header">
      <div>
        <a href="<?php echo getWebroot(); ?>/index.php">
          <img src="<?php echo getWebroot(); ?>/images/18622390_1507902379228378_3089499811326682571_n.jpg" alt="Page Logo">
        </a>
      </div>
      <div class="search-bar">
        <form class="form-inline my-2 my-lg-0" action="<?php echo getWebRoot() . "/search.php" ?>" id="form-search-bar">
          <input placeholder="Search Here" type="search" maxlength="50" name="search" onblur="checkSearchBox(this)" onkeypress="searchSuggestion(this);" id="search-bar"/>
          <button type="submit" id="search-submit"><i class="fa fa-search"></i></button>
        </form>
        <div class="search-list" id="search-list">
          <ul id="search-suggestion">
          </ul>
        </div>
      </div>
    </div>

    <!-- Page Navigation Bar-->
    <nav class="navbar navbar-expand-lg navbar-light bg-light col-sm-12 col-md-12 col-lg-12" style="z-index:-10;">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse " id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item dropdown" id="servicesTab">
            <a class="nav-link" href="<?php echo getWebroot(); ?>/services/calendar.php">Appointment</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="product-tab" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Products</a>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="<?php echo getWebroot(); ?>/products">Show all</a>
              <div class="dropdown-divider"></div>
              <?php getProductCategories(); ?>
            </div>
          </li>
          <li class="nav-item" id="aboutusTab">
            <a class="nav-link" href="<?php echo getWebroot(); ?>/about.php">About</a>
          </li>
          <li class="nav-item" id="contactTab">
            <a class="nav-link" href="<?php echo getWebroot(); ?>/contact.php">Contact</a>
          </li>
        </ul>
        <ul class="navbar-nav ml-auto">
          <?php
          if (isset($_SESSION['7a13ce2a07525b4fd46ebc0226706fab'])) {
          ?>
          <li class="nav-item dropdown ml-auto">
            <div class="float-left">
              <button type="button" class="btn"name="button" id="cartButton" onclick="window.location.href='<?php echo getWebroot(); ?>/account/cart.php'">
                <i class="fas fa-shopping-cart"></i>
                <span class="badge badge-primary" id="cartBadge"><?php echo cartCountReserved(); ?></span>
              </button>
            </div>
            <div class="float-right">
              <a class="nav-link dropdown-toggle" href="#" id="navbarAccount" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php echo "<span id=\"session\">Sign-in as: " . strip_tags($_SESSION['7a13ce2a07525b4fd46ebc0226706fab']) . "</span>"; ?>
              </a>
              <div class="dropdown-menu dropdown-menu-right text-center" aria-labelledby="navbarAccount">
                <a class="dropdown-item" href="<?php echo getWebroot(); ?>/account/myproductreserve.php">My Reservation</a>
                <a class="dropdown-item" href="<?php echo getWebroot(); ?>/account/myappointment.php">My Appointment</a>
                <a class="dropdown-item" href="<?php echo getWebroot(); ?>/account/account.php">My Account</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" onclick="gapi.auth2.getAuthInstance().signOut();" href="<?php echo getWebroot(); ?>/code/logout.php">Logout</a>
              </div>
            </div>
          </li>
          <?php ;
          } else {
          ?>
          <li class="nav-item float-left" id="signinTab">
            <a class="nav-link" href="<?php echo getWebroot(); ?>/account/?signin=1">
              Account
            </a>
          </li>
          <?php
          }
          ?>
        </ul>
      </div>
    </nav>
</div>
<?php
  }
//---Page Footer ---------------
  function getFooterLinks(){ ?>
    <footer class="footer">
          <div class="container">
            <span class="text-muted">Copyrights.</span>
          </div>
    </footer>
    <div class="g-signin2" data-onsuccess="onSignIn" data-theme="dark" style="display: none;"></div>
    <script type="text/javascript">
    var link = location.protocol + '//' + location.host + location.pathname;
    if (link === "<?php echo getWebroot(); ?>/services/grooming.php" || link === "<?php echo getWebroot(); ?>/services/calendar.php") {
      document.getElementById('servicesTab').style.borderBottom = "3px solid #000";
    } else if (link === "<?php echo getWebroot(); ?>/about.php") {
      document.getElementById('aboutusTab').style.borderBottom = "3px solid #000";
    } else if (link === "<?php echo getWebroot(); ?>/products/") {
      document.getElementById('productTab').style.borderBottom = "3px solid #000";
    } else if (link === "<?php echo getWebroot(); ?>/products/details.php") {
      document.getElementById('productTab').style.borderBottom = "3px solid #000";
    } else if (link === "<?php echo getWebroot(); ?>/contact.php") {
      document.getElementById('contactTab').style.borderBottom = "3px solid #000";
    } <?php
      if (isset($_GET['signup'])) {
        echo "document.getElementById('signupTab').style.borderBottom = \"3px solid #000\";";
      } else if(isset($_GET['signin'])) {
        echo "document.getElementById('signinTab').style.borderBottom = \"3px solid #000\";";
      }
    ?>
    </script>
    <script type="text/javascript">
    function searchSuggestion(textbox) {
      if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
      } else {  // code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
          document.getElementById("search-list").style.display = "block";
          document.getElementById("search-suggestion").innerHTML=this.responseText;
        }
      }
      xmlhttp.open("GET","<?php echo getWebroot(); ?>/code/searchSuggestion.php?id="+textbox.value,true);
      xmlhttp.send();
    }
    function checkSearchBox(textboxx){
      if (textboxx.value == "") {
        document.getElementById("search-list").style.display = "none";
      }
    }
    </script>
<?php
  }
//---Head Links
function getHeadLinks(){
  ?>
<!-- ---------------------------------------- -->
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1.0, width=device-width,height=device-height, shrink-to-fit=yes, user-scalable=yes">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta property="og:url" content="https://ginosdogsenbarbers.site/"/>
<meta property="og:type" content="Pet Grooming"/>
<meta property="og:title" content="Ginos Dogs En Barbers"/>
<meta property="og:description" content="Pet Grooming"/>
<meta property="og:image" content="<?php echo getWebroot(); ?>/images/icon/18622390_1507902379228378_3089499811326682571_n.jpg"/>
<meta name="google-signin-client_id" content="744992195094-snkbqp91qobc0d5i8hqrhe50m7chtjhi.apps.googleusercontent.com">
<link rel="shorcut icon" href="<?php echo getWebroot(); ?>/images/icon/18622390_1507902379228378_3089499811326682571_n.jpg" type="icon/image">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" type="text/css"/>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" type="text/css">
<link rel="stylesheet" href="<?php echo getWebroot(); ?>/stylesheet/customBootstrap.css" type="text/css">
<link rel="stylesheet" href="<?php echo getWebroot(); ?>/stylesheet/index.css" type="text/css">
<link rel="stylesheet" href="<?php echo getWebroot(); ?>/stylesheet/header.css" type="text/css">
<link rel="stylesheet" href="<?php echo getWebroot(); ?>/stylesheet/footer.css" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" type="text/javascript"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo getWebroot(); ?>/js/home.js"></script>
<script src="https://apis.google.com/js/platform.js" async defer></script>
<?php }
  //---Footer Links-------------------
  function getPageFooter(){ ?>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" ></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" ></script>
  <script src="https://apis.google.com/js/api:client.js?onload=onLoadCallback" async defer></script>
  <?php } ?>
