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
          $creditHistory = $db->prepare("SELECT A.realname, CH.accountID, CH.price, CH.creationDate FROM CreditHistory CH INNER JOIN Accounts A ON A.id = CH.accountID WHERE CH.type IN (:mobileType, :creditCardType) AND CH.paymentStatus = :paymentStatus AND realname = :realname ORDER BY CH.id DESC LIMIT :dataLimit");
          $creditHistory->bindValue(':realname', get("username"));
        }
        else {
          $creditHistory = $db->prepare("SELECT A.realname, CH.accountID, CH.price, CH.creationDate FROM CreditHistory CH INNER JOIN Accounts A ON A.id = CH.accountID WHERE CH.type IN (:mobileType, :creditCardType) AND CH.paymentStatus = :paymentStatus ORDER BY CH.id DESC LIMIT :dataLimit");
        }
        $creditHistory->bindValue(':mobileType', '1');
        $creditHistory->bindValue(':creditCardType', '2');
        $creditHistory->bindValue(':paymentStatus', '1');
        $creditHistory->bindValue(':dataLimit', $dataLimit, PDO::PARAM_INT);
        $creditHistory->execute();
        $data = array();
        foreach ($creditHistory as $readCreditHistory) {
          array_push($data, array(
            "accountID"     => (int)$readCreditHistory["accountID"],
            "username"      => $readCreditHistory["realname"],
            "amount"        => (int)$readCreditHistory["price"],
            "creationDate"  => $readCreditHistory["creationDate"]
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
