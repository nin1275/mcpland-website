<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  if (isset($_SESSION["login"]) && !moduleIsDisabled('forum')) {
    require_once(__ROOT__ . "/apps/main/private/packages/class/csrf/csrf.php");
    $csrf = new CSRF('csrf-sessions', 'csrf-token');
    if ($csrf->validate('sendReply', $_SERVER["HTTP_X_CSRF_TOKEN"])) {
      require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
      $upload = new \Verot\Upload\Upload($_FILES["upload"], "tr_TR");
      $imageID = md5(uniqid(rand(0, 9999)));
      if ($upload->uploaded) {
        $upload->allowed = array("image/*");
        $upload->file_new_name_body = $imageID;
        $upload->process(__ROOT__ . "/apps/main/public/assets/img/forum/uploads/");
        if ($upload->processed) {
          die(json_encode([
            'url' => '/apps/main/public/assets/img/forum/uploads/' . $imageID .".". $upload->file_dst_name_ext,
          ]));
        } else {
          die(json_encode([
            'status' => false,
            'error'  => $upload->error,
          ]));
        }
      }
      else {
        die("error_1");
      }
    }
    else {
      die("error_csrf");
    }
  }
  else {
    die("error_login");
  }
?>