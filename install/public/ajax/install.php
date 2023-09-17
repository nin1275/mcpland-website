<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/status.php");
  require_once(__ROOT__."/apps/install/private/config/functions.php");

  if (INSTALL_STATUS == true) {
    die("Already installed!");
  }

  if (get("step") == 0) {
    if ((sqlPost("mysqlServer") == null) || (sqlPost("mysqlPort") == null) || (sqlPost("mysqlUsername") == null) || (sqlPost("mysqlPassword") == null) || (sqlPost("mysqlDatabase") == null)) {
      die("Please do not leave any blank spaces!");
    }
    else {
      $mysqlServer   = sqlPost("mysqlServer");
      $mysqlPort     = sqlPost("mysqlPort");
      $mysqlUsername = sqlPost("mysqlUsername");
      $mysqlPassword = sqlPost("mysqlPassword");
      $mysqlDatabase = sqlPost("mysqlDatabase");

      try {
        $db = new PDO("mysql:host=$mysqlServer;port=$mysqlPort;dbname=$mysqlDatabase;charset=utf8", $mysqlUsername, $mysqlPassword);
      } catch (PDOException $e) {
        die('<strong>MySQL connection error: </strong>'.utf8_encode($e->getMessage()));
      }
      die(true);
    }
  }
  else if (get("step") == 1) {
    if ((post("siteSlogan") == null) || (post("siteServerName") == null) || (post("siteServerIP") == null) || (post("siteServerVersion") == null) || (post("sitePasswordType") == null) || (post("siteMaintenance") == null)) {
      die("Please do not leave any blank spaces!");
    }
    else {
      die(true);
    }
  }
  else if (get("step") == 2) {
    $mysqlServer   = sqlPost("mysqlServer");
    $mysqlPort     = sqlPost("mysqlPort");
    $mysqlUsername = sqlPost("mysqlUsername");
    $mysqlPassword = sqlPost("mysqlPassword");
    $mysqlDatabase = sqlPost("mysqlDatabase");

    try {
      $db = new PDO("mysql:host=$mysqlServer;port=$mysqlPort;dbname=$mysqlDatabase;charset=utf8", $mysqlUsername, $mysqlPassword);
    }
    catch (PDOException $e) {
      die('<strong>MySQL connection error: </strong>'.utf8_encode($e->getMessage()));
    }

    if ((post("accountUsername") == null) || (post("accountEmail") == null) || (post("accountPassword") == null) || (post("accountPasswordRe") == null)) {
      die("Please do not leave any blank spaces!");
    }
    else {
      $usernameValid = $db->prepare("SELECT * FROM Accounts WHERE realname = ?");
      $usernameValid->execute(array(post("accountUsername")));

      $emailValid = $db->prepare("SELECT * FROM Accounts WHERE email = ?");
      $emailValid->execute(array(post("accountEmail")));

      if (checkUsername(post("accountUsername"))) {
        die("Invalid username!");
      }
      else if ($usernameValid->rowCount() > 0) {
        die('<strong>'.post("accountUsername").'</strong> already registered!');
      }
      else if ($emailValid->rowCount() > 0) {
        die('<strong>'.post("accountEmail").'</strong> already registered!');
      }
      else if (post("accountPassword") != post("accountPasswordRe")) {
        die("Passwords do not match!");
      }
      else if (checkBadPassword(post("accountPassword"))) {
        die("You cannot use simple passwords!");
      }
      else if (strlen(post("accountUsername")) < 3) {
        die("Username cannot be less than 3 characters!");
      }
      else if (strlen(post("accountUsername")) > 16) {
        die("Username cannot exceed 16 characters!");
      }
      else if (strlen(post("accountPassword")) < 4) {
        die("Password cannot be less than 4 characters!");
      }
      else {
        $dbConnectionForCreate = new PDO("mysql:host=$mysqlServer;port=$mysqlPort;dbname=$mysqlDatabase;charset=utf8", $mysqlUsername, $mysqlPassword);
        $dbFileContents = file_get_contents(__ROOT__."/apps/install/private/sql/database.sql");
        $dbCreate = $dbConnectionForCreate->exec($dbFileContents);
        $dbConnectionForCreate=null;

        $connectFile = (__ROOT__."/apps/main/private/config/connect.php");
        $connectFileData =
'<?php
  define(\'DB_HOST\', \''.$mysqlServer.'\');
  define(\'DB_PORT\', \''.$mysqlPort.'\');
  define(\'DB_USERNAME\', \''.$mysqlUsername.'\');
  define(\'DB_PASSWORD\', \''.phpFileEscape($mysqlPassword).'\');
  define(\'DB_NAME\', \''.$mysqlDatabase.'\');

  try {
    $db = new PDO("mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME.";charset=utf8", DB_USERNAME, DB_PASSWORD);
  }
  catch (PDOException $e) {
    die("<strong>MySQL connection error:</strong> ".utf8_encode($e->getMessage()));
  }
?>';
        $updateConnectFile = file_put_contents($connectFile, $connectFileData) OR die("File error! Please contact LeaderOS. (connect.php)");

        $statusFile = (__ROOT__."/apps/main/private/config/status.php");
        $statusFileData =
'<?php
  define(\'INSTALL_STATUS\', true);
  define(\'VERSION\', \''.VERSION.'\');
  define(\'BUILD_NUMBER\', '.BUILD_NUMBER.');
?>';
        $updateStatusFile = file_put_contents($statusFile, $statusFileData) OR die("File error! Please contact LeaderOS. (status.php)");

        $appFile = (__ROOT__."/apps/main/private/config/app.php");
        $appFileData =
'<?php
  define(\'APP_KEY\', \''.md5(uniqid(mt_rand(), true)).'\');
?>';
        $updateAppFile = file_put_contents($appFile, $appFileData) OR die("File error! Please contact LeaderOS. (app.php)");

        $rules = 'You can change this text from the dashboard.';

        $supportMessageTemplate = '
          <p>Dear <strong><span class="text-primary">%username%</span></strong>,</p>
          <p>%message%</p><p class="mb-1"><strong>%servername% Support Team</strong></p>
          <p class="mb-1"><strong><span class="text-primary">Server IP:</span></strong> %serverip%</p>
          <p class="mb-1"><strong><span class="text-primary">Server Version:</span></strong> %serverversion%</p>
        ';

        $smtpPasswordTemplate = '
          <div style="padding: 3rem 1rem;  color: #333333 !important;  font-family: \'Roboto\', sans-serif !important;  background-color: #ffffff !important;">
            <div style="max-width: 640px !important; margin: 0 auto !important; text-align: center !important; background-color: #ffffff !important; font-size: 1rem !important; line-height: 1.5 !important; border: 1px solid #cccccc !important; border-top: 5px solid #2dce89 !important; border-radius: .5rem !important;">
              <div style="padding: 1.25rem !important;">
                <p style="margin-top: 0 !important; margin-bottom: 1rem !important;">Dear <strong>%username%</strong>,</p>
                <p style="margin-top: 0 !important; margin-bottom: 1rem !important;">We have received your password reset request, you can change your password using the link below.</p>
                <div style="margin: 1.25rem 0 !important;">
                  <a href="%url%" target="_blank" style="color: #ffffff !important; background-color: #2dce89 !important; padding: .5rem 1.5rem !important; margin-bottom: 1rem !important; text-decoration: none !important; text-align: center !important; vertical-align: middle !important; border-radius: 2rem !important; transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out !important">Change Password</a>
                </div>
                <p style="margin-top: 0 !important; margin-bottom: 1rem !important; color: #6c757d !important;">Didn\'t it work? You can use the link below.</p>
                <a href="%url%" target="_blank" style="color: #f5365c !important; text-decoration: none !important;">%url%</a>
              </div>
            </div>
          </div>
        ';
        $smtpTFATemplate = '
          <div style="padding: 3rem 1rem;  color: #333333 !important;  font-family: \'Roboto\', sans-serif !important;  background-color: #ffffff !important;">
            <a href="%url%" target="_blank" style="display: block !important; margin-bottom: 2rem !important; text-align: center !important;">
              <img src="https://i.ibb.co/HC4YrxZ/leaderos.png" style="width: 250px !important; height: auto !important;">
            </a>
            <div style="max-width: 640px !important; margin: 0 auto !important; text-align: center !important; background-color: #ffffff !important; font-size: 1rem !important; line-height: 1.5 !important; border: 1px solid #cccccc !important; border-top: 5px solid #2dce89 !important; border-radius: .5rem !important;">
              <div style="padding: 1.25rem !important;">
                <p style="margin-top: 0 !important; margin-bottom: 1rem !important;">Dear <strong>%username%</strong>,</p>
                <p style="margin-top: 0 !important; margin-bottom: 1rem !important;">We received your request to reset two-step verification, you can reset two-step verification using the link below.</p>
                <div style="margin: 1.25rem 0 !important;">
                  <a href="%url%" target="_blank" style="color: #ffffff !important; background-color: #2dce89 !important; padding: .5rem 1.5rem !important; margin-bottom: 1rem !important; text-decoration: none !important; text-align: center !important; vertical-align: middle !important; border-radius: 2rem !important; transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out !important">Reset</a>
                </div>
                <p style="margin-top: 0 !important; margin-bottom: 1rem !important; color: #6c757d !important;"> 0 !important; margin-bottom: 1rem !important; color: #6c757d !important;">Didn\'t it work? You can use the link below.</p>
                <a href="%url%" target="_blank" style="color: #f5365c !important; text-decoration: none !important;">%url%</a>
              </div>
            </div>
          </div>
        ';

        $recaptchaPagesStatusArray = array(
          "loginPage"     => 0,
          "registerPage"  => 0,
          "recoverPage"   => 0,
          "tfaPage"       => 0,
          "newsPage"      => 0,
          "supportPage"   => 0
        );
        $recaptchaPagesStatusJSON = json_encode($recaptchaPagesStatusArray);

        $webhookMessage = "@everyone";

        $webhookCreditEmbed   = '**%username%** has bought **%credit% credits** ($%money%)';
        $webhookStoreEmbed    = "**%username%** has bought **%product%** from the **%server%**";;
        $webhookSupportEmbed  = "**%username%** has sent a ticket message. \n%panelurl%";
        $webhookNewsEmbed     = "**%username%** has been commented on a blog post. \n%posturl%\n%panelurl%";
        $webhookLotteryEmbed  = "**%username%** has won **%award%** from the **%lottery%** wheel of fortune";
        $webhookApplicationEmbed  = "**%username%** has applied for **%form%** \n%panelurl%";
        
        $creditIcon = ' <i class="credit-icon"></i>';

        $insertSettings = $db->prepare("INSERT INTO Settings (siteSlogan, serverName, serverIP, serverVersion, passwordType, maintenanceStatus, rules, recaptchaPagesStatus, supportMessageTemplate, smtpPasswordTemplate, smtpTFATemplate, storeDiscountProducts, webhookCreditMessage, webhookStoreMessage, webhookSupportMessage, webhookNewsMessage, webhookLotteryMessage, webhookApplicationMessage, webhookCreditEmbed, webhookStoreEmbed, webhookSupportEmbed, webhookNewsEmbed, webhookLotteryEmbed, webhookApplicationEmbed, creditIcon) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insertSettings->execute(array(post("siteSlogan"), post("siteServerName"), post("siteServerIP"), post("siteServerVersion"), post("sitePasswordType"), post("siteMaintenance"), $rules, $recaptchaPagesStatusJSON, $supportMessageTemplate, $smtpPasswordTemplate, $smtpTFATemplate, "0", $webhookMessage, $webhookMessage, $webhookMessage, $webhookMessage, $webhookMessage, $webhookMessage, $webhookCreditEmbed, $webhookStoreEmbed, $webhookSupportEmbed, $webhookNewsEmbed, $webhookLotteryEmbed, $webhookApplicationEmbed, $creditIcon));

        $headerArray = array(
          array(
            "id"        => md5(time()+1),
            "title"     => "Home",
            "icon"      => "fa fas fa-home",
            "url"       => "/",
            "tabstatus" => 0,
            "pagetype"  => "home"
          ),
          array(
            "id"        => md5(time()+2),
            "title"     => "Store",
            "icon"      => "fa fas fa-shopping-cart",
            "url"       => "/store",
            "tabstatus" => 0,
            "pagetype"  => "store"
          ),
          array(
            "id"        => md5(time()+3),
            "title"     => "Buy Credits",
            "icon"      => "fa fas fa-coins",
            "url"       => "/credit/buy",
            "tabstatus" => 0,
            "pagetype"  => "credit"
          ),
          array(
            "id"        => md5(time()+4),
            "title"     => "Leaderboards",
            "icon"      => "fa fas fa-trophy",
            "url"       => "/leaderboards",
            "tabstatus" => 0,
            "pagetype"  => "leaderboards"
          ),
          array(
            "id"        => md5(time()+5),
            "title"     => "Support",
            "icon"      => "fa fas fa-life-ring",
            "url"       => "/support",
            "tabstatus" => 0,
            "pagetype"  => "support"
          ),
          array(
            "id"        => md5(time()+6),
            "title"     => "Download",
            "icon"      => "fa fas fa-download",
            "url"       => "/download",
            "tabstatus" => 0,
            "pagetype"  => "download"
          ),
        );
        $headerJSON = json_encode($headerArray);
        
        $insertTheme = $db->prepare("INSERT INTO Theme (header) VALUES (?)");
        $insertTheme->execute(array($headerJSON));

        $paymentSettingsArray = array(
          "batihost"  => array(
            "batihostID"    => null,
            "batihostEmail" => null,
            "batihostToken" => null
          ),
          "paywant"   => array(
            "paywantAPIKey"         => null,
            "paywantAPISecretKey"   => null,
            "paywantCommissionType" => '1'
          ),
          "rabisu"    => array(
            "rabisuID"    => null,
            "rabisuToken" => null
          ),
          "shopier"   => array(
            "shopierAPIKey"         => null,
            "shopierAPISecretKey"   => null
          ),
          "keyubu"    => array(
            "keyubuID"    => null,
            "keyubuToken" => null
          ),
          "ininal"    => array(
            "ininalBarcodes" => array()
          ),
          "papara"    => array(
            "paparaNumbers" => array()
          ),
          "shipy"     => array(
            "shipyAPIKey" => null
          ),
          "eft"       => array(
            "bankAccounts" => array()
          ),
          "slimmweb"  => array(
            "slimmwebPaymentID" => null,
            "slimmwebToken"     => null
          ),
          "paytr"     => array(
            "paytrID"             => null,
            "paytrAPIKey"         => null,
            "paytrAPISecretKey"   => null
          ),
          "paypal"     => array(
            "paypalEmail"       => null,
            "paypalSandbox"   => null
          ),
          "stripe"     => array(
            "stripeAPIKey"         => null,
            "stripeAPISecretKey"   => null,
            'stripeWebhookKey'     => null
          ),
          "paymax"   => array(
            "paymaxUser"      => null,
            "paymaxKey"       => null,
            "paymaxStoreCode" => null,
            "paymaxHash"      => null
          ),
          "weepay"   => array(
            "weepayID"           => null,
            "weepayAPIKey"       => null,
            "weepayAPISecretKey" => null
          )
        );
        $deletePaymentSettings = $db->query("TRUNCATE TABLE PaymentSettings");
        $insertPaymentSettings = $db->prepare("INSERT INTO PaymentSettings (name, slug, variables) VALUES (?, ?, ?)");
        $insertPaymentSettings->execute(array("Batihost", "batihost", json_encode($paymentSettingsArray["batihost"])));
        $insertPaymentSettings->execute(array("Paywant", "paywant", json_encode($paymentSettingsArray["paywant"])));
        $insertPaymentSettings->execute(array("Rabisu", "rabisu", json_encode($paymentSettingsArray["rabisu"])));
        $insertPaymentSettings->execute(array("Shopier", "shopier", json_encode($paymentSettingsArray["shopier"])));
        $insertPaymentSettings->execute(array("Keyubu", "keyubu", json_encode($paymentSettingsArray["keyubu"])));
        $insertPaymentSettings->execute(array("Ininal", "ininal", json_encode($paymentSettingsArray["ininal"])));
        $insertPaymentSettings->execute(array("Papara", "papara", json_encode($paymentSettingsArray["papara"])));
        $insertPaymentSettings->execute(array("Shipy", "shipy", json_encode($paymentSettingsArray["shipy"])));
        $insertPaymentSettings->execute(array("EFT (IBAN)", "eft", json_encode($paymentSettingsArray["eft"])));
        $insertPaymentSettings->execute(array("SlimmWeb", "slimmweb", json_encode($paymentSettingsArray["slimmweb"])));
        $insertPaymentSettings->execute(array("PayTR", "paytr", json_encode($paymentSettingsArray["paytr"])));
        $insertPaymentSettings->execute(array("PayPal", "paypal", json_encode($paymentSettingsArray["paypal"])));
        $insertPaymentSettings->execute(array("Stripe", "stripe", json_encode($paymentSettingsArray["stripe"])));
        $insertPaymentSettings->execute(array("Paymax", "paymax", json_encode($paymentSettingsArray["paymax"])));
        $insertPaymentSettings->execute(array("Weepay", "weepay", json_encode($paymentSettingsArray["weepay"])));
        
        $languages = [
          "en" => "English",
          "tr" => "Türkçe",
          "de" => "Deutsch",
          "es" => "Español",
          "fr" => "Français",
          "ru" => "Русский",
          "vn" => "Tiếng Việt",
        ];
        foreach ($languages as $key => $value) {
          $insertLanguage = $db->prepare("INSERT INTO Languages (code, name) VALUES (?, ?)");
          $insertLanguage->execute(array($key, $value));
        }

        $loginToken = md5(uniqid(mt_rand(), true));
        
        if (post("sitePasswordType") == 1)
          $password = createSHA256(post("accountPassword"));
        elseif (post("sitePasswordType") == 2)
          $password = md5(post("accountPassword"));
        else
          $password = password_hash(post("accountPassword"), PASSWORD_BCRYPT);

        $insertAccounts = $db->prepare("INSERT INTO Accounts (username, realname, email, password, creationIP, creationDate) VALUES (?, ?, ?, ?, ?, ?)");
        $insertAccounts->execute(array(strtolower(post("accountUsername")), post("accountUsername"), post("accountEmail"), $password, getIP(), date("Y-m-d H:i:s")));
        $accountID = $db->lastInsertId();
        $insertAccountSessions = $db->prepare("INSERT INTO AccountSessions (accountID, loginToken, creationIP, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
        $insertAccountSessions->execute(array($accountID, $loginToken, getIP(), createDuration(0.01666666666), date("Y-m-d H:i:s")));
        $insertAccountNoticationInfo = $db->prepare("INSERT INTO AccountNoticationInfo (accountID, lastReadDate) VALUES (?, ?)");
        $insertAccountNoticationInfo->execute(array($accountID, date("Y-m-d H:i:s")));
  
        $seoPages = [
          [
            'page' => '404',
            'title' => '%serverName% - 404',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'application',
            'title' => '%serverName% - Applications',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'bazaar',
            'title' => '%serverName% - Bazaar',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'checkout',
            'title' => '%serverName% - Checkout',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'chest',
            'title' => '%serverName% - Chest',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'credit',
            'title' => '%serverName% - Credit',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'download',
            'title' => '%serverName% - Download',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'games',
            'title' => '%serverName% - Games',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'gift',
            'title' => '%serverName% - Gift',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'help',
            'title' => '%serverName% - Help Center',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'home',
            'title' => '%serverName% - %title%',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'leaderboards',
            'title' => '%serverName% - Leaderboards',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'login',
            'title' => '%serverName% - Login',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'lottery',
            'title' => '%serverName% - Wheel of Fortune',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'maintenance',
            'title' => '%serverName% - Maintenance',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'manage-bazaar',
            'title' => '%serverName% - Manage Bazaar',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'news',
            'title' => '%serverName% - Blog',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'page',
            'title' => '%serverName% - Page',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'player',
            'title' => '%serverName% - Player',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'profile',
            'title' => '%serverName% - Profile',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'recover',
            'title' => '%serverName% - Recover Account',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'register',
            'title' => '%serverName% - Register',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'rules',
            'title' => '%serverName% - Rules',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'store',
            'title' => '%serverName% - Store',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'support',
            'title' => '%serverName% - Support',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'tfa',
            'title' => '%serverName% - TFA',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'tfa-recover',
            'title' => '%serverName% - Recover TFA',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'gaming-night',
            'title' => '%serverName% - Gaming Night',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'forum',
            'title' => '%serverName% - Forum',
            'description' => null,
            'image' => null,
          ],
        ];
        $insertSeoPages = $db->prepare("INSERT INTO SeoPages (page, title, description, image) VALUES (?, ?, ?, ?)");
        foreach ($seoPages as $seoPage) {
          $insertSeoPages->execute(array($seoPage['page'], $seoPage['title'], $seoPage['description'], $seoPage['image']));
        }
  
        $modules = [
          'Forum' => "forum",
          'Bazaar' => "bazaar",
          'Store' => "store",
          'Blog' => "news",
          'Support' => "support",
          'Help Center' => "help",
          'Leaderboards' => "leaderboards",
          'Chest' => "chest",
          'Credit' => "credit",
          'Download' => "download",
          'Custom Forms' => "form",
          'Games' => "games",
          'Gaming Night' => "gaming-night",
          'Gift' => "gift",
          'Wheel of Fortune' => "lottery",
          'Custom Pages' => "page",
          'Player' => "player",
          'Applications' => "application",
          'Two Factor Auth' => "tfa",
        ];
        $addModule = $db->prepare("INSERT INTO Modules (name, slug, settings, isEnabled) VALUES (?, ?, ?, ?)");
        foreach ($modules as $module_key => $module_value) {
          $addModule->execute(array($module_key, $module_value, '{}', '1'));
        }
  
        $permissions = [
          'SUPER_ADMIN' => "Super Admin",
          'VIEW_DASHBOARD' => "View Dashboard",
          'MANAGE_ACCOUNTS' => "Manage Accounts",
          'MANAGE_APPLICATIONS' => "Manage Applications",
          'MANAGE_BANS' => "Managa Bans",
          'MANAGE_BAZAAR' => "Manage Bazaar",
          'MANAGE_BROADCAST' => "Manage Broadcast",
          'MANAGE_DOWNLOADS' => "Manage Downloads",
          'MANAGE_GAMES' => "Manage Games",
          'MANAGE_GIFTS' => "Manage Gifts",
          'MANAGE_HELP_CENTER' => "Manage Help Center",
          'MANAGE_LEADERBOARDS' => "Manage Leaderboards",
          'MANAGE_LOTTERY' => "Manage Wheel Of Fortune",
          'MANAGE_BLOG' => "Manage Blog",
          'MANAGE_NOTIFICATIONS' => "View Notifications",
          'MANAGE_PAGES' => "Manage Pages",
          'MANAGE_PAYMENT' => "Manage Payments",
          'MANAGE_ROLES' => "Manage Roles",
          'MANAGE_SERVERS' => "Manage Servers",
          'MANAGE_SETTINGS' => "Manage Settings",
          'MANAGE_SLIDER' => "Manage Slider",
          'MANAGE_STORE' => "Manage Store",
          'MANAGE_SUPPORT' => "Manage Support",
          'MANAGE_THEME' => "Manage Themes",
          'MANAGE_UPDATES' => "Manage Updates",
          'MANAGE_LOGS' => "Manage Logs",
          'MANAGE_GAMING_NIGHT' => "Manage Gaming Night",
          'MANAGE_FORUM' => "Manage Forum",
          'MANAGE_CUSTOM_FORMS' => "Manage Custom Forms",
          'MANAGE_MODULES' => "Manage Modules",
        ];
        $addPermission = $db->prepare("INSERT INTO Permissions (name, description) VALUES (?, ?)");
        foreach ($permissions as $key => $value) {
          $addPermission->execute(array($key, $value));
        }
        $addRole = $db->prepare("INSERT INTO Roles (id, name, slug, priority) VALUES (?, ?, ?, ?)");
        $addRole->execute(array(1, "User", "default", 0));
        $addRole->execute(array(2, "Admin", "admin", 99));
  
        $addPermissionToRole = $db->prepare("INSERT INTO RolePermissions (roleID, permissionID) VALUES (?, ?)");
        $addPermissionToRole->execute(array(2, 1));
  
        $addRoleToAccount = $db->prepare("INSERT INTO AccountRoles (accountID, roleID) VALUES (?, ?)");
        $addRoleToAccount->execute(array($accountID, 2));
        
        if (isset($_COOKIE["rememberMe"])) {
          setcookie("rememberMe", "", time()-86400*365, '/');
        }
        $_SESSION["login"] = $loginToken;
        die(true);
      }
    }
  }
  else {
    die("Unknown error!");
  }
?>
