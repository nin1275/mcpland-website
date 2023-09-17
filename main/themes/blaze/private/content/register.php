<?php
  if (isset($_SESSION["login"])) {
    go("/profile");
  }
  use Phelium\Component\reCAPTCHA;
  $recaptchaPagesStatusJSON = $readSettings["recaptchaPagesStatus"];
  $recaptchaPagesStatus = json_decode($recaptchaPagesStatusJSON, true);
  $recaptchaStatus = $readSettings["recaptchaPublicKey"] != '0' && $readSettings["recaptchaPrivateKey"] != '0' && $recaptchaPagesStatus["registerPage"] == 1;
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
        if (isset($_POST["insertAccounts"])) {
          if (!$csrf->validate('insertAccounts')) {
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
          else if (post("username") == null || post("email") == null || post("password") == null || post("passwordRe") == null) {
            echo alertError(t__('Please fill all the fields!'));
          }
          else {
            $usernameValid = $db->prepare("SELECT * FROM Accounts WHERE realname = ?");
            $usernameValid->execute(array(post("username")));
        
            $emailValid = $db->prepare("SELECT * FROM Accounts WHERE email = ?");
            $emailValid->execute(array(post("email")));
        
            $ipCount = $db->prepare("SELECT * FROM Accounts WHERE creationIP = ?");
            $ipCount->execute(array(getIP()));
        
            $badUsernameList = array(
              'yarrak',
              'sikis',
              'serefsiz',
              'amcik',
              'orospu'
            );
        
            if ($registerLimit != 0 && $ipCount->rowCount() >= $registerLimit) {
              echo alertError(t__('You have reached the limit of registrations!'));
            }
            else if (checkUsername(post("username"))) {
              echo alertError(t__('Username is not valid!'));
            }
            else if (strlen(post("username")) < 3) {
              echo alertError(t__('Username must be at least 3 characters long!'));
            }
            else if (strlen(post("username")) > 16) {
              echo alertError(t__('Username must be less than 16 characters!'));
            }
            else if (checkEmail(post("email"))) {
              echo alertError(t__('Please enter a valid email address!'));
            }
            else if ($usernameValid->rowCount() > 0) {
              echo alertError(t__('<strong>%username%</strong> already registered!', ['%username%' => post("username")]));
            }
            else if ($emailValid->rowCount() > 0) {
              echo alertError(t__('<strong>%email%</strong> already registered!', ['%email%' => post("email")]));
            }
            else if (strlen(post("password")) < 4) {
              echo alertError(t__('Password must be at least 4 characters long!'));
            }
            else if (post("password") != post("passwordRe")) {
              echo alertError(t__('Passwords do not match!'));
            }
            else if (checkBadPassword(post("password"))) {
              echo alertError(t__('Your password is too weak!'));
            }
            else if (checkBadUsername(post("username"), $badUsernameList)) {
              echo alertError(t__('Bad username detected!'));
            }
            else {
              $loginToken = md5(uniqid(mt_rand(), true));
              $password = createPassword($readSettings["passwordType"], post("password"));
  
              $insertAccounts = $db->prepare("INSERT INTO Accounts (username, realname, email, password, creationIP, creationDate) VALUES (?, ?, ?, ?, ?, ?)");
              $insertAccounts->execute(array(strtolower(post("username")), post("username"), post("email"), $password, getIP(), date("Y-m-d H:i:s")));
              $accountID = $db->lastInsertId();
              $insertAccountSessions = $db->prepare("INSERT INTO AccountSessions (accountID, loginToken, creationIP, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
              $insertAccountSessions->execute(array($accountID, $loginToken, getIP(), createDuration(0.01666666666), date("Y-m-d H:i:s")));
              $_SESSION["login"] = $loginToken;
              go("/profile");
            }
          }
        }
      ?>
      <div class="signin-form-card">
        <div class="view show" id="signin-view">
          <h1 class="h2 text-center"><?php e__('Register') ?></h1>
          <p class="fs-ms text-muted mb-4 text-center"><?php e__('Sign up to join our server.') ?></p>
          <form action="" method="POST" class="needs-validation" novalidate="">
            <div class="input-group mb-3">
              <i class="shi-user position-absolute top-50 start-0 translate-middle-y ms-3"></i>
              <input name="username" class="form-control rounded" type="text" placeholder="<?php e__('Username') ?>" required="" value="<?php echo ((post("username")) ? post("username") : null); ?>">
            </div>
            <div class="input-group mb-3">
              <i class="shi-mail position-absolute top-50 start-0 translate-middle-y ms-3"></i>
              <input name="email" class="form-control rounded" type="email" placeholder="<?php e__('Email') ?>" required="" value="<?php echo ((post("email")) ? post("email") : null); ?>">
            </div>
            <div class="input-group mb-3">
              <i class="shi-lock position-absolute top-50 start-0 translate-middle-y ms-3"></i>
              <div class="password-toggle w-100">
                <input name="password" class="form-control" type="password" placeholder="<?php e__('Password') ?>" required="">
                <label class="password-toggle-btn" aria-label="Toggle Password">
                  <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                </label>
              </div>
            </div>
            <div class="input-group mb-3">
              <i class="shi-lock position-absolute top-50 start-0 translate-middle-y ms-3"></i>
              <div class="password-toggle w-100">
                <input name="passwordRe" class="form-control" type="password" placeholder="<?php e__('Confirm Password') ?>" required="">
                <label class="password-toggle-btn" aria-label="Toggle Password">
                  <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                </label>
              </div>
            </div>
            <?php if ($recaptchaStatus): ?>
              <div class="mb-3 d-flex justify-content-center">
                <?php echo $reCAPTCHA->getHtml(); ?>
              </div>
            <?php endif; ?>
            <?php echo $csrf->input('insertAccounts'); ?>
            <button name="insertAccounts" class="btn btn-primary d-block w-100" type="submit"><?php e__('Register') ?></button>
          
          </form>
        </div>
        <div class="border-top text-center mt-4 pt-4">
          <p class="fs-sm text-center"><?php e__('Do you have an account?') ?> <a href="/login" class="fw-medium"><?php e__('Login') ?></a></p>
          <p class="text-muted small mb-0"><?php e__('I read the <a href="/rules" rel="external">Rules</a> and I accept.') ?></p>
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