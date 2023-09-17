<?php
  if (!isset($_SESSION["login"])) {
    go("/login");
  }
?>
<section class="section profile-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><?php e__('Home') ?></a></li>
            <?php if (isset($_GET["action"])): ?>
              <li class="breadcrumb-item"><a href="/profile"><?php e__('Profile') ?></a></li>
              <?php if (get("action") == "update"): ?>
                <li class="breadcrumb-item active" aria-current="page"><?php e__('Edit Profile') ?></li>
              <?php elseif (get("action") == "change-password"): ?>
                <li class="breadcrumb-item active" aria-current="page"><?php e__('Change Password') ?></li>
              <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $readAccount["realname"]; ?></li>
              <?php endif; ?>
            <?php else: ?>
              <li class="breadcrumb-item active" aria-current="page"><?php e__('Profile') ?></li>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
      <div class="col-lg-4 mb-4 mb-lg-0">
        <div class="profile-card bg-light rounded-3 shadow-lg">
          <div class="px-4 py-4 mb-1 text-center">
            <?php echo minecraftHead($readSettings["avatarAPI"], $readAccount["realname"], 80); ?>
            <h6 class="mb-0 mt-3">
              <?php echo $readAccount["realname"]; ?>
            </h6>
          </div>
          <div class="d-lg-none px-4 pb-4 text-center">
            <a class="btn btn-primary px-5 mb-2" href="#account-menu" data-bs-toggle="collapse">
              <i class="shi-menu me-2"></i> <?php e__('Profile Menu') ?></a>
          </div>
          <div class="d-lg-block collapse pb-2" id="account-menu">
            <h3 class="d-block bg-secondary fs-sm fw-semibold text-muted mb-0 px-4 py-3"><?php e__('Details') ?></h3>
            <span class="d-flex align-items-center nav-link-style px-4 py-3">
              <i class="shi-mail fs-lg opacity-60 me-2"></i> <?php e__('Email') ?>
              <span class="text-muted fs-sm fw-normal ms-auto"><?php echo $readAccount["email"]; ?></span>
            </span>
            <span class="d-flex align-items-center nav-link-style px-4 py-3">
              <i class="shi-key fs-lg opacity-60 me-2"></i> <?php e__('Role') ?>
              <span class="text-muted fs-sm fw-normal ms-auto">
                <?php echo styledRoles(getRoles($readAccount["id"])); ?>
              </span>
            </span>
            <span class="d-flex align-items-center nav-link-style px-4 py-3">
              <img class="me-2" src="<?php echo themePath(); ?>/public/assets/img/icons/coin.svg" width="18"> <?php e__('Balance') ?>
              <span class="text-muted fs-sm fw-semibold ms-auto">
                <?php echo $readAccount["credit"]; ?>
                <a class="text-success" href="/credit/buy"><i class="shi-plus-circle"></i></a>
              </span>
            </span>
            <span class="d-flex align-items-center nav-link-style px-4 py-3">
              <i class="shi-activity fs-lg opacity-60 me-2"></i> <?php e__('Last Login') ?>
              <span class="text-muted fs-sm fw-normal ms-auto">
                <?php if ($readAccount["lastlogin"] == 0): ?>
                  <?php e__('Not logged in') ?>
                <?php else: ?>
                  <?php echo convertTime(date("Y-m-d H:i:s", ($readAccount["lastlogin"]/1000)), 2, true); ?>
                <?php endif; ?>
              </span>
            </span>
            <span class="d-flex align-items-center nav-link-style px-4 py-3">
              <i class="shi-calendar fs-lg opacity-60 me-2"></i> <?php e__('Reg. Date') ?>
              <span class="text-muted fs-sm fw-normal ms-auto">
                <?php if ($readAccount["creationDate"] == "1000-01-01 00:00:00"): ?>
                  Bilinmiyor
                <?php else: ?>
                  <?php echo convertTime($readAccount["creationDate"], 2, true); ?>
                <?php endif; ?>
              </span>
            </span>
            <?php if ($readSettings["authStatus"] == 1): ?>
              <span class="d-flex align-items-center nav-link-style px-4 py-3">
                <i class="shi-key fs-lg opacity-60 me-2"></i> 2FA
                <span class="text-muted fs-sm fw-normal ms-auto">
                  <?php echo ($readAccount["authStatus"] == 0) ? t__('Disabled') : t__('Enabled'); ?>
                </span>
              </span>
            <?php endif; ?>
        
            <?php
              $accountSocialMedia = $db->prepare("SELECT * FROM AccountSocialMedia WHERE accountID = ?");
              $accountSocialMedia->execute(array($readAccount["id"]));
              $readAccountSocialMedia = $accountSocialMedia->fetch();
            ?>
            <h3 class="d-block bg-secondary fs-sm fw-semibold text-muted mb-0 px-4 py-3"><?php e__('Social') ?></h3>
            <span class="d-flex align-items-center nav-link-style px-4 py-3">
              <i class="shi-discord fs-lg opacity-60 me-2"></i> Discord
              <span class="text-muted fs-sm fw-normal ms-auto">
                <?php if ($accountSocialMedia->rowCount() > 0): ?>
                  <?php echo (($readAccountSocialMedia["discord"] != '0') ? $readAccountSocialMedia["discord"] : "-"); ?>
                <?php else: ?>
                  -
                <?php endif; ?>
              </span>
            </span>
        
            <?php
              $bannedStatus = $db->prepare("SELECT accountID BannedAccounts WHERE accountID = ? AND (expiryDate > ? OR expiryDate = ?)");
              $bannedStatus->execute(array($readAccount["id"], date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
            ?>
            <?php if ($bannedStatus->rowCount() > 0): ?>
              <h3 class="d-block bg-secondary fs-sm fw-semibold text-muted mb-0 px-4 py-3"><?php e__('Bans') ?></h3>
          
              <?php
              $siteBannedAccountStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?) ORDER BY expiryDate DESC LIMIT 1");
              $siteBannedAccountStatus->execute(array($readAccount["id"], 1, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
              $readSiteBannedAccountStatus = $siteBannedAccountStatus->fetch();
              ?>
              <?php if ($siteBannedAccountStatus->rowCount() > 0): ?>
                <span class="d-flex align-items-center nav-link-style px-4 py-3">
                  <i class="shi-slash fs-lg opacity-60 me-2"></i> <?php e__('Ban (Website)') ?>
                  <span class="text-muted fs-sm fw-normal ms-auto">
                    <?php echo ($readSiteBannedAccountStatus["expiryDate"] == '1000-01-01 00:00:00') ? t__('Perma ban') : t__('%day% day(s)', ['%day%' => getDuration($readSiteBannedAccountStatus["expiryDate"])]); ?>
                  </span>
                </span>
              <?php endif; ?>
              <?php
              $supportBannedAccountStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?) ORDER BY expiryDate DESC LIMIT 1");
              $supportBannedAccountStatus->execute(array($readAccount["id"], 2, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
              $readSupportBannedAccountStatus = $supportBannedAccountStatus->fetch();
              ?>
              <?php if ($supportBannedAccountStatus->rowCount() > 0): ?>
                <span class="d-flex align-items-center nav-link-style px-4 py-3">
                  <i class="shi-slash fs-lg opacity-60 me-2"></i> <?php e__('Ban (Support)') ?>
                  <span class="text-muted fs-sm fw-normal ms-auto">
                    <?php echo ($readSupportBannedAccountStatus["expiryDate"] == '1000-01-01 00:00:00') ? t__('Perma ban') : t__('%day% day(s)', ['%day%' => getDuration($readSupportBannedAccountStatus["expiryDate"])]); ?>
                  </span>
                </span>
              <?php endif; ?>
              <?php
              $commentBannedAccountStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?) ORDER BY expiryDate DESC LIMIT 1");
              $commentBannedAccountStatus->execute(array($readAccount["id"], 3, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
              $readCommentBannedAccountStatus = $commentBannedAccountStatus->fetch();
              ?>
              <?php if ($commentBannedAccountStatus->rowCount() > 0): ?>
                <span class="d-flex align-items-center nav-link-style px-4 py-3">
                  <i class="shi-slash fs-lg opacity-60 me-2"></i> <?php e__('Ban (Comment)') ?>
                  <span class="text-muted fs-sm fw-normal ms-auto">
                    <?php echo ($readCommentBannedAccountStatus["expiryDate"] == '1000-01-01 00:00:00') ? t__('Perma ban') : t__('%day% day(s)', ['%day%' => getDuration($readCommentBannedAccountStatus["expiryDate"])]); ?>
                  </span>
                </span>
              <?php endif; ?>
            <?php endif ?>
        
        
            <h3 class="d-block bg-secondary fs-sm fw-semibold text-muted mb-0 px-4 py-3"><?php e__('Settings') ?></h3>
            <a class="d-flex align-items-center nav-link-style px-4 py-3" href="/profile/edit"><?php e__('Edit Profile') ?></a>
            <a class="d-flex align-items-center nav-link-style px-4 py-3 border-top" href="/profile/change-password"><?php e__('Change Password') ?></a>
        
            <a class="d-flex align-items-center nav-link-style px-4 py-3 border-top" href="/logout" onclick="return confirm('<?php e__('Are you sure want to logout?') ?>');">
              <i class="shi-log-out fs-lg opacity-60 me-2"></i> <?php e__('Logout') ?>
            </a>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <?php if (get("target") == 'profile'): ?>
          <?php if (get("action") == 'get'): ?>
            <?php
              $statServers = $db->query("SELECT serverName, serverSlug FROM Leaderboards");
              $statServers->execute();
            ?>
            <?php if ($statServers->rowCount() > 0): ?>
              <div class="card">
                <div class="card-body p-0">
                  <nav>
                    <div class="nav nav-tabs nav-fill">
                      <?php foreach ($statServers as $readStatServers): ?>
                        <?php
                          if (!get("siralama")) {
                            $_GET["siralama"] = $readStatServers["serverSlug"];
                          }
                        ?>
                        <a class="nav-item nav-link <?php echo (get("siralama") == $readStatServers["serverSlug"]) ? "active" : null; ?>" id="nav-<?php echo $readStatServers["serverSlug"]; ?>-tab" href="?siralama=<?php echo $readStatServers["serverSlug"]; ?>">
                          <?php echo $readStatServers["serverName"]; ?>
                        </a>
                      <?php endforeach; ?>
                    </div>
                  </nav>
                  <div class="tab-content" id="nav-tabContent">
                    <?php
                      $statServer = $db->query("SELECT * FROM Leaderboards");
                      $statServer->execute();
                    ?>
                    <?php foreach ($statServer as $readStatServer): ?>
                      <?php
                        $usernameColumn = $readStatServer["usernameColumn"];
                        $mysqlTable = $readStatServer["mysqlTable"];
                        $sorter = $readStatServer["sorter"];
                        $tableTitles = $readStatServer["tableTitles"];
                        $tableData = $readStatServer["tableData"];
                        $tableTitlesArray = explode(",", $tableTitles);
                        $tableDataArray = explode(",", $tableData);

                        if ($readStatServer["mysqlServer"] == '0') {
                          $accountOrder = $db->prepare("SELECT $usernameColumn,$tableData FROM $mysqlTable WHERE $usernameColumn = ? ORDER BY $sorter DESC LIMIT 1");
                          $accountOrder->execute(array($readAccount["realname"]));
                        }
                        else {
                          try {
                            $newDB = new PDO("mysql:host=".$readStatServer["mysqlServer"]."; port=".$readStatServer["mysqlPort"]."; dbname=".$readStatServer["mysqlDatabase"]."; charset=utf8", $readStatServer["mysqlUsername"], $readStatServer["mysqlPassword"]);
                          }
                          catch (PDOException $e) {
                            die("<strong>MySQL connection error:</strong> ".utf8_encode($e->getMessage()));
                          }
                          $accountOrder = $newDB->prepare("SELECT $usernameColumn,$tableData FROM $mysqlTable WHERE $usernameColumn = ? ORDER BY $sorter DESC LIMIT 1");
                          $accountOrder->execute(array($readAccount["realname"]));
                        }
                      ?>
                      <div class="tab-pane fade <?php echo (get("siralama") == $readStatServer["serverSlug"]) ? "show active" : null; ?>" id="nav-<?php echo $readStatServer["serverSlug"] ?>">
                        <?php if ($accountOrder->rowCount() > 0): ?>
                          <div class="table-responsive">
                            <table class="table table-hover">
                              <thead>
                                <tr>
                                  <th class="text-center" style="width: 40px;"><?php e__('Rank') ?></th>
                                  <th class="text-center" style="width: 20px;">#</th>
                                  <th><?php e__('Username') ?></th>
                                  <?php
                                    foreach ($tableTitlesArray as $readTableTitles) {
                                      echo '<th class="text-center">'.$readTableTitles.'</th>';
                                    }
                                  ?>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($accountOrder as $readAccountOrder): ?>
                                  <tr>
                                    <td class="text-center" style="width: 40px;">
                                      <?php
                                        if ($readStatServer["mysqlServer"] == '0') {
                                          $userPosition = $db->prepare("SELECT $usernameColumn FROM $mysqlTable ORDER BY $sorter DESC");
                                          $userPosition->execute();
                                        }
                                        else {
                                          $userPosition = $newDB->prepare("SELECT $usernameColumn FROM $mysqlTable ORDER BY $sorter DESC");
                                          $userPosition->execute();
                                        }
                                      ?>
                                      <?php $rank = 1; ?>
                                      <?php foreach ($userPosition as $readUserPosition): ?>
                                        <?php if ($readUserPosition[$usernameColumn] == $readAccount["realname"]): ?>
                                          <?php if ($rank == 1): ?>
                                            <img src="<?php echo themePath(); ?>/public/assets/img/icons/medal-1.svg" width="25">
                                          <?php elseif ($rank == 2): ?>
                                            <img src="<?php echo themePath(); ?>/public/assets/img/icons/medal-2.svg" width="25">
                                          <?php elseif ($rank == 3): ?>
                                            <img src="<?php echo themePath(); ?>/public/assets/img/icons/medal-3.svg" width="25">
                                          <?php else: ?>
                                            <?php echo $rank; ?>
                                          <?php endif; ?>
                                          <?php break; ?>
                                        <?php endif; ?>
                                        <?php $rank++; ?>
                                      <?php endforeach; ?>
                                    </td>
                                    <td class="text-center" style="width: 20px;">
                                      <?php echo minecraftHead($readSettings["avatarAPI"], $readAccount["realname"], 20); ?>
                                    </td>
                                    <td>
                                      <?php echo $readAccount["realname"]; ?>
                                    </td>
                                    <?php foreach ($tableDataArray as $readTableData): ?>
                                      <td class="text-center"><?php echo $readAccountOrder[$readTableData]; ?></td>
                                    <?php endforeach; ?>
                                  </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                          </div>
                        <?php else: ?>
                          <div class="p-4"><?php echo alertError(t__('No data found!'), false); ?></div>
                        <?php endif; ?>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            <?php endif; ?>
            <div class="card">
              <div class="card-body p-0">
                <nav>
                  <div class="nav nav-tabs nav-fill" id="nav-profile-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-support-tab" data-bs-toggle="tab" href="#nav-support" role="tab" aria-controls="nav-support" aria-selected="true"><?php e__('Tickets') ?></a>
                    <a class="nav-item nav-link" id="nav-credit-history-tab" data-bs-toggle="tab" href="#nav-credit-history" role="tab" aria-controls="nav-credit-history" aria-selected="false"><?php e__('Credit History') ?></a>
                    <a class="nav-item nav-link" id="nav-store-history-tab" data-bs-toggle="tab" href="#nav-store-history" role="tab" aria-controls="nav-store-history" aria-selected="false"><?php e__('Store History') ?></a>
                    <a class="nav-item nav-link" id="nav-store-bazaar-tab" data-bs-toggle="tab" href="#nav-bazaar-history" role="tab" aria-controls="nav-bazaar-history" aria-selected="false"><?php e__('Bazaar History') ?></a>
                  </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                  <div class="tab-pane fade show active" id="nav-support" role="tabpanel" aria-labelledby="nav-support-tab">
                    <?php
                      $supports = $db->prepare("SELECT S.*, SC.name as categoryName, Se.name as serverName FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id WHERE S.accountID = ? ORDER BY S.updateDate DESC LIMIT 50");
                      $supports->execute(array($readAccount["id"]));
                    ?>
                    <?php if ($supports->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($supports->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th class="text-center" style="width: 40px;">ID</th>
                              <th><?php e__('Title') ?></th>
                              <th><?php e__('Category') ?></th>
                              <th><?php e__('Last Updated') ?></th>
                              <th class="text-center"><?php e__('Status') ?></th>
                              <th class="text-center"> </th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($supports as $readSupports): ?>
                              <tr>
                                <td class="text-center" style="width: 40px;">
                                  <a href="/support/view/<?php echo $readSupports["id"]; ?>/">
                                    #<?php echo $readSupports["id"]; ?>
                                  </a>
                                </td>
                                <td>
                                  <a href="/support/view/<?php echo $readSupports["id"]; ?>/">
                                    <?php echo $readSupports["title"]; ?>
                                  </a>
                                </td>
                                <td>
                                  <?php echo $readSupports["categoryName"]; ?>
                                </td>
                                <td>
                                  <?php echo convertTime($readSupports["updateDate"]); ?>
                                </td>
                                <td class="text-center">
                                  <?php if ($readSupports["statusID"] == 1): ?>
                                    <span class="badge badge-pill bg-info"><?php e__('Open') ?></span>
                                  <?php elseif ($readSupports["statusID"] == 2): ?>
                                    <span class="badge badge-pill bg-success"><?php e__('Answered') ?></span>
                                  <?php elseif ($readSupports["statusID"] == 3): ?>
                                    <span class="badge badge-pill bg-warning"><?php e__('User-Reply') ?></span>
                                  <?php elseif ($readSupports["statusID"] == 4): ?>
                                    <span class="badge badge-pill bg-danger"><?php e__('Closed') ?></span>
                                  <?php else: ?>
                                    <span class="badge badge-pill bg-danger"><?php e__('Error') ?></span>
                                  <?php endif; ?>
                                </td>
                                <td class="text-center">
                                  <a class="btn btn-success btn-circle" href="/support/view/<?php echo $readSupports["id"]; ?>/" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                                    <i class="shi-eye"></i>
                                  </a>
                                  <a class="btn btn-warning btn-circle clickdelete" href="/support/delete/<?php echo $readSupports["id"]; ?>/" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                                    <i class="shi-x"></i>
                                  </a>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError(t__('History not found!'), false); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="tab-pane fade" id="nav-credit-history" role="tabpanel" aria-labelledby="nav-credit-history-tab">
                    <?php
                      $creditHistory = $db->prepare("SELECT CH.*, PY.name as paymentGatewayName FROM CreditHistory CH LEFT JOIN PaymentSettings PY ON CH.paymentAPI = PY.slug WHERE CH.accountID = ? AND CH.paymentStatus = ? ORDER BY CH.id DESC LIMIT 50");
                      $creditHistory->execute(array($readAccount["id"], 1));
                    ?>
                    <?php if ($creditHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($creditHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th class="text-center"><?php e__('Amount') ?></th>
                              <th class="text-center"><?php e__('Type') ?></th>
                              <th><?php e__('Date') ?></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($creditHistory as $readCreditHistory): ?>
                              <tr>
                                <td class="text-center">#<?php echo $readCreditHistory["id"]; ?></td>
                                <td class="text-center"><?php echo ($readCreditHistory["type"] == 3 || $readCreditHistory["type"] == 5) ? '<span class="text-danger">-'.$readCreditHistory["price"].'</span>' : '<span class="text-success">+'.$readCreditHistory["price"].'</span>'; ?></td>
                                <td class="text-center">
                                  <?php if ($readCreditHistory["type"] == 1): ?>
                                    <i class="fa fa-mobile" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('Mobile Payment (%gateway%)', ['%gateway%' => $readCreditHistory["paymentGatewayName"]]) ?>"></i>
                                  <?php elseif ($readCreditHistory["type"] == 2): ?>
                                    <i class="fa fa-credit-card" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('Credit Card (%gateway%)', ['%gateway%' => $readCreditHistory["paymentGatewayName"]]) ?>"></i>
                                  <?php elseif ($readCreditHistory["type"] == 3): ?>
                                    <i class="fa fa-paper-plane" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('Transfer (Sender)') ?>"></i>
                                  <?php elseif ($readCreditHistory["type"] == 4): ?>
                                    <i class="fa fa-paper-plane" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('Transfer (Receiver)') ?> "></i>
                                  <?php elseif ($readCreditHistory["type"] == 5): ?>
                                    <i class="fa fa-ticket-alt" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('Wheel of Fortune (Ticket)') ?>"></i>
                                  <?php elseif ($readCreditHistory["type"] == 6): ?>
                                    <i class="fa fa-ticket-alt" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('Wheel of Fortune (Prize)') ?>"></i>
                                  <?php else: ?>
                                    <i class="fa fa-paper-plane"></i>
                                  <?php endif; ?>
                                </td>
                                <td><?php echo convertTime($readCreditHistory["creationDate"], 2, true); ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError(t__('History not found!'), false); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="tab-pane fade" id="nav-store-history" role="tabpanel" aria-labelledby="nav-store-history-tab">
                    <?php
                      $storeHistory = $db->prepare("SELECT * FROM Orders WHERE accountID = ? ORDER BY id DESC LIMIT 50");
                      $storeHistory->execute(array($readAccount["id"]));
                    ?>
                    <?php if ($storeHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($storeHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th><?php e__('Total') ?></th>
                              <th><?php e__('Date') ?></th>
                              <th class="text-end">&nbsp;</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($storeHistory as $readStoreHistory): ?>
                              <tr>
                                <td class="text-center">#<?php echo $readStoreHistory["id"]; ?></td>
                                <td><?php e__('%credit% credit(s)', ['%credit%' => $readStoreHistory["subtotal"]]); ?></td>
                                <td><?php echo convertTime($readStoreHistory["creationDate"], 2, true); ?></td>
                                <td class="text-end">
                                  <a class="btn btn-success btn-circle" href="/orders/view/<?php echo $readStoreHistory["id"]; ?>/" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                                    <i class="fa fa-eye"></i>
                                  </a>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError(t__('History not found!'), false); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="tab-pane fade" id="nav-bazaar-history" role="tabpanel" aria-labelledby="nav-bazaar-history-tab">
                    <?php
                      $bazaarHistory = $db->prepare("SELECT BH.*, BI.name as itemName, BI.price as itemPrice FROM BazaarHistory BH INNER JOIN BazaarItems BI ON BH.itemID = BI.id WHERE BH.accountID = ? ORDER BY BH.id DESC LIMIT 50");
                      $bazaarHistory->execute(array($readAccount["id"]));
                    ?>
                    <?php if ($bazaarHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($bazaarHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-hover">
                          <thead>
                          <tr>
                            <th class="text-center">ID</th>
                            <th><?php e__('Item') ?></th>
                            <th class="text-center"><?php e__('Type') ?></th>
                            <th><?php e__('Date') ?></th>
                          </tr>
                          </thead>
                          <tbody>
                          <?php foreach ($bazaarHistory as $readBazaarHistory): ?>
                            <tr>
                              <td class="text-center">#<?php echo $readBazaarHistory["id"]; ?></td>
                              <td><?php echo $readBazaarHistory["itemName"]; ?></td>
                              <td class="text-center">
                                <?php if ($readBazaarHistory["type"] == 0): ?>
                                  <span class="text-danger" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('Purchase') ?>">-<?php echo $readBazaarHistory["itemPrice"] ?> <i class="fa fa-coins"></i></span>
                                <?php elseif ($readBazaarHistory["type"] == 1): ?>
                                  <span class="text-success" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('Sell') ?>">+<?php echo $readBazaarHistory["itemPrice"] ?> <i class="fa fa-coins"></i></span>
                                <?php else: ?>
                                  <i class="fa fa-check"></i>
                                <?php endif; ?>
                              </td>
                              <td><?php echo convertTime($readBazaarHistory["creationDate"], 2, true); ?></td>
                            </tr>
                          <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError(t__('History not found!'), false); ?></div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-body p-0">
                <nav>
                  <div class="nav nav-tabs nav-fill" id="nav-profile-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-lottery-history-tab" data-bs-toggle="tab" href="#nav-lottery-history" role="tab" aria-controls="nav-lottery-history" aria-selected="false"><?php e__('Wheel of Fortune History') ?></a>
                    <a class="nav-item nav-link" id="nav-gift-history-tab" data-bs-toggle="tab" href="#nav-gift-history" role="tab" aria-controls="nav-gift-history" aria-selected="false"><?php e__('Gift History') ?></a>
                    <a class="nav-item nav-link" id="nav-chest-history-tab" data-bs-toggle="tab" href="#nav-chest-history" role="tab" aria-controls="nav-chest-history" aria-selected="false"><?php e__('Chest History') ?></a>
                  </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                  <div class="tab-pane fade show active" id="nav-lottery-history" role="tabpanel" aria-labelledby="nav-lottery-history-tab">
                    <?php
                      $lotteryHistory = $db->prepare("SELECT LH.*, L.title as lotteryTitle, LA.title as awardTitle, LA.awardType, LA.award FROM LotteryHistory LH INNER JOIN LotteryAwards LA ON LH.lotteryAwardID = LA.id INNER JOIN Lotteries L ON LA.lotteryID = L.id WHERE LH.accountID = ? AND LA.awardType != ? ORDER by LH.id DESC LIMIT 50");
                      $lotteryHistory->execute(array($readAccount["id"], 3));
                    ?>
                    <?php if ($lotteryHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($lotteryHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th><?php e__('Name') ?></th>
                              <th><?php e__('Prize') ?></th>
                              <th><?php e__('Date') ?></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($lotteryHistory as $readLotteryHistory): ?>
                              <tr>
                                <td class="text-center">#<?php echo $readLotteryHistory["id"]; ?></td>
                                <td>
                                  <?php echo $readLotteryHistory["lotteryTitle"]; ?>
                                </td>
                                <td>
                                  <?php echo $readLotteryHistory["awardTitle"]; ?>
                                </td>
                                <td><?php echo convertTime($readLotteryHistory["creationDate"], 2, true); ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError(t__('History not found!'), false); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="tab-pane fade" id="nav-gift-history" role="tabpanel" aria-labelledby="nav-gift-history-tab">
                    <?php
                      $giftHistory = $db->prepare("SELECT PGH.*, PG.name, PG.giftType, PG.gift FROM ProductGiftsHistory PGH INNER JOIN ProductGifts PG ON PGH.giftID = PG.id WHERE PGH.accountID = ? ORDER by PGH.id DESC LIMIT 50");
                      $giftHistory->execute(array($readAccount["id"]));
                    ?>
                    <?php if ($giftHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($giftHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th><?php e__('Code') ?></th>
                              <th><?php e__('Gift') ?></th>
                              <th><?php e__('Date') ?></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($giftHistory as $readGiftHistory): ?>
                              <tr>
                                <td class="text-center">#<?php echo $readGiftHistory["id"]; ?></td>
                                <td>
                                  <?php echo $readGiftHistory["name"]; ?>
                                </td>
                                <td>
                                  <?php if ($readGiftHistory["giftType"] == 1): ?>
                                    <?php
                                      $product = $db->prepare("SELECT name FROM Products WHERE id = ?");
                                      $product->execute(array($readGiftHistory["gift"]));
                                      $readProduct = $product->fetch();
                                      echo $readProduct["name"];
                                    ?>
                                  <?php else: ?>
                                    <?php e__('%credit% credit(s)', ['%credit%' => $readGiftHistory["gift"]]) ?>
                                  <?php endif; ?>
                                </td>
                                <td><?php echo convertTime($readGiftHistory["creationDate"], 2, true); ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError(t__('History not found!'), false); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="tab-pane fade" id="nav-chest-history" role="tabpanel" aria-labelledby="nav-chest-history-tab">
                    <?php
                      $chestsHistory = $db->prepare("SELECT CH.*, P.name as productName, S.name as serverName FROM ChestsHistory CH INNER JOIN Chests C ON CH.chestID = C.id INNER JOIN Products P ON C.productID = P.id INNER JOIN Servers S ON P.serverID = S.id WHERE CH.accountID = ? ORDER BY CH.id DESC LIMIT 5");
                      $chestsHistory->execute(array($readAccount["id"]));
                    ?>
                    <?php if ($chestsHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($chestsHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th><?php e__('Product') ?></th>
                              <th><?php e__('Server') ?></th>
                              <th class="text-center"><?php e__('Type') ?></th>
                              <th><?php e__('Date') ?></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($chestsHistory as $readChestsHistory): ?>
                              <tr>
                                <td class="text-center">
                                  #<?php echo $readChestsHistory["id"]; ?>
                                </td>
                                <td><?php echo $readChestsHistory["productName"]; ?></td>
                                <td><?php echo $readChestsHistory["serverName"]; ?></td>
                                <td class="text-center">
                                  <?php if ($readChestsHistory["type"] == 1): ?>
                                    <i class="fa fa-check" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('Delivery') ?>"></i>
                                  <?php elseif ($readChestsHistory["type"] == 2): ?>
                                    <i class="fa fa-gift" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('Gift (Giver)') ?>"></i>
                                  <?php elseif ($readChestsHistory["type"] == 3): ?>
                                    <i class="fa fa-gift" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('Gift (Receiver)') ?>"></i>
                                  <?php else: ?>
                                    <i class="fa fa-check"></i>
                                  <?php endif; ?>
                                </td>
                                <td><?php echo convertTime($readChestsHistory["creationDate"], 2, true); ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError(t__('History not found!'), false); ?></div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            <?php
            $applications = $db->prepare("SELECT AP.id, AF.title, AP.reason, AP.status FROM Applications AP INNER JOIN Accounts A ON A.id = AP.accountID INNER JOIN ApplicationForms AF ON AF.id = AP.formID WHERE AP.accountID = ? ORDER BY AP.id DESC LIMIT 50");
            $applications->execute(array($readAccount["id"]));
            ?>
            <?php if ($applications->rowCount() > 0): ?>
              <div class="card">
                <div class="card-body p-0">
                  <nav>
                    <div class="nav nav-tabs nav-fill" role="tablist">
                      <a class="nav-item nav-link active" href="#!" role="tab"><?php e__('Applications') ?></a>
                    </div>
                  </nav>
                  <div class="tab-content">
                    <div class="tab-pane fade show active" role="tabpanel">
                      <div class="table-responsive" <?php echo ($applications->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-hover">
                          <thead>
                          <tr>
                            <th class="text-center" style="width: 40px;">ID</th>
                            <th><?php e__('Title') ?></th>
                            <th><?php e__('Reason') ?></th>
                            <th class="text-center"><?php e__('Status') ?></th>
                            <th class="text-end"> </th>
                          </tr>
                          </thead>
                          <tbody>
                          <?php foreach ($applications as $readApplications): ?>
                            <tr>
                              <td class="text-center" style="width: 40px;">
                                <a href="/applications/<?php echo $readApplications["id"]; ?>">
                                  #<?php echo $readApplications["id"]; ?>
                                </a>
                              </td>
                              <td>
                                <a href="/applications/<?php echo $readApplications["id"]; ?>">
                                  <?php echo $readApplications["title"]; ?>
                                </a>
                              </td>
                              <td>
                                <?php echo ($readApplications["reason"] == '') ? '-' : $readApplications["reason"]; ?>
                              </td>
                              <td class="text-center">
                                <?php if ($readApplications["status"] == 0): ?>
                                  <span class="badge badge-pill bg-danger"><?php e__('Rejected') ?></span>
                                <?php elseif ($readApplications["status"] == 1): ?>
                                  <span class="badge badge-pill bg-success"><?php e__('Approved') ?></span>
                                <?php elseif ($readApplications["status"] == 2): ?>
                                  <span class="badge badge-pill bg-warning"><?php e__('Pending Approval') ?></span>
                                <?php else: ?>
                                  <span class="badge badge-pill bg-danger"><?php e__('Error') ?></span>
                                <?php endif; ?>
                              </td>
                              <td class="text-end">
                                <a class="btn btn-success btn-circle" href="/applications/<?php echo $readApplications["id"]; ?>" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                                  <i class="fa fa-eye"></i>
                                </a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          <?php elseif (get("action") == 'update'): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');

              $accountSocialMedia = $db->prepare("SELECT * FROM AccountSocialMedia WHERE accountID = ?");
              $accountSocialMedia->execute(array($readAccount["id"]));
              $readAccountSocialMedia = $accountSocialMedia->fetch();

              if (isset($_POST["updateAccounts"])) {
                if (post("skype") == null) {
                  $_POST["skype"] = '0';
                }
                if (post("discord") == null) {
                  $_POST["discord"] = '0';
                }
                if (!$csrf->validate('updateAccounts')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if (post("email") == null || ($readSettings["authStatus"] == 1 && post("authStatus") == null)) {
                  echo alertError(t__('Please fill all the fields!'));
                }
                else if (post("password") == null) {
                  echo alertError(t__('Enter your password to save changes!'));
                }
                else {
                  $emailValid = $db->prepare("SELECT * FROM Accounts WHERE email = ?");
                  $emailValid->execute(array(post("email")));
                  $password = checkPassword($readSettings["passwordType"], post("password"), $readAccount["password"]);
                  
                  if (!$password) {
                    echo alertError(t__('Wrong password!'));
                  }
                  else if (checkEmail(post("email"))) {
                    echo alertError(t__('Please enter a valid email!'));
                  }
                  else if (post("email") != $readAccount["email"] && $emailValid->rowCount() > 0) {
                    echo alertError(t__('This email is already in use!'));
                  }
                  else {
                    if ($readAccount["email"] != post("email")) {
                      $loginToken = md5(uniqid(mt_rand(), true));
                      $updateAccounts = $db->prepare("UPDATE Accounts SET email = ? WHERE id = ?");
                      $updateAccounts->execute(array(post("email"), $readAccount["id"]));
                      $deleteAccountSessions = $db->prepare("DELETE FROM AccountSessions WHERE accountID = ?");
                      $deleteAccountSessions->execute(array($readAccount["id"]));
                      $insertAccountSessions = $db->prepare("INSERT INTO AccountSessions (accountID, loginToken, creationIP, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
                      $insertAccountSessions->execute(array($readAccount["id"], $loginToken, getIP(), createDuration(((isset($_COOKIE["rememberMe"])) ? 365 : 0.01666666666)), date("Y-m-d H:i:s")));
                      $_SESSION["login"] = $loginToken;
                      if (isset($_COOKIE["rememberMe"])) {
                        createCookie("rememberMe", $loginToken, 365, $sslStatus);
                      }
                    }
                    if ($accountSocialMedia->rowCount() > 0) {
                      $updateAccountSocialMedia = $db->prepare("UPDATE AccountSocialMedia SET skype = ?, discord = ? WHERE accountID = ?");
                      $updateAccountSocialMedia->execute(array(post("skype"), post("discord"), $readAccount["id"]));
                    }
                    else {
                      $insertAccountSocialMedia = $db->prepare("INSERT INTO AccountSocialMedia (accountID, skype, discord) VALUES (?, ?, ?)");
                      $insertAccountSocialMedia->execute(array($readAccount["id"], post("skype"), post("discord")));
                    }

                    if ($readSettings["authStatus"] == 1 && (post("authStatus") == 0 || post("authStatus") == 1)) {
                      if (post("authStatus") == 1 && $readAccount["authStatus"] == 0) {
                        $deleteAccountSessions = $db->prepare("DELETE FROM AccountSessions WHERE accountID = ? AND loginToken = ? AND creationIP = ?");
                        $deleteAccountSessions->execute(array($readAccount["id"], $_SESSION["login"], getIP()));
                        unset($_SESSION["login"]);
                        $_SESSION["tfa"] = array(
                          'accountID'     => $readAccount["id"],
                          'profileUpdate' => 'true',
                          'rememberMe'    => (isset($_COOKIE["rememberMe"])) ? 'true' : 'false',
                          'ipAddress'     => getIP(),
                          'expiryDate'    => createDuration(0.00347222222)
                        );
                        removeCookie("rememberMe");
                        go("/verify");
                      }
                      else {
                        $deleteAccountAuths = $db->prepare("DELETE FROM AccountAuths WHERE accountID = ?");
                        $deleteAccountAuths->execute(array($readAccount["id"]));
                        $updateAccounts = $db->prepare("UPDATE Accounts SET authStatus = ? WHERE id = ?");
                        $updateAccounts->execute(array(0, $readAccount["id"]));
                      }
                    }

                    echo alertSuccess(t__("Your profile has been updated."));
                  }
                }
              }
            ?>
            <div class="card">
              <div class="card-header">
                <h2 class="card-title">
                  <?php e__('Edit Profile') ?>
                </h2>
              </div>
              <div class="card-body">
                <form action="" method="post" class="needs-validation" novalidate="">
                  <div class="mb-3">
                    <label class="form-label px-0" for="inputEmail"><?php e__('Email') ?>:</label>
                    <div class="input-group">
                      <i class="shi-mail position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                      <input name="email" id="inputEmail" class="form-control rounded" type="email" required="" value="<?php echo $readAccount["email"]; ?>">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="mb-3">
                        <label class="form-label px-0" for="inputDiscord">Discord:</label>
                        <input name="discord" class="form-control" type="text" id="inputDiscord" value="<?php echo (($accountSocialMedia->rowCount() > 0 && $readAccountSocialMedia["discord"] != '0') ? $readAccountSocialMedia["discord"] : null); ?>">
                      </div>
                    </div>
                  </div>
                  <?php if ($readSettings["authStatus"] == 1): ?>
                    <div class="mb-3">
                      <label class="form-label px-0" for="inputEmail">
                        2FA:
                        <a href="https://egitim.leaderos.net/google-authenticator" rel="external">
                          <i class="shi-help-circle theme-color text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="İki Adımlı Doğrulama"></i>
                        </a>
                      </label>
                      <select class="form-control" name="authStatus" data-toggle="select2">
                        <option value="0" <?php echo ($readAccount["authStatus"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Disabled') ?></option>
                        <option value="1" <?php echo ($readAccount["authStatus"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Enabled') ?></option>
                      </select>
                    </div>
                  <?php endif; ?>
                  <hr>
                  <div class="mb-3">
                    <label class="form-label px-0" for="inputPassword"><?php e__('Password') ?>:</label>
                    <div class="input-group">
                      <i class="shi-lock position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                      <div class="password-toggle w-100">
                        <input name="password" id="inputPassword" class="form-control" type="password" placeholder="<?php e__('You must enter your current password.') ?>" required="">
                        <label class="password-toggle-btn" aria-label="Toggle Password">
                          <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateAccounts'); ?>
                  <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success btn-rounded" name="updateAccounts"><?php e__('Save Changes') ?></button>
                  </div>
                </form>
              </div>
            </div>
          <?php elseif (get("action") == 'change-password'): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["changePassword"])) {
                if (!$csrf->validate('changePassword')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if ((post("currentPassword") == null) || (post("password") == null) || (post("passwordRe") == null)) {
                  echo alertError(t__('Please fill all the fields!'));
                }
                else {
                  $currentPassword = checkPassword($readSettings["passwordType"], post("currentPassword"), $readAccount["password"]);
                  
                  if (!$currentPassword) {
                    echo alertError(t__('Your current password is incorrect!'));
                  }
                  else if (strlen(post("password")) < 4) {
                    echo alertError(t__('Your password must be at least 4 characters long!'));
                  }
                  else if (post("password") != post("passwordRe")) {
                    echo alertError(t__('Passwords do not match!'));
                  }
                  else if (checkBadPassword(post("password"))) {
                    echo alertError(t__('Your password is too weak!'));
                  }
                  else {
                    $loginToken = md5(uniqid(mt_rand(), true));
                    $password = createPassword($readSettings["passwordType"], post("password"));
                    
                    $updateAccounts = $db->prepare("UPDATE Accounts SET password = ? WHERE id = ?");
                    $updateAccounts->execute(array($password, $readAccount["id"]));
                    $deleteAccountSessions = $db->prepare("DELETE FROM AccountSessions WHERE accountID = ?");
                    $deleteAccountSessions->execute(array($readAccount["id"]));
                    $insertAccountSessions = $db->prepare("INSERT INTO AccountSessions (accountID, loginToken, creationIP, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
                    $insertAccountSessions->execute(array($readAccount["id"], $loginToken, getIP(), createDuration(((isset($_COOKIE["rememberMe"])) ? 365 : 0.01666666666)), date("Y-m-d H:i:s")));
                    echo alertSuccess(t__('Your password has been changed!'));
                    $_SESSION["login"] = $loginToken;
                    if (isset($_COOKIE["rememberMe"])) {
                      createCookie("rememberMe", $loginToken, 365, $sslStatus);
                    }
                  }
                }
              }
            ?>
            <div class="card">
              <div class="card-header">
                <h2 class="card-title">
                  <?php e__('Change Password') ?>
                </h2>
              </div>
              <div class="card-body">
                <form action="" method="post" class="needs-validation" novalidate="">
                  <div class="mb-3">
                    <label class="form-label px-0" for="inputCurretPassword"><?php e__('Current Password') ?>:</label>
                    <div class="input-group">
                      <i class="shi-lock position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                      <div class="password-toggle w-100">
                        <input name="currentPassword" id="inputCurretPassword" class="form-control" type="password" placeholder="<?php e__('Enter your current password.') ?>" required="">
                        <label class="password-toggle-btn" aria-label="Şifreyi Gizle/Göster">
                          <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <hr>
                  <div class="mb-3">
                    <label class="form-label px-0" for="inputPassword"><?php e__('New Password') ?>:</label>
                    <div class="input-group">
                      <i class="shi-lock position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                      <div class="password-toggle w-100">
                        <input name="password" class="form-control" id="inputPassword" type="password" required="">
                        <label class="password-toggle-btn" aria-label="Şifreyi Gizle/Göster">
                          <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label px-0" for="inputPasswordRe"><?php e__('Confirm New Password') ?>:</label>
                    <div class="input-group">
                      <i class="shi-lock position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                      <div class="password-toggle w-100">
                        <input name="passwordRe" id="inputPasswordRe" class="form-control" type="password" required="">
                        <label class="password-toggle-btn" aria-label="Şifreyi Gizle/Göster">
                          <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('changePassword'); ?>
                  <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success btn-rounded" name="changePassword"><?php e__('Change Password') ?></button>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php go('/404'); ?>
          <?php endif; ?>
        <?php else: ?>
          <?php go('/404'); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
