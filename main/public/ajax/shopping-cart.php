<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  
  if (moduleIsDisabled('store')) die("error");
  
  function checkCoupon($coupon, $items, $checkPayAmount = true) {
    global $db, $readAccount;
  
    $itemIDs = [];
    $total = 0;
    if ($checkPayAmount) {
      foreach ($items as $item) {
        $itemIDs[] = $item["id"];
        $total += ($item["discountedPrice"] > 0 ? $item["discountedPrice"] : $item["price"]) * $item["quantity"];
      }
    }
    else {
      $itemIDs = $items;
    }
    
    
    $productCoupons = $db->prepare("SELECT * FROM ProductCoupons WHERE name = ? AND (expiryDate > ? OR expiryDate = ?)");
    $productCoupons->execute(array($coupon, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
    $readProductCoupons = $productCoupons->fetch();
    if ($productCoupons->rowCount() > 0) {
      $productCouponsHistory = $db->prepare("SELECT * FROM ProductCouponsHistory WHERE couponID = ?");
      $productCouponsHistory->execute(array($readProductCoupons["id"]));
      if ($readProductCoupons["piece"] > $productCouponsHistory->rowCount() || $readProductCoupons["piece"] == 0) {
        $productCouponsHistory = $db->prepare("SELECT * FROM ProductCouponsHistory WHERE accountID = ? AND couponID = ?");
        $productCouponsHistory->execute(array($readAccount["id"], $readProductCoupons["id"]));
        if ($productCouponsHistory->rowCount() == 0) {
          $products = explode(",", $readProductCoupons["products"]);
          if (!empty(array_intersect($itemIDs, $products)) || $readProductCoupons["products"] == '0') {
            if (!$checkPayAmount || $total >= $readProductCoupons["minPayment"]) {
              return [
                'status' => true,
                'data' => $readProductCoupons
              ];
            }
            else {
              return [
                'status' => false,
                'data' => 'error_coupon_min_payment|'.$readProductCoupons["minPayment"]
              ];
            }
          }
          else {
            return [
              'status' => false,
              'data' => 'error_coupon_no_product'
            ];
          }
        }
        else {
          return [
            'status' => false,
            'data' => 'error_coupon_used'
          ];
        }
      }
      else {
        return [
          'status' => false,
          'data' => 'error_coupon_limit'
        ];
      }
    }
    else {
      return [
        'status' => false,
        'data' => 'error_coupon_not_found'
      ];
    }
  }
  
  if (isset($_SESSION["login"])) {
    $shoppingCart = $db->prepare("SELECT * FROM ShoppingCarts WHERE accountID = ?");
    $shoppingCart->execute(array($readAccount["id"]));
    $readShoppingCart = $shoppingCart->fetch();
  
    $couponID = null;
    $couponName = null;
    $shoppingCartID = $readAccount["id"];
    if ($shoppingCart->rowCount() == 0) {
      $insertShoppingCart = $db->prepare("INSERT INTO ShoppingCarts (accountID, creationDate) VALUES (?, ?)");
      $insertShoppingCart->execute(array($readAccount["id"], date("Y-m-d H:i:s")));
    }
    
    if (get("action") == "add" && get("productID") != null) {
      $product = $db->prepare("SELECT * FROM Products WHERE id = ?");
      $product->execute(array(get("productID")));
      $readProduct = $product->fetch();
      if ($product->rowCount() > 0) {
        $checkProduct = $db->prepare("SELECT * FROM ShoppingCartProducts WHERE shoppingCartID = ? AND productID = ?");
        $checkProduct->execute(array($shoppingCartID, get("productID")));
        
        if ($checkProduct->rowCount() == 0)
          $quantity = get("quantity") != null ? get("quantity") : 1;
        else
          $quantity = $checkProduct->fetch()["quantity"] + (get("quantity") != null ? get("quantity") : 1);
  
        if ($readProduct["stock"] >= $quantity || $readProduct["stock"] == -1) {
          if ($checkProduct->rowCount() == 0) {
            $insertShoppingCartItem = $db->prepare("INSERT INTO ShoppingCartProducts (shoppingCartID, productID, quantity) VALUES (?, ?, ?)");
            $insertShoppingCartItem->execute(array($shoppingCartID, $readProduct["id"], $quantity));
          }
          else {
            $updateShoppingCartItem = $db->prepare("UPDATE ShoppingCartProducts SET quantity = ? WHERE shoppingCartID = ? AND productID = ?");
            $updateShoppingCartItem->execute(array($quantity, $shoppingCartID, $readProduct["id"]));
          }
        }
        else {
          die("error_stock");
        }
      }
      else {
        die("error_product");
      }
    }
    else if (get("action") == "remove" && get("productID") != null) {
      $deleteShoppingCartItem = $db->prepare("DELETE FROM ShoppingCartProducts WHERE shoppingCartID = ? AND productID = ?");
      $deleteShoppingCartItem->execute(array($shoppingCartID, get("productID")));
    }
    else if (get("action") == "update" && get("productID") != null) {
      $product = $db->prepare("SELECT * FROM Products WHERE id = ?");
      $product->execute(array(get("productID")));
      $readProduct = $product->fetch();
      if ($product->rowCount() > 0) {
        $quantity = get("quantity") != null ? get("quantity") : 1;
        if ($readProduct["stock"] >= $quantity || $readProduct["stock"] == -1) {
          if ($quantity > 0) {
            $updateShoppingCartItem = $db->prepare("UPDATE ShoppingCartProducts SET quantity = ? WHERE shoppingCartID = ? AND productID = ?");
            $updateShoppingCartItem->execute(array($quantity, $readAccount["id"], $readProduct["id"]));
          }
          else {
            die("error_value");
          }
        }
        else {
          die("error_stock");
        }
      }
      else {
        die("error_product");
      }
    }
    else if (get("action") == "setCoupon" && get("coupon") != null) {
      $shoppingCartProducts = $db->prepare("SELECT P.id, P.price, P.discountedPrice, SCP.quantity FROM ShoppingCartProducts SCP INNER JOIN Products P ON SCP.productID = P.id WHERE SCP.shoppingCartID = ?");
      $shoppingCartProducts->execute(array($readAccount["id"]));
      $shoppingCartProducts = $shoppingCartProducts->fetchAll();
      $couponStatus = checkCoupon(get("coupon"), $shoppingCartProducts);
      if ($couponStatus["status"]) {
        $couponID = $couponStatus["data"]["id"];
        $couponName = $couponStatus["data"]["name"];
        $updateShoppingCartCoupon = $db->prepare("UPDATE ShoppingCarts SET couponID = ? WHERE accountID = ?");
        $updateShoppingCartCoupon->execute(array($couponID, $readAccount["id"]));
      }
      else {
        die($couponStatus["data"]);
      }
    }
    else if (get("action") == "removeCoupon") {
      $updateShoppingCartCoupon = $db->prepare("UPDATE ShoppingCarts SET couponID = ? WHERE accountID = ?");
      $updateShoppingCartCoupon->execute(array(null, $readAccount["id"]));
    }
    
    if ($shoppingCart->rowCount() > 0 && get("action") != "setCoupon" && get("action") != "removeCoupon") {
      if ($readShoppingCart["couponID"] != null) {
        $coupon = $db->prepare("SELECT * FROM ProductCoupons WHERE id = ?");
        $coupon->execute(array($readShoppingCart["couponID"]));
        $readCoupon = $coupon->fetch();
        if ($coupon->rowCount() > 0) {
          $shoppingCartProducts = $db->prepare("SELECT P.id, P.price, P.discountedPrice, SCP.quantity FROM ShoppingCartProducts SCP INNER JOIN Products P ON SCP.productID = P.id WHERE SCP.shoppingCartID = ?");
          $shoppingCartProducts->execute(array($readAccount["id"]));
          $shoppingCartProducts = $shoppingCartProducts->fetchAll();
          $couponStatus = checkCoupon($readCoupon["name"], $shoppingCartProducts);
          if ($couponStatus["status"]) {
            $couponID = $couponStatus["data"]["id"];
            $couponName = $couponStatus["data"]["name"];
          }
          else {
            $updateShoppingCartCoupon = $db->prepare("UPDATE ShoppingCarts SET couponID = ? WHERE accountID = ?");
            $updateShoppingCartCoupon->execute(array(null, $readAccount["id"]));
          }
        }
        else {
          $updateShoppingCartCoupon = $db->prepare("UPDATE ShoppingCarts SET couponID = ? WHERE accountID = ?");
          $updateShoppingCartCoupon->execute(array(null, $readAccount["id"]));
        }
      }
    }
    
    $shoppingCartProducts = $db->prepare("SELECT P.*, SCP.quantity, S.name as serverName, PC.name as categoryName FROM ShoppingCartProducts SCP INNER JOIN Products P ON P.id = SCP.productID INNER JOIN Servers S ON S.id = P.serverID LEFT JOIN ProductCategories PC ON PC.id = P.categoryID WHERE SCP.shoppingCartID = ?");
    $shoppingCartProducts->execute(array($shoppingCartID));
    $shoppingCartProductsArray = [];
    $total = 0;
    $discount = 0;
    if ($couponID != null) {
      if ($shoppingCartProducts->rowCount() == 0) {
        $couponID = null;
        $couponName = null;
        $deleteCouponFromShoppingCart = $db->prepare("UPDATE ShoppingCarts SET couponID = ? WHERE accountID = ?");
        $deleteCouponFromShoppingCart->execute(array(null, $readAccount["id"]));
      }
    }
    foreach ($shoppingCartProducts as $shoppingCartProduct) {
      $total += $shoppingCartProduct["price"] * $shoppingCartProduct["quantity"];
      $discountedPrice = 0;
      $discountProducts = explode(",", $readSettings["storeDiscountProducts"]);
      $discountedPriceStatus = ($shoppingCartProduct["discountedPrice"] != 0 && ($shoppingCartProduct["discountExpiryDate"] > date("Y-m-d H:i:s") || $shoppingCartProduct["discountExpiryDate"] == '1000-01-01 00:00:00'));
      $storeDiscountStatus = ($readSettings["storeDiscount"] != 0 && (in_array($shoppingCartProduct["id"], $discountProducts) || $readSettings["storeDiscountProducts"] == '0') && ($readSettings["storeDiscountExpiryDate"] > date("Y-m-d H:i:s") || $readSettings["storeDiscountExpiryDate"] == '1000-01-01 00:00:00'));
      if ($discountedPriceStatus || $storeDiscountStatus) {
        $discountedPrice = ($storeDiscountStatus ? (($shoppingCartProduct["price"]*(100- $readSettings["storeDiscount"]))/100) : $shoppingCartProduct["discountedPrice"]);
      }
  
      $discountedPriceWithCoupon = null;
      if ($couponID != null) {
        $couponStatus = checkCoupon($couponName, [$shoppingCartProduct["id"]], false);
        if ($couponStatus["status"]) {
          $discountedPriceWithCoupon = (($discountedPrice == 0 ? $shoppingCartProduct["price"] : $discountedPrice)*((100-$couponStatus["data"]["discount"])/100));
        }
      }
      
      if ($discountedPriceWithCoupon != null) {
        $discount += ($shoppingCartProduct["price"] - $discountedPriceWithCoupon) * $shoppingCartProduct["quantity"];
      }
      else {
        if ($discountedPriceStatus || $storeDiscountStatus) {
          $discount += (($shoppingCartProduct["price"] - $discountedPrice) * $shoppingCartProduct["quantity"]);
        }
      }
      $shoppingCartProductsArray[] = [
        "id" => $shoppingCartProduct["id"],
        "server" => $shoppingCartProduct["serverName"],
        "category" => $shoppingCartProduct["categoryName"],
        "name" => $shoppingCartProduct["name"],
        "price" => (int)$shoppingCartProduct["price"],
        "discountedPrice" => (int)$discountedPrice,
        "duration" => (int)$shoppingCartProduct["duration"],
        "stock" => (int)$shoppingCartProduct["stock"],
        "image" => "/apps/main/public/assets/img/store/products/".$shoppingCartProduct["imageID"].".".$shoppingCartProduct["imageType"],
        "quantity" => (int)$shoppingCartProduct["quantity"]
      ];
    }
    $subtotal = ceil($total-$discount);
    if (get("action") == 'pay') {
      if ($subtotal > 0) {
        if ($readAccount["credit"] >= $subtotal) {
          $updateCredit = $db->prepare("UPDATE Accounts SET credit = credit - ? WHERE id = ?");
          $updateCredit->execute(array($subtotal, $readAccount["id"]));
    
          $createOrder = $db->prepare("INSERT INTO Orders (accountID, coupon, total, discount, subtotal, creationDate) VALUES (?, ?, ?, ?, ?, ?)");
          $createOrder->execute(array($readAccount["id"], $couponName, $total, $discount, $subtotal, date("Y-m-d H:i:s")));
          $orderID = $db->lastInsertId();
          $createOrderProducts = $db->prepare("INSERT INTO OrderProducts (orderID, productID, quantity) VALUES (?, ?, ?)");
          foreach ($shoppingCartProductsArray as $shoppingCartProduct) {
            $createOrderProducts->execute(array($orderID, $shoppingCartProduct["id"], $shoppingCartProduct["quantity"]));
          }
          if ($couponID != null) {
            $insertProductCouponsHistory = $db->prepare("INSERT INTO ProductCouponsHistory (accountID, couponID, productID, creationDate) VALUES (?, ?, ?, ?)");
            $insertProductCouponsHistory->execute(array($readAccount["id"], $couponID, $orderID, date("Y-m-d H:i:s")));
          }
    
          foreach ($shoppingCartProductsArray as $shoppingCartProduct) {
            for ($i = 0; $i < $shoppingCartProduct["quantity"]; $i++) {
              $insertChests = $db->prepare("INSERT INTO Chests (accountID, productID, status, creationDate) VALUES (?, ?, ?, ?)");
              $insertChests->execute(array($readAccount["id"], $shoppingCartProduct["id"], 0, date("Y-m-d H:i:s")));
            }
      
            if ($shoppingCartProduct["stock"] != -1) {
              $updateStock = $db->prepare("UPDATE Products SET stock = stock - ? WHERE id = ?");
              $updateStock->execute(array($shoppingCartProduct["quantity"], $shoppingCartProduct["id"]));
            }
          }
    
          $deleteShoppingCartProducts = $db->prepare("DELETE FROM ShoppingCartProducts WHERE shoppingCartID = ?");
          $deleteShoppingCartProducts->execute(array($readAccount["id"]));
    
          die("successful");
        }
        else {
          die("error_credit");
        }
      }
      else {
        die("error_empty");
      }
    }
    
    $shoppingCart = array(
      'items' => $shoppingCartProductsArray,
      'coupon' => $couponName,
      'total' => $total,
      'discount' => floor($discount),
      'subtotal' => $subtotal,
    );
    die(json_encode($shoppingCart));
  }
  else {
    die("error_login");
  }