<?php
  session_start();
  session_unset($_SESSION['7a13ce2a07525b4fd46ebc0226706fab']);//Unset Customer Name
  session_unset($_SESSION['cb24373bb88538168c8e839069491f18']);//Unset Customer Id
  session_unset($_SESSION['cfecb706488b9b67825b14c6792f0bcc']);//Unset Customer Email
  session_destroy();
  //---Google Logout function
  header("Location: ../");
?>
