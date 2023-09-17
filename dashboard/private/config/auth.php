<?php
  if (isset($_COOKIE["rememberMe"]) || isset($_SESSION["login"])) {
    $loginToken = ((isset($_COOKIE["rememberMe"])) ? $_COOKIE["rememberMe"] : ((isset($_SESSION["login"])) ? $_SESSION["login"] : null));
    $admin = $db->prepare("SELECT A.*, ASe.loginToken, ANI.lastReadDate FROM Accounts A LEFT JOIN AccountNoticationInfo ANI ON A.id = ANI.accountID INNER JOIN AccountSessions ASe ON A.id = ASe.accountID WHERE ASe.loginToken = ? AND ASe.creationIP = ? AND ASe.expiryDate > ?");
    $admin->execute(array($loginToken, getIP(), date("Y-m-d H:i:s")));
    $readAdmin = $admin->fetch();
    if ($admin->rowCount() > 0) {
      $siteBannedStatus = $db->prepare("SELECT id FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?)");
      $siteBannedStatus->execute(array($readAdmin["id"], 1, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
      if ($siteBannedStatus->rowCount() == 0) {
        $readAdmin["permissions"] = getPermissions($readAdmin["id"]);
        $readAdmin["roles"] = getRoles($readAdmin["id"]);
        
        if (checkStaff($readAdmin)) {
          $_SESSION["login"] = $readAdmin["loginToken"];
          if (!isset($_COOKIE["rememberMe"])) {
            $updateAccountsSessions = $db->prepare("UPDATE AccountSessions SET expiryDate = ? WHERE accountID = ? AND loginToken = ?");
            $updateAccountsSessions->execute(array(createDuration(0.01666666666), $readAdmin["id"], $loginToken));
          }
          $onlineAccountsHistory = $db->prepare("SELECT * FROM OnlineAccountsHistory WHERE accountID = ?");
          $onlineAccountsHistory->execute(array($readAdmin["id"]));
          if ($onlineAccountsHistory->rowCount() > 0) {
            $updateOnlineAccountsHistory = $db->prepare("UPDATE OnlineAccountsHistory SET expiryDate = ?, creationDate = ? WHERE accountID = ?");
            $updateOnlineAccountsHistory->execute(array(createDuration(0.00347222222), date("Y-m-d H:i:s"), $readAdmin["id"]));
          }
          else {
            $insertOnlineAccountsHistory = $db->prepare("INSERT INTO OnlineAccountsHistory (accountID, type, expiryDate, creationDate) VALUES (?, ?, ?, ?)");
            $insertOnlineAccountsHistory->execute(array($readAdmin["id"], 1, createDuration(0.00347222222), date("Y-m-d H:i:s")));
          }
        }
        else {
          removeCookie("rememberMe");
          session_destroy();
          go("/login");
        }
      }
      else {
        removeCookie("rememberMe");
        session_destroy();
        go("/login");
      }
    }
    else {
      removeCookie("rememberMe");
      session_destroy();
      go("/login");
    }
  }
  else {
    removeCookie("rememberMe");
    session_destroy();
    go("/login");
  }
?>