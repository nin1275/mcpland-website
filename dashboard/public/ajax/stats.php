<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/dashboard/private/config/settings.php");

  if (get("target") == 'check') {
    if (get("action") == 'account-cache') {
      if (checkPerm($readAdmin, 'MANAGE_ACCOUNTS')) {
        if (createDuration(-1) > $readSettings["lastCheckAccounts"]) {
          $totalAccountCount = $db->query("SELECT id FROM Accounts");
          $totalAccountCount = $totalAccountCount->rowCount();

          $thisYearAccountCount = $db->prepare("SELECT id FROM Accounts WHERE creationDate LIKE ?");
          $thisYearAccountCount->execute(array("%".date("Y")."%"));
          $thisYearAccountCount = $thisYearAccountCount->rowCount();

          $thisMonthAccountCount = $db->prepare("SELECT id FROM Accounts WHERE creationDate LIKE ?");
          $thisMonthAccountCount->execute(array("%".date("Y-m")."%"));
          $thisMonthAccountCount = $thisMonthAccountCount->rowCount();

          $lastMonthAccountCount = $db->prepare("SELECT id FROM Accounts WHERE creationDate LIKE ?");
          $lastMonthAccountCount->execute(array("%".date("Y-m", strtotime("first day of last month"))."%"));
          $lastMonthAccountCount = $lastMonthAccountCount->rowCount();
          
          $resetOnlineAccountsHistory = $db->prepare("DELETE FROM OnlineAccountsHistory WHERE expiryDate < ?");
          $resetOnlineAccountsHistory->execute(array(date("Y-m-d H:i:s")));

          $updateSettings = $db->prepare("UPDATE Settings SET lastCheckAccounts = ?, totalAccountCount = ?, thisYearAccountCount = ?, thisMonthAccountCount = ?, lastMonthAccountCount = ? WHERE id = ?");
          $updateSettings->execute(array(date("Y-m-d H:i:s"), $totalAccountCount, $thisYearAccountCount, $thisMonthAccountCount, $lastMonthAccountCount, $readSettings["id"]));
        }
        else {
          die(false);
        }
      }
      else {
        die(false);
      }
    }
    else {
      die(false);
    }
  }
  else if (get("target") == 'chart') {
    if (get("action") == 'user') {
      if (checkPerm($readAdmin, 'MANAGE_ACCOUNTS')) {
        // DATA
        for ($i=1, $yearRegisteredUsersData=null; $i <= 12; $i++) {
          $month = sprintf("%02d", $i);
          $registeredUsers = $db->prepare("SELECT id FROM Accounts WHERE creationDate LIKE ?");
          $registeredUsers->execute(array("%".date("Y")."-".$month."%"));
          $yearRegisteredUsersData .= $registeredUsers->rowCount().",";
        }
        $yearRegisteredUsersData = rtrim($yearRegisteredUsersData, ",");

        // VALUE
        $thisYearAccountCount = $readSettings["thisYearAccountCount"];

        $jsonData = json_encode(array(
          'yearRegisteredUsersData'   => $yearRegisteredUsersData,
          'yearRegisteredUsersValue'  => convertNumber($thisYearAccountCount)
        ));
        die($jsonData);
      }
      else {
        $jsonData = json_encode(array(
          'yearRegisteredUsersData'   => "0,0,0,0,0,0,0,0,0,0,0,0",
          'yearRegisteredUsersValue'  => 0
        ));
        die($jsonData);
      }
    }
    else if (get("action") == 'store') {
      if (checkPerm($readAdmin, 'MANAGE_STORE')) {
        // DATA
        for ($i=1, $yearStoreHistoryData=null; $i <= 12; $i++) {
          $month = sprintf("%02d", $i);
          $storeHistory = $db->prepare("SELECT id FROM Orders WHERE creationDate LIKE ?");
          $storeHistory->execute(array("%".date("Y")."-".$month."%"));
          $yearStoreHistoryData .= $storeHistory->rowCount().",";
        }
        $yearStoreHistoryData = rtrim($yearStoreHistoryData, ",");

        // VALUE
        $storeHistory = $db->prepare("SELECT id FROM Orders WHERE creationDate LIKE ?");
        $storeHistory->execute(array("%".date("Y")."%"));

        $jsonData = json_encode(array(
          'yearStoreHistoryData'   => $yearStoreHistoryData,
          'yearStoreHistoryValue'  => convertNumber($storeHistory->rowCount())
        ));
        die($jsonData);
      }
      else {
        $jsonData = json_encode(array(
          'yearStoreHistoryData'   => "0,0,0,0,0,0,0,0,0,0,0,0",
          'yearStoreHistoryValue'  => 0
        ));
        die($jsonData);
      }
    }
    else if (get("action") == 'earn') {
      if (checkPerm($readAdmin, 'SUPER_ADMIN')) {
        // DATA
        for ($i=1, $yearEarnedMoneyData=null; $i <= 12; $i++) {
          $month = sprintf("%02d", $i);
          $earnedMoney = $db->prepare("SELECT SUM(earnings) AS earnings FROM CreditHistory WHERE paymentStatus = ? AND type IN (?, ?) AND creationDate LIKE ?");
          $earnedMoney->execute(array(1, 1, 2, "%".date("Y")."-".$month."%"));
          $readEarnedMoney = $earnedMoney->fetch();
          if ($readEarnedMoney["earnings"] == null) {
            $readEarnedMoney["earnings"] = 0;
          }
          $yearEarnedMoneyData .= $readEarnedMoney["earnings"].",";
        }
        $yearEarnedMoneyData = rtrim($yearEarnedMoneyData, ",");

        // VALUE
        $earnedMoney = $db->prepare("SELECT SUM(earnings) AS earnings FROM CreditHistory WHERE paymentStatus = ? AND type IN (?, ?) AND creationDate LIKE ?");
        $earnedMoney->execute(array(1, 1, 2, "%".date("Y")."%"));
        $readEarnedMoney = $earnedMoney->fetch();
        if ($readEarnedMoney["earnings"] == null) {
          $readEarnedMoney["earnings"] = 0;
        }

        $jsonData = json_encode(array(
          'yearEarnedMoneyData'   => $yearEarnedMoneyData,
          'yearEarnedMoneyValue'  => number_format($readEarnedMoney["earnings"], 0, ',', '.')
        ));
        die($jsonData);
      }
      else {
        $jsonData = json_encode(array(
          'yearEarnedMoneyData'   => "0,0,0,0,0,0,0,0,0,0,0,0",
          'yearEarnedMoneyValue'  => 0
        ));
        die($jsonData);
      }
    }
    else {
      die(false);
    }
  }
  else if (get("target") == 'card') {
    if (get("action") == 'user') {
      $totalAccountCount = $readSettings["totalAccountCount"];
      $thisYearAccountCount = $readSettings["thisYearAccountCount"];
      $thisMonthAccountCount = $readSettings["thisMonthAccountCount"];
      $lastMonthAccountCount = $readSettings["lastMonthAccountCount"];

      $jsonData = json_encode(array(
        'totalAccountCount'     => $totalAccountCount,
        'thisYearAccountCount'  => $thisYearAccountCount,
        'thisMonthAccountCount' => $thisMonthAccountCount,
        'lastMonthAccountCount' => $lastMonthAccountCount
      ));
      die($jsonData);
    }
    else if (get("action") == 'this-month-earn') {
      if (checkPerm($readAdmin, 'SUPER_ADMIN')) {
        $thisMonthEarnedMoney = $db->prepare("SELECT SUM(earnings) AS earnings FROM CreditHistory WHERE paymentStatus = ? AND type IN (?, ?) AND creationDate LIKE ?");
        $thisMonthEarnedMoney->execute(array(1, 1, 2, "%".date("Y-m")."%"));
        $readThisMonthEarnedMoney = $thisMonthEarnedMoney->fetch();
        if ($readThisMonthEarnedMoney["earnings"] == null) {
          $readThisMonthEarnedMoney["earnings"] = 0;
        }

        $lastMonthEarnedMoney = $db->prepare("SELECT SUM(earnings) AS earnings FROM CreditHistory WHERE paymentStatus = ? AND type IN (?, ?) AND creationDate LIKE ?");
        $lastMonthEarnedMoney->execute(array(1, 1, 2, "%".date("Y-m", strtotime("first day of last month"))."%"));
        $readLastMonthEarnedMoney = $lastMonthEarnedMoney->fetch();
        if ($readLastMonthEarnedMoney["earnings"] == null) {
          $readLastMonthEarnedMoney["earnings"] = 0;
        }
        $jsonData = json_encode(array(
          'thisMonthEarnedMoney' => $readThisMonthEarnedMoney["earnings"],
          'lastMonthEarnedMoney' => $readLastMonthEarnedMoney["earnings"]
        ));
        die($jsonData);
      }
      else {
        $jsonData = json_encode(array(
          'thisMonthEarnedMoney' => 0,
          'lastMonthEarnedMoney' => 0
        ));
        die($jsonData);
      }
    }
    else if (get("action") == 'waiting-comments') {
      if (checkPerm($readAdmin, 'MANAGE_BLOG')) {
        $unconfirmedComments = $db->prepare("SELECT NC.id FROM NewsComments NC INNER JOIN Accounts A ON NC.accountID = A.id INNER JOIN News N ON NC.newsID = N.id WHERE NC.status = ?");
        $unconfirmedComments->execute(array(0));
        $jsonData = json_encode(array(
          'rowCount' => $unconfirmedComments->rowCount()
        ));
        die($jsonData);
      }
      else {
        $jsonData = json_encode(array(
          'rowCount' => 0
        ));
        die($jsonData);
      }
    }
    else if (get("action") == 'this-month-news') {
      if (checkPerm($readAdmin, 'MANAGE_BLOG')) {
        $writedNews = $db->prepare("SELECT N.id FROM News N INNER JOIN Accounts A ON N.accountID = A.id INNER JOIN NewsCategories NC ON N.categoryID = NC.id WHERE N.creationDate LIKE ?");
        $writedNews->execute(array("%".date("Y-m")."%"));
        $jsonData = json_encode(array(
          'rowCount' => $writedNews->rowCount()
        ));
        die($jsonData);
      }
      else {
        $jsonData = json_encode(array(
          'rowCount' => 0
        ));
        die($jsonData);
      }
    }
    else if (get("action") == 'this-month-support') {
      if (checkPerm($readAdmin, 'MANAGE_SUPPORT')) {
        $supportMessages = $db->prepare("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.creationDate LIKE ?");
        $supportMessages->execute(array("%".date("Y-m")."%"));
        $jsonData = json_encode(array(
          'rowCount' => $supportMessages->rowCount()
        ));
        die($jsonData);
      }
      else {
        $jsonData = json_encode(array(
          'rowCount' => 0
        ));
        die($jsonData);
      }
    }
    else if (get("action") == 'waiting-support') {
      $unreadSupportMessages = $db->prepare("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.statusID IN (?, ?)");
      $unreadSupportMessages->execute(array(1, 3));
      $jsonData = json_encode(array(
        'rowCount' => $unreadSupportMessages->rowCount()
      ));
      die($jsonData);
    }
    else {
      die(false);
    }
  }
  else {
    die(false);
  }
?>
