<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  die(false);
  if (isset($_SESSION["login"])) {
  	if (post("couponName") != null || post("productID") != null) {
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
            if (in_array(post("productID"), $products) || $readProductCoupons["products"] == '0') {
              die($readProductCoupons["discount"]);
            }
            else {
              die("error_product");
            }
          }
          else {
            die("error_use");
          }
        }
        else {
          die("error_piece");
        }
  		}
  		else {
        die("error_coupon");
  		}
  	}
  	else {
      die("error_coupon");
  	}
  }
  else {
    die("error_login");
  }
?>
