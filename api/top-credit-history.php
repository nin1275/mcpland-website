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
          $topCreditHistory = $db->prepare("SELECT SUM(CH.price) as totalPrice, COUNT(CH.id) as totalProcess, A.realname, CH.accountID, CH.creationDate FROM CreditHistory CH INNER JOIN Accounts A ON A.id = CH.accountID WHERE CH.type IN (:mobileType, :creditCardType) AND CH.paymentStatus = :paymentStatus AND CH.creationDate LIKE :creationDate AND realname = :realname GROUP BY CH.accountID HAVING totalProcess > 0 ORDER BY totalPrice DESC LIMIT :dataLimit");
          $topCreditHistory->bindValue(':realname', get("username"));
        }
        else {
          $topCreditHistory = $db->prepare("SELECT SUM(CH.price) as totalPrice, COUNT(CH.id) as totalProcess, A.realname, CH.accountID, CH.creationDate FROM CreditHistory CH INNER JOIN Accounts A ON A.id = CH.accountID WHERE CH.type IN (:mobileType, :creditCardType) AND CH.paymentStatus = :paymentStatus AND CH.creationDate LIKE :creationDate GROUP BY CH.accountID HAVING totalProcess > 0 ORDER BY totalPrice DESC LIMIT :dataLimit");
        }
        $topCreditHistory->bindValue(':mobileType', '1');
        $topCreditHistory->bindValue(':creditCardType', '2');
        $topCreditHistory->bindValue(':paymentStatus', '1');
        $topCreditHistory->bindValue(':creationDate', '%'.date("Y-m").'%');
        $topCreditHistory->bindValue(':dataLimit', $dataLimit, PDO::PARAM_INT);
        $topCreditHistory->execute();
        $data = array();
        foreach ($topCreditHistory as $readTopCreditHistory) {
          array_push($data, array(
            "accountID"     => (int)$readTopCreditHistory["accountID"],
            "username"      => $readTopCreditHistory["realname"],
            "amount"        => (int)$readTopCreditHistory["totalPrice"],
            "count"         => (int)$readTopCreditHistory["totalProcess"],
            "creationDate"  => $readTopCreditHistory["creationDate"]
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
