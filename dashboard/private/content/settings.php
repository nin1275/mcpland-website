<?php
  if (!checkPerm($readAdmin, 'MANAGE_SETTINGS')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  if (get("target") == 'general' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/settings.general.js');
  }
  if (get("target") == 'system' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/settings.system.js');
  }
  if (get("target") == 'seo' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/settings.seo.js');
  }
  if (get("target") == 'smtp' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/settings.smtp.check.js');
  }
  if (get("target") == 'webhooks' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/settings.webhooks.js');
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/settings.webhooks.check.js');
  };
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
?>
<?php if (get("target") == 'general'): ?>
  <?php if (get("action") == 'update'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('General Settings') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Settings') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('General Settings') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["updateGeneralSettings"])) {
              $supportMessageTemplateCheckMessage = strpos($_POST["supportMessageTemplate"], "%message%");
              if (post("footerFacebook") == null) {
                $_POST["footerFacebook"] = '0';
              }
              if (post("footerTwitter") == null) {
                $_POST["footerTwitter"] = '0';
              }
              if (post("footerInstagram") == null) {
                $_POST["footerInstagram"] = '0';
              }
              if (post("footerYoutube") == null) {
                $_POST["footerYoutube"] = '0';
              }
              if (post("footerDiscord") == null) {
                $_POST["footerDiscord"] = '0';
              }
              if (post("footerEmail") == null) {
                $_POST["footerEmail"] = '0';
              }
              if (post("footerPhone") == null) {
                $_POST["footerPhone"] = '0';
              }
              if (post("footerWhatsapp") == null) {
                $_POST["footerWhatsapp"] = '0';
              }
              if (post("footerAboutText") == null) {
                $_POST["footerAboutText"] = '0';
              }
              if (!$csrf->validate('updateGeneralSettings')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("siteSlogan") == null || post("serverName") == null || post("serverIP") == null || post("supportMessageTemplate") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else if ($supportMessageTemplateCheckMessage == false) {
                echo alertError(t__('Please add <strong>%message%</strong> parameter to Support Message Template'));
              }
              else {
                if ($_FILES["lottery-bg"]["size"] != null) {
                  require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                  $upload = new \Verot\Upload\Upload($_FILES["lottery-bg"]);
                  if ($upload->uploaded) {
                    $upload->allowed = array("image/*");
                    $upload->file_overwrite = true;
                    $upload->file_new_name_body = "lottery-bg";
                    $upload->image_convert = "png";
                    $upload->image_resize = true;
                    $upload->image_ratio_crop = true;
                    $upload->image_x = 150;
                    $upload->image_y = 150;
                    $upload->process(__ROOT__."/apps/main/public/assets/img/extras/");
                    if (!$upload->processed) {
                      echo alertError(t__('An error occupied while uploading a wheel of fortune image: %error%', ['%error%' => $upload->error]));
                    }
                  }
                }
                if ($_FILES["favicon"]["size"] != null) {
                  require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                  $upload = new \Verot\Upload\Upload($_FILES["favicon"]);
                  if ($upload->uploaded) {
                    $upload->allowed = array("image/*");
                    $upload->file_overwrite = true;
                    $upload->file_new_name_body = "favicon";
                    $upload->image_convert = "png";
                    $upload->image_resize = true;
                    $upload->image_ratio_crop = true;
                    $upload->image_x = 64;
                    $upload->image_y = 64;
                    $upload->process(__ROOT__."/apps/main/public/assets/img/extras/");
                    if (!$upload->processed) {
                      echo alertError(t__('An error occupied while uploading a favicon: %error%', ['%error%' => $upload->error]));
                    }
                  }
                }
                if ($_FILES["logo"]["size"] != null) {
                  require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                  $upload = new \Verot\Upload\Upload($_FILES["logo"]);
                  if ($upload->uploaded) {
                    $upload->allowed = array("image/*");
                    $upload->file_overwrite = true;
                    $upload->file_new_name_body = "logo";
                    $upload->image_convert = "png";
                    $upload->process(__ROOT__."/apps/main/public/assets/img/extras/");
                    if (!$upload->processed) {
                      echo alertError(t__('An error occupied while uploading a logo: %error%', ['%error%' => $upload->error]));
                    }
                  }
                }

                $updateSettings = $db->prepare("UPDATE Settings SET siteSlogan = ?, serverName = ?, serverIP = ?, serverVersion = ?, siteTags = ?, siteDescription = ?, rules = ?, supportMessageTemplate = ?, footerFacebook = ?, footerTwitter = ?, footerInstagram = ?, footerYoutube = ?, footerDiscord = ?, footerEmail = ?, footerPhone = ?, footerWhatsapp = ?, footerAboutText = ?, headerLogoType = ?, updatedAt = ? WHERE id = ?");
                $updateSettings->execute(array(post("siteSlogan"), post("serverName"), post("serverIP"), post("serverVersion"), post("siteTags"), post("siteDescription"), filteredContent($_POST["rules"]), filteredContent($_POST["supportMessageTemplate"]), post("footerFacebook"), post("footerTwitter"), post("footerInstagram"), post("footerYoutube"), post("footerDiscord"), post("footerEmail"), post("footerPhone"), post("footerWhatsapp"), post("footerAboutText"), post("headerLogoType"), time(), $readSettings["id"]));
                echo alertSuccess(t__('Changes has been saved successfully!'));
                echo goDelay("/dashboard/settings/general", 2);
  
                createLog($readAdmin["id"], "GENERAL_SETTINGS_UPDATED");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group row">
                  <label for="inputServerName" class="col-sm-2 col-form-label"><?php e__('Server Name') ?>:*</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputServerName" class="form-control" name="serverName" placeholder="<?php e__('Enter the server name') ?>." value="<?php echo $readSettings["serverName"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputSiteSlogan" class="col-sm-2 col-form-label"><?php e__('Site Title') ?>:*</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputSiteSlogan" class="form-control" name="siteSlogan" placeholder="<?php e__('Enter the site title') ?>." value="<?php echo $readSettings["siteSlogan"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputServerIP" class="col-sm-2 col-form-label"><?php e__('Server IP') ?>:*</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputServerIP" class="form-control" name="serverIP" placeholder="<?php e__('Enter the server ip address') ?>." value="<?php echo $readSettings["serverIP"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputServerVersion" class="col-sm-2 col-form-label"><?php e__('Server Version') ?>:*</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputServerVersion" class="form-control" name="serverVersion" placeholder="<?php e__('Enter the server version') ?>." value="<?php echo $readSettings["serverVersion"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputSiteTags" class="col-sm-2 col-form-label"><?php e__('Google Tags') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTags" class="form-control" data-toggle="tagsinput" name="siteTags" placeholder="<?php e__('Enter the google tags') ?>." value="<?php echo $readSettings["siteTags"]; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaSiteDescription" class="col-sm-2 col-form-label"><?php e__('Google Description') ?>:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaSiteDescription" class="form-control" name="siteDescription" maxlength="155" placeholder="<?php e__('Enter the description of site, it will be show in google') ?>." rows="5"><?php echo $readSettings["siteDescription"]; ?></textarea>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterFacebook" class="col-sm-2 col-form-label">Facebook URL:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterFacebook" class="form-control" name="footerFacebook" placeholder="<?php e__('Enter your facebook page URL') ?>." value="<?php echo ($readSettings["footerFacebook"] != '0') ? $readSettings["footerFacebook"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterTwitter" class="col-sm-2 col-form-label">Twitter URL:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterTwitter" class="form-control" name="footerTwitter" placeholder="<?php e__('Enter your twitter page URL') ?>." value="<?php echo ($readSettings["footerTwitter"] != '0') ? $readSettings["footerTwitter"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterInstagram" class="col-sm-2 col-form-label">Instagram URL:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterInstagram" class="form-control" name="footerInstagram" placeholder="<?php e__('Enter your instagram page URL') ?>." value="<?php echo ($readSettings["footerInstagram"] != '0') ? $readSettings["footerInstagram"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterYoutube" class="col-sm-2 col-form-label">YouTube URL:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterYoutube" class="form-control" name="footerYoutube" placeholder="<?php e__('Enter your youtube page URL') ?>." value="<?php echo ($readSettings["footerYoutube"] != '0') ? $readSettings["footerYoutube"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterDiscord" class="col-sm-2 col-form-label">Discord URL:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterDiscord" class="form-control" name="footerDiscord" placeholder="<?php e__('Enter your discord servers invite URL') ?>." value="<?php echo ($readSettings["footerDiscord"] != '0') ? $readSettings["footerDiscord"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterEmail" class="col-sm-2 col-form-label"><?php e__('E-mail') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterEmail" class="form-control" name="footerEmail" placeholder="<?php e__('Enter an e-mail to contact') ?>." value="<?php echo ($readSettings["footerEmail"] != '0') ? $readSettings["footerEmail"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterPhone" class="col-sm-2 col-form-label"><?php e__('Phone Number') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterPhone" class="form-control" name="footerPhone" placeholder="<?php e__('Enter your phone number to contact') ?>." value="<?php echo ($readSettings["footerPhone"] != '0') ? $readSettings["footerPhone"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterWhatsapp" class="col-sm-2 col-form-label">WhatsApp:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterWhatsapp" class="form-control" name="footerWhatsapp" placeholder="<?php e__('Enter your whatsapp number to contact') ?>." value="<?php echo ($readSettings["footerWhatsapp"] != '0') ? $readSettings["footerWhatsapp"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaFooterAboutText" class="col-sm-2 col-form-label"><?php e__('About Us') ?>:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaFooterAboutText" class="form-control" name="footerAboutText" maxlength="255" placeholder="<?php e__('Enter the about us message you want to show in footer') ?>." rows="5"><?php echo ($readSettings["footerAboutText"] != '0') ? $readSettings["footerAboutText"] : null; ?></textarea>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaRules" class="col-sm-2 col-form-label"><?php e__('Rules') ?>:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaRules" class="form-control" data-toggle="textEditor" name="rules" placeholder="<?php e__('Enter the rules') ?>."><?php echo $readSettings["rules"] ?></textarea>
                    <small class="form-text text-muted pt-2"><strong><?php e__('Server Name') ?>:</strong> %servername%</small>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaSupportMessageTemplate" class="col-sm-2 col-form-label"><?php e__('Support Message Template') ?>:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaSupportMessageTemplate" class="form-control" data-toggle="textEditor" name="supportMessageTemplate" placeholder="<?php e__("It's a template for support messages") ?>."><?php echo $readSettings["supportMessageTemplate"] ?></textarea>
                    <small class="form-text text-muted pt-2"><strong>Note:</strong> <?php e__('%message% is required') ?></small>
                    <small class="form-text text-muted"><strong><?php e__('Message') ?>:</strong> %message%</small>
                    <small class="form-text text-muted"><strong><?php e__('Username') ?>:</strong> %username%</small>
                    <small class="form-text text-muted"><strong><?php e__('Server Name') ?>:</strong> %servername%</small>
                    <small class="form-text text-muted"><strong><?php e__('Server IP') ?>:</strong> %serverip%</small>
                    <small class="form-text text-muted"><strong><?php e__('Server Version') ?>:</strong> %serverversion%</small>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="fileLottery" class="col-sm-2 col-form-label"><?php e__('Wheel of Fortune Logo') ?>:</label>
                  <div class="col-sm-10">
                    <div data-toggle="dropimage" class="dropimage <?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/lottery-bg.png")) ? "active" : null; ?>">
                      <div class="di-thumbnail">
                        <img src="<?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/lottery-bg.png")) ? "/apps/main/public/assets/img/extras/lottery-bg.png?cache".$readSettings["updatedAt"] : null; ?>" alt="<?php e__('Preview') ?>">
                      </div>
                      <div class="di-select">
                        <label for="fileLottery"><?php e__('Select Image') ?></label>
                        <input type="file" id="fileLottery" name="lottery-bg" accept="image/*">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectHeaderLogoType" class="col-sm-2 col-form-label"><?php e__('Server Logo') ?> (Header):</label>
                  <div class="col-sm-10">
                    <select id="selectHeaderLogoType" class="form-control" name="headerLogoType" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1" <?php echo ($readSettings["headerLogoType"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Text') ?></option>
                      <option value="2" <?php echo ($readSettings["headerLogoType"] == 2) ? 'selected="selected"' : null; ?>><?php e__('Image') ?></option>
                    </select>
                  </div>
                </div>
                <div id="headerLogoOptions" style="<?php echo (($readSettings["headerLogoType"] == 1) ? "display: none;" : (($readSettings["headerLogoType"] == 2) ? "display: block;" : "display: none;")); ?>">
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div data-toggle="dropimage" class="dropimage <?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/logo.png")) ? "active" : null; ?>">
                        <div class="di-thumbnail">
                          <img src="<?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/logo.png")) ? "/apps/main/public/assets/img/extras/logo.png?cache".$readSettings["updatedAt"] : null; ?>" alt="<?php e__('Preview') ?>">
                        </div>
                        <div class="di-select">
                          <label for="fileLogo"><?php e__('Select Image') ?></label>
                          <input type="file" id="fileLogo" name="logo" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="fileFavicon" class="col-sm-2 col-form-label"><?php e__('Server Logo') ?> (Favicon):</label>
                  <div class="col-sm-10">
                    <div data-toggle="dropimage" class="dropimage <?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/favicon.png")) ? "active" : null; ?>">
                      <div class="di-thumbnail">
                        <img src="<?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/favicon.png")) ? "/apps/main/public/assets/img/extras/favicon.png?cache".$readSettings["updatedAt"] : null; ?>" alt="<?php e__('Preview') ?>">
                      </div>
                      <div class="di-select">
                        <label for="fileFavicon"><?php e__('Select Image') ?></label>
                        <input type="file" id="fileFavicon" name="favicon" accept="image/*">
                      </div>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('updateGeneralSettings'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateGeneralSettings"><?php e__('Save Changes') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'system'): ?>
  <?php if (get("action") == 'update'): ?>
    <?php
      $recaptchaPagesStatusJSON = $readSettings["recaptchaPagesStatus"];
      $recaptchaPagesStatus = json_decode($recaptchaPagesStatusJSON, true);
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('System Settings') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Settings') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('System Settings') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["updateSystemSettings"])) {
              if (post("analyticsStatus") == 0) {
                $_POST["analyticsUA"] = '0';
              }
              if (post("oneSignalStatus") == 0) {
                $_POST["oneSignalAppID"] = '0';
                $_POST["oneSignalAPIKey"] = '0';
              }
              if (post("tawktoStatus") == 0) {
                $_POST["tawktoID"] = '0';
              }
              if (post("bonusCreditStatus") == 0) {
                $_POST["bonusCredit"] = '0';
                $_POST["bonusCreditMinAmount"] = '0';
              }
              if (post("recaptchaStatus") == 0) {
                $_POST["recaptchaPublicKey"] = '0';
                $_POST["recaptchaPrivateKey"] = '0';
              }
              if (!$csrf->validate('updateSystemSettings')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("timezone") == null || post("currency") == null || post("creditMultiplier") == null || post("debugModeStatus") == null || post("avatarAPI") == null || post("onlineAPI") == null || post("passwordType") == null || post("sslStatus") == null || post("maintenanceStatus") == null || post("creditStatus") == null || post("giftStatus") == null || post("topSalesStatus") == null || post("preloaderStatus") == null || post("authStatus") == null || post("commentsStatus") == null || post("analyticsStatus") == null || post("oneSignalStatus") == null || post("tawktoStatus") == null || post("bonusCreditStatus") == null || post("recaptchaStatus") == null || post("minPay") == null || post("maxPay") == null || post("newsLimit") == null || post("registerLimit") == null || post("bazaarCommission") == null) {
                echo alertError(t__('Please fill all the fields.'));
              }
              else {
                if ($readSettings["creditMultiplier"] != post("creditMultiplier")) {
                  if ($readSettings["minPay"] == post("minPay"))
                    $_POST["minPay"] = 1 * post("creditMultiplier");
  
                  if ($readSettings["maxPay"] == post("maxPay"))
                    $_POST["maxPay"] = 100 * post("creditMultiplier");
                }
                
                if (post("updateProductPrices")) {
                  $products = $db->query("SELECT * FROM Products");
                  foreach ($products as $readProduct) {
                    $updateProduct = $db->prepare("UPDATE Products SET price = ?, discountedPrice = ? WHERE id = ?");
                    $updateProduct->execute(array($readProduct["price"] * post("creditMultiplier"), $readProduct["discountedPrice"] * post("creditMultiplier"), $readProduct["id"]));
                  }
                }
                $recaptchaPagesStatusArray = $recaptchaPagesStatus;
                $recaptchaPagesStatusArray["loginPage"] = $_POST["recaptchaPagesStatus"][0];
                $recaptchaPagesStatusArray["registerPage"] = $_POST["recaptchaPagesStatus"][1];
                $recaptchaPagesStatusArray["recoverPage"] = $_POST["recaptchaPagesStatus"][2];
                $recaptchaPagesStatusArray["newsPage"] = $_POST["recaptchaPagesStatus"][3];
                $recaptchaPagesStatusArray["supportPage"] = $_POST["recaptchaPagesStatus"][4];
                $recaptchaPagesStatusArray["tfaPage"] = $_POST["recaptchaPagesStatus"][5];
                $recaptchaPagesStatusJSON = json_encode($recaptchaPagesStatusArray);
                $updateSettings = $db->prepare("UPDATE Settings SET timezone = ?, currency = ?, creditIcon = ?, creditMultiplier = ?, debugModeStatus = ?, avatarAPI = ?, onlineAPI = ?, passwordType = ?, sslStatus = ?, maintenanceStatus = ?, creditStatus = ?, giftStatus = ?, topSalesStatus = ?, preloaderStatus = ?, authStatus = ?, commentsStatus = ?, analyticsUA = ?, oneSignalAppID = ?, oneSignalAPIKey = ?, tawktoID = ?, bonusCredit = ?, bonusCreditMinAmount = ?, recaptchaPagesStatus = ?, recaptchaPublicKey = ?, recaptchaPrivateKey = ?, minPay = ?, maxPay = ?, newsLimit = ?, registerLimit = ?, bazaarCommission = ? WHERE id = ?");
                $updateSettings->execute(array(post("timezone"), post("currency"), filteredContent($_POST["creditIcon"]), post("creditMultiplier"), post("debugModeStatus"), post("avatarAPI"), post("onlineAPI"), post("passwordType"), post("sslStatus"), post("maintenanceStatus"), post("creditStatus"), post("giftStatus"), post("topSalesStatus"), post("preloaderStatus"), post("authStatus"), post("commentsStatus"), post("analyticsUA"), post("oneSignalAppID"), post("oneSignalAPIKey"), post("tawktoID"), post("bonusCredit"), post("bonusCreditMinAmount"), $recaptchaPagesStatusJSON, post("recaptchaPublicKey"), post("recaptchaPrivateKey"), post("minPay"), post("maxPay"), post("newsLimit"), post("registerLimit"), post("bazaarCommission"), $readSettings["id"]));
                echo alertSuccess(t__('Changes has been saved successfully!'));
                echo goDelay("/dashboard/settings/system", 2);
  
                createLog($readAdmin["id"], "SYSTEM_SETTINGS_UPDATED");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="selectTimezone" class="col-sm-2 col-form-label"><?php e__('Timezone') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectTimezone" class="form-control" name="timezone" data-toggle="select">
                      <?php
                        $zoneIdentifiers = timezone_identifiers_list();
                        $zoneLocations = array();
    
                        foreach ($zoneIdentifiers as $zoneIdentifier)
                        {
                          $zone = explode('/', $zoneIdentifier);
                          $desiredRegions = array(
                            'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific'
                          );
                          if (in_array($zone[0], $desiredRegions))
                          {
                            if (isset($zone[1]) != '')
                            {
                              $area = str_replace('_', ' ', $zone[1]);
                              if (!empty($zone[2]))
                              {
                                $area = $area . ' (' . str_replace('_', ' ', $zone[2]) . ')';
                              }
                              $zoneLocations[$zone[0]][$zoneIdentifier] = $zone[0] . '/' .  $area;
                            }
                          }
                        }
    
                        foreach($zoneLocations as $zoneRegion => $regionAreas)
                        {
                          foreach($regionAreas as $regionArea => $zoneLabel)
                          {
                            $currentTimeInZone = new DateTime("now", new DateTimeZone($regionArea));
                            $currentTimeDiff = $currentTimeInZone->format('P');
                            echo '<option value="'.$regionArea.'" '.($regionArea==$readSettings["timezone"] ? "selected" : null).'>(GMT '.$currentTimeDiff.') '.$zoneLabel.'</option>';
                          }
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectCurrency" class="col-sm-2 col-form-label"><?php e__('Currency') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectCurrency" class="form-control" name="currency" data-toggle="select" data-minimum-results-for-search="-1">
                      <?php $currencies = [
                          'USD', 'EUR', 'GBP', 'TRY'
                      ]; ?>
                      <?php foreach ($currencies as $currency): ?>
                        <option value="<?php echo $currency ?>" <?php echo ($readSettings["currency"] == $currency) ? 'selected="selected"' : null; ?>><?php echo $currency ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputCreditIcon" class="col-sm-2 col-form-label"><?php e__('Credit Icon') ?></label>
                  <div class="col-sm-10">
                    <textarea id="inputCreditIcon" class="form-control" name="creditIcon" placeholder="<?php e__('You can use HTML') ?>." rows="1"><?php echo $readSettings["creditIcon"]; ?></textarea>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectSSLStatus" class="col-sm-2 col-form-label">DEBUG Mod:*</label>
                  <div class="col-sm-10">
                    <select id="selectDebugModeStatus" class="form-control" name="debugModeStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["debugModeStatus"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["debugModeStatus"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectAvatarAPI" class="col-sm-2 col-form-label">Avatar API:*</label>
                  <div class="col-sm-10">
                    <select id="selectAvatarAPI" class="form-control" name="avatarAPI" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1" <?php echo ($readSettings["avatarAPI"] == 1) ? 'selected="selected"' : null; ?>>minotar.net (<?php e__('Recommended') ?>)</option>
                      <option value="2" <?php echo ($readSettings["avatarAPI"] == 2) ? 'selected="selected"' : null; ?>>cravatar.eu</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectOnlineAPI" class="col-sm-2 col-form-label">Online API:*</label>
                  <div class="col-sm-10">
                    <select id="selectOnlineAPI" class="form-control" name="onlineAPI" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1" <?php echo ($readSettings["onlineAPI"] == 1) ? 'selected="selected"' : null; ?>>mcapi.us (<?php e__('Recommended') ?>)</option>
                      <option value="2" <?php echo ($readSettings["onlineAPI"] == 2) ? 'selected="selected"' : null; ?>>mc-api.net</option>
                      <option value="3" <?php echo ($readSettings["onlineAPI"] == 3) ? 'selected="selected"' : null; ?>>mcapi.tc</option>
                      <option value="4" <?php echo ($readSettings["onlineAPI"] == 4) ? 'selected="selected"' : null; ?>>keyubu.net</option>
                      <option value="5" <?php echo ($readSettings["onlineAPI"] == 5) ? 'selected="selected"' : null; ?>>mcsrvstat.us</option>
                      <option value="6" <?php echo ($readSettings["onlineAPI"] == 6) ? 'selected="selected"' : null; ?>>mcsrvstat.us (Pocket Edition)</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectPasswordType" class="col-sm-2 col-form-label"><?php e__('Password Hashing') ?>:*</label>
                  <div class="col-sm-10">
                    <select id="selectPasswordType" class="form-control" name="passwordType" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1" <?php echo ($readSettings["passwordType"] == 1) ? 'selected="selected"' : null; ?>>SHA256</option>
                      <option value="2" <?php echo ($readSettings["passwordType"] == 2) ? 'selected="selected"' : null; ?>>MD5</option>
                      <option value="3" <?php echo ($readSettings["passwordType"] == 3) ? 'selected="selected"' : null; ?>>Bcrypt</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectSSLStatus" class="col-sm-2 col-form-label"><?php e__('HTTPS Redirect') ?> (SSL):*</label>
                  <div class="col-sm-10">
                    <select id="selectSSLStatus" class="form-control" name="sslStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["sslStatus"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["sslStatus"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectMaintenanceStatus" class="col-sm-2 col-form-label"><?php e__('Maintenance') ?>:*</label>
                  <div class="col-sm-10">
                    <select id="selectMaintenanceStatus" class="form-control" name="maintenanceStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["maintenanceStatus"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["maintenanceStatus"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectCreditStatus" class="col-sm-2 col-form-label"><?php e__('Sending Credits One Another Players') ?>:*</label>
                  <div class="col-sm-10">
                    <select id="selectCreditStatus" class="form-control" name="creditStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["creditStatus"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["creditStatus"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectGiftStatus" class="col-sm-2 col-form-label"><?php e__('Sending Gifts One Another Players') ?>:*</label>
                  <div class="col-sm-10">
                    <select id="selectGiftStatus" class="form-control" name="giftStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["giftStatus"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["giftStatus"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectTopSalesStatus" class="col-sm-2 col-form-label"><?php e__('Best Selling Products') ?>:*</label>
                  <div class="col-sm-10">
                    <select id="selectTopSalesStatus" class="form-control" name="topSalesStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["topSalesStatus"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["topSalesStatus"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectPreloaderStatus" class="col-sm-2 col-form-label">Preloader:*</label>
                  <div class="col-sm-10">
                    <select id="selectPreloaderStatus" class="form-control" name="preloaderStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["preloaderStatus"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["preloaderStatus"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputBazaarCommission" class="col-sm-2 col-form-label"><?php e__('Bazaar Commission') ?> (%):</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputBazaarCommission" class="form-control" name="bazaarCommission" placeholder="<?php e__('Enter the percentage of commission to be taken from the seller.') ?>" value="<?php echo $readSettings["bazaarCommission"]; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectAuthStatus" class="col-sm-2 col-form-label">
                      <?php e__('2FA') ?>:*
                    <a href="https://help.leaderos.net/google-authenticator" rel="external">
                      <i class="fa fa-question-circle text-primary" data-toggle="tooltip" data-placement="top" title="<?php e__('Two-Factor Authentication') ?>"></i>
                    </a>
                  </label>
                  <div class="col-sm-10">
                    <select id="selectAuthStatus" class="form-control" name="authStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["authStatus"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["authStatus"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectCommentsStatus" class="col-sm-2 col-form-label"><?php e__('Comments') ?>:*</label>
                  <div class="col-sm-10">
                    <select id="selectCommentsStatus" class="form-control" name="commentsStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["commentsStatus"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["commentsStatus"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectOneSignalStatus" class="col-sm-2 col-form-label">One Signal (<?php e__('Notifications') ?>):*</label>
                  <div class="col-sm-10">
                    <select id="selectOneSignalStatus" class="form-control" name="oneSignalStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["oneSignalAppID"] == '0' || $readSettings["oneSignalAPIKey"] == '0') ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["oneSignalAppID"] != '0' && $readSettings["oneSignalAPIKey"] != '0') ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div id="oneSignalOptions" style="<?php echo ($readSettings["oneSignalAppID"] == '0' || $readSettings["oneSignalAPIKey"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-group row">
                    <label for="inputOneSignalAppID" class="col-sm-2 col-form-label">One Signal App ID:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputOneSignalAppID" class="form-control" name="oneSignalAppID" placeholder="<?php e__('Enter the APP ID you get from One Signal') ?>." value="<?php echo ($readSettings["oneSignalAppID"] != '0' && $readSettings["oneSignalAPIKey"] != '0') ? $readSettings["oneSignalAppID"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputOneSignalAPIKey" class="col-sm-2 col-form-label">One Signal REST API Key:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputOneSignalAPIKey" class="form-control" name="oneSignalAPIKey" placeholder="<?php e__('Enter the Rest API Key you get from One Signal') ?>." value="<?php echo ($readSettings["oneSignalAppID"] != '0' && $readSettings["oneSignalAPIKey"] != '0') ? $readSettings["oneSignalAPIKey"] : null; ?>">
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectTawktoStatus" class="col-sm-2 col-form-label">Tawk.to (<?php e__('Live Chat') ?>):*</label>
                  <div class="col-sm-10">
                    <select id="selectTawktoStatus" class="form-control" name="tawktoStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["tawktoID"] == '0') ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["tawktoID"] != '0') ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div id="tawktoOptions" style="<?php echo ($readSettings["tawktoID"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-group row">
                    <label for="inputTawktoID" class="col-sm-2 col-form-label">Tawkto Site ID:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTawktoID" class="form-control" name="tawktoID" placeholder="<?php e__('Enter the Site ID you get from tawk.to') ?>." value="<?php echo ($readSettings["tawktoID"] != '0') ? $readSettings["tawktoID"] : null; ?>">
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputCreditMultiplier" class="col-sm-2 col-form-label"><?php e__('Credit Multiplier') ?>:</label>
                  <div class="col-sm-10">
                    <input type="number" id="inputCreditMultiplier" class="form-control" name="creditMultiplier" placeholder="<?php e__('Enter the credit multiplier value.') ?>." value="<?php echo $readSettings["creditMultiplier"]; ?>">
                    <small id="helpCreditMultiplier" class="form-text text-muted"><?php e__('Example: When Credit Multiplier is 100 => 1 %currency% = 100 credits', ['%currency%' => $readSettings["currency"]]) ?></small>
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="updateProductPrices" name="updateProductPrices">
                      <label class="custom-control-label" for="updateProductPrices">
                        <?php e__('Update product prices by multiplier.') ?>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectBonusCreditStatus" class="col-sm-2 col-form-label"><?php e__('Bonus Credit') ?>:*</label>
                  <div class="col-sm-10">
                    <select id="selectBonusCreditStatus" class="form-control" name="bonusCreditStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["bonusCredit"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["bonusCredit"] != 0) ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div id="bonusCreditOptions" style="<?php echo ($readSettings["bonusCredit"] == 0) ? "display: none;" : "display: block;"; ?>">
                  <div class="form-group row">
                    <label for="inputBonusCredit" class="col-sm-2 col-form-label"><?php e__('Bonus Credit Percentage') ?> (%):</label>
                    <div class="col-sm-10">
                      <input type="number" id="inputBonusCredit" class="form-control" name="bonusCredit" placeholder="<?php e__('Enter the percentage which will be going to be given to player as extra credit') ?>." value="<?php echo ($readSettings["bonusCredit"] != 0) ? $readSettings["bonusCredit"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputBonusCreditMinAmount" class="col-sm-2 col-form-label"><?php e__('Min Payment Amount for Bonus Credit') ?>:</label>
                    <div class="col-sm-10">
                      <input type="number" id="inputBonusCreditMinAmount" class="form-control" name="bonusCreditMinAmount" placeholder="<?php e__('You can give the bonus credit for X credits or more. Type 0 for all') ?>" value="<?php echo $readSettings["bonusCreditMinAmount"] == 0 ? null : $readSettings["bonusCreditMinAmount"]; ?>">
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectAnalyticsStatus" class="col-sm-2 col-form-label">Google Analytics:*</label>
                  <div class="col-sm-10">
                    <select id="selectAnalyticsStatus" class="form-control" name="analyticsStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["analyticsUA"] == '0') ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["analyticsUA"] != '0') ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div id="analyticsOptions" style="<?php echo ($readSettings["analyticsUA"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-group row">
                    <label for="inputAnalyticsUA" class="col-sm-2 col-form-label"><?php e__('Google Analytics Identity') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputAnalyticsUA" class="form-control" name="analyticsUA" placeholder="<?php e__('Enter the Identity you get from Google Analytics. (For Exm: UA-000001)') ?>" value="<?php echo ($readSettings["analyticsUA"] != '0') ? $readSettings["analyticsUA"] : null; ?>">
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectRECAPTCHAStatus" class="col-sm-2 col-form-label">
                    Google reCAPTCHA:*
                    <a href="https://help.leaderos.net/google-recaptcha" rel="external">
                      <i class="fa fa-info-circle text-primary"></i>
                    </a>
                  </label>
                  <div class="col-sm-10">
                    <select id="selectRECAPTCHAStatus" class="form-control" name="recaptchaStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["recaptchaPublicKey"] == '0' || $readSettings["recaptchaPrivateKey"] == '0') ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["recaptchaPublicKey"] != '0' && $readSettings["recaptchaPrivateKey"] != '0') ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div id="recaptchaOptions" style="<?php echo ($readSettings["recaptchaPublicKey"] == '0' || $readSettings["recaptchaPrivateKey"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-group row">
                    <label for="inputRECAPTCHAPagesStatus" class="col-sm-2 col-form-label">reCAPTCHA <?php e__('Active Pages') ?>:</label>
                    <div class="d-flex">
                      <label for="switchLoginPage" class="col-auto col-form-label"><?php e__('Login') ?>:</label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="recaptchaPagesStatus[]">
                          <input type="checkbox" id="switchLoginPage" class="custom-control-input" <?php echo ($recaptchaPagesStatus["loginPage"] == 1) ? "checked" : null; ?>>
                          <label for="switchLoginPage" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <label for="switchRegisterPage" class="col-auto col-form-label"><?php e__('Register') ?>:</label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="recaptchaPagesStatus[]">
                          <input type="checkbox" id="switchRegisterPage" class="custom-control-input" <?php echo ($recaptchaPagesStatus["registerPage"] == 1) ? "checked" : null; ?>>
                          <label for="switchRegisterPage" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <label for="switchRecoverPage" class="col-auto col-form-label"><?php e__('Recover Password') ?>:</label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="recaptchaPagesStatus[]">
                          <input type="checkbox" id="switchRecoverPage" class="custom-control-input" <?php echo ($recaptchaPagesStatus["recoverPage"] == 1) ? "checked" : null; ?>>
                          <label for="switchRecoverPage" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <label for="switchNewsPage" class="col-auto col-form-label"><?php e__('News') ?>:</label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="recaptchaPagesStatus[]">
                          <input type="checkbox" id="switchNewsPage" class="custom-control-input" <?php echo ($recaptchaPagesStatus["newsPage"] == 1) ? "checked" : null; ?>>
                          <label for="switchNewsPage" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <label for="switchSupportPage" class="col-auto col-form-label"><?php e__('Support') ?>:</label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="recaptchaPagesStatus[]">
                          <input type="checkbox" id="switchSupportPage" class="custom-control-input" <?php echo ($recaptchaPagesStatus["supportPage"] == 1) ? "checked" : null; ?>>
                          <label for="switchSupportPage" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <label for="switchTFAPage" class="col-auto col-form-label">
                        2FA:
                        <a href="https://help.leaderos.net/google-authenticator" rel="external">
                          <i class="fa fa-question-circle text-primary" data-toggle="tooltip" data-placement="top" title="<?php e__('Two-Factor Authentication') ?>"></i>
                        </a>
                      </label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="recaptchaPagesStatus[]">
                          <input type="checkbox" id="switchTFAPage" class="custom-control-input" <?php echo ($recaptchaPagesStatus["tfaPage"] == 1) ? "checked" : null; ?>>
                          <label for="switchTFAPage" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputRECAPTCHAPublicKey" class="col-sm-2 col-form-label">reCAPTCHA <?php e__('Site Key') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputRECAPTCHAPublicKey" class="form-control" name="recaptchaPublicKey" placeholder="<?php e__('Enter the Site Key you get from reCaptcha') ?>." value="<?php echo ($readSettings["recaptchaPublicKey"] != '0') ? $readSettings["recaptchaPublicKey"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputRECAPTCHAPrivateKey" class="col-sm-2 col-form-label">reCAPTCHA <?php e__('Secret Key') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputRECAPTCHAPrivateKey" class="form-control" name="recaptchaPrivateKey" placeholder="<?php e__('Enter the Secret Key you get from reCaptcha') ?>." value="<?php echo ($readSettings["recaptchaPrivateKey"] != '0') ? $readSettings["recaptchaPrivateKey"] : null; ?>">
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputMinPay" class="col-sm-2 col-form-label"><?php e__('Min. Payment Amount') ?>:</label>
                  <div class="col-sm-10">
                    <input type="number" id="inputMinPay" class="form-control" name="minPay" placeholder="<?php e__('Enter the minimum payment amount') ?>." value="<?php echo $readSettings["minPay"]; ?>" min="1" max="99999">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputMaxPay" class="col-sm-2 col-form-label"><?php e__('Max Payment Amount') ?>:</label>
                  <div class="col-sm-10">
                    <input type="number" id="inputMaxPay" class="form-control" name="maxPay" placeholder="<?php e__('Enter the maximum payment amount') ?>." value="<?php echo $readSettings["maxPay"]; ?>" min="1" max="99999">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectNewsLimit" class="col-sm-2 col-form-label"><?php e__('News Limit') ?> (<?php e__('Each Page') ?>):*</label>
                  <div class="col-sm-10">
                    <select id="selectNewsLimit" class="form-control" name="newsLimit" data-toggle="select" data-minimum-results-for-search="-1">
                      <?php
                        for ($i=1; $i <= 12; $i++) {
                          if ($i % 3 == 0) {
                            if ($readSettings["newsLimit"] == $i) {
                              echo '<option value="'.$i.'" selected>'.$i.'</option>';
                            }
                            else {
                              echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                          }
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectRegisterLimit" class="col-sm-2 col-form-label"><?php e__('Register Limit') ?>:*</label>
                  <div class="col-sm-10">
                    <select id="selectRegisterLimit" class="form-control" name="registerLimit" data-toggle="select" data-minimum-results-for-search="-1">
                      <?php
                        for ($i=1; $i <= 3; $i++) {
                          if ($readSettings["registerLimit"] == $i) {
                            echo '<option value="'.$i.'" selected>'.$i.'</option>';
                          }
                          else {
                            echo '<option value="'.$i.'">'.$i.'</option>';
                          }
                        }
                        if ($readSettings["registerLimit"] == 0) {
                          echo '<option value="0" selected>'.t__('Unlimited').'</option>';
                        }
                        else {
                          echo '<option value="0">'.t__('Unlimited').'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <?php echo $csrf->input('updateSystemSettings'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateSystemSettings"><?php e__('Save Changes') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'seo'): ?>
  <?php if (get("action") == 'update'): ?>
    <?php
    $seoPages = $db->query("SELECT * FROM SeoPages");
    $seoPageNames = [
      '404' => '404',
      'application' => 'Applications',
      'bazaar' => 'Bazaar',
      'manage-bazaar' => 'Manage Bazaar',
      'checkout' => 'Checkout',
      'chest' => 'Chest',
      'credit' => 'Credit',
      'download' => 'Download',
      'games' => 'Games',
      'gift' => 'Gift',
      'help' => 'Help Center',
      'home' => 'Home',
      'leaderboards' => 'Leaderboards',
      'login' => 'Login',
      'lottery' => 'Wheel of Fortune',
      'maintenance' => 'Maintenance',
      'news' => 'Blog',
      'page' => 'Page',
      'player' => 'Player',
      'profile' => 'Profile',
      'recover' => 'Recover Account',
      'register' => 'Register',
      'rules' => 'Rules',
      'store' => 'Store',
      'support' => 'Support',
      'tfa' => 'TFA',
      'tfa-recover' => 'Recover TFA',
      'gaming-night' => 'Gaming Night',
      'forum' => 'Forum',
    ];
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('SEO Settings') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Settings') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('SEO Settings') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["updateSeoSettings"])) {
              if (!$csrf->validate('updateSeoSettings')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
                echo goDelay("/dashboard/settings/seo", 2);
              }
              else {
                foreach ($_POST as $key => $value) {
                  $fields = explode('_', $key);
                  if (isset($fields[1])) {
                    $updateSettings = $db->prepare("UPDATE SeoPages SET $fields[1] = ? WHERE id = ?");
                    $updateSettings->execute(array(post($key), $fields[0]));
                  }
                }
                echo alertSuccess(t__('Changes has been saved successfully!'));
                echo goDelay("/dashboard/settings/seo", 2);
                
                createLog($readAdmin["id"], "SEO_SETTINGS_UPDATED");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <div class="mb-4">
                <?php foreach ($seoPageNames as $key => $value): ?>
                  <button class="btn btn-light mr-1 mb-2 changeSeoPage" data-key="<?php echo $key; ?>">
                    <?php e__($value); ?>
                  </button>
                <?php endforeach; ?>
              </div>
              <form action="" method="post">
                <?php foreach ($seoPages as $readSeoPages): ?>
                  <div id="seoPageBlock_<?php echo $readSeoPages["page"]; ?>" style="display: none;">
                    <div class="form-group row">
                      <label for="inputTitle_<?php echo $readSeoPages["id"]; ?>" class="col-sm-2 col-form-label"><?php e__('Title') ?>:</label>
                      <div class="col-sm-10">
                        <input type="text" id="inputTitle_<?php echo $readSeoPages["id"]; ?>" class="form-control" name="<?php echo $readSeoPages["id"]; ?>_title" placeholder="<?php e__('Enter the page title.') ?>" value="<?php echo $readSeoPages["title"]; ?>" required>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputDesc_<?php echo $readSeoPages["id"]; ?>" class="col-sm-2 col-form-label"><?php e__('Description') ?> (<?php e__('Optional') ?>):</label>
                      <div class="col-sm-10">
                        <textarea id="inputDesc_<?php echo $readSeoPages["id"]; ?>" class="form-control" name="<?php echo $readSeoPages["id"]; ?>_description" rows="3" placeholder="<?php e__('Enter the page description.') ?>"><?php echo $readSeoPages["description"]; ?></textarea>
                        <small class="form-text text-muted pt-2"><?php e__('If you leave the description blank, the default value of "Google Description" will be used.') ?></small>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputImage_<?php echo $readSeoPages["id"]; ?>" class="col-sm-2 col-form-label"><?php e__('Image') ?> (<?php e__('Optional') ?>):</label>
                      <div class="col-sm-10">
                        <input type="text" id="inputImage_<?php echo $readSeoPages["id"]; ?>" class="form-control" name="<?php echo $readSeoPages["id"]; ?>_image" placeholder="<?php e__('Enter the page image link.') ?>" value="<?php echo $readSeoPages["image"]; ?>">
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
                
                <?php echo $csrf->input('updateSeoSettings'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateSeoSettings" style="display: none;"><?php e__('Save Changes') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'smtp'): ?>
  <?php if (get("action") == 'update'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('SMTP Settings') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Settings') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('SMTP Settings') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["updateSMTPSettings"])) {
              if (!$csrf->validate('updateSMTPSettings')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("smtpServer") == null || post("smtpPort") == null || post("smtpSecure") == null || post("smtpUsername") == null || post("smtpPassword") == null || post("smtpPasswordTemplate") == null || post("smtpTFATemplate") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else {
                $updateSettings = $db->prepare("UPDATE Settings SET smtpServer = ?, smtpPort = ?, smtpSecure = ?, smtpUsername = ?, smtpPassword = ?, smtpPasswordTemplate = ?, smtpTFATemplate = ? WHERE id = ?");
                $updateSettings->execute(array(post("smtpServer"), post("smtpPort"), post("smtpSecure"), post("smtpUsername"), post("smtpPassword"), filteredContent($_POST["smtpPasswordTemplate"]), filteredContent($_POST["smtpTFATemplate"]), $readSettings["id"]));
                echo alertSuccess(t__('Changes has been saved successfully!'));
                echo goDelay("/dashboard/settings/smtp", 2);
  
                createLog($readAdmin["id"], "SMTP_SETTINGS_UPDATED");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputSMTPServer" class="col-sm-2 col-form-label"><?php e__('SMTP Server') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputSMTPServer" class="form-control" name="smtpServer" placeholder="<?php e__('Enter the SMTP Server') ?>. (For Exm: mail.leaderos.info)" value="<?php echo $readSettings["smtpServer"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputSMTPPort" class="col-sm-2 col-form-label"><?php e__('SMTP Port') ?>:</label>
                  <div class="col-sm-10">
                    <input type="number" id="inputSMTPPort" class="form-control" name="smtpPort" placeholder="<?php e__('Enter the SMTP Port') ?>. (For Exm: 587)" value="<?php echo $readSettings["smtpPort"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectSMTPSecure" class="col-sm-2 col-form-label"><?php e__('SMTP Security') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectSMTPSecure" class="form-control" name="smtpSecure" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1" <?php echo ($readSettings["smtpSecure"] == 1) ? 'selected="selected"' : null; ?>>SSL</option>
                      <option value="2" <?php echo ($readSettings["smtpSecure"] == 2) ? 'selected="selected"' : null; ?>>TLS</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputSMTPUsername" class="col-sm-2 col-form-label"><?php e__('SMTP E-Mail') ?>:</label>
                  <div class="col-sm-10">
                    <input type="email" id="inputSMTPUsername" class="form-control" name="smtpUsername" placeholder="<?php e__('Enter the SMTP E-mail') ?>. (For Exm: destek@leaderos.info)" value="<?php echo $readSettings["smtpUsername"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputSMTPPassword" class="col-sm-2 col-form-label"><?php e__('SMTP E-Mail Password') ?>:</label>
                  <div class="col-sm-10">
                    <input type="password" id="inputSMTPPassword" class="form-control" name="smtpPassword" placeholder="<?php e__('Enter the SMTP Password') ?>. (For Exm: emailpassword123)" value="<?php echo $readSettings["smtpPassword"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaSMTPPasswordTemplate" class="col-sm-2 col-form-label"><?php e__('Recover Password E-Mail Template') ?>:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaSMTPPasswordTemplate" class="form-control" data-toggle="textEditor" name="smtpPasswordTemplate" placeholder="<?php e__("It's a template for recover password e-mail") ?>."><?php echo $readSettings["smtpPasswordTemplate"] ?></textarea>
                    <small class="form-text text-muted pt-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                    <small class="form-text text-muted"><strong><?php e__('Recover Password URL') ?>:</strong> %url%</small>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaSMTPTFATemplate" class="col-sm-2 col-form-label"><?php e__('TFA Recover Template') ?>:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaSMTPTFATemplate" class="form-control" data-toggle="textEditor" name="smtpTFATemplate" placeholder="<?php e__("It's a template for TFA recover e-mail") ?>."><?php echo $readSettings["smtpTFATemplate"] ?></textarea>
                    <small class="form-text text-muted pt-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                    <small class="form-text text-muted"><strong><?php e__('TFA Recover URL') ?>:</strong> %url%</small>
                  </div>
                </div>
                <?php echo $csrf->input('updateSMTPSettings'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="button" id="testSMTP" class="btn btn-rounded btn-info">
                      <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                        <span class="sr-only">-/-</span>
                      </div>
                      <span><?php e__('Check Connection') ?></span>
                    </button>
                    <button type="submit" class="btn btn-rounded btn-success" name="updateSMTPSettings"><?php e__('Save Changes') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'webhooks'): ?>
  <?php if (get("action") == 'update'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Discord Webhook Settings') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Settings') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Discord Webhook Settings') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            $webhookMessage       = "@everyone";
            $webhookCreditEmbed   = '**%username%** has bought **%credit% credits** ($%money%)';
            $webhookStoreEmbed    = "**%username%** has bought **%product%** from the **%server%**";;
            $webhookSupportEmbed  = "**%username%** has sent a ticket message. \n%panelurl%";
            $webhookNewsEmbed     = "**%username%** has been commented on a blog post. \n%posturl%\n%panelurl%";
            $webhookLotteryEmbed  = "**%username%** has won **%award%** from the **%lottery%** wheel of fortune";
            $webhookApplicationEmbed  = "**%username%** has applied for **%form%** \n%panelurl%";
            if (isset($_POST["updateWebhookSettings"])) {
              $_POST["webhookCreditAdStatus"] = (post("webhookCreditAdStatus")) ? '1' : '0';
              $_POST["webhookStoreAdStatus"] = (post("webhookStoreAdStatus")) ? '1' : '0';
              $_POST["webhookSupportAdStatus"] = (post("webhookSupportAdStatus")) ? '1' : '0';
              $_POST["webhookNewsAdStatus"] = (post("webhookNewsAdStatus")) ? '1' : '0';
              $_POST["webhookLotteryAdStatus"] = (post("webhookLotteryAdStatus")) ? '1' : '0';
              $_POST["webhookApplicationAdStatus"] = (post("webhookApplicationAdStatus")) ? '1' : '0';

              if (post("webhookCreditStatus") == 0) {
                $_POST["webhookCreditURL"] = '0';
                $_POST["webhookCreditTitle"] = 'Credits';
                $_POST["webhookCreditImage"] = '0';
                $_POST["webhookCreditColor"] = '000000';
                $_POST["webhookCreditAdStatus"] = '1';
                $_POST["webhookCreditMessage"] = $webhookMessage;
                $_POST["webhookCreditEmbed"] = $webhookCreditEmbed;
              }
              if (post("webhookCreditMessage") == null) {
                $_POST["webhookCreditMessage"] = '0';
              }
              if (post("webhookCreditImage") == null) {
                $_POST["webhookCreditImage"] = '0';
              }

              if (post("webhookStoreStatus") == 0) {
                $_POST["webhookStoreURL"] = '0';
                $_POST["webhookStoreTitle"] = 'Store';
                $_POST["webhookStoreImage"] = '0';
                $_POST["webhookStoreColor"] = '000000';
                $_POST["webhookStoreAdStatus"] = '1';
                $_POST["webhookStoreMessage"] = $webhookMessage;
                $_POST["webhookStoreEmbed"] = $webhookStoreEmbed;
              }
              if (post("webhookStoreMessage") == null) {
                $_POST["webhookStoreMessage"] = '0';
              }
              if (post("webhookStoreImage") == null) {
                $_POST["webhookStoreImage"] = '0';
              }

              if (post("webhookSupportStatus") == 0) {
                $_POST["webhookSupportURL"] = '0';
                $_POST["webhookSupportTitle"] = 'Support';
                $_POST["webhookSupportImage"] = '0';
                $_POST["webhookSupportColor"] = '000000';
                $_POST["webhookSupportAdStatus"] = '1';
                $_POST["webhookSupportMessage"] = $webhookMessage;
                $_POST["webhookSupportEmbed"] = $webhookSupportEmbed;
              }
              if (post("webhookSupportMessage") == null) {
                $_POST["webhookSupportMessage"] = '0';
              }
              if (post("webhookSupportImage") == null) {
                $_POST["webhookSupportImage"] = '0';
              }

              if (post("webhookNewsStatus") == 0) {
                $_POST["webhookNewsURL"] = '0';
                $_POST["webhookNewsTitle"] = 'News';
                $_POST["webhookNewsImage"] = '0';
                $_POST["webhookNewsColor"] = '000000';
                $_POST["webhookNewsAdStatus"] = '1';
                $_POST["webhookNewsMessage"] = $webhookMessage;
                $_POST["webhookNewsEmbed"] = $webhookNewsEmbed;
              }
              if (post("webhookNewsMessage") == null) {
                $_POST["webhookNewsMessage"] = '0';
              }
              if (post("webhookNewsImage") == null) {
                $_POST["webhookNewsImage"] = '0';
              }

              if (post("webhookLotteryStatus") == 0) {
                $_POST["webhookLotteryURL"] = '0';
                $_POST["webhookLotteryTitle"] = 'Wheel of Fortune';
                $_POST["webhookLotteryImage"] = '0';
                $_POST["webhookLotteryColor"] = '000000';
                $_POST["webhookLotteryAdStatus"] = '1';
                $_POST["webhookLotteryMessage"] = $webhookMessage;
                $_POST["webhookLotteryEmbed"] = $webhookLotteryEmbed;
              }
              if (post("webhookLotteryMessage") == null) {
                $_POST["webhookLotteryMessage"] = '0';
              }
              if (post("webhookLotteryImage") == null) {
                $_POST["webhookLotteryImage"] = '0';
              }
  
              if (post("webhookApplicationStatus") == 0) {
                $_POST["webhookApplicationURL"] = '0';
                $_POST["webhookApplicationTitle"] = 'Application';
                $_POST["webhookApplicationImage"] = '0';
                $_POST["webhookApplicationColor"] = '000000';
                $_POST["webhookApplicationAdStatus"] = '1';
                $_POST["webhookApplicationMessage"] = $webhookMessage;
                $_POST["webhookApplicationEmbed"] = $webhookApplicationEmbed;
              }
              if (post("webhookApplicationMessage") == null) {
                $_POST["webhookApplicationMessage"] = '0';
              }
              if (post("webhookApplicationImage") == null) {
                $_POST["webhookApplicationImage"] = '0';
              }

              if (!$csrf->validate('updateWebhookSettings')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("webhookCreditStatus") == null ||
              post("webhookCreditTitle") == null ||
              post("webhookCreditColor") == null ||
              post("webhookCreditImage") == null ||
              post("webhookCreditURL") == null ||
              post("webhookCreditMessage") == null ||
              post("webhookCreditEmbed") == null ||
              post("webhookCreditAdStatus") == null ||
              post("webhookStoreStatus") == null ||
              post("webhookStoreTitle") == null ||
              post("webhookStoreColor") == null ||
              post("webhookStoreImage") == null ||
              post("webhookStoreURL") == null ||
              post("webhookStoreMessage") == null ||
              post("webhookStoreEmbed") == null ||
              post("webhookStoreAdStatus") == null ||
              post("webhookSupportStatus") == null ||
              post("webhookSupportTitle") == null ||
              post("webhookSupportColor") == null ||
              post("webhookSupportImage") == null ||
              post("webhookSupportURL") == null ||
              post("webhookSupportMessage") == null ||
              post("webhookSupportEmbed") == null ||
              post("webhookSupportAdStatus") == null ||
              post("webhookNewsStatus") == null ||
              post("webhookNewsTitle") == null ||
              post("webhookNewsColor") == null ||
              post("webhookNewsImage") == null ||
              post("webhookNewsURL") == null ||
              post("webhookNewsMessage") == null ||
              post("webhookNewsEmbed") == null ||
              post("webhookNewsAdStatus") == null ||
              post("webhookLotteryStatus") == null ||
              post("webhookLotteryTitle") == null ||
              post("webhookLotteryColor") == null ||
              post("webhookLotteryImage") == null ||
              post("webhookLotteryURL") == null ||
              post("webhookLotteryMessage") == null ||
              post("webhookLotteryEmbed") == null ||
              post("webhookLotteryAdStatus") == null ||
              post("webhookApplicationStatus") == null ||
              post("webhookApplicationTitle") == null ||
              post("webhookApplicationColor") == null ||
              post("webhookApplicationImage") == null ||
              post("webhookApplicationURL") == null ||
              post("webhookApplicationMessage") == null ||
              post("webhookApplicationEmbed") == null ||
              post("webhookApplicationAdStatus") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else {
                $_POST["webhookCreditColor"] = ltrim($_POST["webhookCreditColor"], '#');
                $_POST["webhookStoreColor"] = ltrim($_POST["webhookStoreColor"], '#');
                $_POST["webhookSupportColor"] = ltrim($_POST["webhookSupportColor"], '#');
                $_POST["webhookNewsColor"] = ltrim($_POST["webhookNewsColor"], '#');
                $_POST["webhookLotteryColor"] = ltrim($_POST["webhookLotteryColor"], '#');
                $_POST["webhookApplicationColor"] = ltrim($_POST["webhookApplicationColor"], '#');
                $updateSettings = $db->prepare("UPDATE Settings SET webhookCreditTitle = ?, webhookCreditColor = ?, webhookCreditImage = ?, webhookCreditURL = ?, webhookCreditMessage = ?, webhookCreditEmbed = ?, webhookCreditAdStatus = ?, webhookStoreTitle = ?, webhookStoreColor = ?, webhookStoreImage = ?, webhookStoreURL = ?, webhookStoreMessage = ?, webhookStoreEmbed = ?, webhookStoreAdStatus = ?, webhookSupportTitle = ?, webhookSupportColor = ?, webhookSupportImage = ?, webhookSupportURL = ?, webhookSupportMessage = ?, webhookSupportEmbed = ?, webhookSupportAdStatus = ?, webhookNewsTitle = ?, webhookNewsColor = ?, webhookNewsImage = ?, webhookNewsURL = ?, webhookNewsMessage = ?, webhookNewsEmbed = ?, webhookNewsAdStatus = ?, webhookLotteryTitle = ?, webhookLotteryColor = ?, webhookLotteryImage = ?, webhookLotteryURL = ?, webhookLotteryMessage = ?, webhookLotteryEmbed = ?, webhookLotteryAdStatus = ?, webhookApplicationTitle = ?, webhookApplicationColor = ?, webhookApplicationImage = ?, webhookApplicationURL = ?, webhookApplicationMessage = ?, webhookApplicationEmbed = ?, webhookApplicationAdStatus = ? WHERE id = ?");
                $updateSettings->execute(array(post("webhookCreditTitle"), post("webhookCreditColor"), post("webhookCreditImage"), post("webhookCreditURL"), strip_tags($_POST["webhookCreditMessage"]), strip_tags($_POST["webhookCreditEmbed"]), post("webhookCreditAdStatus"), post("webhookStoreTitle"), post("webhookStoreColor"), post("webhookStoreImage"), post("webhookStoreURL"), strip_tags($_POST["webhookStoreMessage"]), strip_tags($_POST["webhookStoreEmbed"]), post("webhookStoreAdStatus"), post("webhookSupportTitle"), post("webhookSupportColor"), post("webhookSupportImage"), post("webhookSupportURL"), strip_tags($_POST["webhookSupportMessage"]), strip_tags($_POST["webhookSupportEmbed"]), post("webhookSupportAdStatus"), post("webhookNewsTitle"), post("webhookNewsColor"), post("webhookNewsImage"), post("webhookNewsURL"), strip_tags($_POST["webhookNewsMessage"]), strip_tags($_POST["webhookNewsEmbed"]), post("webhookNewsAdStatus"), post("webhookLotteryTitle"), post("webhookLotteryColor"), post("webhookLotteryImage"), post("webhookLotteryURL"), strip_tags($_POST["webhookLotteryMessage"]), strip_tags($_POST["webhookLotteryEmbed"]), post("webhookLotteryAdStatus"), post("webhookApplicationTitle"), post("webhookApplicationColor"), post("webhookApplicationImage"), post("webhookApplicationURL"), strip_tags($_POST["webhookApplicationMessage"]), strip_tags($_POST["webhookApplicationEmbed"]), post("webhookApplicationAdStatus"), $readSettings["id"]));
                echo alertSuccess(t__('Changes has been saved successfully!'));
                echo goDelay("/dashboard/settings/webhooks", 2);
  
                createLog($readAdmin["id"], "WEBHOOK_SETTINGS_UPDATED");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="selectWebhookCreditStatus" class="col-sm-2 col-form-label"><?php e__('Buy Credits') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectWebhookCreditStatus" class="form-control" name="webhookCreditStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["webhookCreditURL"] == '0') ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["webhookCreditURL"] != '0') ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div id="webhookCreditOptions" style="<?php echo ($readSettings["webhookCreditURL"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-row row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label for="inputWebhookCreditTitle"><?php e__('Title') ?>:</label>
                          <input type="text" name="webhookCreditTitle" id="inputWebhookCreditTitle" class="form-control" placeholder="<?php e__('Enter the title of message') ?>." value="<?php echo $readSettings["webhookCreditTitle"]; ?>">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputWebhookCreditColor"><?php e__('Color') ?>:</label>
                          <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                            <input type="text" id="inputWebhookCreditColor" class="form-control form-control-appended" name="webhookCreditColor" placeholder="<?php e__('Enter the color code') ?>." value="<?php echo ($readSettings["webhookCreditColor"] != null) ? "#".$readSettings["webhookCreditColor"] : "#000000"; ?>" required>
                            <div class="input-group-append">
                              <div class="input-group-text input-group-addon">
                                <i></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookCreditURL">Webhook URL:</label>
                      <input type="text" id="inputWebhookCreditURL" class="form-control" name="webhookCreditURL" placeholder="https://discordapp.com/api/webhooks/XXXXXXXXXXX/XXXXXXXXXXX" value="<?php echo ($readSettings["webhookCreditURL"] != '0') ? $readSettings["webhookCreditURL"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookCreditImage"><?php e__('Image') ?>:</label>
                      <input type="text" id="inputWebhookCreditImage" class="form-control" name="webhookCreditImage" placeholder="<?php e__('Enter a Image URL (You can pass)') ?>" value="<?php echo ($readSettings["webhookCreditImage"] != '0') ? $readSettings["webhookCreditImage"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookCreditMessage"><?php e__('Message') ?>:</label>
                      <textarea id="inputWebhookCreditMessage" class="form-control" name="webhookCreditMessage" placeholder="<?php e__('Enter the Message') ?>." rows="2"><?php echo ($readSettings["webhookCreditMessage"] != '0') ? $readSettings["webhookCreditMessage"] : null; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                      <small class="form-text text-muted"><strong><?php e__('Credits Amount (Extras Included)') ?>:</strong> %credit%</small>
                      <small class="form-text text-muted"><strong><?php e__('Earned Money (Extras Not Included)') ?>:</strong> %money%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookCreditEmbed">Embed:</label>
                      <textarea id="inputWebhookCreditEmbed" class="form-control" name="webhookCreditEmbed" placeholder="<?php e__('Enter the embed content') ?>." rows="2"><?php echo $readSettings["webhookCreditEmbed"]; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                      <small class="form-text text-muted"><strong><?php e__('Credits Amount (Extras Included)') ?>:</strong> %credit%</small>
                      <small class="form-text text-muted"><strong><?php e__('Earned Money (Extras Not Included)') ?>:</strong> %money%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="col">
                          <div id="testWebhookCredit">
                            <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                              <span class="sr-only">-/-</span>
                            </div>
                            <a href="javascript:void(0);"><?php e__('Send test message.') ?></a>
                          </div>
                        </div>
                        <div class="col-auto">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="webhookCreditAdStatus" class="custom-control-input" id="checkboxWebhookCreditAdStatus" <?php echo ($readSettings["webhookCreditAdStatus"] == 1) ? 'checked="checked"' : null; ?>>
                            <label class="custom-control-label" for="checkboxWebhookCreditAdStatus"><?php e__('Show powered by LeaderOS text') ?></label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="selectWebhookStoreStatus" class="col-sm-2 col-form-label"><?php e__('Store') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectWebhookStoreStatus" class="form-control" name="webhookStoreStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["webhookStoreURL"] == '0') ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["webhookStoreURL"] != '0') ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div id="webhookStoreOptions" style="<?php echo ($readSettings["webhookStoreURL"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-row row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label for="inputWebhookStoreTitle"><?php e__('Title') ?>:</label>
                          <input type="text" name="webhookStoreTitle" id="inputWebhookStoreTitle" class="form-control" placeholder="<?php e__('Enter the title of message') ?>." value="<?php echo $readSettings["webhookStoreTitle"]; ?>">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputWebhookStoreColor"><?php e__('Color') ?>:</label>
                          <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                            <input type="text" id="inputWebhookStoreColor" class="form-control form-control-appended" name="webhookStoreColor" placeholder="<?php e__('Enter the color code') ?>." value="<?php echo ($readSettings["webhookStoreColor"] != null) ? "#".$readSettings["webhookStoreColor"] : "#000000"; ?>" required>
                            <div class="input-group-append">
                              <div class="input-group-text input-group-addon">
                                <i></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookStoreURL">Webhook URL:</label>
                      <input type="text" id="inputWebhookStoreURL" class="form-control" name="webhookStoreURL" placeholder="https://discordapp.com/api/webhooks/XXXXXXXXXXX/XXXXXXXXXXX" value="<?php echo ($readSettings["webhookStoreURL"] != '0') ? $readSettings["webhookStoreURL"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookStoreImage"><?php e__('Image') ?>:</label>
                      <input type="text" id="inputWebhookStoreImage" class="form-control" name="webhookStoreImage" placeholder="<?php e__('Enter a Image URL (You can pass)') ?>." value="<?php echo ($readSettings["webhookStoreImage"] != '0') ? $readSettings["webhookStoreImage"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookStoreMessage"><?php e__('Message') ?>:</label>
                      <textarea id="inputWebhookStoreMessage" class="form-control" name="webhookStoreMessage" placeholder="<?php e__('Enter the message content') ?>." rows="2"><?php echo ($readSettings["webhookStoreMessage"] != '0') ? $readSettings["webhookStoreMessage"] : null; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                      <small class="form-text text-muted"><strong><?php e__('Server Name') ?>:</strong> %server%</small>
                      <small class="form-text text-muted"><strong><?php e__('Product Name') ?>:</strong> %product%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookStoreEmbede">Embed:</label>
                      <textarea id="inputWebhookStoreEmbed" class="form-control" name="webhookStoreEmbed" placeholder="<?php e__('Enter the embed content') ?>." rows="2"><?php echo $readSettings["webhookStoreEmbed"]; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                      <small class="form-text text-muted"><strong><?php e__('Server Name') ?>:</strong> %server%</small>
                      <small class="form-text text-muted"><strong><?php e__('Product Name') ?>:</strong> %product%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="col">
                          <div id="testWebhookStore">
                            <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                              <span class="sr-only">-/-</span>
                            </div>
                            <a href="javascript:void(0);"><?php e__('Send test message.') ?></a>
                          </div>
                        </div>
                        <div class="col-auto">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="webhookStoreAdStatus" class="custom-control-input" id="checkboxWebhookStoreAdStatus" <?php echo ($readSettings["webhookStoreAdStatus"] == 1) ? 'checked="checked"' : null; ?>>
                            <label class="custom-control-label" for="checkboxWebhookStoreAdStatus"><?php e__('Show powered by LeaderOS text') ?></label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="selectWebhookSupportStatus" class="col-sm-2 col-form-label"><?php e__('Support Messages') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectWebhookSupportStatus" class="form-control" name="webhookSupportStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["webhookSupportURL"] == '0') ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["webhookSupportURL"] != '0') ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div id="webhookSupportOptions" style="<?php echo ($readSettings["webhookSupportURL"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-row row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label for="inputWebhookSupportTitle"><?php e__('Title') ?>:</label>
                          <input type="text" name="webhookSupportTitle" id="inputWebhookSupportTitle" class="form-control" placeholder="<?php e__('Enter the message title') ?>." value="<?php echo $readSettings["webhookSupportTitle"]; ?>">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputWebhookSupportColor"><?php e__('Color') ?>:</label>
                          <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                            <input type="text" id="inputWebhookSupportColor" class="form-control form-control-appended" name="webhookSupportColor" placeholder="Enter the color code." value="<?php echo ($readSettings["webhookSupportColor"] != null) ? "#".$readSettings["webhookSupportColor"] : "#000000"; ?>" required>
                            <div class="input-group-append">
                              <div class="input-group-text input-group-addon">
                                <i></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookSupportURL">Webhook URL:</label>
                      <input type="text" id="inputWebhookSupportURL" class="form-control" name="webhookSupportURL" placeholder="https://discordapp.com/api/webhooks/XXXXXXXXXXX/XXXXXXXXXXX" value="<?php echo ($readSettings["webhookSupportURL"] != '0') ? $readSettings["webhookSupportURL"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookSupportImage"><?php e__('Image') ?>:</label>
                      <input type="text" id="inputWebhookSupportImage" class="form-control" name="webhookSupportImage" placeholder="<?php e__('Enter a Image URL (You can pass)') ?>" value="<?php echo ($readSettings["webhookSupportImage"] != '0') ? $readSettings["webhookSupportImage"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookSupportMessage"><?php e__('Message') ?>:</label>
                      <textarea id="inputWebhookSupportMessage" class="form-control" name="webhookSupportMessage" placeholder="<?php e__('Enter the message content') ?>." rows="2"><?php echo ($readSettings["webhookSupportMessage"] != '0') ? $readSettings["webhookSupportMessage"] : null; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                      <small class="form-text text-muted"><strong><?php e__('Dashboard URL') ?>:</strong> %panelurl%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookSupportEmbed">Embed:</label>
                      <textarea id="inputWebhookSupportEmbed" class="form-control" name="webhookSupportEmbed" placeholder="Enter the embed content." rows="2"><?php echo $readSettings["webhookSupportEmbed"]; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                      <small class="form-text text-muted"><strong><?php e__('Dashboard URL') ?>:</strong> %panelurl%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="col">
                          <div id="testWebhookSupport">
                            <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                              <span class="sr-only">-/-</span>
                            </div>
                            <a href="javascript:void(0);"><?php e__('Send Test Message') ?></a>
                          </div>
                        </div>
                        <div class="col-auto">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="webhookSupportAdStatus" class="custom-control-input" id="checkboxWebhookSupportAdStatus" <?php echo ($readSettings["webhookSupportAdStatus"] == 1) ? 'checked="checked"' : null; ?>>
                            <label class="custom-control-label" for="checkboxWebhookSupportAdStatus"><?php e__('Show powered by LeaderOS text') ?></label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="selectWebhookNewsStatus" class="col-sm-2 col-form-label"><?php e__('Comments') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectWebhookNewsStatus" class="form-control" name="webhookNewsStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["webhookNewsURL"] == '0') ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["webhookNewsURL"] != '0') ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div id="webhookNewsOptions" style="<?php echo ($readSettings["webhookNewsURL"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-row row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label for="inputWebhookNewsTitle"><?php e__('Title') ?>:</label>
                          <input type="text" name="webhookNewsTitle" id="inputWebhookNewsTitle" class="form-control" placeholder="<?php e__('Enter the message title') ?>." value="<?php echo $readSettings["webhookNewsTitle"]; ?>">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputWebhookNewsColor"><?php e__('Color') ?>:</label>
                          <div id="colorPicker" id="inputWebhookNewsColor" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                            <input type="text" class="form-control form-control-appended" name="webhookNewsColor" placeholder="<?php e__('Enter the color code') ?>." value="<?php echo ($readSettings["webhookNewsColor"] != null) ? "#".$readSettings["webhookNewsColor"] : "#000000"; ?>" required>
                            <div class="input-group-append">
                              <div class="input-group-text input-group-addon">
                                <i></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookNewsURL">Webhook URL:</label>
                      <input type="text" id="inputWebhookNewsURL" class="form-control" name="webhookNewsURL" placeholder="https://discordapp.com/api/webhooks/XXXXXXXXXXX/XXXXXXXXXXX" value="<?php echo ($readSettings["webhookNewsURL"] != '0') ? $readSettings["webhookNewsURL"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookNewsImage"><?php e__('Image') ?>:</label>
                      <input type="text" id="inputWebhookNewsImage" class="form-control" name="webhookNewsImage" placeholder="<?php e__('Enter a Image URL (You can pass)') ?>" value="<?php echo ($readSettings["webhookNewsImage"] != '0') ? $readSettings["webhookNewsImage"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookNewsMessage"><?php e__('Message') ?>:</label>
                      <textarea id="inputWebhookNewsMessage" class="form-control" name="webhookNewsMessage" placeholder="<?php e__('Enter the message') ?>." rows="2"><?php echo ($readSettings["webhookNewsMessage"] != '0') ? $readSettings["webhookNewsMessage"] : null; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                      <small class="form-text text-muted"><strong><?php e__('Dashboard URL') ?>:</strong> %panelurl%</small>
                      <small class="form-text text-muted"><strong><?php e__('News URL') ?>:</strong> %posturl%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookNewsEmbed">Embed:</label>
                      <textarea id="inputWebhookNewsEmbed" class="form-control" name="webhookNewsEmbed" placeholder="<?php e__('Enter the embed content') ?>." rows="2"><?php echo $readSettings["webhookNewsEmbed"]; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                      <small class="form-text text-muted"><strong><?php e__('Dashboard URL') ?>:</strong> %panelurl%</small>
                      <small class="form-text text-muted"><strong><?php e__('News URL') ?>:</strong> %posturl%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="col">
                          <div id="testWebhookNews">
                            <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                              <span class="sr-only">-/-</span>
                            </div>
                            <a href="javascript:void(0);"><?php e__('Send the test message') ?></a>
                          </div>
                        </div>
                        <div class="col-auto">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="webhookNewsAdStatus" class="custom-control-input" id="checkboxWebhookNewsAdStatus" <?php echo ($readSettings["webhookNewsAdStatus"] == 1) ? 'checked="checked"' : null; ?>>
                            <label class="custom-control-label" for="checkboxWebhookNewsAdStatus"><?php e__('Show the powered by LeaderOS text') ?></label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="selectWebhookLotteryStatus" class="col-sm-2 col-form-label"><?php e__('Wheel of Fortune') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectWebhookLotteryStatus" class="form-control" name="webhookLotteryStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["webhookLotteryURL"] == '0') ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["webhookLotteryURL"] != '0') ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div id="webhookLotteryOptions" style="<?php echo ($readSettings["webhookLotteryURL"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-row row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label for="inputWebhookLotteryTitle"><?php e__('Title') ?>:</label>
                          <input type="text" name="webhookLotteryTitle" id="inputWebhookLotteryTitle" class="form-control" placeholder="<?php e__('Enter the message title') ?>." value="<?php echo $readSettings["webhookLotteryTitle"]; ?>">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputWebhookLotteryColor"><?php e__('Color') ?>:</label>
                          <div id="colorPicker" id="inputWebhookLotteryColor" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                            <input type="text" class="form-control form-control-appended" name="webhookLotteryColor" placeholder="<?php e__('Enter the color code') ?>." value="<?php echo ($readSettings["webhookLotteryColor"] != null) ? "#".$readSettings["webhookLotteryColor"] : "#000000"; ?>" required>
                            <div class="input-group-append">
                              <div class="input-group-text input-group-addon">
                                <i></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookLotteryURL">Webhook URL:</label>
                      <input type="text" id="inputWebhookLotteryURL" class="form-control" name="webhookLotteryURL" placeholder="https://discordapp.com/api/webhooks/XXXXXXXXXXX/XXXXXXXXXXX" value="<?php echo ($readSettings["webhookLotteryURL"] != '0') ? $readSettings["webhookLotteryURL"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookLotteryImage"><?php e__('Image') ?>:</label>
                      <input type="text" id="inputWebhookLotteryImage" class="form-control" name="webhookLotteryImage" placeholder="<?php e__('Enter a Image URL (You can pass)') ?>" value="<?php echo ($readSettings["webhookLotteryImage"] != '0') ? $readSettings["webhookLotteryImage"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookLotteryMessage"><?php e__('Message') ?>:</label>
                      <textarea id="inputWebhookLotteryMessage" class="form-control" name="webhookLotteryMessage" placeholder="<?php e__('Enter the message') ?>." rows="2"><?php echo ($readSettings["webhookLotteryMessage"] != '0') ? $readSettings["webhookLotteryMessage"] : null; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                      <small class="form-text text-muted"><strong><?php e__('Wheel of Fortune Name') ?>:</strong> %lottery%</small>
                      <small class="form-text text-muted"><strong><?php e__('Award') ?>:</strong> %award%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookLotteryEmbed">Embed:</label>
                      <textarea id="inputWebhookLotteryEmbed" class="form-control" name="webhookLotteryEmbed" placeholder="<?php e__('Enter the embed content') ?>." rows="2"><?php echo $readSettings["webhookLotteryEmbed"]; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                      <small class="form-text text-muted"><strong><?php e__('Wheel of Fortune Name') ?>:</strong> %lottery%</small>
                      <small class="form-text text-muted"><strong><?php e__('Award') ?>:</strong> %award%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="col">
                          <div id="testWebhookLottery">
                            <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                              <span class="sr-only">-/-</span>
                            </div>
                            <a href="javascript:void(0);"><?php e__('Send the test message') ?></a>
                          </div>
                        </div>
                        <div class="col-auto">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="webhookLotteryAdStatus" class="custom-control-input" id="checkboxWebhookLotteryAdStatus" <?php echo ($readSettings["webhookLotteryAdStatus"] == 1) ? 'checked="checked"' : null; ?>>
                            <label class="custom-control-label" for="checkboxWebhookLotteryAdStatus"><?php e__('Show powered by LeaderOS text') ?></label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectWebhookApplicationStatus" class="col-sm-2 col-form-label"><?php e__('Application') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectWebhookApplicationStatus" class="form-control" name="webhookApplicationStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["webhookApplicationURL"] == '0') ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["webhookApplicationURL"] != '0') ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div id="webhookApplicationOptions" style="<?php echo ($readSettings["webhookApplicationURL"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-row row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label for="inputWebhookApplicationTitle"><?php e__('Title') ?>:</label>
                          <input type="text" name="webhookApplicationTitle" id="inputWebhookApplicationTitle" class="form-control" placeholder="<?php e__('Enter the message title') ?>." value="<?php echo $readSettings["webhookApplicationTitle"]; ?>">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputWebhookApplicationColor"><?php e__('Color') ?>:</label>
                          <div id="colorPicker" id="inputWebhookApplicationColor" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                            <input type="text" class="form-control form-control-appended" name="webhookApplicationColor" placeholder="<?php e__('Enter the color code') ?>." value="<?php echo ($readSettings["webhookApplicationColor"] != null) ? "#".$readSettings["webhookApplicationColor"] : "#000000"; ?>" required>
                            <div class="input-group-append">
                              <div class="input-group-text input-group-addon">
                                <i></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookApplicationURL">Webhook URL:</label>
                      <input type="text" id="inputWebhookApplicationURL" class="form-control" name="webhookApplicationURL" placeholder="https://discordapp.com/api/webhooks/XXXXXXXXXXX/XXXXXXXXXXX" value="<?php echo ($readSettings["webhookApplicationURL"] != '0') ? $readSettings["webhookApplicationURL"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookApplicationImage"><?php e__('Image') ?>:</label>
                      <input type="text" id="inputWebhookApplicationImage" class="form-control" name="webhookApplicationImage" placeholder="<?php e__('Enter a Image URL (You can pass)') ?>" value="<?php echo ($readSettings["webhookApplicationImage"] != '0') ? $readSettings["webhookApplicationImage"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookApplicationMessage"><?php e__('Message') ?>:</label>
                      <textarea id="inputWebhookApplicationMessage" class="form-control" name="webhookApplicationMessage" placeholder="<?php e__('Enter the message') ?>." rows="2"><?php echo ($readSettings["webhookApplicationMessage"] != '0') ? $readSettings["webhookApplicationMessage"] : null; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                      <small class="form-text text-muted"><strong><?php e__('Wheel of Fortune Name') ?>:</strong> %lottery%</small>
                      <small class="form-text text-muted"><strong><?php e__('Award') ?>:</strong> %award%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookApplicationEmbed">Embed:</label>
                      <textarea id="inputWebhookApplicationEmbed" class="form-control" name="webhookApplicationEmbed" placeholder="<?php e__('Enter the embed content') ?>." rows="2"><?php echo $readSettings["webhookApplicationEmbed"]; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                      <small class="form-text text-muted"><strong><?php e__('Wheel of Fortune Name') ?>:</strong> %lottery%</small>
                      <small class="form-text text-muted"><strong><?php e__('Award') ?>:</strong> %award%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="col">
                          <div id="testWebhookApplication">
                            <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                              <span class="sr-only">-/-</span>
                            </div>
                            <a href="javascript:void(0);"><?php e__('Send the test message') ?></a>
                          </div>
                        </div>
                        <div class="col-auto">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="webhookApplicationAdStatus" class="custom-control-input" id="checkboxWebhookApplicationAdStatus" <?php echo ($readSettings["webhookApplicationAdStatus"] == 1) ? 'checked="checked"' : null; ?>>
                            <label class="custom-control-label" for="checkboxWebhookApplicationAdStatus"><?php e__('Show powered by LeaderOS text') ?></label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('updateWebhookSettings'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateWebhookSettings"><?php e__('Save Changes') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'language'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Languages') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Settings') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Languages') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php $languages = $db->query("SELECT * FROM Languages"); ?>
          <?php if ($languages->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["languageCode", "languageName"]'>
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <div class="row align-items-center">
                      <div class="col-auto pr-0">
                        <span class="fe fe-search text-muted"></span>
                      </div>
                      <div class="col">
                        <input type="search" class="form-control form-control-flush search" name="search" placeholder="<?php e__('Search') ?>">
                      </div>
                    </div>
                  </div>
                  <div class="col-auto">
                    <a class="btn btn-sm btn-white" href="/dashboard/settings/language/create"><?php e__('Add Language') ?></a>
                  </div>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                    <tr>
                      <th class="text-center" style="width: 40px;">
                        <a href="#" class="text-muted sort" data-sort="languageCode">
                          <?php e__('Code') ?>
                        </a>
                      </th>
                      <th>
                        <a href="#" class="text-muted sort" data-sort="languageName">
                          <?php e__('Name') ?>
                        </a>
                      </th>
                      <th class="text-right">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    <?php foreach ($languages as $readLanguage): ?>
                      <tr>
                        <td class="languageCode text-center" style="width: 40px;">
                          <?php echo $readLanguage["code"]; ?>
                        </td>
                        <td class="languageName">
                          <?php echo $readLanguage["name"]; ?>
                          <?php if ($readSettings["language"] == $readLanguage["code"]): ?>
                            (<?php e__('Default') ?>)
                          <?php else: ?>
                            (<a href="/dashboard/settings/language/set/<?php echo $readLanguage["code"] ?>"><?php e__('Set Default') ?></a>)
                          <?php endif; ?>
                        </td>
                        <td class="text-right">
                          <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/settings/language/edit/<?php echo $readLanguage["code"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                            <i class="fe fe-edit-2"></i>
                          </a>
                          <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/settings/language/delete/<?php echo $readLanguage["code"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                            <i class="fe fe-trash-2"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'insert'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Add Language') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Settings') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/settings/language"><?php e__('Languages') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Add Language') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["insertLanguages"])) {
              if (!$csrf->validate('insertLanguages')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("languageCode") == null || post("languageName") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else {
                $insertLanguages = $db->prepare("INSERT INTO Languages (code, name) VALUES (?, ?)");
                $insertLanguages->execute(array(post("languageCode"), post("languageName")));
                
                if (!file_exists(__ROOT__."/apps/main/private/languages/".post("languageCode").".json")) {
                  $defaultFile = file_get_contents(__ROOT__."/apps/main/private/languages/en.json");
                  file_put_contents(__ROOT__.'/apps/main/private/languages/'.post("languageCode").'.json', $defaultFile);
                }
                
                createLog($readAdmin["id"], "LANGUAGE_ADDED");
                go("/dashboard/settings/language/edit/" . post("languageCode"));
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputLanguageCode" class="col-sm-2 col-form-label"><?php e__('Language Code') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputLanguageCode" name="languageCode" class="form-control" placeholder="<?php e__('For example: en') ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputLanguageName" class="col-sm-2 col-form-label"><?php e__('Language') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputLanguageName" name="languageName" class="form-control" placeholder="<?php e__('For example: English') ?>" required>
                  </div>
                </div>
                <?php echo $csrf->input('insertLanguages'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertLanguages"><?php e__('Add') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'update'): ?>
    <?php
    $language = $db->prepare("SELECT * FROM Languages WHERE code = ?");
    $language->execute(array(get("id")));
    $readLanguage = $language->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Edit Language') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Settings') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/settings/language"><?php e__('Languages') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/settings/language"><?php e__('Edit Language') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($language->rowCount() > 0) ? substr($readLanguage["name"], 0, 30): t__('Not found!'); ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php if ($language->rowCount() > 0): ?>
            <?php
            $languageFile = __ROOT__.'/apps/main/private/languages/'.$readLanguage["code"].'.json';
            $themeLanguageFile = themePath(true).'/private/languages/'.$readLanguage["code"].'.json';
            
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["updateLanguages"])) {
              if (!$csrf->validate('updateLanguages')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("languageName") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else {
                $updateLanguages = $db->prepare("UPDATE Languages SET name = ? WHERE code = ?");
                $updateLanguages->execute(array(post("languageName"), $readLanguage["code"]));
                echo alertSuccess(t__('Changes has been saved successfully!'));
                echo goDelay("/dashboard/settings/language/edit/".$readLanguage["code"], 2);
                
                if (file_exists($languageFile)) {
                  $languageContent = [];
                  foreach ($_POST["languageKeys"] as $key => $value) {
                    $languageContent[$value] = $_POST["languageValues"][$key];
                  }
                  file_put_contents($languageFile, json_encode($languageContent, JSON_PRETTY_PRINT));
                }
                if (file_exists($themeLanguageFile)) {
                  $themeLanguageContent = [];
                  foreach ($_POST["theme_languageKeys"] as $key => $value) {
                    $themeLanguageContent[$value] = $_POST["theme_languageValues"][$key];
                  }
                  file_put_contents($themeLanguageFile, json_encode($themeLanguageContent, JSON_PRETTY_PRINT));
                }
                
                createLog($readAdmin["id"], "LANGUAGE_UPDATED");
              }
            }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputLanguageCode" class="col-sm-2 col-form-label"><?php e__('Language Code') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputLanguageCode" name="languageCode" class="form-control" placeholder="<?php e__('For example: en') ?>" value="<?php echo $readLanguage["code"] ?>" readonly>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputLanguageName" class="col-sm-2 col-form-label"><?php e__('Language') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputLanguageName" name="languageName" class="form-control" placeholder="<?php e__('For example: English') ?>" value="<?php echo $readLanguage["name"] ?>" required>
                    </div>
                  </div>
                  <?php if (file_exists($languageFile)): ?>
                    <?php $languageContent = json_decode(file_get_contents($languageFile), true); ?>
                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                        <tr>
                          <th style="width: 40%"><?php e__('Key') ?></th>
                          <th><?php e__('Value') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($languageContent as $key => $value): ?>
                          <tr>
                            <td><?php echo $key; ?></td>
                            <td>
                              <textarea name="languageKeys[]" class="form-control" required style="display: none;"><?php echo $key ?></textarea>
                              <textarea name="languageValues[]" class="form-control" required><?php echo $value ?></textarea>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                        <?php if (file_exists($themeLanguageFile)): ?>
                          <?php $themeLanguageContent = json_decode(file_get_contents($themeLanguageFile), true); ?>
                          <?php foreach ($themeLanguageContent as $key => $value): ?>
                            <tr>
                              <td><?php echo $key; ?></td>
                              <td>
                                <textarea name="theme_languageKeys[]" class="form-control" required style="display: none;"><?php echo $key ?></textarea>
                                <textarea name="theme_languageValues[]" class="form-control" required><?php echo $value ?></textarea>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php endif; ?>
                  <?php echo $csrf->input('updateLanguages'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <button type="submit" class="btn btn-rounded btn-success" name="updateLanguages"><?php e__('Save Changes') ?></button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
    $deleteLanguage = $db->prepare("DELETE FROM Languages WHERE code = ?");
    $deleteLanguage->execute(array(get("id")));
    go("/dashboard/settings/language");
    ?>
  <?php elseif (get("action") == 'set' && get("id")): ?>
    <?php
    $setLanguage = $db->prepare("UPDATE Settings SET language = ? WHERE id = ?");
    $setLanguage->execute(array(get("id"), $readSettings["id"]));
    go("/dashboard/settings/language");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
