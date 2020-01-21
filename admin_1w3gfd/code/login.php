<?php
  require "config.php";
  function validateData() {
    $error = array();
    if (explode('?', $_SERVER['HTTP_REFERER'], 2)[0] != getWebRoot() . "/login.php") {
      header("Location: ../login.php?login=1");
    }
    if(empty($_POST['username'])){
      array_push($error, "1");
    }
    if (empty($_POST['password'])) {
      array_push($error, "2");
    }
    if (count($error) > 0) {
      getError($error);
    } else {
      loginQuery();
    }
    getFormData();
  }
  function getError($error) {
    for ($x=0; $x < count($error) ; $x++) {
      echo $error[$x] . "<br/>";
    }
    $x = implode(",", $error);
    header("Location: ../login.php?error=". $x);
  }
  //-------------------------------------------------//
  function loginQuery() {
    try {
      $queryLogin = getConnection()->prepare("SELECT * FROM
        `admin_user`
         WHERE
          `username`=:username
          AND
          `password`=:password;
        ");
      $queryLogin->execute(
        array(
          ":username"=>getUsername(),
          ":password"=>getPassword()
        )
      );
      $row = $queryLogin->fetch();
      if($queryLogin->rowCount() == 1) {
        $_SESSION['admin_id'] = $row['admin_id'];
        $_SESSION['admin_email'] = $row['email_address'];
        $_SESSION['admin_firstname'] = $row['first_name'];
        $_SESSION['admin_lastname'] = $row['last_name'];
        $_SESSION['admin_dateCreated'] = $row['created_at'];
        $_SESSION['admin_roles'] = $row['role_value'];
        recordActivityLog($row['admin_id']);
        checkIfNewUser($row['admin_id']);
        Set_session($row['admin_id']);
      } else {
        header("Location: ../login.php?exist=0");
      }
    } catch (Exception $e) {
      echo "Login Query Error: " . $e->getMessage();
      echo "Error in Mysql Syntax. Call your System Administrator to Resolved this problem!!!";
    }
  }
  //-------------------------------------------------//
  function Set_session($admin_id) {
    try {
      $query = getConnection()->prepare("INSERT INTO `admin_session`(
        `admin_id`,
        `ip_address`
      )VALUES(
          :adminId,
          :ipAddress
        );
      ");
      $query->execute(
        array(
          ":adminId" => $admin_id,
          ":ipAddress" => $_SERVER["REMOTE_ADDR"]
        )
      );
      try {
        $_SESSION['admin_username'] = getUsername();
      } catch (Exception $e) {
        echo "Session Error: " . $e->getMessage();
      }
    } catch (Exception $e) {
      echo "Session Error: " . $e->getMessage();
    }
  }
  //-------------------------------------------------//
  //-------------------------------------------------//

  function recordActivityLog($adminId) {
    try {
      $query = getConnection()->prepare("INSERT INTO `admin_activity_log`(
          `admin_id`,
          `log_description`,
          `log_time`
        ) VALUES (
          :adminId,
          :logDescription,
          :logTime
        );
      ");
      $query->execute(
        array(
          ":adminId" => $adminId,
          ":logDescription" => "Logged In",
          ":logTime" => getTimeStamp()
        )
      );
    } catch (Exception $e) {
      echo "Record Activity Error: " . $e->getMessage();
    }
  }
  function checkIfNewUser($adminId){
    try {
      $query = getConnection()->prepare("SELECT * FROM `admin_session` WHERE `admin_id`=:adminId;");
      $query->bindParam(':adminId', $adminId);
      $query->execute();
      $row = $query->rowCount();
      if ($row > 0) {
        header("Location: ../dashboard.php");
      }else {
        header("Location: ../account/newuser.php");
      }
    } catch (Exception $e) {
      echo "Check If New User Error: " . $e->getMessage();
    }
  }
  function getUsername(){
    return strip_tags($_POST['username']);
  }
  function getPassword() {
    return md5(strip_tags($_POST['password']));
  }
  validateData();
?>
