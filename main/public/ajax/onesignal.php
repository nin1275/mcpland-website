<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  require_once(__ROOT__."/apps/main/private/packages/class/onesignal/onesignal.php");

  if ($_POST) {
    if (post("appKey") == APP_KEY) {
      if (post("type") == "credit") {
        if (isset($_POST["username"]) && isset($_POST["credit"]) && isset($_POST["earnings"])) {
          $username = post("username");
          $credit = post("credit");
          $earnings = post("earnings");
          $superAdminPermission = $db->prepare("SELECT * FROM Permissions WHERE name = ?");
          $superAdminPermission->execute(array("SUPER_ADMIN"));
          $readSuperAdminPermission = $superAdminPermission->fetch();
          if ($superAdminPermission->rowCount() > 0) {
            $adminAccounts = $db->prepare("SELECT AOSI.oneSignalID FROM Accounts A INNER JOIN AccountOneSignalInfo AOSI ON A.id = AOSI.accountID LEFT JOIN AccountRoles AR ON AR.accountID = A.id INNER JOIN Roles R ON AR.roleID = R.id INNER JOIN RolePermissions RP ON RP.roleID = R.id LEFT JOIN AccountPermissions AP ON AP.accountID = A.id WHERE AP.permissionID = :perm OR RP.permissionID = :perm GROUP BY A.id");
            $adminAccounts->execute(array(
              'perm' => $readSuperAdminPermission["id"],
            ));
            if ($adminAccounts->rowCount() > 0) {
              $oneSignalIDList = array();
              foreach ($adminAccounts as $readAdminAccounts) {
                array_push($oneSignalIDList, $readAdminAccounts["oneSignalID"]);
              }
              $oneSignal = new OneSignal($readSettings["oneSignalAppID"], $readSettings["oneSignalAPIKey"], $oneSignalIDList);
              $oneSignal->sendMessage(t__('LeaderOS Notifications'), t__('%username% has bought %credit% credits (%earnings% %currency%)', ['%username%' => $username, '%credit%' => $credit, '%earnings%' => $earnings, '%currency%' => $readSettings["currency"]]), '/dashboard/store/credit-purchase-logs');
            }
          }
        }
        else {
          die("Required values not found!");
        }
      }
      else {
        die("Invalid notifications type entered!");
      }
    }
    else {
      die("Security failed!");
    }
  }
  else {
    die("POST data not found!");
  }

?>
