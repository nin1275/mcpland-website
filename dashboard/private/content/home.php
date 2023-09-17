<?php
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/home.js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/home.stats.js');
?>
<?php if (checkPerm($readAdmin, 'SUPER_ADMIN')): ?>
  <div class="header">
    <div class="container-fluid">
      <div class="header-body">
        <div class="row align-items-end">
          <div class="col">
            <h6 class="header-pretitle text-secondary">
              <?php e__('Overview'); ?>
            </h6>
            <h1 class="header-title">
              <?php e__('Stats'); ?> (<?php echo date("Y"); ?>)
            </h1>
          </div>
          <div class="col-auto">
            <ul class="nav nav-tabs header-tabs">
              <li id="earnChartData" class="nav-item disabledlink" data-toggle="chart" data-target="#dashboardChart" data-update='{"data":{"datasets":[{"data":[0,0,0,0,0,0,0,0,0,0,0,0]}]}}'>
                <a href="#" class="nav-link text-center active" data-toggle="tab">
                  <h6 class="header-pretitle text-secondary">
                    <?php e__('Earnings') ?>
                  </h6>
                  <div id="earnChartValue">
                    <div class="spinner-border spinner-border-sm" role="status">
                      <span class="sr-only"><?php e__("Loading") ?>...</span>
                    </div>
                    <h3 class="mb-0" style="display: none;">
                      0
                      <small class="ml-2">
                        <?php echo $readSettings["currency"] ?>
                      </small>
                    </h3>
                  </div>
                </a>
              </li>
              <li id="userChartData" class="nav-item disabledlink" data-toggle="chart" data-target="#dashboardChart" data-update='{"data":{"datasets":[{"data":[0,0,0,0,0,0,0,0,0,0,0,0]}]}}'>
                <a href="#" class="nav-link text-center" data-toggle="tab">
                  <h6 class="header-pretitle text-secondary">
                      <?php e__("REGISTRY") ?>
                  </h6>
                  <div id="userChartValue">
                    <div class="spinner-border spinner-border-sm" role="status">
                      <span class="sr-only"><?php e__("Loading") ?>...</span>
                    </div>
                    <h3 class="mb-0" style="display: none;">0</h3>
                  </div>
                </a>
              </li>
              <li id="storeChartData" class="nav-item disabledlink" data-toggle="chart" data-target="#dashboardChart" data-update='{"data":{"datasets":[{"data":[0,0,0,0,0,0,0,0,0,0,0,0]}]}}'>
                <a href="#" class="nav-link text-center" data-toggle="tab">
                  <h6 class="header-pretitle text-secondary">
                      <?php e__("STORE") ?>
                  </h6>
                  <div id="storeChartValue">
                    <div class="spinner-border spinner-border-sm" role="status">
                      <span class="sr-only"><?php e__("Loading") ?>...</span>
                    </div>
                    <h3 class="mb-0" style="display: none;">0</h3>
                  </div>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="header-footer">
        <div class="chart">
          <canvas id="dashboardChart" class="chart-canvas"></canvas>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<div class="container-fluid pt-5">
  <div class="row">
    <?php if (checkPerm($readAdmin, 'MANAGE_ACCOUNTS')): ?>
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col">
                <h6 class="card-title text-uppercase text-muted mb-2">
                  <?php e__("Registered User") ?>
                </h6>
                <div id="userCardData">
                  <div class="spinner-border spinner-border-sm" role="status">
                    <span class="sr-only"><?php e__("Loading") ?>...</span>
                  </div>
                  <span class="h2 mb-0" style="display: none;">-</span>
                  <span class="badge badge-soft-secondary mt--1" style="display: none;">0%</span>
                </div>
              </div>
              <div class="col-auto">
                <span class="h2 fe fe-users text-muted mb-0"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <?php if (checkPerm($readAdmin, 'SUPER_ADMIN')): ?>
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col">
                <h6 class="card-title text-uppercase text-muted mb-2">
                  <?php e__("This Month Earning") ?>
                </h6>
                <div id="thisMonthEarnData">
                  <div class="spinner-border spinner-border-sm" role="status">
                    <span class="sr-only"><?php e__("Loading") ?>...</span>
                  </div>
                  <span class="h2 mb-0" style="display: none;">-</span>
                  <span class="badge badge-soft-secondary mt--1" style="display: none;">0%</span>
                </div>
              </div>
              <div class="col-auto">
                <span class="h2 fe fe-dollar-sign text-muted mb-0"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <?php if (checkPerm($readAdmin, 'MANAGE_BLOG')): ?>
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col">
                <h6 class="card-title text-uppercase text-muted mb-2">
                  <?php e__('Comments Pending Confirmation') ?>
                </h6>
                <div id="waitingCommentsCardData">
                  <div class="spinner-border spinner-border-sm" role="status">
                    <span class="sr-only"><?php e__("Loading") ?>...</span>
                  </div>
                  <span class="h2 mb-0" style="display: none;">-</span>
                </div>
              </div>
              <div class="col-auto">
                <span class="h2 fe fe-message-circle text-muted mb-0"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <?php if (checkPerm($readAdmin, 'MANAGE_BLOG')): ?>
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col">
                <h6 class="card-title text-uppercase text-muted mb-2">
                  <?php e__("This Month News") ?>
                </h6>
                <div id="thisMonthNewsCardData">
                  <div class="spinner-border spinner-border-sm" role="status">
                    <span class="sr-only"><?php e__("Loading") ?>...</span>
                  </div>
                  <span class="h2 mb-0" style="display: none;">-</span>
                </div>
              </div>
              <div class="col-auto">
                <span class="h2 fe fe-edit text-muted mb-0"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <?php if (checkPerm($readAdmin, 'MANAGE_SUPPORT')): ?>
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col">
                <h6 class="card-title text-uppercase text-muted mb-2">
                  <?php e__("This Month Supports") ?>
                </h6>
                <div id="thisMonthSupportCardData">
                  <div class="spinner-border spinner-border-sm" role="status">
                    <span class="sr-only"><?php e__("Loading") ?>...</span>
                  </div>
                  <span class="h2 mb-0" style="display: none;">-</span>
                </div>
              </div>
              <div class="col-auto">
                <span class="h2 fe fe-message-square text-muted mb-0"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <?php if (checkPerm($readAdmin, 'MANAGE_SUPPORT')): ?>
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col">
                <h6 class="card-title text-uppercase text-muted mb-2">
                  <?php e__("Supports Waiting Reply") ?>
                </h6>
                <div id="waitingSupportCardData">
                  <div class="spinner-border spinner-border-sm" role="status">
                    <span class="sr-only"><?php e__("Loading") ?>...</span>
                  </div>
                  <span class="h2 mb-0" style="display: none;">-</span>
                </div>
              </div>
              <div class="col-auto">
                <span class="h2 fe fe-message-square text-muted mb-0"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h6 class="card-title text-uppercase text-muted mb-2">
                  <?php e__("Online Players") ?>
              </h6>
              <span class="h2 mb-0" data-toggle="onlinetext" server-ip="<?php echo $serverIP; ?>">-/-</span>
            </div>
            <div class="col-auto">
              <span class="h2 fe fe-globe text-muted mb-0"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col">
              <h4 class="card-header-title">
                  <?php e__("Staff Chat") ?>
              </h4>
            </div>
            <div class="col-auto">
              <a id="chatRefresh" class="small text-muted" href="#">
                <i class="fe fe-refresh-cw"></i>
              </a>
            </div>
          </div>
        </div>
        <div id="chatBox" class="card-body" style="height: 200px !important; overflow: auto;">
          <div id="spinner">
            <div class="spinner-border" role="status">
              <span class="sr-only">-/-</span>
            </div>
          </div>
          <div id="chatHistory"></div>
        </div>
        <div class="card-footer p-0">
          <input type="text" id="chatMessage" class="form-control border-0" style="padding: .75rem 1.5rem; border-radius: 0 0 .375rem .375rem;" name="message" placeholder="<?php e__("Enter your message") ?>.">
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <?php
        $creditHistory = $db->prepare("SELECT CH.*, A.realname FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE CH.type IN (?, ?) AND CH.paymentStatus = ? ORDER BY CH.id DESC LIMIT 5");
        $creditHistory->execute(array(1, 2, 1));
      ?>
      <?php if ($creditHistory->rowCount() > 0): ?>
        <div class="card">
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col">
                <h4 class="card-header-title">
                    <?php e__("Credit History") ?>
                </h4>
              </div>
              <div class="col-auto">
                <a class="small" href="/dashboard/store/credit-purchase-logs"><?php e__("All") ?></a>
              </div>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive mb-0">
              <table class="table table-sm table-no-wrap card-table">
                <thead>
                  <tr>
                    <th><?php e__("User") ?></th>
                    <th class="text-center"><?php e__("Amount") ?></th>
                    <th class="text-center"><?php e__("Payment") ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($creditHistory as $readCreditHistory): ?>
                    <tr>
                      <td>
                        <a class="avatar avatar-xs d-inline-block" href="/dashboard/users/view/<?php echo $readCreditHistory["realname"]; ?>">
                          <img src="https://minotar.net/avatar/<?php echo $readCreditHistory["realname"]; ?>/20.png" alt="Player" class="rounded-circle">
                        </a>
                        <a href="/dashboard/users/view/<?php echo $readCreditHistory["realname"]; ?>"><?php echo $readCreditHistory["realname"]; ?></a>
                      </td>
                      <td class="text-center">
                        <?php if ($readCreditHistory["type"] == 3 || $readCreditHistory["type"] == 5): ?>
                          <span class="text-danger">-<?php echo $readCreditHistory["price"]; ?></span>
                        <?php else: ?>
                          <span class="text-success">+<?php echo $readCreditHistory["price"]; ?></span>
                        <?php endif; ?>
                      </td>
                      <td class="text-center">
                        <?php if ($readCreditHistory["type"] == 1): ?>
                          <i class="fa fa-mobile" data-toggle="tooltip" data-placement="top" title="<?php e__("Mobile Payment") ?>"></i>
                        <?php elseif ($readCreditHistory["type"] == 2): ?>
                          <i class="fa fa-credit-card" data-toggle="tooltip" data-placement="top" title="<?php e__("Credit Card Payment") ?>"></i>
                        <?php elseif ($readCreditHistory["type"] == 3): ?>
                          <i class="fa fa-paper-plane" data-toggle="tooltip" data-placement="top" title="<?php e__("Transfer (Sender)") ?>"></i>
                        <?php elseif ($readCreditHistory["type"] == 4): ?>
                          <i class="fa fa-paper-plane" data-toggle="tooltip" data-placement="top" title="<?php e__("Transfer (Receiver)") ?>"></i>
                        <?php elseif ($readCreditHistory["type"] == 5): ?>
                          <i class="fa fa-ticket-alt" data-toggle="tooltip" data-placement="top" title="<?php e__("Wheel of Fortune (Ticket)") ?>"></i>
                        <?php elseif ($readCreditHistory["type"] == 6): ?>
                          <i class="fa fa-ticket-alt" data-toggle="tooltip" data-placement="top" title="<?php e__("Wheel of Fortune (Earning)") ?>"></i>
                        <?php else: ?>
                          <i class="fa fa-paper-plane"></i>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php else: ?>
        <?php echo alertError(t__("No credit history found!")); ?>
      <?php endif; ?>
    </div>
    <?php if (checkPerm($readAdmin, 'MANAGE_STORE')): ?>
      <div class="col-md-4">
        <?php $storeHistory = $db->query("SELECT P.name as productName, S.name as serverName, A.realname FROM OrderProducts OP INNER JOIN Orders O ON OP.orderID = O.id INNER JOIN Products P ON OP.productID = P.id INNER JOIN Servers S ON P.serverID = S.id INNER JOIN Accounts A ON O.accountID = A.id ORDER BY O.id DESC LIMIT 5"); ?>
        <?php if ($storeHistory->rowCount() > 0): ?>
          <div class="card">
            <div class="card-header">
              <div class="row align-items-center">
                <div class="col">
                  <h4 class="card-header-title">
                    <?php e__("Story History") ?>
                  </h4>
                </div>
                <div class="col-auto">
                  <a class="small" href="/dashboard/store/store-logs"><?php e__("All") ?></a>
                </div>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive mb-0">
                <table class="table table-sm table-no-wrap card-table">
                  <thead>
                  <tr>
                    <th><?php e__("User") ?></th>
                    <th class="text-center"><?php e__("Product") ?></th>
                    <th class="text-center"><?php e__("Server") ?></th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($storeHistory as $readStoreHistory): ?>
                    <tr>
                      <td>
                        <a class="avatar avatar-xs d-inline-block" href="/dashboard/users/view/<?php echo $readStoreHistory["realname"]; ?>">
                          <img src="https://minotar.net/avatar/<?php echo $readStoreHistory["realname"]; ?>/20.png" alt="Marketi Kullanan Kullanıcı" class="rounded-circle">
                        </a>
                        <a href="/dashboard/users/view/<?php echo $readStoreHistory["realname"]; ?>"><?php echo $readStoreHistory["realname"]; ?></a>
                      </td>
                      <td class="text-center"><?php echo $readStoreHistory["productName"]; ?></td>
                      <td class="text-center"><?php echo $readStoreHistory["serverName"]; ?></td>
                    </tr>
                  <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php else: ?>
          <?php echo alertError(t__("No store history found!")); ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
