<?php
  if (isset($_COOKIE["rememberMe"]) || isset($_SESSION["login"])) {
    $loginToken = ((isset($_COOKIE["rememberMe"])) ? $_COOKIE["rememberMe"] : ((isset($_SESSION["login"])) ? $_SESSION["login"] : null));
    $accountSearch = $db->prepare("SELECT A.*, ASe.loginToken FROM Accounts A INNER JOIN AccountSessions ASe ON A.id = ASe.accountID WHERE ASe.loginToken = ? AND ASe.creationIP = ? AND ASe.expiryDate > ?");
    $accountSearch->execute(array($loginToken, getIP(), date("Y-m-d H:i:s")));
    $readAccount = $accountSearch->fetch();
    if ($accountSearch->rowCount() > 0) {
      $siteBannedStatus = $db->prepare("SELECT id FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?)");
      $siteBannedStatus->execute(array($readAccount["id"], 1, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
      if ($siteBannedStatus->rowCount() == 0) {
        $_SESSION["login"] = $readAccount["loginToken"];
        $readAccount["permissions"] = getPermissions($readAccount["id"]);
        $readAccount["roles"] = getRoles($readAccount["id"]);
        
        if (!isset($_COOKIE["rememberMe"])) {
          $updateAccountsSessions = $db->prepare("UPDATE AccountSessions SET expiryDate = ? WHERE accountID = ? AND loginToken = ?");
          $updateAccountsSessions->execute(array(createDuration(0.01666666666), $readAccount["id"], $loginToken));
        }
        $onlineAccountsHistory = $db->prepare("SELECT * FROM OnlineAccountsHistory WHERE accountID = ?");
        $onlineAccountsHistory->execute(array($readAccount["id"]));
        if ($onlineAccountsHistory->rowCount() > 0) {
          $updateOnlineAccountsHistory = $db->prepare("UPDATE OnlineAccountsHistory SET expiryDate = ?, creationDate = ? WHERE accountID = ?");
          $updateOnlineAccountsHistory->execute(array(createDuration(0.00347222222), date("Y-m-d H:i:s"), $readAccount["id"]));
        }
        else {
          $insertOnlineAccountsHistory = $db->prepare("INSERT INTO OnlineAccountsHistory (accountID, type, expiryDate, creationDate) VALUES (?, ?, ?, ?)");
          $insertOnlineAccountsHistory->execute(array($readAccount["id"], (checkStaff($readAccount) ? 1 : 0), createDuration(0.00347222222), date("Y-m-d H:i:s")));
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
?>