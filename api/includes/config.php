<?php
  require_once(__ROOT__."/apps/main/private/config/status.php");
  if (INSTALL_STATUS == false) {
    exit();
  }

  require_once(__ROOT__."/apps/main/private/config/connect.php");
  require_once(__ROOT__."/apps/api/includes/functions.php");

  header("Content-Type: application/json");
  date_default_timezone_set("Europe/Istanbul");
?>
