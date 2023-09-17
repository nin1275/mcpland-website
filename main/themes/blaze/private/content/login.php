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
<section class="container d-flex justify-content-center align-items-center pt-7 pb-4" style="flex: 1 0 auto;">
  <div class="signin-form mt-3">
    <div class="signin-form-wrapper">
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
      <div class="signin-form-card">
        <div class="view show" id="signin-view">
          <h1 class="h2 text-center"><?php e__('Login') ?></h1>
          <p class="fs-ms text-muted mb-4 text-center"><?php e__('Sign in to access your account.') ?></p>
          <form action="" method="POST" class="needs-validation" novalidate="">
            <div class="input-group mb-3">
              <i class="shi-user position-absolute top-50 start-0 translate-middle-y ms-3"></i>
              <input name="username" class="form-control rounded" type="text" placeholder="<?php e__('Username') ?>" required="" value="<?php echo ((post("username")) ? post("username") : null); ?>">
            </div>
            <div class="input-group mb-3">
              <i class="shi-lock position-absolute top-50 start-0 translate-middle-y ms-3"></i>
              <div class="password-toggle w-100">
                <input name="password" class="form-control" type="password" placeholder="<?php e__('Password') ?>" required="">
                <label class="password-toggle-btn" aria-label="Toggle Password">
                  <input class="password-toggle-check" type="checkbox">
                  <span class="password-toggle-indicator"></span>
                </label>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3 pb-1">
              <div class="form-check">
                <input name="rememberMe" class="form-check-input" type="checkbox" id="keep-signed-2">
                <label class="form-check-label" for="keep-signed-2"><?php e__('Remember me') ?></label>
              </div>
              <a class="nav-link-style fs-ms" href="/recover-account"><?php e__('Forgot Password?') ?></a>
            </div>
            <?php if ($recaptchaStatus) : ?>
              <div class="mb-3 d-flex justify-content-center">
                <?php echo $reCAPTCHA->getHtml(); ?>
              </div>
            <?php endif; ?>
            <?php echo $csrf->input('login'); ?>
            <button name="login" class="btn btn-primary d-block w-100" type="submit"><?php e__('Login') ?></button>
          </form>
        </div>
        <div class="border-top text-center mt-4 pt-4">
          <p class="fs-sm mb-0 text-center"><?php e__("Don't have an account?") ?> <a href="/register" class="fw-medium"><?php e__('Register') ?></a></p>
        </div>
      </div>
    </div>
  </div>
</section>
<a href="/" class="btn btn-light btn-icon rounded-circle position-absolute d-inline-flex" style="z-index: 9999; top: 1.5rem; left: 1.5rem;" data-bs-toggle="tooltip" data-bs-placement="right" title="<?php e__('Back to home!') ?>">
  <span class="btn-inner--icon">
    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
  </span>
</a>