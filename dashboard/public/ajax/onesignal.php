<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/dashboard/private/config/settings.php");

  if (post('id')) {
    $insertAccountOneSignalInfo = $db->prepare("INSERT INTO AccountOneSignalInfo (accountID, oneSignalID) VALUES (?, ?)");
    $insertAccountOneSignalInfo->execute(array($readAdmin["id"], post('id')));
  }
  else {
    die(false);
  }
?>
