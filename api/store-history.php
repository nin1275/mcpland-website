<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/api/includes/config.php");

  $defaultDataLimit = 5;
  $maxDataLimit = 10;
  $dataLimit = (get("limit") ? get("limit") : $defaultDataLimit);
  if ($dataLimit) {
    if (is_numeric($dataLimit)) {
      if ($dataLimit <= $maxDataLimit) {
        if (get("username")) {
          $storeHistory = $db->prepare("SELECT A.realname, S.name as serverName, P.name as productName, SH.accountID, SH.price, SH.creationDate FROM StoreHistory SH INNER JOIN Accounts A ON A.id = SH.accountID INNER JOIN Servers S ON S.id = SH.serverID INNER JOIN Products P ON P.id = SH.productID WHERE realname = :realname ORDER BY SH.id DESC LIMIT :dataLimit");
          $storeHistory->bindValue(':realname', get("username"));
        }
        else {
          $storeHistory = $db->prepare("SELECT A.realname, S.name as serverName, P.name as productName, SH.accountID, SH.price, SH.creationDate FROM StoreHistory SH INNER JOIN Accounts A ON A.id = SH.accountID INNER JOIN Servers S ON S.id = SH.serverID INNER JOIN Products P ON P.id = SH.productID ORDER BY SH.id DESC LIMIT :dataLimit");
        }
        $storeHistory->bindValue(':dataLimit', $dataLimit, PDO::PARAM_INT);
        $storeHistory->execute();
        $data = array();
        foreach ($storeHistory as $readStoreHistory) {
          array_push($data, array(
            "accountID"     => (int)$readStoreHistory["accountID"],
            "username"      => $readStoreHistory["realname"],
            "serverName"    => $readStoreHistory["serverName"],
            "productName"   => $readStoreHistory["productName"],
            "price"         => (int)$readStoreHistory["price"],
            "creationDate"  => $readStoreHistory["creationDate"]
          ));
        }
        die(json_encode($data));
      }
      else {
        die("ERROR_MAX_DATA_LIMIT");
      }
    }
    else {
      die("ERROR_IS_NUMERIC");
    }
  }
  else {
    die("ERROR_GET_COUNT");
  }
?>
