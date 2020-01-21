<?php
include_once 'config.php';
function destroySession(){
  session_unset($_SESSION['admin_id']);
  session_unset($_SESSION['admin_email']);
  session_unset($_SESSION['admin_firstname']);
  session_unset($_SESSION['admin_lastname']);
  session_unset($_SESSION['admin_dateCreated']);
  session_unset($_SESSION['admin_roles']);
  session_destroy($_SESSION['admin_id']);
  session_destroy($_SESSION['admin_email']);
  session_destroy($_SESSION['admin_firstname']);
  session_destroy($_SESSION['admin_lastname']);
  session_destroy($_SESSION['admin_dateCreated']);
  session_destroy($_SESSION['admin_roles']);
  header("Location: ../login.php?login=1");
}
function recordLog(){
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
        ":adminId" => getSessionAdminId(),
        ":logDescription" => "Logged Out",
        ":logTime" => getTimeStamp()
      )
    );
    destroySession();
  } catch (Exception $e) {
    echo "Record Activity Error: " . $e->getMessage();
  }
}
function validate(){
  if (isset($_SESSION['admin_username'])) {
    recordLog();
  } else{
    header("Location: ../");
  }
}
validate();

/**/
?>
