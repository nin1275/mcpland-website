<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  if (moduleIsDisabled('gaming-night')) die("error");
  if (isset($_SESSION["login"])) {
    if (post("productID") != null) {
      $product = $db->prepare("SELECT P.id, P.name, P.price, GNP.price as discountedPrice, GNP.stock, S.name as serverName FROM GamingNightProducts GNP INNER JOIN Products P ON GNP.productID = P.id INNER JOIN Servers S ON P.serverID = S.id WHERE GNP.productID = ?");
      $product->execute(array(post("productID")));
      $readProduct = $product->fetch();
      if ($product->rowCount() > 0) {
        if ($readProduct["stock"] != 0) {
          $couponName = null;
          $total = $readProduct["price"];
          $productPrice = $readProduct["discountedPrice"];
          $discount = $total - $productPrice;
          
          if ($readAccount["credit"] >= $productPrice) {
            $createOrder = $db->prepare("INSERT INTO Orders (accountID, coupon, total, discount, subtotal, creationDate) VALUES (?, ?, ?, ?, ?, ?)");
            $createOrder->execute(array($readAccount["id"], $couponName, $total, $discount, $productPrice, date("Y-m-d H:i:s")));
            $orderID = $db->lastInsertId();
            $createOrderProducts = $db->prepare("INSERT INTO OrderProducts (orderID, productID, quantity) VALUES (?, ?, ?)");
            $createOrderProducts->execute(array($orderID, $readProduct["id"], 1));
            
            $notificationsVariables = $readProduct["serverName"].",".$readProduct["name"];
            $insertNotifications = $db->prepare("INSERT INTO Notifications (accountID, type, variables, creationDate) VALUES (?, ?, ?, ?)");
            $insertNotifications->execute(array($readAccount["id"], 4, $notificationsVariables, date("Y-m-d H:i:s")));

            if ($readSettings["webhookStoreURL"] != '0') {
              require_once(__ROOT__."/apps/main/private/packages/class/webhook/webhook.php");
              $search = array("%username%", "%server%", "%product%");
              $replace = array($readAccount["realname"], $readProduct["serverName"], $readProduct["name"]);
              $webhookMessage = $readSettings["webhookStoreMessage"];
              $webhookEmbed = $readSettings["webhookStoreEmbed"];
              $postFields = (array(
                'content'     => ($webhookMessage != '0') ? str_replace($search, $replace, $webhookMessage) : null,
                'avatar_url'  => 'https://minotar.net/avatar/'.$readAccount["realname"].'/256.png',
                'tts'         => false,
                'embeds'      => array(
                  array(
                    'type'        => 'rich',
                    'title'       => $readSettings["webhookStoreTitle"],
                    'color'       => hexdec($readSettings["webhookStoreColor"]),
                    'description' => str_replace($search, $replace, $webhookEmbed),
                    'image'       => array(
                      'url' => ($readSettings["webhookStoreImage"] != '0') ? $readSettings["webhookStoreImage"] : null
                    ),
                    'footer'      =>
                    ($readSettings["webhookStoreAdStatus"] == 1) ? array(
                      'text'      => 'Powered by LeaderOS',
                      'icon_url'  => 'https://i.ibb.co/wNHKQ7B/leaderos-logo.png'
                    ) : array()
                  )
                )
              ));
              $curl = new \LeaderOS\Http\Webhook($readSettings["webhookStoreURL"]);
              $curl(json_encode($postFields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            }
  
            if ($readSettings["oneSignalAppID"] != '0' && $readSettings["oneSignalAPIKey"] != '0') {
              require_once(__ROOT__."/apps/main/private/packages/class/onesignal/onesignal.php");
              $notificationPermission = $db->prepare("SELECT * FROM Permissions WHERE name = ?");
              $notificationPermission->execute(array("MANAGE_STORE"));
              $readNotificationPermission = $notificationPermission->fetch();
              if ($notificationPermission->rowCount() > 0) {
                $adminAccounts = $db->prepare("SELECT AOSI.oneSignalID FROM Accounts A INNER JOIN AccountOneSignalInfo AOSI ON A.id = AOSI.accountID LEFT JOIN AccountRoles AR ON AR.accountID = A.id INNER JOIN Roles R ON AR.roleID = R.id INNER JOIN RolePermissions RP ON RP.roleID = R.id LEFT JOIN AccountPermissions AP ON AP.accountID = A.id WHERE AP.permissionID = :perm OR RP.permissionID = :perm GROUP BY A.id");
                $adminAccounts->execute(array(
                  'perm' => $readNotificationPermission["id"],
                ));
                if ($adminAccounts->rowCount() > 0) {
                  $oneSignalIDList = array();
                  foreach ($adminAccounts as $readAdminAccounts) {
                    array_push($oneSignalIDList, $readAdminAccounts["oneSignalID"]);
                  }
                  $oneSignal = new OneSignal($readSettings["oneSignalAppID"], $readSettings["oneSignalAPIKey"], $oneSignalIDList);
                  $oneSignal->sendMessage(t__('LeaderOS Notifications'), t__('%player% bought the %product% from %server%', ['%player%' => $readAccount["realname"], '%server%' => $readProduct["serverName"], '%product%' => $readProduct["name"]]), '/dashboard/store/store-logs');
                }
              }
            }
            
            $updateCredit = $db->prepare("UPDATE Accounts SET credit = credit - ? WHERE id = ?");
            $updateCredit->execute(array($productPrice, $readAccount["id"]));

            $insertChests = $db->prepare("INSERT INTO Chests (accountID, productID, status, creationDate) VALUES (?, ?, ?, ?)");
            $insertChests->execute(array($readAccount["id"], $readProduct["id"], 0, date("Y-m-d H:i:s")));

            if ($readProduct["stock"] != -1) {
              $updateStock = $db->prepare("UPDATE GamingNightProducts SET stock = stock - 1 WHERE productID = ?");
              $updateStock->execute(array($readProduct["id"]));
            }

            die("successful");
          }
          else {
            die("unsuccessful");
          }
        }
        else {
          die("stock_error");
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
