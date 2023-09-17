<?php
  if (isset($_SESSION["login"])) {
    go("/profile");
  }
  use Phelium\Component\reCAPTCHA;
  $recaptchaPagesStatusJSON = $readSettings["recaptchaPagesStatus"];
  $recaptchaPagesStatus = json_decode($recaptchaPagesStatusJSON, true);
  $recaptchaStatus = $readSettings["recaptchaPublicKey"] != '0' && $readSettings["recaptchaPrivateKey"] != '0' && $recaptchaPagesStatus["loginPage"] == 1;
  if ($recaptchaStatus) {
    require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
    require_once(__ROOT__.'/apps/main/private/packages/class/recaptcha/recaptcha.php');
    $reCAPTCHA = new reCAPTCHA($readSettings["recaptchaPublicKey"], $readSettings["recaptchaPrivateKey"]);
    $reCAPTCHA->setRemoteIp(getIP());
    $reCAPTCHA->setLanguage($lang);
    $reCAPTCHA->setTheme((themeSettings("recaptchaTheme") == "light") ? "light" : ((themeSettings("recaptchaTheme") == "dark") ? "dark" : "light"));
    $extraResourcesJS = new ExtraResources('js');
    $extraResourcesJS->addResource($reCAPTCHA->getScriptURL(), true, true);
  }
?>
<section class="section page-section">
  <div class="container">
    <div class="row">
      <div class="col-md-4 offset-md-4">
        <?php
          require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
          $csrf = new CSRF('csrf-sessions', 'csrf-token');
          if (isset($_POST["login"])) {
            if (!$csrf->validate('login')) {
              echo alertError(t__('Something went wrong! Please try again later.'));
            }
            else if ($recaptchaStatus && post("g-recaptcha-response") == null) {
              echo alertError(t__('Please verify you are not a robot.'));
            }
            else if ($recaptchaStatus && !$reCAPTCHA->isValid(post("g-recaptcha-response"))) {
              // Hata Tespit
              //var_dump($reCAPTCHA->getErrorCodes());
              echo alertError(t__('Spam detected!'));
            }
            else if (post("username") == null || post("password") == null) {
              echo alertError(t__('Please fill all the fields!'));
            }
            else {
              $login = $db->prepare("SELECT * FROM Accounts WHERE realname = ?");
              $login->execute(array(post("username")));
              $readAccount = $login->fetch();
              if ($login->rowCount() > 0) {
                $password = checkPassword($readSettings["passwordType"], post("password"), $readAccount["password"]);
                
                if ($password) {
                  $siteBannedStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?)");
                  $siteBannedStatus->execute(array($readAccount["id"], 1, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
                  if ($siteBannedStatus->rowCount() > 0) {
                    echo alertError(t__('Your account is banned!'));
                  }
                  else {
                    $readAccount["permissions"] = getPermissions($readAccount["id"]);
                    if ($readSettings["maintenanceStatus"] == 1 && !checkStaff($readAccount)) {
                      echo alertError(t__('The site is under maintenance!'));
                    }
                    else {
                      if ($readSettings["authStatus"] == 1 && $readAccount["authStatus"] == 1) {
                        $_SESSION["tfa"] = array(
                          'accountID'   => $readAccount["id"],
                          'rememberMe'  => (post("rememberMe")) ? 'true' : 'false',
                          'ipAddress'   => getIP(),
                          'expiryDate'  => createDuration(0.00347222222)
                        );
                        go("/verify");
                      }
                      else {
                        $loginType = 'NEW';
                        if ($loginType == 'NEW') {
                          $db->beginTransaction();
                          $deleteOldSessions = $db->prepare("DELETE FROM AccountSessions WHERE accountID = ?");
                          $deleteOldSessions->execute(array($readAccount["id"]));

                          $loginToken = md5(uniqid(mt_rand(), true));
                          $insertAccountSessions = $db->prepare("INSERT INTO AccountSessions (accountID, loginToken, creationIP, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
                          $insertAccountSessions->execute(array($readAccount["id"], $loginToken, getIP(), createDuration(((isset($_POST["rememberMe"])) ? 365 : 0.01666666666)), date("Y-m-d H:i:s")));

                          if ($deleteOldSessions && $insertAccountSessions){
                            $db->commit(); // işlemi tamamla
                            if (post("rememberMe")) {
                              createCookie("rememberMe", $loginToken, 365, $sslStatus);
                            }
                            $_SESSION["login"] = $loginToken;
                            if (get("redirect") && isRedirectable(get("redirect"))) {
                              go(get("redirect"));
                            }
                            else {
                              go("/profile");
                            }
                          }
                          else {
                            $db->rollBack(); // işlemi geri al
                            alertError(t__('Error!'));
                          }
                        }
                        else {
                          $loginToken = md5(uniqid(mt_rand(), true));
                          $insertAccountSessions = $db->prepare("INSERT INTO AccountSessions (accountID, loginToken, creationIP, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
                          $insertAccountSessions->execute(array($readAccount["id"], $loginToken, getIP(), createDuration(((isset($_POST["rememberMe"])) ? 365 : 0.01666666666)), date("Y-m-d H:i:s")));

                          if (post("rememberMe")) {
                            createCookie("rememberMe", $loginToken, 365, $sslStatus);
                          }
                          $_SESSION["login"] = $loginToken;
                          if (get("redirect") && isRedirectable(get("redirect"))) {
                            go(get("redirect"));
                          }
                          else {
                            go("/profile");
                          }
                        }
                      }
                    }
                  }
                }
                else {
                  echo alertError(t__('Wrong password!'));
                }
              }
              else {
                echo alertError(t__('<strong>%username%</strong> not found!', ['%username%' => post("username")]));
              }
            }
          }
        ?>
        <div class="card">
          <div class="card-header">
            <?php e__('Login') ?>
          </div>
          <div class="card-body">
            <form action="" method="post">
              <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="<?php e__('Username') ?>" value="<?php echo ((post("username")) ? post("username") : null); ?>">
              </div>
              <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="<?php e__('Password') ?>">
              </div>
              <div class="form-group custom-control custom-checkbox">
                <div class="row">
                  <div class="col">
                    <input type="checkbox" id="rememberMe" class="custom-control-input" name="rememberMe" checked>
                    <label for="rememberMe" class="custom-control-label" name="rememberMe"><?php e__('Remember me') ?></label>
                  </div>
                  <div class="col-auto">
                    <a href="/recover-account"><?php e__('Forgot Password?') ?></a>
                  </div>
                </div>
              </div>
              <?php if ($recaptchaStatus): ?>
                <div class="form-group d-flex justify-content-center">
                  <?php echo $reCAPTCHA->getHtml(); ?>
                </div>
              <?php endif; ?>
              <?php echo $csrf->input('login'); ?>
              <button type="submit" class="theme-color btn btn-primary w-100" name="login"><?php e__('Login') ?></button>
            </form>
          </div>
          <div class="card-footer text-center">
            <?php e__("Don't have an account?") ?>
            <a href="/register"><?php e__('Register') ?></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
