<?php
  if (checkPerm($readAdmin, 'MANAGE_NOTIFICATIONS')) {
    $notificationsUnreadeds = $db->prepare("SELECT id FROM Notifications WHERE creationDate > ? ORDER BY id LIMIT 100");
    $notificationsUnreadeds->execute(array((($readAdmin["lastReadDate"]) ? $readAdmin["lastReadDate"] : '1000-01-01 00:00:00')));
  }
?>
<!-- Modal: Customize -->
<div class="modal fade fixed-right" id="modalCustomize" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-vertical" role="document">
    <form class="modal-content" id="themeForm">
      <div class="modal-body">
        <!-- Close -->
        <a class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </a>
        <h2 class="text-center mb-2">
          <?php e__('Customize') ?>
        </h2>
        <hr class="mb-4">
        <h4 class="mb-1">
          <?php e__('Theme') ?>
        </h4>
        <p class="small text-muted mb-3">
          <?php e__('You can choose the theme here.') ?>
        </p>
        <div class="btn-group-toggle d-flex mb-4" data-toggle="buttons">
          <label class="btn btn-white active col">
            <input type="radio" name="colorScheme" id="colorSchemeLight" value="light"><i class="fe fe-sun mr-2"></i> Light
          </label>
          <label class="btn btn-white col ml-2">
            <input type="radio" name="colorScheme" id="colorSchemeDark" value="dark"><i class="fe fe-moon mr-2"></i> Dark
          </label>
        </div>
        <input type="radio" id="navPositionCombo" class="d-none" name="navPosition" value="combo" checked>
        <input type="radio" id="sidebarColorDefault" class="d-none" name="sidebarColor" value="default" checked>
      </div>

      <div class="modal-footer border-0">
        <button type="submit" class="btn btn-block btn-success mt-auto">
          <?php e__('Save Changes') ?>
        </button>
      </div>
    </form>
  </div>
