<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  die(false);
  if (isset($_SESSION["login"])) {
    if (post("productID") != null) {
      $product = $db->prepare("SELECT P.*, S.name as serverName FROM Products P INNER JOIN Servers S ON P.serverID = S.id WHERE P.id = ?");
      $product->execute(array(post("productID")));
      $readProduct = $product->fetch();
      if ($product->rowCount() > 0) {
        if ($readProduct["stock"] != 0) {
          $coupon = false;

          $discountProducts = explode(",", $readSettings["storeDiscountProducts"]);
          $discountedPriceStatus = ($readProduct["discountedPrice"] != 0 && ($readProduct["discountExpiryDate"] > date("Y-m-d H:i:s") || $readProduct["discountExpiryDate"] == '1000-01-01 00:00:00'));
          $storeDiscountStatus = ($readSettings["storeDiscount"] != 0 && (in_array($readProduct["id"], $discountProducts) || $readSettings["storeDiscountProducts"] == '0') && ($readSettings["storeDiscountExpiryDate"] > date("Y-m-d H:i:s") || $readSettings["storeDiscountExpiryDate"] == '1000-01-01 00:00:00'));
          if ($discountedPriceStatus == true || $storeDiscountStatus == true) {
            $productPrice = (($storeDiscountStatus == true) ? round(($readProduct["price"]*(100-$readSettings["storeDiscount"]))/100) : $readProduct["discountedPrice"]);
          }
          else {
            $productPrice = $readProduct["price"];
          }

          if (post("couponName")) {
            $productCoupons = $db->prepare("SELECT * FROM ProductCoupons WHERE name = ? AND (expiryDate > ? OR expiryDate = ?)");
            $productCoupons->execute(array(post("couponName"), date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
            $readProductCoupons = $productCoupons->fetch();
            if ($productCoupons->rowCount() > 0) {
              $productCouponsHistory = $db->prepare("SELECT * FROM ProductCouponsHistory WHERE couponID = ?");
              $productCouponsHistory->execute(array($readProductCoupons["id"]));
              if ($readProductCoupons["piece"] > $productCouponsHistory->rowCount() || $readProductCoupons["piece"] == 0) {
                $productCouponsHistory = $db->prepare("SELECT * FROM ProductCouponsHistory WHERE accountID = ? AND couponID = ?");
                $productCouponsHistory->execute(array($readAccount["id"], $readProductCoupons["id"]));
                if ($productCouponsHistory->rowCount() == 0) {
                  $products = explode(",", $readProductCoupons["products"]);
                  if (in_array($readProduct["id"], $products) || $readProductCoupons["products"] == '0') {
                    $coupon = true;
                    $productPrice = round($productPrice*((100-$readProductCoupons["discount"])/100));
                  }
                }
              }
            }
          }

          if ($readAccount["credit"] >= $productPrice) {
            if ($coupon == true) {
              $insertProductCouponsHistory = $db->prepare("INSERT INTO ProductCouponsHistory (accountID, couponID, productID, creationDate) VALUES (?, ?, ?, ?)");
              $insertProductCouponsHistory->execute(array($readAccount["id"], $readProductCoupons["id"], $readProduct["id"], date("Y-m-d H:i:s")));
            }
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
              $adminAccounts = $db->prepare("SELECT AOSI.oneSignalID FROM Accounts A INNER JOIN AccountOneSignalInfo AOSI ON A.id = AOSI.accountID WHERE A.permission IN (?, ?, ?, ?)");
              $adminAccounts->execute(array(1, 2, 3));
              if ($adminAccounts->rowCount() > 0) {
                $oneSignalIDList = array();
                foreach ($adminAccounts as $readAdminAccounts) {
                  array_push($oneSignalIDList, $readAdminAccounts["oneSignalID"]);
                }
                $oneSignal = new OneSignal($readSettings["oneSignalAppID"], $readSettings["oneSignalAPIKey"], $oneSignalIDList);
                $oneSignal->sendMessage(t__('LeaderOS Notifications'), t__('%player% bought the %product% from %server%', ['%player%' => $readAccount["realname"], '%server%' => $readProduct["serverName"], '%product%' => $readProduct["name"]]), '/dashboard/store/store-logs');
              }
            }
            $insertStoreHistory = $db->prepare("INSERT INTO StoreHistory (accountID, productID, serverID, price, creationDate) VALUES (?, ?, ?, ?, ?)");
            $insertStoreHistory->execute(array($readAccount["id"], $readProduct["id"], $readProduct["serverID"], $productPrice, date("Y-m-d H:i:s")));
            $updateCredit = $db->prepare("UPDATE Accounts SET credit = credit - ? WHERE id = ?");
            $updateCredit->execute(array($productPrice, $readAccount["id"]));

            $insertChests = $db->prepare("INSERT INTO Chests (accountID, productID, status, creationDate) VALUES (?, ?, ?, ?)");
            $insertChests->execute(array($readAccount["id"], $readProduct["id"], 0, date("Y-m-d H:i:s")));

            if ($readProduct["stock"] != -1) {
              $updateStock = $db->prepare("UPDATE Products SET stock = stock - 1 WHERE id = ?");
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
