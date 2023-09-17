<?php
  if (isset($_SESSION["login"]) && strtolower($readAccount["realname"]) == strtolower(get("id"))) {
    go('/profile');
  }
?>
<section class="section profile-section">
  <div class="container">
    <?php if (isset($_GET["id"])): ?>
      <?php
        $player = $db->prepare("SELECT * FROM Accounts WHERE realname = ? ORDER BY id DESC LIMIT 1");
        $player->execute(array(get("id")));
        $readPlayer = $player->fetch();
      ?>
      <?php if ($player->rowCount() > 0): ?>
        <div class="row">
          <div class="col-md-12">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><?php e__('Home') ?></a></li>
                <li class="breadcrumb-item"><a href="/"><?php e__('Player') ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $readPlayer["realname"]; ?></li>
              </ol>
            </nav>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="d-flex justify-content-center py-3">
                <a href="/player/<?php echo $readPlayer["realname"]; ?>">
                  <?php echo minecraftHead($readSettings["avatarAPI"], $readPlayer["realname"], 70); ?>
                </a>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <label class="col-sm-5"><?php e__('Username') ?>:</label>
                  <label class="col-sm-7">
                    <?php echo $readPlayer["realname"]; ?>
                  </label>
                </div>
                <div class="form-group row">
                  <label class="col-sm-5"><?php e__('Role') ?>:</label>
                  <label class="col-sm-7">
                    <?php echo styledRoles(getRoles($readPlayer["id"])); ?>
                  </label>
                </div>
                <div class="form-group row">
                  <label class="col-sm-5"><?php e__('Balance') ?>:</label>
                  <label class="col-sm-7">
                    <?php echo $readPlayer["credit"]; ?>
                  </label>
                </div>
                <div class="form-group row">
                  <label class="col-sm-5"><?php e__('Last Login') ?>:</label>
                  <label class="col-sm-7">
                    <?php if ($readPlayer["lastlogin"] == 0): ?>
                      <?php e__('Not logged in') ?>
                    <?php else: ?>
                      <?php echo convertTime(date("Y-m-d H:i:s", ($readPlayer["lastlogin"]/1000)), 2, true); ?>
                    <?php endif; ?>
                  </label>
                </div>
                <div class="form-group row">
                  <label class="col-sm-5"><?php e__('Reg. Date') ?>:</label>
                  <label class="col-sm-7">
                    <?php if ($readPlayer["creationDate"] == "1000-01-01 00:00:00"): ?>
                      -
                    <?php else: ?>
                      <?php echo convertTime($readPlayer["creationDate"], 2, true); ?>
                    <?php endif; ?>
                  </label>
                </div>
                <?php
                  $accountSocialMedia = $db->prepare("SELECT * FROM AccountSocialMedia WHERE accountID = ?");
                  $accountSocialMedia->execute(array($readPlayer["id"]));
                  $readAccountSocialMedia = $accountSocialMedia->fetch();
                ?>
                <div class="form-group row">
                  <label class="col-sm-5">Discord:</label>
                  <label class="col-sm-7">
                    <?php if ($accountSocialMedia->rowCount() > 0): ?>
                      <?php echo (($readAccountSocialMedia["discord"] != '0') ? $readAccountSocialMedia["discord"] : "-"); ?>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </label>
                </div>
                <?php
                  $siteBannedAccountStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?) ORDER BY expiryDate DESC LIMIT 1");
                  $siteBannedAccountStatus->execute(array($readPlayer["id"], 1, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
                  $readSiteBannedAccountStatus = $siteBannedAccountStatus->fetch();
                ?>
                <?php if ($siteBannedAccountStatus->rowCount() > 0): ?>
                  <div class="form-group row">
                    <label class="col-sm-5"><?php e__('Ban (Website)') ?>:</label>
                    <label class="col-sm-7">
                      <?php echo ($readSiteBannedAccountStatus["expiryDate"] == '1000-01-01 00:00:00') ? t__('Perma ban') : t__('%day% day(s)', ['%day%' => getDuration($readSiteBannedAccountStatus["expiryDate"])]); ?>
                    </label>
                  </div>
                <?php endif; ?>
                <?php
                  $supportBannedAccountStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?) ORDER BY expiryDate DESC LIMIT 1");
                  $supportBannedAccountStatus->execute(array($readPlayer["id"], 2, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
                  $readSupportBannedAccountStatus = $supportBannedAccountStatus->fetch();
                ?>
                <?php if ($supportBannedAccountStatus->rowCount() > 0): ?>
                  <div class="form-group row">
                    <label class="col-sm-5"><?php e__('Ban (Support)') ?>:</label>
                    <label class="col-sm-7">
                      <?php echo ($readSupportBannedAccountStatus["expiryDate"] == '1000-01-01 00:00:00') ? t__('Perma ban') : t__('%day% day(s)', ['%day%' => getDuration($readSupportBannedAccountStatus["expiryDate"])]); ?>
                    </label>
                  </div>
                <?php endif; ?>
                <?php
                  $commentBannedAccountStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?) ORDER BY expiryDate DESC LIMIT 1");
                  $commentBannedAccountStatus->execute(array($readPlayer["id"], 3, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
                  $readCommentBannedAccountStatus = $commentBannedAccountStatus->fetch();
                ?>
                <?php if ($commentBannedAccountStatus->rowCount() > 0): ?>
                  <div class="form-group row">
                    <label class="col-sm-5"><?php e__('Ban (Comment)') ?>:</label>
                    <label class="col-sm-7">
                      <?php echo ($readCommentBannedAccountStatus["expiryDate"] == '1000-01-01 00:00:00') ? t__('Perma ban') : t__('%day% day(s)', ['%day%' => getDuration($readCommentBannedAccountStatus["expiryDate"])]); ?>
                    </label>
                  </div>
                <?php endif; ?>
                <?php if (isset($_SESSION["login"]) && $readSettings["creditStatus"] == 1): ?>
                  <div class="row">
                    <div class="col-md-12">
                      <a class="btn btn-success w-100 mb-2" href="/credit/send/<?php echo $readPlayer["id"]; ?>"><?php e__('Send Credits') ?></a>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="col-md-8">
            <?php
              $statServers = $db->query("SELECT serverName, serverSlug FROM Leaderboards");
              $statServers->execute();
            ?>
            <?php if ($statServers->rowCount() > 0): ?>
              <div class="card">
                <nav>
                  <div class="nav nav-tabs nav-fill">
                    <?php foreach ($statServers as $readStatServers): ?>
                      <?php
                        if (!get("leaderboard")) {
                          $_GET["leaderboard"] = $readStatServers["serverSlug"];
                        }
                      ?>
                      <a class="nav-item nav-link <?php echo (get("leaderboard") == $readStatServers["serverSlug"]) ? "active" : null; ?>" id="nav-<?php echo $readStatServers["serverSlug"]; ?>-tab" href="?leaderboard=<?php echo $readStatServers["serverSlug"]; ?>">
                        <?php echo $readStatServers["serverName"]; ?>
                      </a>
                    <?php endforeach; ?>
                  </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                  <?php
                    $statServer = $db->prepare("SELECT * FROM Leaderboards WHERE serverSlug = ?");
                    $statServer->execute(array(get("leaderboard")));
                    $readStatServer = $statServer->fetch();
                  ?>
                  <?php if ($statServer->rowCount() > 0): ?>
                    <?php
                      $usernameColumn = $readStatServer["usernameColumn"];
                      $mysqlTable = $readStatServer["mysqlTable"];
                      $sorter = $readStatServer["sorter"];
                      $tableTitles = $readStatServer["tableTitles"];
                      $tableData = $readStatServer["tableData"];
                      $tableTitlesArray = explode(",", $tableTitles);
                      $tableDataArray = explode(",", $tableData);

                      if ($readStatServer["mysqlServer"] == '0') {
                        $accountOrder = $db->prepare("SELECT $usernameColumn,$tableData FROM $mysqlTable WHERE $usernameColumn = ? ORDER BY $sorter DESC");
                        $accountOrder->execute(array($readPlayer["realname"]));
                      }
                      else {
                        try {
                          $newDB = new PDO("mysql:host=".$readStatServer["mysqlServer"]."; port=".$readStatServer["mysqlPort"]."; dbname=".$readStatServer["mysqlDatabase"]."; charset=utf8", $readStatServer["mysqlUsername"], $readStatServer["mysqlPassword"]);
                        }
                        catch (PDOException $e) {
                          die("<strong>MySQL connection error:</strong> ".utf8_encode($e->getMessage()));
                        }
                        $accountOrder = $newDB->prepare("SELECT $usernameColumn,$tableData FROM $mysqlTable WHERE $usernameColumn = ? ORDER BY $sorter DESC");
                        $accountOrder->execute(array($readPlayer["realname"]));
                      }
                    ?>
                    <div class="tab-pane fade <?php echo (get("leaderboard") == $readStatServer["serverSlug"]) ? "show active" : null; ?>" id="nav-<?php echo $readStatServer["serverSlug"] ?>">
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
                                      <?php if ($readUserPosition[$usernameColumn] == $readPlayer["realname"]): ?>
                                        <?php if ($rank == 1): ?>
                                          <strong class="text-success">1</strong>
                                        <?php elseif ($rank == 2): ?>
                                          <strong class="text-warning">2</strong>
                                        <?php elseif ($rank == 3): ?>
                                          <strong class="text-danger">3</strong>
                                        <?php else: ?>
                                          <?php echo $rank; ?>
                                        <?php endif; ?>
                                        <?php break; ?>
                                      <?php endif; ?>
                                      <?php $rank++; ?>
                                    <?php endforeach; ?>
                                  </td>
                                  <td class="text-center" style="width: 20px;">
                                    <?php echo minecraftHead($readSettings["avatarAPI"], $readPlayer["realname"], 20); ?>
                                  </td>
                                  <td>
                                    <?php echo $readPlayer["realname"]; ?>
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
                  <?php endif; ?>
                </div>
              </div>
            <?php else: ?>
              <?php echo alertError(t__('Leaderboard not found!')); ?>
            <?php endif; ?>
          </div>
        </div>
      <?php else: ?>
        <?php echo alertError(t__('Player not found!')); ?>
      <?php endif; ?>
    <?php else: ?>
      <?php go("/404"); ?>
    <?php endif; ?>
  </div>
</section>
