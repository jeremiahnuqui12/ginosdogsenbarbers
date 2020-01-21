<?php
  require 'config.php';
  function validate() {
    $x = explode("?", $_SERVER['HTTP_REFERER']);
    if ($x[0] == getWebRoot() . "/settings/admins.php") {
      deleteAdmin();
    }
  }
  function deleteAdmin(){
    try {
      $delete = getConnection()->prepare("UPDATE `admin_user` SET `status`='Deleted' WHERE `admin_id`=:id");
      $delete->execute(
        array(
          ":id" => $_GET['id']
        )
      );
      recordActivity("Admin " . $_GET['username'] . " has been deleted");
    } catch (Exception $e) {
      echo "Delete Query Error: " . $e->getMessage();
    }
  }
  validate();
?>
