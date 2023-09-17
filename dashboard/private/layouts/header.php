<header>
  <nav class="navbar navbar-expand-md navbar-light d-none d-md-flex">
    <div class="container-fluid">
      <a class="navbar-brand mr-auto" href="/dashboard">
        <?php e__('Dashboard') ?>
      </a>
      <div class="navbar-user">

        <div class="d-flex mr-4">
          <a href="/" class="text-muted" rel="external" data-toggle="tooltip" data-placement="bottom" title="<?php e__('View Website') ?>">
            <i class="fe fe-home"></i>
          </a>
        </div>

        <div class="d-flex mr-4">
          <a href="#modalCustomize" class="text-muted" data-toggle="modal">
            <div data-toggle="tooltip" data-placement="bottom" title="<?php e__('Customize') ?>">
              <i class="fe fe-sliders"></i>
            </div>
          </a>
        </div>

        <div class="d-flex mr-4">
          <a href="https://help.leaderos.net/" class="text-muted" rel="external" data-toggle="tooltip" data-placement="bottom" title="<?php e__('Help') ?>">
            <i class="fe fe-help-circle"></i>
          </a>
        </div>

        <!-- Dropdown -->
        <?php if (checkPerm($readAdmin, 'MANAGE_NOTIFICATIONS')): ?>
          <div class="dropdown mr-4 d-none d-lg-flex">
            <!-- Toggle -->
            <a href="#" class="text-muted" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="icon <?php echo (($notificationsUnreadeds->rowCount() > 0) || (($needUpdate == true && checkPerm($readAdmin, 'MANAGE_UPDATES')))) ? "active" : null; ?>">
                <i class="fe fe-bell"></i>
              </span>
            </a>
            <!-- Menu -->
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-card">
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <!-- Title -->
                    <h5 class="card-header-title">
                      <?php e__('Notifications') ?>
                    </h5>
                  </div>
                  <div class="col-auto">
                    <!-- Link -->
                    <a href="/dashboard/notifications" class="small">
                      <?php e__('View All') ?>
                    </a>
                  </div>
                </div> <!-- / .row -->
              </div> <!-- / .card-header -->
              <div class="card-body px-0">
                <!-- List group -->
                <div class="list-group list-group-flush my-n3">
                  <?php if ($needUpdate == true && checkPerm($readAdmin, 'MANAGE_UPDATES')): ?>
                    <div class="list-group-item px-4">
                      <div class="row">
                        <div class="col-auto d-flex align-items-center">
                          <!-- Avatar -->
                          <div class="avatar avatar-sm d-flex align-items-center">
                            <i class="fe fe-alert-circle" style="font-size: 32px;"></i>
                          </div>
                        </div>
                        <div class="col ml-n2">
                          <!-- Content -->
                          <div class="small text-muted">
                            <strong class="text-danger"><?php e__('System') ?>:</strong>
                            <span><?php e__('A new update is available! Latest Version: %version%', ['version' => $newVersion]) ?></span>
                          </div>
                        </div>
                        <div class="col-auto">
                          <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/update" data-toggle="tooltip" data-placement="top" title="<?php e__('Update') ?>">
                            <i class="fe fe-refresh-cw"></i>
                          </a>
                        </div>
                      </div> <!-- / .row -->
                    </div>
                  <?php endif; ?>
                  <?php
                    $notifications = $db->prepare("SELECT N.*, A.realname FROM Notifications N INNER JOIN Accounts A ON N.accountID = A.id WHERE N.type in (?, ?, ?, ?) AND N.creationDate > ? ORDER BY N.id DESC LIMIT 5");
                    $notifications->execute(array(1, 2, 3, 4, ($readAdmin["lastReadDate"]) ? $readAdmin["lastReadDate"] : '1000-01-01 00:00:00'));
                  ?>
                  <?php if ($notifications->rowCount() > 0): ?>
                    <?php foreach ($notifications as $readNotifications): ?>
                      <a class="notification-block list-group-item px-4" href="<?php echo (($readNotifications["type"] == 1) ? "/dashboard/support/view/".$readNotifications["variables"] : (($readNotifications["type"] == 2) ? "/dashboard/blog/comments/edit/".$readNotifications["variables"] : "#")) ?>">
                        <div class="row">
                          <div class="col-auto">
                            <!-- Avatar -->
                            <div class="avatar avatar-sm">
                              <?php echo minecraftHead($readSettings["avatarAPI"], $readNotifications["realname"], 32, "avatar-img"); ?>
                            </div>
                          </div>
                          <div class="col ml-n2">
                            <!-- Content -->
                            <div class="small text-muted">
                              <strong class="text-primary"><?php echo $readNotifications["realname"]; ?> </strong>
                              <?php if ($readNotifications["type"] == 1): ?>
                                <?php e__('sent a support message!') ?>
                              <?php elseif ($readNotifications["type"] == 2): ?>
                                <?php e__('commented on the article!') ?>
                              <?php elseif ($readNotifications["type"] == 3): ?>
                                <?php e__('has bought %credit% credits.', ['%credit%' => $readNotifications["variables"]]) ?>
                              <?php elseif ($readNotifications["type"] == 4): ?>
                                <?php $readNotifications["variables"] = explode(",", $readNotifications["variables"]); ?>
                                <?php e__('has bought %product% from %server%', ['%product%' => $readNotifications["variables"][0], '%server%' => $readNotifications["variables"][1]]) ?>
                              <?php else: ?>
                                <?php e__('Error') ?>
                              <?php endif; ?>
                            </div>
                          </div>
                          <div class="col-auto">
                            <small class="text-muted">
                              <?php echo convertTime($readNotifications["creationDate"]); ?>
                            </small>
                          </div>
                        </div> <!-- / .row -->
                      </a>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <?php if (checkPerm($readAdmin, 'MANAGE_UPDATES')): ?>
                      <?php if ($needUpdate == false): ?>
                        <span class="text-muted text-center"><?php e__('No notifications') ?></span>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="text-muted text-center"><?php e__('No notifications') ?></span>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div> <!-- / .dropdown-menu -->
          </div>
        <?php endif; ?>
        
        <div class="dropdown">
          <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="clearfix">
              <div class="account-img avatar avatar-sm avatar-online float-left mr-2">
                <?php echo minecraftHead($readSettings["avatarAPI"], $readAdmin["realname"], 40, "avatar-img"); ?>
              </div>
              <div class="account-info float-left">
                <span class="account-username"><?php echo $readAdmin["realname"]; ?></span>
                <span class="account-position">
                  <?php echo styledRoles($readAdmin["roles"]); ?>
                </span>
              </div>
            </div>
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <a href="/dashboard/users/view/<?php echo $readAdmin["id"]; ?>" class="dropdown-item">
              <i class="fe fe-user mr-2"></i> <?php e__('Profile') ?>
            </a>
            <a href="/dashboard/settings/general" class="dropdown-item">
              <i class="fe fe-settings mr-2"></i> <?php e__('General Settings') ?>
            </a>
            <hr class="dropdown-divider">
            <a class="dropdown-item" href="/logout" onclick="return confirm('<?php e__('Are you sure you want to logout?') ?>');">
              <i class="fe fe-power mr-2"></i> <?php e__('Logout') ?>
            </a>
          </div>
        </div>
      </div>
    </div>
  </nav>
</header>
