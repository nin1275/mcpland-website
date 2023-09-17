<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  if (isset($_SESSION["login"])) {
    if (post("chestID") != null) {
      $amount = get("amount") != null ? get("amount") : 1;
      $chest = $db->prepare("SELECT id, productID FROM Chests WHERE id = ? AND accountID = ? AND isLocked = ? AND status = ?");
      $chest->execute(array(post("chestID"), $readAccount["id"], 0, 0));
      $readChest = $chest->fetch();
      
      if ($chest->rowCount() > 0) {
        $chestAmount = $chest = $db->prepare("SELECT COUNT(P.id) as amount FROM Chests C INNER JOIN Products P ON C.productID = P.id WHERE C.accountID = ? AND C.status = ? AND C.isLocked = ? AND P.id = ? GROUP BY P.id");
        $chestAmount->execute(array($readAccount["id"], 0, 0, $readChest["productID"]));
        $readChestAmount = $chestAmount->fetch();
        if ($chestAmount->rowCount() > 0) {
          if ($readChestAmount["amount"] >= $amount) {
            $chestForDelivering = $db->prepare("SELECT C.id FROM Chests C INNER JOIN Products P ON C.productID = P.id WHERE C.accountID = ? AND P.id = ? AND C.status = ? AND C.isLocked = ? ORDER BY C.id DESC LIMIT $amount");
            $chestForDelivering->execute(array($readAccount["id"], $readChest["productID"], 0, 0));
            
            if ($chestForDelivering->rowCount() > 0) {
              $chestForDelivering = $chestForDelivering->fetchAll();
              
              $product = $db->prepare("SELECT P.*, S.ip as serverIP, S.consoleID, S.consolePort, S.consolePassword, S.name as serverName FROM Products P INNER JOIN Servers S ON P.serverID = S.id WHERE P.id = ?");
              $product->execute(array($readChest["productID"]));
              $readProduct = $product->fetch();
  
              if ($product->rowCount() > 0) {
                $consoleIP = $readProduct["serverIP"];
                $consoleID = $readProduct["consoleID"];
                $consolePort = $readProduct["consolePort"];
                $consolePassword = $readProduct["consolePassword"];
                $consoleTimeout = 3;
    
                if ($consoleID == 1) {
                  require_once(__ROOT__."/apps/main/private/packages/class/websend/websend.php");
                  $console = new Websend($consoleIP, $consolePort);
                  $console->password = $consolePassword;
                }
                else if ($consoleID == 2) {
                  require_once(__ROOT__."/apps/main/private/packages/class/rcon/rcon.php");
                  $console = new Rcon($consoleIP, $consolePort, $consolePassword, $consoleTimeout);
                }
                else if ($consoleID == 3) {
                  require_once(__ROOT__."/apps/dashboard/private/packages/class/websender/websender.php");
                  $console = new Websender($consoleIP, $consolePassword, $consolePort);
                }
                else {
                  require_once(__ROOT__."/apps/main/private/packages/class/websend/websend.php");
                  $console = new Websend($consoleIP, $consolePort);
                  $console->password = $consolePassword;
                }
    
                foreach ($chestForDelivering as $readChestForDelivering) {
                  $lockTheChest = $db->prepare("UPDATE Chests SET isLocked = ? WHERE id = ? AND accountID = ?");
                  $lockTheChest->execute(array(1, $readChestForDelivering["id"], $readAccount["id"]));
                }
    
                if (@$console->connect()) {
                  foreach ($chestForDelivering as $readChestForDelivering) {
                    if ($readProduct["giveRoleID"] != 0) {
                      if ($readProduct["duration"] != -1) {
                        $roles = explode(',', $readProduct["giveRoleID"]);
                        foreach ($roles as $role) {
                          $checkRole = $db->prepare("SELECT * FROM Roles WHERE id = ?");
                          $checkRole->execute(array($role));
                          if ($checkRole->rowCount() > 0) {
                            $checkAccountRole = $db->prepare("SELECT * FROM AccountRoles WHERE accountID = ? AND roleID = ?");
                            $checkAccountRole->execute(array($readAccount["id"], $role));
                            if ($checkAccountRole->rowCount() > 0) {
                              $readCheckAccountRole = $checkAccountRole->fetch();
                              $expiryDate = $readProduct["duration"] == 0 ? "1000-01-01 00:00:00" : createDuration(getDuration($readCheckAccountRole["expiryDate"]) + $readProduct["duration"]);
                              $updateRole = $db->prepare("UPDATE AccountRoles SET expiryDate = ? WHERE accountID = ? AND roleID = ?");
                              $updateRole->execute(array($expiryDate, $readAccount["id"], $role));
                            }
                            else {
                              $expiryDate = $readProduct["duration"] == 0 ? "1000-01-01 00:00:00" : createDuration($readProduct["duration"]);
                              $giveRole = $db->prepare("INSERT INTO AccountRoles (accountID, roleID, expiryDate) VALUES (?, ?, ?)");
                              $giveRole->execute(array($readAccount["id"], $role, $expiryDate));
                            }
                          }
                        }
                      }
                    }
                    
                    $updateChest = $db->prepare("UPDATE Chests SET isLocked = ?, status = ? WHERE id = ? AND accountID = ? AND status = ?");
                    $updateChest->execute(array(0, 1, $readChestForDelivering["id"], $readAccount["id"], 0));
        
                    $insertChestHistory = $db->prepare("INSERT INTO ChestsHistory (accountID, chestID, type, creationDate) VALUES (?, ?, ?, ?)");
                    $insertChestHistory->execute(array($readAccount["id"], $readChestForDelivering["id"], 1, date("Y-m-d H:i:s")));
        
                    $productCommands = $db->prepare("SELECT PC.command FROM ProductCommands PC INNER JOIN Products P ON PC.productID = P.id WHERE PC.productID = ?");
                    $productCommands->execute(array($readProduct["id"]));
                    foreach ($productCommands as $readProductCommands) {
                      $console->sendCommand(str_replace("%username%", $readAccount["realname"], $readProductCommands["command"]));
                    }
                  }
      
                  $console->disconnect();
                  die("successful");
                }
                else {
                  foreach ($chestForDelivering as $readChestForDelivering) {
                    $unlockTheChest = $db->prepare("UPDATE Chests SET isLocked = ? WHERE id = ? AND accountID = ?");
                    $unlockTheChest->execute(array(0, $readChestForDelivering["id"], $readAccount["id"]));
                  }
                  die("error_connection");
                }
              }
              else {
                die("error");
              }
            }
            else {
              die("error");
            }
          }
          else {
            die("error_amount");
          }
        }
        else {
          die("error");
        }
      }
      else {
        die("error");
      }
    }
    else {
      die("error");
    }
  }
  else {
    die("error_login");
  }
?>
