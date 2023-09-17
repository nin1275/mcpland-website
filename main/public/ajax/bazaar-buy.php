<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  if (isset($_SESSION["login"])) {
    if (!moduleIsDisabled('bazaar')) {
      if (post("productID") != null) {
        $product = $db->prepare("SELECT * FROM BazaarItems WHERE id = ? AND price > ? AND sold = ?");
        $product->execute(array(post("productID"), 0, 0));
        $readProduct = $product->fetch();
        if ($product->rowCount() > 0) {
          if ($readProduct["owner"] != $readAccount["id"]) {
            $productPrice = $readProduct["price"];
        
            if ($readAccount["credit"] >= $productPrice) {
              $insertBazaarHistory = $db->prepare("INSERT INTO BazaarHistory (itemID, accountID, type, creationDate) VALUES (?, ?, ?, ?)");
              $insertBazaarHistory->execute(array($readProduct["id"], $readAccount["id"], 0, date("Y-m-d H:i:s")));
              $insertBazaarHistory->execute(array($readProduct["id"], $readProduct["owner"], 1, date("Y-m-d H:i:s")));
          
              $updateCredit = $db->prepare("UPDATE Accounts SET credit = credit - ? WHERE id = ?");
              $updateCredit->execute(array($productPrice, $readAccount["id"]));
          
              
              $creditForSeller = $productPrice;
              if ($readSettings["bazaarCommission"] > 0) {
                $creditForSeller = $productPrice - ($productPrice * $readSettings["bazaarCommission"] / 100);
              }
              $updateCredit = $db->prepare("UPDATE Accounts SET credit = credit + ? WHERE id = ?");
              $updateCredit->execute(array($creditForSeller, $readProduct["owner"]));
          
              $updateProduct = $db->prepare("UPDATE BazaarItems SET sold = ? WHERE id = ?");
              $updateProduct->execute(array(1, $readProduct["id"]));
          
              $insertProduct = $db->prepare("INSERT INTO BazaarItems (owner, serverID, itemID, name, lore, amount, durability, maxDurability, enchantments, base64, price, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
              $insertProduct->execute(array($readAccount["id"], $readProduct["serverID"], $readProduct["itemID"], $readProduct["name"], $readProduct["lore"], $readProduct["amount"], $readProduct["durability"], $readProduct["maxDurability"], $readProduct["enchantments"], $readProduct["base64"], 0, date("Y-m-d H:i:s")));
          
              die("successful");
            }
            else {
              die("unsuccessful");
            }
          }
          else {
            die("error_self");
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
