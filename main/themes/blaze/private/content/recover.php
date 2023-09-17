<?php
  if (isset($_SESSION["login"])) {
    go("/profile");
  }
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;
  use PHPMailer\PHPMailer\Exception;
  use Phelium\Component\reCAPTCHA;
  $recaptchaPagesStatusJSON = $readSettings["recaptchaPagesStatus"];
  $recaptchaPagesStatus = json_decode($recaptchaPagesStatusJSON, true);
  $recaptchaStatus = $readSettings["recaptchaPublicKey"] != '0' && $readSettings["recaptchaPrivateKey"] != '0' && $recaptchaPagesStatus["recoverPage"] == 1;
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
      <?php if (get("id") && get("token")): ?>
        <?php
        require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
        $csrf = new CSRF('csrf-sessions', 'csrf-token');
        $checkToken = $db->prepare("SELECT * FROM AccountRecovers WHERE accountID = ? AND recoverToken = ? AND creationIP = ? AND expiryDate > ?");
        $checkToken->execute(array(get("id"), get("token"), getIP(), date("Y-m-d H:i:s")));
        ?>
        <?php if ($checkToken->rowCount() > 0): ?>
          <?php
          if (isset($_POST["recoverAccount"])) {
            if (!$csrf->validate('recoverAccount')) {
              echo alertError(t__('Something went wrong! Please try again later.'));
            }
            else if (post("password") == null || post("passwordRe") == null) {
              echo alertError(t__('Please fill all the fields!'));
            }
            else if (post("password") != post("passwordRe")) {
              echo alertError(t__('Passwords do not match!'));
            }
            else if (strlen(post("password")) < 4) {
              echo alertError(t__('Password must be at least 4 characters long!'));
            }
            else if (checkBadPassword(post("password"))) {
              echo alertError(t__('Your password is too weak!'));
            }
            else {
              $password = createPassword($readSettings["passwordType"], post("password"));
  
              $updateAccounts = $db->prepare("UPDATE Accounts SET password = ? WHERE id = ?");
              $updateAccounts->execute(array($password, get("id")));
              $deleteAccountRecovers = $db->prepare("DELETE FROM AccountRecovers WHERE accountID = ?");
              $deleteAccountRecovers->execute(array(get("id")));
              $deleteAccountSessions = $db->prepare("DELETE FROM AccountSessions WHERE accountID = ?");
              $deleteAccountSessions->execute(array(get("id")));
              echo alertSuccess(t__('Your password has been changed successfully! You are redirected...'));
              echo goDelay("/login", 2);
            }
          }
          ?>
          <div class="signin-form-card">
            <div class="view show" id="signin-view">
              <h1 class="h2 text-center"><?php e__('Change Password') ?></h1>
              <p class="fs-ms text-muted mb-4 text-center"><?php e__('Change your password to access your account.') ?></p>
              <form action="" method="POST" class="needs-validation" novalidate="">
                <div class="input-group mb-3">
                  <i class="shi-lock position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                  <div class="password-toggle w-100">
                    <input name="password" class="form-control" type="password" placeholder="<?php e__('New Password') ?>" required="">
                    <label class="password-toggle-btn" aria-label="Toggle Password">
                      <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                    </label>
                  </div>
                </div>
                <div class="input-group mb-3">
                  <i class="shi-lock position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                  <div class="password-toggle w-100">
                    <input name="passwordRe" class="form-control" type="password" placeholder="<?php e__('Confirm New Password') ?>" required="">
                    <label class="password-toggle-btn" aria-label="Toggle Password">
                      <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                    </label>
                  </div>
                </div>
                <?php echo $csrf->input('recoverAccount'); ?>
                <button class="btn btn-primary d-block w-100" type="submit" name="recoverAccount"><?php e__('Change Password') ?></button>
              </form>
            </div>
          </div>
        <?php else: ?>
          <?php echo alertError(t__('Password reset link is invalid!')); ?>
        <?php endif; ?>
      <?php else: ?>
        <?php
        require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
        $csrf = new CSRF('csrf-sessions', 'csrf-token');
        if (isset($_POST["sendEmail"])) {
          if (!$csrf->validate('sendEmail')) {
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
          else if (post("username") == null || post("email") == null) {
            echo alertError(t__('Please fill all the fields!'));
          }
          else {
            $checkAccount = $db->prepare("SELECT * FROM Accounts WHERE realname = ? AND email = ?");
            $checkAccount->execute(array(post("username"), post("email")));
            $readAccount = $checkAccount->fetch();
            if ($checkAccount->rowCount() > 0) {
              $recoverToken = md5(uniqid(mt_rand(), true));
              $url = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http").'://'.$_SERVER["SERVER_NAME"].'/recover-account/'.$readAccount["id"].'/'.$recoverToken);
              $search = array("%username%", "%url%");
              $replace = array($readAccount["realname"], $url);
              $template = $readSettings["smtpPasswordTemplate"];
              $content = str_replace($search, $replace, $template);
              require_once(__ROOT__."/apps/main/private/packages/class/phpmailer/exception.php");
              require_once(__ROOT__."/apps/main/private/packages/class/phpmailer/phpmailer.php");
              require_once(__ROOT__."/apps/main/private/packages/class/phpmailer/smtp.php");
              $phpMailer = new PHPMailer(true);
              try {
                $phpMailer->IsSMTP();
                $phpMailer->setLanguage('tr', __ROOT__.'/apps/main/private/packages/class/phpmailer/lang/');
                $phpMailer->SMTPAuth = true;
                $phpMailer->Host = $readSettings["smtpServer"];
                $phpMailer->Port = $readSettings["smtpPort"];
                $phpMailer->SMTPSecure = (($readSettings["smtpSecure"] == 1) ? PHPMailer::ENCRYPTION_SMTPS : (($readSettings["smtpSecure"] == 2) ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS));
                $phpMailer->Username = $readSettings["smtpUsername"];
                $phpMailer->Password = $readSettings["smtpPassword"];
                $phpMailer->SetFrom($phpMailer->Username, $readSettings["serverName"]);
                $phpMailer->AddAddress($readAccount["email"], $readAccount["realname"]);
                $phpMailer->isHTML(true);
                $phpMailer->CharSet = 'UTF-8';
                $phpMailer->Subject = $readSettings["serverName"]." - ".t__('Reset your password!');
                $phpMailer->Body = $content;
                $phpMailer->send();
                $checkAccountRecovers = $db->prepare("SELECT * FROM AccountRecovers WHERE accountID = ?");
                $checkAccountRecovers->execute(array($readAccount["id"]));
                if ($checkAccountRecovers->rowCount() > 0) {
                  $updateAccountRecovers = $db->prepare("UPDATE AccountRecovers SET recoverToken = ?, creationIP = ?, expiryDate = ?, creationDate = ? WHERE accountID = ?");
                  $updateAccountRecovers->execute(array($recoverToken, getIP(), createDuration(0.04166666666), date("Y-m-d H:i:s"), $readAccount["id"]));
                }
                else {
                  $insertAccountRecovers = $db->prepare("INSERT INTO AccountRecovers (accountID, recoverToken, creationIP, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
                  $insertAccountRecovers->execute(array($readAccount["id"], $recoverToken, getIP(), createDuration(0.04166666666), date("Y-m-d H:i:s")));
                }
                echo alertSuccess(t__('A reset link has been sent to your email address!'));
              } catch (Exception $e) {
                echo alertError(t__('Could not send mail due to a system error:')." ".$e->errorMessage());
              }
            }
            else {
              echo alertError(t__('Username and email address do not match!'));
            }
          }
        }
        ?>
        <div class="signin-form-card">
          <div class="view show" id="signin-view">
            <h1 class="h2 text-center"><?php e__('Recover Account') ?></h1>
            <p class="fs-ms text-muted mb-4 text-center"><?php e__('Submit a reset request to access your account.') ?></p>
            <form action="" method="POST" class="needs-validation" novalidate="">
              <div class="input-group mb-3">
                <i class="shi-user position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                <input name="username" class="form-control rounded" type="text" placeholder="<?php e__('Username') ?>" required="">
              </div>
              <div class="input-group mb-3">
                <i class="shi-mail position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                <input name="email" class="form-control rounded" type="email" placeholder="<?php e__('Email') ?>" required="">
              </div>
              <?php if ($recaptchaStatus): ?>
                <div class="mb-3 d-flex justify-content-center">
                  <?php echo $reCAPTCHA->getHtml(); ?>
                </div>
              <?php endif; ?>
              <?php echo $csrf->input('sendEmail'); ?>
              <button class="btn btn-primary d-block w-100" type="submit" name="sendEmail"><?php e__('Send') ?></button>
            </form>
          </div>
          <div class="border-top text-center mt-4 pt-4">
            <p class="fs-sm mb-0 text-center"><?php e__('Remember your password?') ?> <a href="/login" class="fw-medium"><?php e__('Login') ?></a></p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<a href="/" class="btn btn-light btn-icon rounded-circle position-absolute d-inline-flex" style="z-index: 9999; top: 1.5rem; left: 1.5rem;" data-bs-toggle="tooltip" data-bs-placement="right" title="<?php e__('Back to home!') ?>">
  <span class="btn-inner--icon">
    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
  </span>
</a>
