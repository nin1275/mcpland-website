<?php
  if (!checkPerm($readAdmin, 'MANAGE_SETTINGS')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
?>
<?php if (get("target") == 'module'): ?>
  <?php if (get("action") == 'update'): ?>
    <?php
      $modules = $db->query("SELECT * FROM Modules");
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Modules') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Modules') ?></li>
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
            if (isset($_POST["updateModules"])) {
              if (!$csrf->validate('updateModules')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
                echo goDelay("/dashboard/modules", 2);
              }
              else {
                foreach ($_POST as $key => $value) {
                  $fields = explode('_', $key);
                  if (isset($fields[1])) {
                    $updateModules = $db->prepare("UPDATE Modules SET $fields[1] = ? WHERE id = ?");
                    $updateModules->execute(array(post($key), $fields[0]));
                  }
                }
                echo alertSuccess(t__('Changes has been saved successfully!'));
                echo goDelay("/dashboard/modules", 2);
                
                createLog($readAdmin["id"], "MODULES_UPDATED");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <?php foreach ($modules as $readModules): ?>
                  <div class="form-group row">
                    <label for="inputStatus_<?php echo $readModules["id"]; ?>" class="col-sm-2 col-form-label"><?php e__($readModules["name"]) ?>:</label>
                    <div class="col-sm-10">
                      <select class="form-control" name="<?php echo $readModules["id"]; ?>_isEnabled" id="inputStatus_<?php echo $readModules["id"]; ?>" data-toggle="select" data-minimum-results-for-search="-1" required>
                        <option value="0" <?php echo ($readModules["isEnabled"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Disabled') ?></option>
                        <option value="1" <?php echo ($readModules["isEnabled"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Enabled') ?></option>
                      </select>
                    </div>
                  </div>
                <?php endforeach; ?>
                
                <?php echo $csrf->input('updateModules'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateModules"><?php e__('Save Changes') ?></button>
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
                
                echo alertSuccess(t__('Language has been added successfully!'));
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
            <div class="alert alert-warning" style="color: #000!important;">
              <?php e__('You can translate your file from <strong>/apps/main/private/languages/%code%.json</strong>', ['%code%' => $readLanguage["code"]]); ?>
            </div>
            <?php
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