<script type="text/javascript">
  var username        = '<?php echo $readAdmin["realname"]; ?>';
  var creationDate    = '<?php echo date('H:i'); ?>';
</script>

<?php if ($needUpdate == true && !isset($_SESSION["needUpdateInfo"]) && checkPerm($readAdmin, 'MANAGE_UPDATES')): ?>
  <?php $_SESSION["needUpdateInfo"] = true; ?>
  <script type="text/javascript">
    var alertText     = `<?php e__('<p>A new update released! To update new version <a target="_blank" href="https://www.leaderos.net/blog">LeaderOS v%version%</a>, click the <strong class="text-success">Update</strong> button.</p>', ['%version%' => $newVersion]) ?>`;
    var alertLocation = '/dashboard/update';
  </script>
  <?php $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/home.alert.update.js'); ?>
<?php endif; ?>
<?php if (get("alert") == 001): ?>
  <script type="text/javascript">
    var alertText     = "<?php e__("You don't have access to view this page!") ?>";
    var alertLocation = '/dashboard';
  </script>
  <?php $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/home.alert.js'); ?>
<?php endif; ?>
<?php if (get("alert") == 002): ?>
  <script type="text/javascript">
    var alertText     = "<?php e__("You don't have access to do it!") ?>";
    var alertLocation = '/dashboard';
  </script>
  <?php $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/home.alert.js'); ?>
<?php endif; ?>
<?php if (get("alert") == 101): ?>
  <script type="text/javascript">
    var alertText     = "<?php e__("You can't delete yourself!") ?>";
    var alertLocation = '/dashboard/users';
  </script>
  <?php $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/home.alert.js'); ?>
<?php endif; ?>
