<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/dashboard/private/config/settings.php");
  if (!checkPerm($readAdmin, 'MANAGE_STORE') && !checkPerm($readAdmin, 'MANAGE_GIFTS')) {
    go('/dashboard/error/001');
  }
  $treeArray = array();
  $servers = $db->query("SELECT * FROM Servers");
  foreach ($servers as $readServers) {
    array_push($treeArray, array(
      "id"      => "s_".$readServers["id"],
      "parent"  => "#",
      "text"    => $readServers["name"],
      "type"    => "server"
    ));
    $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ?");
    $productCategories->execute(array($readServers["id"]));
  	foreach ($productCategories as $readProductCategories) {
      array_push($treeArray, array(
        "id"      => "c_".$readProductCategories["id"],
        "parent"  => "s_".$readServers["id"],
        "text"    => $readProductCategories["name"],
        "type"    => "category"
      ));
      $products = $db->prepare("SELECT * FROM Products WHERE serverID = ? AND categoryID = ?");
      $products->execute(array($readServers["id"], $readProductCategories["id"]));
  		foreach ($products as $readProducts) {
        array_push($treeArray, array(
          "id"      => $readProducts["id"],
          "parent"  => "c_".$readProductCategories["id"],
          "text"    => $readProducts["name"],
          "type"    => "product"
        ));
  		}
  	}
  	$productsNoCategory = $db->prepare("SELECT * FROM Products WHERE serverID = ? AND categoryID = ?");
  	$productsNoCategory->execute(array($readServers["id"], "0"));
  	foreach ($productsNoCategory as $readProductNoCategory) {
      array_push($treeArray, array(
        "id"      => $readProductNoCategory["id"],
        "parent"  => "s_".$readServers["id"],
        "text"    => $readProductNoCategory["name"],
        "type"    => "product"
      ));
  	}
  }
  if ($treeArray != null) {
  	$treeJSON = json_encode($treeArray);
  	echo $treeJSON;
  }
?>
