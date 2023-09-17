<?php
  if (!checkPerm($readAdmin, 'MANAGE_PAYMENT')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  if (get("target") == 'payment' && (get("action") == 'insert') || (get("action") == 'update' && get("id"))) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/payment.js');
  }
  if (get("target") == 'settings' && (get("action") == 'update' && get("id"))) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/payment.settings.js');
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/leaderboards.js');
  }
?>
<?php if (get("target") == 'payment'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Payment Method') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Payment Method') ?></li>
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
          <?php $payment = $db->query("SELECT P.*, PS.name as apiName, PS.status as apiStatus FROM Payment P INNER JOIN PaymentSettings PS ON P.apiID = PS.slug ORDER BY P.id DESC"); ?>
          <?php if ($payment->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["paymentID", "paymentTitle", "paymentAPI", "paymentType"]'>
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
                    <a class="btn btn-sm btn-white" href="/dashboard/payment/create"><?php e__('Add Payment Method') ?></a>
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
                          <a href="#" class="text-muted sort" data-sort="paymentID">#ID</a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="paymentTitle"><?php e__('Title') ?></a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="paymentAPI"><?php e__('Payment Gateway') ?></a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="paymentType"><?php e__('Type') ?></a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($payment as $readPayment): ?>
                        <tr>
                          <td class="paymentID text-center" style="width: 40px;">
                            <a href="/dashboard/payment/edit/<?php echo $readPayment["id"]; ?>">
                              #<?php echo $readPayment["id"]; ?>
                            </a>
                          </td>
                          <td class="paymentTitle">
                            <a href="/dashboard/payment/edit/<?php echo $readPayment["id"]; ?>">
                              <?php echo $readPayment["title"]; ?>
                            </a>
                          </td>
                          <td class="paymentAPI">
                            <?php if ($readPayment["apiStatus"] == 0): ?>
                              <span class="text-danger">●</span>
                            <?php else: ?>
                              <span class="text-success">●</span>
                            <?php endif; ?>
                            <?php echo $readPayment["apiName"]; ?>
                          </td>
                          <td class="paymentType">
                            <?php echo (($readPayment["type"] == 1) ? t__('Mobile Payment') : (($readPayment["type"] == 2) ? t__('Credit Card') : (($readPayment["type"] == 3) ? t__('EFT') : t__('Error')))); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/payment/edit/<?php echo $readPayment["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/payment/delete/<?php echo $readPayment["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
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
                  <h2 class="header-title"><?php e__('Add Payment') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/payment"><?php e__('Payment Method') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Add') ?></li>
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
            if (isset($_POST["insertPayment"])) {
              if (!$csrf->validate('insertPayment')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("title") == null || post("apiID") == null || post("type") == null || post("bonusCreditStatus") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else {
                if (post("bonusCreditStatus") == 0) {
                  $_POST["bonusCredit"] = '0';
                  $_POST["bonusCreditMinAmount"] = '0';
                }
  
                $insertPayments = $db->prepare("INSERT INTO Payment (title, apiID, type, bonusCredit, bonusCreditMinAmount) VALUES (?, ?, ?, ?, ?)");
                $insertPayments->execute(array(post("title"), post("apiID"), post("type"), post("bonusCredit"), post("bonusCreditMinAmount")));
                echo alertSuccess(t__('Payment method has been added successfully!'));
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                    <label for="inputTitle" class="col-sm-2 col-form-label"><?php e__('Title') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="<?php e__('Enter the title') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectAPIID" class="col-sm-2 col-form-label"><?php e__('Payment Gateway') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectAPIID" class="form-control" name="apiID" data-toggle="select" data-minimum-results-for-search="-1">
                      <?php $paymentSettings = $db->query("SELECT name, slug FROM PaymentSettings ORDER BY slug ASC"); ?>
                      <?php if ($paymentSettings->rowCount() > 0): ?>
                        <?php foreach ($paymentSettings as $readPaymentSettings): ?>
                          <option value="<?php echo $readPaymentSettings["slug"]; ?>"><?php echo $readPaymentSettings["name"]; ?></option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectType" class="col-sm-2 col-form-label"><?php e__('Type') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectType" class="form-control" name="type" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="2"><?php e__('Credit Card') ?></option>
                      <option value="1"><?php e__('Mobile Payment') ?></option>
                      <option value="3" disabled="disabled"><?php e__('EFT') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectBonusCreditStatus" class="col-sm-2 col-form-label"><?php e__('Bonus Credit') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectBonusCreditStatus" class="form-control" name="bonusCreditStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0"><?php e__('Disable') ?></option>
                      <option value="1"><?php e__('Enable') ?></option>
                    </select>
                  </div>
                </div>
                <div id="bonusCreditOptions" style="display: none;">
                  <div class="form-group row">
                    <label for="inputBonusCredit" class="col-sm-2 col-form-label"><?php e__('Bonus Credit Percentage (%)') ?>:</label>
                    <div class="col-sm-10">
                      <input type="number" id="inputBonusCredit" class="form-control" name="bonusCredit" placeholder="<?php e__('Enter the percentage (%) of extra credit to be given to the user.') ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputBonusCreditMinAmount" class="col-sm-2 col-form-label"><?php e__('Min Payment Amount for Bonus Credit') ?>:</label>
                    <div class="col-sm-10">
                      <input type="number" id="inputBonusCreditMinAmount" class="form-control" name="bonusCreditMinAmount" placeholder="<?php e__('You can give the bonus credit for X credits or more. Type 0 for all') ?>">
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('insertPayment'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertPayment"><?php e__('Add') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'update' && get("id")): ?>
    <?php
      $payment = $db->prepare("SELECT * FROM Payment WHERE id = ?");
      $payment->execute(array(get("id")));
      $readPayment = $payment->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Edit Payment Method') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/payment"><?php e__('Payment Method') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/payment"><?php e__('Edit') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($payment->rowCount() > 0) ? $readPayment["title"] : t__('Not found!'); ?></li>
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
          <?php if ($payment->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updatePayment"])) {
                if (!$csrf->validate('updatePayment')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if (post("title") == null || post("apiID") == null || post("type") == null) {
                  echo alertError(t__('Please fill all the fields!'));
                }
                else {
                  if (post("bonusCreditStatus") == 0) {
                    $_POST["bonusCredit"] = '0';
                    $_POST["bonusCreditMinAmount"] = '0';
                  }
  
                  $updatePayments = $db->prepare("UPDATE Payment SET title = ?, apiID = ?, type = ?, bonusCredit = ?, bonusCreditMinAmount = ? WHERE id = ?");
                  $updatePayments->execute(array(post("title"), post("apiID"), post("type"), post("bonusCredit"), post("bonusCreditMinAmount"), get("id")));
                  echo alertSuccess(t__('Changes has been saved successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                      <label for="inputTitle" class="col-sm-2 col-form-label"><?php e__('Title') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="<?php e__('Enter the title') ?>." value="<?php echo $readPayment["title"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectAPIID" class="col-sm-2 col-form-label"><?php e__('Payment Gateway') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectAPIID" class="form-control" name="apiID" data-toggle="select" data-minimum-results-for-search="-1">
                        <?php $paymentSettings = $db->query("SELECT name, slug FROM PaymentSettings ORDER BY slug ASC"); ?>
                        <?php if ($paymentSettings->rowCount() > 0): ?>
                          <?php foreach ($paymentSettings as $readPaymentSettings): ?>
                            <option value="<?php echo $readPaymentSettings["slug"]; ?>" <?php echo ($readPaymentSettings["slug"] == $readPayment["apiID"]) ? 'selected="selected"' : null; ?>><?php echo $readPaymentSettings["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectType" class="col-sm-2 col-form-label"><?php e__('Type') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectType" class="form-control" name="type" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="1" <?php echo ($readPayment["type"] == 1) ? 'selected="selected"' : null; ?> <?php echo ($readPayment["apiID"] == "shopier" || $readPayment["apiID"] == "ininal" || $readPayment["apiID"] == "papara" || $readPayment["apiID"] == "eft" || $readPayment["apiID"] == "paytr") ? "disabled" : null; ?>><?php e__('Mobile Payment') ?></option>
                        <option value="2" <?php echo ($readPayment["type"] == 2) ? 'selected="selected"' : null; ?> <?php echo ($readPayment["apiID"] == "eft" || $readPayment["apiID"] == "slimmweb") ? "disabled" : null; ?>><?php e__('Credit Card') ?></option>
                        <option value="3" <?php echo ($readPayment["type"] == 3) ? 'selected="selected"' : null; ?> <?php echo ($readPayment["apiID"] != "paywant" && $readPayment["apiID"] != "shipy" && $readPayment["apiID"] != "eft") ? "disabled" : null; ?>><?php e__('EFT') ?></option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectBonusCreditStatus" class="col-sm-2 col-form-label"><?php e__('Bonus Credit') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectBonusCreditStatus" class="form-control" name="bonusCreditStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readPayment["bonusCredit"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                        <option value="1" <?php echo ($readPayment["bonusCredit"] != 0) ? 'selected="selected"' : null; ?>><?php e__('Enable') ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="bonusCreditOptions" style="<?php echo ($readPayment["bonusCredit"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <label for="inputBonusCredit" class="col-sm-2 col-form-label"><?php e__('Bonus Credit Percentage (%)') ?>:</label>
                      <div class="col-sm-10">
                        <input type="number" id="inputBonusCredit" class="form-control" name="bonusCredit" placeholder="<?php e__('Enter the percentage (%) of extra credit to be given to the user.') ?>" value="<?php echo ($readPayment["bonusCredit"] != 0) ? $readPayment["bonusCredit"] : null; ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputBonusCreditMinAmount" class="col-sm-2 col-form-label"><?php e__('Min Payment Amount for Bonus Credit') ?>:</label>
                      <div class="col-sm-10">
                        <input type="number" id="inputBonusCreditMinAmount" class="form-control" name="bonusCreditMinAmount" placeholder="<?php e__('You can give the bonus credit for X credits or more. Type 0 for all') ?>" value="<?php echo $readPayment["bonusCreditMinAmount"] == 0 ? null : $readPayment["bonusCreditMinAmount"]; ?>">
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updatePayment'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/payment/delete/<?php echo $readPayment["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updatePayment"><?php e__('Save Changes') ?></button>
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
      $deletePayment = $db->prepare("DELETE FROM Payment WHERE id = ?");
      $deletePayment->execute(array(get("id")));
      go("/dashboard/payment");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'settings'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Payment Gateways') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Payment Gateways') ?></li>
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
          <?php $paymentSettings = $db->query("SELECT * FROM PaymentSettings ORDER BY slug ASC"); ?>
          <?php if ($paymentSettings->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["paymentSettingsName", "paymentSettingsStatus"]'>
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
                        <th>
                          <a href="#" class="text-muted sort" data-sort="paymentSettingsName"><?php e__('Gateway') ?></a>
                        </th>
                        <th class="text-center">
                          <a href="#" class="text-muted sort" data-sort="paymentSettingsStatus"><?php e__('Status') ?></a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($paymentSettings as $readPaymentSettings): ?>
                        <tr>
                          <td class="paymentSettingsName">
                            <a href="/dashboard/payment/settings/edit/<?php echo $readPaymentSettings["slug"]; ?>">
                              <?php echo $readPaymentSettings["name"]; ?>
                            </a>
                          </td>
                          <td class="paymentSettingsStatus text-center">
                            <?php if ($readPaymentSettings["status"] == 0): ?>
                              <span class="badge badge-pill badge-soft-danger"><?php e__('Disabled') ?></span>
                            <?php else: ?>
                              <span class="badge badge-pill badge-soft-success"><?php e__('Active') ?></span>
                            <?php endif; ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/payment/settings/edit/<?php echo $readPaymentSettings["slug"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                              <i class="fe fe-edit-2"></i>
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
  <?php elseif (get("action") == 'update' && get("id")): ?>
    <?php
      $paymentSettings = $db->prepare("SELECT * FROM PaymentSettings WHERE slug = ?");
      $paymentSettings->execute(array(get("id")));
      $readPaymentSettings = $paymentSettings->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Payment Gateways Settings') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/payment/settings"><?php e__('Payment Gateways') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/payment/settings"><?php e__('Edit') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($paymentSettings->rowCount() > 0) ? $readPaymentSettings["name"] : t__('Not found!'); ?></li>
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
          <?php if ($paymentSettings->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              $readVariables = json_decode($readPaymentSettings["variables"], true);
              if (isset($_POST["updatePaymentSettings"])) {
                if (!$csrf->validate('updatePaymentSettings')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else {
                  if ($readPaymentSettings["slug"] == "batihost") {
                    $variablesArray = array(
                      "batihostID"          => post("batihostID"),
                      "batihostEmail"       => post("batihostEmail"),
                      "batihostToken"       => post("batihostToken")
                    );
                  }
                  else if ($readPaymentSettings["slug"] == "paywant") {
                    $variablesArray = array(
                      "paywantAPIKey"         => post("paywantAPIKey"),
                      "paywantAPISecretKey"   => post("paywantAPISecretKey"),
                      "paywantCommissionType" => post("paywantCommissionType")
                    );
                  }
                  else if ($readPaymentSettings["slug"] == "rabisu") {
                    $variablesArray = array(
                      "rabisuID"        => post("rabisuID"),
                      "rabisuToken"     => post("rabisuToken")
                    );
                  }
                  else if ($readPaymentSettings["slug"] == "shopier") {
                    $variablesArray = array(
                      "shopierAPIKey"         => post("shopierAPIKey"),
                      "shopierAPISecretKey"   => post("shopierAPISecretKey")
                    );
                  }
                  else if ($readPaymentSettings["slug"] == "keyubu") {
                    $variablesArray = array(
                      "keyubuID"    => post("keyubuID"),
                      "keyubuToken" => post("keyubuToken")
                    );
                  }
                  else if ($readPaymentSettings["slug"] == "ininal") {
                    $variablesArray = array(
                      "ininalBarcodes" => $_POST["ininalBarcodes"]
                    );
                  }
                  else if ($readPaymentSettings["slug"] == "papara") {
                    $variablesArray = array(
                      "paparaNumbers" => $_POST["paparaNumbers"]
                    );
                  }
                  else if ($readPaymentSettings["slug"] == "shipy") {
                    $variablesArray = array(
                      "shipyAPIKey" => post("shipyAPIKey")
                    );
                  }
                  else if ($readPaymentSettings["slug"] == "eft") {
                    $variablesArray = array(
                      "bankAccounts" => array()
                    );
                    foreach ($_POST["eftFullName"] as $key => $value) {
                      if ($value != "") {
                        array_push($variablesArray["bankAccounts"], array(
                          "fullName"  => $_POST["eftFullName"][$key],
                          "bankName"  => $_POST["eftBankName"][$key],
                          "iban"      => $_POST["eftIBAN"][$key]
                        ));
                      }
                    }
                  }
                  else if ($readPaymentSettings["slug"] == "slimmweb") {
                    $variablesArray = array(
                      "slimmwebPaymentID" => post("slimmwebPaymentID"),
                      "slimmwebToken"     => post("slimmwebToken")
                    );
                  }
                  else if ($readPaymentSettings["slug"] == "paytr") {
                    $variablesArray = array(
                      "paytrID"           => post("paytrID"),
                      "paytrAPIKey"       => post("paytrAPIKey"),
                      "paytrAPISecretKey" => post("paytrAPISecretKey")
                    );
                  }
                  else if ($readPaymentSettings["slug"] == "stripe") {
                    $variablesArray = array(
                      "stripeAPIKey"       => post("stripeAPIKey"),
                      "stripeAPISecretKey" => post("stripeAPISecretKey"),
                      "stripeWebhookKey" => post("stripeWebhookKey")
                    );
                  }
                  else if ($readPaymentSettings["slug"] == "paypal") {
                    $variablesArray = array(
                      "paypalEmail"        => post("paypalEmail"),
                      "paypalSandbox" => post("paypalSandbox")
                    );
                  }
                  else {
                    echo alertError(t__('No payment gateway found!'));
                  }

                  $variablesJSON = json_encode($variablesArray);
                  $updatePaymentSettings = $db->prepare("UPDATE PaymentSettings SET status = ?, variables = ? WHERE slug = ?");
                  $updatePaymentSettings->execute(array(post("status"), $variablesJSON, get("id")));
                  echo alertSuccess(t__('Changes has been saved successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                      <label for="inputName" class="col-sm-2 col-form-label"><?php e__('Gateway') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputName" class="form-control" value="<?php echo $readPaymentSettings["name"]; ?>" readonly="readonly">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectPaymentSettingsStatus" class="col-sm-2 col-form-label"><?php e__('Status') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectPaymentSettingsStatus" class="form-control" name="status" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readPaymentSettings["status"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Disabled') ?></option>
                        <option value="1" <?php echo ($readPaymentSettings["status"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="paymentSettingsBlock" style="<?php echo ($readPaymentSettings["status"] == 0) ? 'display: none;' : 'display: block;'; ?>">
                    <?php if ($readPaymentSettings["slug"] == "batihost"): ?>
                      <div class="form-group row">
                        <label for="inputBatihostID" class="col-sm-2 col-form-label">Batihost ID:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputBatihostID" class="form-control" name="batihostID" placeholder="Batihost hesabınıza ait ID'yi yazınız." value="<?php echo (isset($readVariables["batihostID"])) ? $readVariables["batihostID"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputBatihostEmail" class="col-sm-2 col-form-label">Batihost Email:</label>
                        <div class="col-sm-10">
                          <input type="email" id="inputBatihostEmail" class="form-control" name="batihostEmail" placeholder="Batihost hesabınıza ait E-Posta adresini yazınız." value="<?php echo (isset($readVariables["batihostEmail"])) ? $readVariables["batihostEmail"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputBatihostToken" class="col-sm-2 col-form-label">Batihost Token:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputBatihostToken" class="form-control" name="batihostToken" placeholder="Batihost'dan aldığınız Tokeni yazınız." value="<?php echo (isset($readVariables["batihostToken"])) ? $readVariables["batihostToken"] : null; ?>">
                        </div>
                      </div>
                    <?php elseif ($readPaymentSettings["slug"] == "paywant"): ?>
                      <div class="form-group row">
                        <label for="inputPaywantAPIKey" class="col-sm-2 col-form-label">Paywant API Key:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputPaywantAPIKey" class="form-control" name="paywantAPIKey" placeholder="Paywant'dan aldığınız API Key'i yazınız." value="<?php echo (isset($readVariables["paywantAPIKey"])) ? $readVariables["paywantAPIKey"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputPaywantAPISecretKey" class="col-sm-2 col-form-label">Paywant API Secret Key:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputPaywantAPISecretKey" class="form-control" name="paywantAPISecretKey" placeholder="Paywant'dan aldığınız API Secret Key'i yazınız." value="<?php echo (isset($readVariables["paywantAPISecretKey"])) ? $readVariables["paywantAPISecretKey"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="selectCommissionType" class="col-sm-2 col-form-label">Komisyon:</label>
                        <div class="col-sm-10">
                          <select id="selectCommissionType" class="form-control" name="paywantCommissionType" data-toggle="select" data-minimum-results-for-search="-1">
                            <option value="1" <?php echo (isset($readVariables["paywantCommissionType"]) && $readVariables["paywantCommissionType"] == '1') ? 'selected="selected"' : null; ?>>Üstlen</option>
                            <option value="2" <?php echo (isset($readVariables["paywantCommissionType"]) && $readVariables["paywantCommissionType"] == '2') ? 'selected="selected"' : null; ?>>Kullanıcıya Yansıt</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="staticCallback" class="col-sm-2 col-form-label">Geri Dönüş URL (Callback):</label>
                        <div class="col-sm-10">
                        	<?php $paywantCallback = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"].'/callback/paywant'); ?>
                        	<span id="staticCallback" class="form-control-plaintext" rel="external"><?php echo $paywantCallback; ?></span>
                        </div>
                      </div>
                    <?php elseif ($readPaymentSettings["slug"] == "rabisu"): ?>
                      <div class="form-group row">
                        <label for="inputRabisuID" class="col-sm-2 col-form-label">Rabisu ID:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputRabisuID" class="form-control" name="rabisuID" placeholder="Rabisu'dan aldığınız ID'yi yazınız." value="<?php echo (isset($readVariables["rabisuID"])) ? $readVariables["rabisuID"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputRabisuToken" class="col-sm-2 col-form-label">Rabisu Token:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputRabisuToken" class="form-control" name="rabisuToken" placeholder="Rabisu'dan aldığınız Token'i yazınız." value="<?php echo (isset($readVariables["rabisuToken"])) ? $readVariables["rabisuToken"] : null; ?>">
                        </div>
                      </div>
                    <?php elseif ($readPaymentSettings["slug"] == "shopier"): ?>
                      <div class="form-group row">
                        <label for="inputShopierAPIKey" class="col-sm-2 col-form-label">Shopier API Kullanıcı:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputShopierAPIKey" class="form-control" name="shopierAPIKey" placeholder="Shopier'den aldığınız API Kullanıcı bilgisini yazınız." value="<?php echo (isset($readVariables["shopierAPIKey"])) ? $readVariables["shopierAPIKey"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputShopierAPISecretKey" class="col-sm-2 col-form-label">Shopier API Şifre:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputShopierAPISecretKey" class="form-control" name="shopierAPISecretKey" placeholder="Shopier'den aldığınız API Şifre bilgisini yazınız." value="<?php echo (isset($readVariables["shopierAPISecretKey"])) ? $readVariables["shopierAPISecretKey"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="staticCallback" class="col-sm-2 col-form-label">Geri Dönüş URL (Callback):</label>
                        <div class="col-sm-10">
                        	<?php $shopierCallback = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"].'/callback/shopier'); ?>
                        	<span id="staticCallback" class="form-control-plaintext" rel="external"><?php echo $shopierCallback; ?></span>
                        </div>
                      </div>
                    <?php elseif ($readPaymentSettings["slug"] == "keyubu"): ?>
                      <div class="form-group row">
                        <label for="inputKeyubuID" class="col-sm-2 col-form-label">Keyubu ID:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputKeyubuID" class="form-control" name="keyubuID" placeholder="Keyubu'dan aldığınız ID'yi yazınız." value="<?php echo (isset($readVariables["keyubuID"])) ? $readVariables["keyubuID"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputKeyubuToken" class="col-sm-2 col-form-label">Keyubu Token:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputKeyubuToken" class="form-control" name="keyubuToken" placeholder="Keyubu'dan aldığınız Token'i yazınız." value="<?php echo (isset($readVariables["keyubuToken"])) ? $readVariables["keyubuToken"] : null; ?>">
                        </div>
                      </div>
                    <?php elseif ($readPaymentSettings["slug"] == "ininal"): ?>
                      <div class="form-group row">
                        <label for="inputTable" class="col-sm-2 col-form-label">Ininal Barkodları:</label>
                        <div class="col-sm-10">
                          <div class="table-responsive">
                            <table id="tableitems" class="table table-sm table-hover table-nowrap array-table">
                              <thead>
                                <tr>
                                  <th>Ininal Barkod NO</th>
                                  <th class="text-center pt-0 pb-0 align-middle">
                                    <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                                      <i class="fe fe-plus"></i>
                                    </button>
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php if (count(array_filter($readVariables["ininalBarcodes"]))): ?>
                                  <?php foreach ($readVariables["ininalBarcodes"] as $ininalBarcode): ?>
                                    <tr>
                                      <td>
                                        <input type="text" class="form-control" name="ininalBarcodes[]" placeholder="Ödeme sayfasında gözükecek Ininal Barkod NO'yu yazınız." value="<?php echo $ininalBarcode; ?>">
                                      </td>
                                      <td class="text-center align-middle" style="">
                                        <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                          <i class="fe fe-trash-2"></i>
                                        </button>
                                      </td>
                                    </tr>
                                  <?php endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                    <td>
                                      <input type="text" class="form-control" name="ininalBarcodes[]" placeholder="Ödeme sayfasında gözükecek Ininal Barkod NO'yu yazınız.">
                                    </td>
                                    <td class="text-center align-middle" style="">
                                      <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                        <i class="fe fe-trash-2"></i>
                                      </button>
                                    </td>
                                  </tr>
                                <?php endif; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    <?php elseif ($readPaymentSettings["slug"] == "papara"): ?>
                      <div class="form-group row">
                        <label for="inputTable" class="col-sm-2 col-form-label">Papara Numaraları:</label>
                        <div class="col-sm-10">
                          <div class="table-responsive">
                            <table id="tableitems" class="table table-sm table-hover table-nowrap array-table">
                              <thead>
                                <tr>
                                  <th>Papara NO</th>
                                  <th class="text-center pt-0 pb-0 align-middle">
                                    <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                                      <i class="fe fe-plus"></i>
                                    </button>
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php if (count(array_filter($readVariables["paparaNumbers"]))): ?>
                                  <?php foreach ($readVariables["paparaNumbers"] as $paparaNumber): ?>
                                    <tr>
                                      <td>
                                        <input type="text" class="form-control" name="paparaNumbers[]" placeholder="Ödeme sayfasında gözükecek Papara NO'yu yazınız." value="<?php echo $paparaNumber; ?>">
                                      </td>
                                      <td class="text-center align-middle" style="">
                                        <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                          <i class="fe fe-trash-2"></i>
                                        </button>
                                      </td>
                                    </tr>
                                  <?php endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                    <td>
                                      <input type="text" class="form-control" name="paparaNumbers[]" placeholder="Ödeme sayfasında gözükecek Papara NO'yu yazınız.">
                                    </td>
                                    <td class="text-center align-middle" style="">
                                      <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                        <i class="fe fe-trash-2"></i>
                                      </button>
                                    </td>
                                  </tr>
                                <?php endif; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    <?php elseif ($readPaymentSettings["slug"] == "shipy"): ?>
                      <div class="form-group row">
                        <label for="inputShipyAPIKey" class="col-sm-2 col-form-label">Shipy API Key:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputShipyAPIKey" class="form-control" name="shipyAPIKey" placeholder="Shipy'den aldığınız API Key'i yazınız." value="<?php echo (isset($readVariables["shipyAPIKey"])) ? $readVariables["shipyAPIKey"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="staticSuccess" class="col-sm-2 col-form-label">Başarılı URL:</label>
                        <div class="col-sm-10">
                        	<?php $shipySuccess = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"].'/credit/buy/success'); ?>
                        	<span id="staticSuccess" class="form-control-plaintext" rel="external"><?php echo $shipySuccess; ?></span>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="staticError" class="col-sm-2 col-form-label">Başarısız URL:</label>
                        <div class="col-sm-10">
                        	<?php $shipyError = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"].'/credit/buy/error'); ?>
                        	<span id="staticError" class="form-control-plaintext" rel="external"><?php echo $shipyError; ?></span>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="staticCallback" class="col-sm-2 col-form-label">Geri Dönüş URL (Callback):</label>
                        <div class="col-sm-10">
                        	<?php $shipyCallback = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"].'/callback/shipy'); ?>
                        	<span id="staticCallback" class="form-control-plaintext" rel="external"><?php echo $shipyCallback; ?></span>
                        </div>
                      </div>
                    <?php elseif ($readPaymentSettings["slug"] == "eft"): ?>
                      <div class="form-group row">
                        <label for="inputTable" class="col-sm-2 col-form-label">Banka Hesapları:</label>
                        <div class="col-sm-10">
                          <div class="table-responsive">
                            <table id="tableitems" class="table table-sm table-hover table-nowrap array-table">
                              <thead>
                                <tr>
                                  <th>Ad Soyad</th>
                                  <th>Banka Adı</th>
                                  <th>IBAN</th>
                                  <th class="text-center pt-0 pb-0 align-middle">
                                    <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                                      <i class="fe fe-plus"></i>
                                    </button>
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php if (count(array_filter($readVariables["bankAccounts"]))): ?>
                                  <?php foreach ($readVariables["bankAccounts"] as $bankAccount): ?>
                                    <tr>
                                      <td>
                                        <input type="text" class="form-control" name="eftFullName[]" placeholder="Ödeme sayfasında gözükecek olan Ad Soyad'ı yazınız." value="<?php echo $bankAccount["fullName"]; ?>">
                                      </td>
                                      <td>
                                        <input type="text" class="form-control" name="eftBankName[]" placeholder="Ödeme sayfasında gözükecek olan banka adını yazınız." value="<?php echo $bankAccount["bankName"]; ?>">
                                      </td>
                                      <td>
                                        <input type="text" class="form-control" name="eftIBAN[]" placeholder="Ödeme sayfasında gözükecek olan IBAN'ı yazınız." value="<?php echo $bankAccount["iban"]; ?>">
                                      </td>
                                      <td class="text-center align-middle" style="">
                                        <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                          <i class="fe fe-trash-2"></i>
                                        </button>
                                      </td>
                                    </tr>
                                  <?php endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                    <td>
                                      <input type="text" class="form-control" name="eftFullName[]" placeholder="Ödeme sayfasında gözükecek olan Ad Soyad'ı yazınız.">
                                    </td>
                                    <td>
                                      <input type="text" class="form-control" name="eftBankName[]" placeholder="Ödeme sayfasında gözükecek olan banka adını yazınız.">
                                    </td>
                                    <td>
                                      <input type="text" class="form-control" name="eftIBAN[]" placeholder="Ödeme sayfasında gözükecek olan IBAN'ı yazınız.">
                                    </td>
                                    <td class="text-center align-middle" style="">
                                      <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                        <i class="fe fe-trash-2"></i>
                                      </button>
                                    </td>
                                  </tr>
                                <?php endif; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    <?php elseif ($readPaymentSettings["slug"] == "slimmweb"): ?>
                      <div class="form-group row">
                        <label for="inputSlimmwebID" class="col-sm-2 col-form-label">Ödeme ID:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputSlimmwebID" class="form-control" name="slimmwebPaymentID" placeholder="SlimmWeb'den aldığınız Ödeme ID'yi yazınız." value="<?php echo (isset($readVariables["slimmwebPaymentID"])) ? $readVariables["slimmwebPaymentID"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputSlimmwebToken" class="col-sm-2 col-form-label">SlimmWeb Token:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputSlimmwebToken" class="form-control" name="slimmwebToken" placeholder="SlimmWeb'den aldığınız Token'i yazınız." value="<?php echo (isset($readVariables["slimmwebToken"])) ? $readVariables["slimmwebToken"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="staticCallback" class="col-sm-2 col-form-label">Geri Dönüş URL (Callback):</label>
                        <div class="col-sm-10">
                          <?php $slimmwebCallback = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"].'/callback/slimmweb'); ?>
                          <span id="staticCallback" class="form-control-plaintext" rel="external"><?php echo $slimmwebCallback; ?></span>
                        </div>
                      </div>
                    <?php elseif ($readPaymentSettings["slug"] == "paytr"): ?>
                      <div class="form-group row">
                        <label for="inputPaytrID" class="col-sm-2 col-form-label">PayTR Mağaza No:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputPaytrID" class="form-control" name="paytrID" placeholder="PayTR'den aldığınız Mağaza No bilgisini yazınız." value="<?php echo (isset($readVariables["paytrID"])) ? $readVariables["paytrID"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputPaytrAPIKey" class="col-sm-2 col-form-label">PayTR Mağaza Parola:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputPaytrAPIKey" class="form-control" name="paytrAPIKey" placeholder="PayTR'den aldığınız Mağaza Parola bilgisini yazınız." value="<?php echo (isset($readVariables["paytrAPIKey"])) ? $readVariables["paytrAPIKey"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputPaytrAPISecretKey" class="col-sm-2 col-form-label">PayTR Mağaza Gizli Anahtar:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputPaytrAPISecretKey" class="form-control" name="paytrAPISecretKey" placeholder="PayTR'den aldığınız Mağaza Gizli Anahtar bilgisini yazınız." value="<?php echo (isset($readVariables["paytrAPISecretKey"])) ? $readVariables["paytrAPISecretKey"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="staticCallback" class="col-sm-2 col-form-label">Bildirim URL (Callback):</label>
                        <div class="col-sm-10">
                        	<?php $paytrCallback = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"].'/callback/paytr'); ?>
                        	<span id="staticCallback" class="form-control-plaintext" rel="external"><?php echo $paytrCallback; ?></span>
                        </div>
                      </div>
                    <?php elseif ($readPaymentSettings["slug"] == "stripe"): ?>
                      <div class="form-group row">
                        <label for="inputStripeAPIKey" class="col-sm-2 col-form-label">Stripe Publishable Key:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputStripeAPIKey" class="form-control" name="stripeAPIKey" placeholder="<?php e__('Enter the Publishable Key you get from Stripe') ?>." value="<?php echo (isset($readVariables["stripeAPIKey"])) ? $readVariables["stripeAPIKey"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputStripeAPISecretKey" class="col-sm-2 col-form-label">Stripe Secret Key:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputStripeAPISecretKey" class="form-control" name="stripeAPISecretKey" placeholder="<?php e__('Enter the Secret Key you get from Stripe') ?>." value="<?php echo (isset($readVariables["stripeAPISecretKey"])) ? $readVariables["stripeAPISecretKey"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputStripeWebhookKey" class="col-sm-2 col-form-label">Stripe Webhook Key:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputStripeWebhookKey" class="form-control" name="stripeWebhookKey" placeholder="<?php e__('Enter the Webhook Key you get from Stripe') ?>." value="<?php echo (isset($readVariables["stripeWebhookKey"])) ? $readVariables["stripeWebhookKey"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="staticCallback" class="col-sm-2 col-form-label">Webhook URL:</label>
                        <div class="col-sm-10">
                          <?php $stripeCallback = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"].'/callback/stripe'); ?>
                          <span id="staticCallback" class="form-control-plaintext" rel="external"><?php echo $stripeCallback; ?></span>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="staticEvent" class="col-sm-2 col-form-label">Webhook Event:</label>
                        <div class="col-sm-10">
                          <span id="staticEvent" class="form-control-plaintext" rel="external">checkout.session.completed</span>
                        </div>
                      </div>
                    <?php elseif ($readPaymentSettings["slug"] == "paypal"): ?>
                      <div class="form-group row">
                        <label for="inputPaypalEmail" class="col-sm-2 col-form-label">Paypal Email:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputPaypalEmail" class="form-control" name="paypalEmail" placeholder="<?php e__('Enter the Secret Key you get from PayPal') ?>." value="<?php echo (isset($readVariables["paypalEmail"])) ? $readVariables["paypalEmail"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputPaypalSandbox" class="col-sm-2 col-form-label">Paypal Sandbox:</label>
                        <div class="col-sm-10">
                          <select name="paypalSandbox" id="inputPaypalSandbox" class="form-control">
                            <option value="false" <?php echo (isset($readVariables["paypalSandbox"]) && $readVariables["paypalSandbox"] == "false" ? "selected" : null); ?>><?php e__('Disable') ?></option>
                            <option value="true" <?php echo (isset($readVariables["paypalSandbox"]) && $readVariables["paypalSandbox"] == "true" ? "selected" : null); ?>><?php e__('Enable') ?></option>
                          </select>
                        </div>
                      </div>
                    <?php else: ?>
                      <?php echo alertError(t__('No data for this page!')); ?>
                    <?php endif; ?>
                  </div>
                  <?php echo $csrf->input('updatePaymentSettings'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <button type="submit" class="btn btn-rounded btn-success" name="updatePaymentSettings"><?php e__('Save Changes') ?></button>
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
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