</div>
<nav id="navbar" class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light">
  <div class="container-fluid">
    <!-- Toggler -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidebarCollapse" aria-controls="sidebarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- Brand -->
    <a class="navbar-brand" href="/dashboard">
      <?php echo $serverName; ?>
    </a>
    <!-- User (xs) -->
    <div class="navbar-user d-md-none">
      <!-- Dropdown -->
      <div class="dropdown">
        <!-- Toggle -->
        <a href="#!" id="sidebarIcon" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <div class="avatar avatar-sm avatar-online">
            <?php echo minecraftHead($readSettings["avatarAPI"], $readAdmin["realname"], 40, "avatar-img"); ?>
          </div>
        </a>
        <!-- Menu -->
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="sidebarIcon">
          <a href="/dashboard/users/view/<?php echo $readAdmin["id"]; ?>" class="dropdown-item">
            <i class="fe fe-user mr-2"></i> <?php e__('Profile') ?>
          </a>
          <?php if (checkPerm($readAdmin, 'MANAGE_NOTIFICATIONS')): ?>
            <a href="/dashboard/notifications" class="dropdown-item">
              <?php
                $notificationCount = $notificationsUnreadeds->rowCount();
                if ($needUpdate == true) {
                  $notificationCount++;
                }
              ?>
              <i class="fe fe-bell mr-2"></i> <?php e__('Notifications') ?> (<?php echo (($notificationCount > 99) ? '99+' : $notificationCount); ?>)
            </a>
          <?php endif; ?>
          <?php if (checkPerm($readAdmin, 'MANAGE_SETTINGS')): ?>
            <a href="/dashboard/settings/general" class="dropdown-item">
              <i class="fe fe-settings mr-2"></i> <?php e__('General Settings') ?>
            </a>
          <?php endif; ?>
          <hr class="dropdown-divider">
          <a href="/" rel="external" class="dropdown-item">
            <i class="fe fe-home mr-2"></i> <?php e__('View Website') ?>
          </a>
          <a href="#modalCustomize" class="dropdown-item" data-toggle="modal">
            <i class="fe fe-sliders mr-2"></i> <?php e__('Customize') ?>
          </a>
          <a href="https://help.leaderos.net/" rel="external" class="dropdown-item">
            <i class="fe fe-help-circle mr-2"></i> <?php e__('Help') ?>
          </a>
          <hr class="dropdown-divider">
          <a href="/logout" class="dropdown-item">
            <i class="fe fe-power mr-2"></i> <?php e__('Logout') ?>
          </a>
        </div>
      </div>
    </div>
    <!-- Collapse -->
    <div class="collapse navbar-collapse" id="sidebarCollapse">
      <!-- Navigation -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link <?php echo (get("route") == "home") ? "active" : null; ?>" href="/dashboard">
            <i class="fe fe-activity"></i> <?php e__('Dashboard') ?>
          </a>
        </li>
        <?php if (checkPerm($readAdmin, 'MANAGE_FORUM')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarForum" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-message-square"></i> <?php e__('Forum') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "forum") ? "show" : null; ?>" id="sidebarForum">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/forum/threads" class="nav-link <?php echo ((get("route") == "forum") && (get("target") == "thread") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Threads') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/forum/categories" class="nav-link <?php echo ((get("route") == "forum") && (get("target") == "category") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Categories') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/forum/categories/create" class="nav-link <?php echo ((get("route") == "forum") && (get("target") == "category") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Category') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_BLOG')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarNews" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-edit"></i> <?php e__('Blog') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "news") ? "show" : null; ?>" id="sidebarNews">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/blog" class="nav-link <?php echo ((get("route") == "news") && (get("target") == "news") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Posts') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/blog/create" class="nav-link <?php echo ((get("route") == "news") && (get("target") == "news") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Post') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/blog/categories" class="nav-link <?php echo ((get("route") == "news") && (get("target") == "category") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Categories') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/blog/categories/create" class="nav-link <?php echo ((get("route") == "news") && (get("target") == "category") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Category') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/blog/comments" class="nav-link <?php echo ((get("route") == "news") && (get("target") == "comment") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Comments') ?>
                    <?php
                      $unconfirmedNewsComments = $db->prepare("SELECT id FROM NewsComments WHERE status = ?");
                      $unconfirmedNewsComments->execute(array(0));
                    ?>
                    <?php if ($unconfirmedNewsComments->rowCount() > 0): ?>
                      <span class="badge badge-primary rounded-pill ml-auto"><?php echo $unconfirmedNewsComments->rowCount(); ?></span>
                    <?php endif; ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_GAMES')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarGame" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-play-circle"></i> <?php e__('Game') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "game") ? "show" : null; ?>" id="sidebarGame">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/games" class="nav-link <?php echo ((get("route") == "game") && (get("target") == "game") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Games') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/games/create" class="nav-link <?php echo ((get("route") == "game") && (get("target") == "game") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Game') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_SERVERS')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarServer" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-server"></i> <?php e__('Server') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "server") ? "show" : null; ?>" id="sidebarServer">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/servers" class="nav-link <?php echo ((get("route") == "server") && (get("target") == "server") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Servers') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/servers/create" class="nav-link <?php echo ((get("route") == "server") && (get("target") == "server") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Server') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_STORE')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarStore" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-shopping-cart"></i> <?php e__('Store') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "store") ? "show" : null; ?>" id="sidebarStore">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/store/products" class="nav-link <?php echo ((get("route") == "store") && (get("target") == "product") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Products') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/store/products/create" class="nav-link <?php echo ((get("route") == "store") && (get("target") == "product") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Product') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/store/categories" class="nav-link <?php echo ((get("route") == "store") && (get("target") == "category") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Categories') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/store/categories/create" class="nav-link <?php echo ((get("route") == "store") && (get("target") == "category") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Category') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/store/coupons" class="nav-link <?php echo ((get("route") == "store") && (get("target") == "coupon") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Coupons') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/store/coupons/create" class="nav-link <?php echo ((get("route") == "store") && (get("target") == "coupon") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Coupon') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/store/credit/send" class="nav-link <?php echo ((get("route") == "store") && (get("target") == "credit") && (get("action") == "send")) ? "active" : null; ?>">
                    <?php e__('Send Credits') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/store/chest-item/send" class="nav-link <?php echo ((get("route") == "store") && (get("target") == "chest") && (get("action") == "send")) ? "active" : null; ?>">
                    <?php e__('Send Chest Item') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/store/bulk-discount" class="nav-link <?php echo ((get("route") == "store") && (get("target") == "discount") && (get("action") == "update")) ? "active" : null; ?>">
                    <?php e__('Bulk Discount') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/store/chest-logs" class="nav-link <?php echo ((get("route") == "store") && (get("target") == "chest-history") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Chest Logs') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/store/coupon-logs" class="nav-link <?php echo ((get("route") == "store") && (get("target") == "coupon-history") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Coupon Logs') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/store/credit-purchase-logs" class="nav-link <?php echo ((get("route") == "store") && (get("target") == "credit-purchase-history") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Credit Purchase Logs') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/store/credit-usage-logs" class="nav-link <?php echo ((get("route") == "store") && (get("target") == "credit-usage-history") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Credit Usage Logs') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/store/store-logs" class="nav-link <?php echo ((get("route") == "store") && (get("target") == "store-history") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Store Logs') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarVIP" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-layout"></i> <?php e__('VIP Table') ?>
            </a>
            <div class="collapse <?php echo ((get("route") == "vip-table") && get("target") == "vip") ? "show" : null; ?>" id="sidebarVIP">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/vip/table" class="nav-link <?php echo ((get("route") == "vip-table") && (get("target") == "vip") && (get("action") == "list")) ? "active" : null; ?>">
                    <?php e__('VIP Table') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/vip/table/create" class="nav-link <?php echo ((get("route") == "vip-table") && (get("target") == "vip") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add VIP Table') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/vip/features" class="nav-link <?php echo ((get("route") == "vip-table") && (get("target") == "vip") && (get("action") == "featureList")) ? "active" : null; ?>">
                    <?php e__('VIP Features') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/vip/descriptions" class="nav-link <?php echo ((get("route") == "vip-table") && (get("target") == "vip") && (get("action") == "explainList")) ? "active" : null; ?>">
                    <?php e__('VIP Descriptions') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_GAMING_NIGHT')): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo (get("route") == "gaming-night") ? "active" : null; ?>" href="/dashboard/gaming-night">
              <i class="fe fe-moon"></i> <?php e__('Gaming Night') ?>
            </a>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_LOTTERY')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarLottery" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-pie-chart"></i> <?php e__('Fortune Wheel') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "lottery") ? "show" : null; ?>" id="sidebarLottery">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/fortune-wheel" class="nav-link <?php echo ((get("route") == "lottery") && (get("target") == "lottery") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Fortune Wheels') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/fortune-wheel/create" class="nav-link <?php echo ((get("route") == "lottery") && (get("target") == "lottery") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Fortune Wheel') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/fortune-wheel/logs" class="nav-link <?php echo ((get("route") == "lottery") && (get("target") == "lottery-history") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Fortune Wheel Logs') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_BAZAAR')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarBazaar" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-shopping-bag"></i> <?php e__('Bazaar') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "bazaar") ? "show" : null; ?>" id="sidebarBazaar">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/bazaar" class="nav-link <?php echo ((get("route") == "bazaar") && (get("target") == "bazaar") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Bazaar') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/bazaar/bazaar-logs" class="nav-link <?php echo ((get("route") == "bazaar") && (get("target") == "bazaar-history") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Bazaar Logs') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_GIFTS')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarGift" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-gift"></i> <?php e__('Gift') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "gift") ? "show" : null; ?>" id="sidebarGift">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/gifts" class="nav-link <?php echo ((get("route") == "gift") && (get("target") == "gift") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Gifts') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/gifts/create" class="nav-link <?php echo ((get("route") == "gift") && (get("target") == "gift") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Gift') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/gifts/logs" class="nav-link <?php echo ((get("route") == "gift") && (get("target") == "gift-history") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Gift Logs') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_ACCOUNTS')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarAccount" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-users"></i> <?php e__('User') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "account" || get("route") == "role") ? "show" : null; ?>" id="sidebarAccount">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/users" class="nav-link <?php echo ((get("route") == "account") && (get("target") == "account") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Users') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/users/create" class="nav-link <?php echo ((get("route") == "account") && (get("target") == "account") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add User') ?>
                  </a>
                </li>
                <?php if (checkPerm($readAdmin, 'MANAGE_ROLES')): ?>
                  <li class="nav-item">
                    <a href="/dashboard/roles" class="nav-link <?php echo ((get("route") == "role") && (get("target") == "role") && (get("action") == "getAll")) ? "active" : null; ?>">
                      <?php e__('Roles') ?>
                    </a>
                  </li>
                <?php endif; ?>
                <li class="nav-item">
                  <a href="/dashboard/users/staffs" class="nav-link <?php echo ((get("route") == "account") && (get("target") == "authorized") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Staffs') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_SUPPORT')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarSupport" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-life-buoy"></i> <?php e__('Support') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "support") ? "show" : null; ?>" id="sidebarSupport">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/support" class="nav-link <?php echo ((get("route") == "support") && (get("target") == "support") && (get("category") == false) && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('All') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/support/waiting-reply" class="nav-link <?php echo ((get("route") == "support") && (get("target") == "support") && (get("category") == "unread") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Waiting Reply') ?>
                    <?php
                      $unreadSupports = $db->prepare("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.statusID IN (?, ?)");
                      $unreadSupports->execute(array(1, 3));
                    ?>
                    <?php if ($unreadSupports->rowCount() > 0): ?>
                      <span class="badge badge-warning rounded-pill ml-auto"><?php echo $unreadSupports->rowCount(); ?></span>
                    <?php endif; ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/support/answered" class="nav-link <?php echo ((get("route") == "support") && (get("target") == "support") && (get("category") == "readed") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Answered') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/support/closed" class="nav-link <?php echo ((get("route") == "support") && (get("target") == "support") && (get("category") == "closed") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Closed') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/support/categories" class="nav-link <?php echo ((get("route") == "support") && (get("target") == "category") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Categories') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/support/categories/create" class="nav-link <?php echo ((get("route") == "support") && (get("target") == "category") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Category') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/support/quick-answers" class="nav-link <?php echo ((get("route") == "support") && (get("target") == "answer") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Quick Answers') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/support/quick-answers/create" class="nav-link <?php echo ((get("route") == "support") && (get("target") == "answer") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Quick Answer') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_LEADERBOARDS')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarLeaderboard" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-list"></i> <?php e__('Leaderboard') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "leaderboards") ? "show" : null; ?>" id="sidebarLeaderboard">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/leaderboards" class="nav-link <?php echo ((get("route") == "leaderboards") && (get("target") == "leaderboards") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Leaderboards') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/leaderboards/create" class="nav-link <?php echo ((get("route") == "leaderboards") && (get("target") == "leaderboards") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Leaderboard') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_PAGES')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarPage" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-layout"></i> <?php e__('Page') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "page") ? "show" : null; ?>" id="sidebarPage">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/pages" class="nav-link <?php echo ((get("route") == "page") && (get("target") == "page") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Pages') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/pages/create" class="nav-link <?php echo ((get("route") == "page") && (get("target") == "page") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Page') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_BANS')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarBannedAccounts" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-slash"></i> <?php e__('Ban') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "banned") ? "show" : null; ?>" id="sidebarBannedAccounts">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/bans" class="nav-link <?php echo ((get("route") == "banned") && (get("target") == "ban") && (!get("category")) && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('All') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/bans/website" class="nav-link <?php echo ((get("route") == "banned") && (get("target") == "ban") && (get("category") == "site") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Website') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/bans/support" class="nav-link <?php echo ((get("route") == "banned") && (get("target") == "ban") && (get("category") == "support") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Support') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/bans/comment" class="nav-link <?php echo ((get("route") == "banned") && (get("target") == "ban") && (get("category") == "comment") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Comment') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/bans/create" class="nav-link <?php echo ((get("route") == "banned") && (get("target") == "ban") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Ban User') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_DOWNLOADS')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarDownload" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-download"></i> <?php e__('Download') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "download") ? "show" : null; ?>" id="sidebarDownload">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/files" class="nav-link <?php echo ((get("route") == "download") && (get("target") == "file") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Files') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/files/create" class="nav-link <?php echo ((get("route") == "download") && (get("target") == "file") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add File') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_BROADCAST')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarBroadcast" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-rss"></i> <?php e__('Announcement') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "broadcast") ? "show" : null; ?>" id="sidebarBroadcast">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/announcements" class="nav-link <?php echo ((get("route") == "broadcast") && (get("target") == "broadcast") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Announcements') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/announcements/create" class="nav-link <?php echo ((get("route") == "broadcast") && (get("target") == "broadcast") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Announcement') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_SLIDER')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarSlider" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-image"></i> Slider
            </a>
            <div class="collapse <?php echo (get("route") == "slider") ? "show" : null; ?>" id="sidebarSlider">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/slider" class="nav-link <?php echo ((get("route") == "slider") && (get("target") == "slider") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Slider Items') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/slider/create" class="nav-link <?php echo ((get("route") == "slider") && (get("target") == "slider") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Slider Item') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_APPLICATIONS')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarApplication" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-file-text"></i> <?php e__('Application') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "application") ? "show" : null; ?>" id="sidebarApplication">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/applications" class="nav-link <?php echo ((get("route") == "application") && (get("target") == "application") && (get("action") == "getAll") && get("status") == null) ? "active" : null; ?>">
                    <?php e__('All') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/applications?status=0" class="nav-link <?php echo ((get("route") == "application") && (get("target") == "application") && (get("action") == "getAll") && get("status") === "0") ? "active" : null; ?>">
                    <?php e__('Rejected') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/applications?status=1" class="nav-link <?php echo ((get("route") == "application") && (get("target") == "application") && (get("action") == "getAll") && get("status") === "1") ? "active" : null; ?>">
                    <?php e__('Approved') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/applications?status=2" class="nav-link <?php echo ((get("route") == "application") && (get("target") == "application") && (get("action") == "getAll") && get("status") === "2") ? "active" : null; ?>">
                    <?php e__('Pending Approval') ?>
                    <?php
                      $pendingApplications = $db->prepare("SELECT id FROM Applications WHERE status = ?");
                      $pendingApplications->execute(array(2));
                    ?>
                    <?php if ($pendingApplications->rowCount() > 0): ?>
                      <span class="badge badge-warning rounded-pill ml-auto"><?php echo $pendingApplications->rowCount(); ?></span>
                    <?php endif; ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/applications/forms" class="nav-link <?php echo ((get("route") == "application") && (get("target") == "form") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Application Forms') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/applications/forms/create" class="nav-link <?php echo ((get("route") == "application") && (get("target") == "form") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Application Form') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_CUSTOM_FORMS')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarCustomForm" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-file-text"></i> <?php e__('Custom Forms') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "form") ? "show" : null; ?>" id="sidebarCustomForm">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/form-answers" class="nav-link <?php echo ((get("route") == "form") && (get("target") == "answers") && (get("action") == "getAll") && get("status") == null) ? "active" : null; ?>">
                    <?php e__('Form Answers') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/form" class="nav-link <?php echo ((get("route") == "form") && (get("target") == "form") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Custom Forms') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/form/create" class="nav-link <?php echo ((get("route") == "form") && (get("target") == "form") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Custom Form') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_HELP_CENTER')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarHelpCenter" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-help-circle"></i> <?php e__('Help Center'); ?>
            </a>
            <div class="collapse <?php echo (get("route") == "help") ? "show" : null; ?>" id="sidebarHelpCenter">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/help" class="nav-link <?php echo ((get("route") == "help") && (get("target") == "article") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Articles') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/help/create" class="nav-link <?php echo ((get("route") == "help") && (get("target") == "article") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Article') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/help/topics" class="nav-link <?php echo ((get("route") == "help") && (get("target") == "topic") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Topics') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/help/topics/create" class="nav-link <?php echo ((get("route") == "help") && (get("target") == "topic") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Topic') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_PAYMENT')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarPayment" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-dollar-sign"></i> <?php e__('Payment') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "payment") ? "show" : null; ?>" id="sidebarPayment">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/payment" class="nav-link <?php echo ((get("route") == "payment") && (get("target") == "payment") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Payment Methods') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/payment/create" class="nav-link <?php echo ((get("route") == "payment") && (get("target") == "payment") && (get("action") == "insert")) ? "active" : null; ?>">
                    <?php e__('Add Payment Method') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/payment/settings" class="nav-link <?php echo ((get("route") == "payment") && (get("target") == "settings")) ? "active" : null; ?>">
                    <?php e__('Payment Gateway Settings') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_THEME')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarTheme" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-layers"></i> <?php e__('Theme') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "theme") ? "show" : null; ?>" id="sidebarTheme">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/theme/themes" class="nav-link <?php echo ((get("route") == "theme") && (get("target") == "themes") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Themes') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/theme/settings" class="nav-link <?php echo ((get("route") == "theme") && (get("target") == "general") && (get("action") == "update")) ? "active" : null; ?>">
                    <?php e__('Settings') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/theme/header" class="nav-link <?php echo ((get("route") == "theme") && (get("target") == "header") && (get("action") == "update")) ? "active" : null; ?>">
                    Header
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/theme/css" class="nav-link <?php echo ((get("route") == "theme") && (get("target") == "css") && (get("action") == "update")) ? "active" : null; ?>">
                    CSS
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_SETTINGS')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarSettings" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-settings"></i> <?php e__('Settings') ?>
            </a>
            <div class="collapse <?php echo (get("route") == "settings") ? "show" : null; ?>" id="sidebarSettings">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a href="/dashboard/settings/general" class="nav-link <?php echo ((get("route") == "settings") && (get("target") == "general") && (get("action") == "update")) ? "active" : null; ?>">
                    <?php e__('General') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/settings/system" class="nav-link <?php echo ((get("route") == "settings") && (get("target") == "system") && (get("action") == "update")) ? "active" : null; ?>">
                    <?php e__('System') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/settings/seo" class="nav-link <?php echo ((get("route") == "settings") && (get("target") == "seo") && (get("action") == "update")) ? "active" : null; ?>">
                    <?php e__('SEO') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/settings/language" class="nav-link <?php echo ((get("route") == "settings") && (get("target") == "language") && (get("action") == "getAll")) ? "active" : null; ?>">
                    <?php e__('Language') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/settings/smtp" class="nav-link <?php echo ((get("route") == "settings") && (get("target") == "smtp") && (get("action") == "update")) ? "active" : null; ?>">
                    SMTP
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/dashboard/settings/webhooks" class="nav-link <?php echo ((get("route") == "settings") && (get("target") == "webhooks") && (get("action") == "update")) ? "active" : null; ?>">
                    Discord Webhook
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_LOGS')): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo (get("route") == "logs") ? "active" : null; ?>" href="/dashboard/logs">
              <i class="fe fe-save"></i> <?php e__('Logs') ?>
            </a>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_MODULES')): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo (get("route") == "module") ? "active" : null; ?>" href="/dashboard/modules">
              <i class="fe fe-grid"></i> <?php e__('Modules') ?>
            </a>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_UPDATES')): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo (get("route") == "update") ? "active" : null; ?>" href="/dashboard/update">
              <i class="fe fe-refresh-cw"></i> <?php e__('Updates') ?>
              <?php if ($needUpdate == true): ?>
                <span class="badge badge-primary rounded-pill ml-auto">1</span>
              <?php endif; ?>
            </a>
          </li>
        <?php endif; ?>
        <?php if (checkPerm($readAdmin, 'SUPER_ADMIN')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#sidebarFreeServices" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
              <i class="fe fe-gift"></i> <?php e__('Free Services') ?>
            </a>
            <div class="collapse" id="sidebarFreeServices">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a rel="external" href="https://help.leaderos.net/free-services/free-plugin" class="nav-link">
                    <?php e__('Free Plugin') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a rel="external" href="https://help.leaderos.net/free-services/free-icon-pack" class="nav-link">
                    <?php e__('Free Icon Pack') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a rel="external" href="https://help.leaderos.net/free-services/free-web-hosting" class="nav-link">
                    <?php e__('Free Web Hosting') ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a rel="external" href="https://help.leaderos.net/free-services/free-domain" class="nav-link">
                    <?php e__('Free Domain') ?>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link" rel="external" href="https://help.leaderos.net/">
            <i class="fe fe-help-circle"></i> <?php e__('Help') ?>
          </a>
        </li>
      </ul>
      <?php
        $onlineAccountsHistory = $db->prepare("SELECT OAH.*, A.realname FROM OnlineAccountsHistory OAH INNER JOIN Accounts A ON OAH.accountID = A.id WHERE OAH.expiryDate > ? AND OAH.type = ?");
        $onlineAccountsHistory->execute(array(date("Y-m-d H:i:s"), 1));
      ?>
      <?php if ($onlineAccountsHistory->rowCount() > 0): ?>
        <hr class="my-3">
        <h6 class="navbar-heading text-muted">
          <?php e__('Online Staffs') ?>
        </h6>
        <ul class="navbar-nav">
          <?php foreach ($onlineAccountsHistory as $readOnlineAccountsHistory): ?>
            <li class="nav-item">
              <a class="d-block nav-link <?php echo ($readOnlineAccountsHistory["realname"] == $readAdmin["realname"]) ? "active" : null; ?>" href="/dashboard/users/view/<?php echo $readOnlineAccountsHistory["accountID"]; ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php e__('Last Seen: %time%', ['%time%' => convertTime($readOnlineAccountsHistory["creationDate"])]); ?>">
                <div class="row">
                  <div class="col">
                    <?php echo minecraftHead($readSettings["avatarAPI"], $readOnlineAccountsHistory["realname"], 20, "mr-2"); ?>
                    <span>
                      <?php echo $readOnlineAccountsHistory["realname"]; ?>
                    </span>
                  </div>
                  <div class="col-auto">
                    <span class="text-success">●</span>
                  </div>
                </div>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <!-- Push content down -->
      <div class="mt-auto"></div>
      <!-- Customize -->
      <a href="#modalCustomize" class="btn btn-block btn-primary mt-4" data-toggle="modal">
        <?php e__('Customize') ?>
      </a>
    </div> <!-- / .navbar-collapse -->
  </div> <!-- / .container-fluid -->
</nav>
