<?php if (get("route") != "login" && get("route") != "register" && get("route") != "recover"): ?>
  <?php
    if (isset($_POST["searchAccount"]) && post("search") != null) {
      go('/player/'.post("search"));
    }
    if (isset($_SESSION["login"])) {
      $chestCount = $db->prepare("SELECT C.id FROM Chests C INNER JOIN Products P ON C.productID = P.id INNER JOIN Servers S ON P.serverID = S.id WHERE C.accountID = ? AND C.status = ?");
      $chestCount->execute(array($readAccount["id"], 0));
      $chestCount = $chestCount->rowCount();
    }
    $shoppingCartCount = 0;
    if (isset($_SESSION["login"])) {
      $shoppingCartCount = $db->prepare("SELECT SC.accountID FROM ShoppingCartProducts SCP INNER JOIN ShoppingCarts SC ON SC.accountID = SCP.shoppingCartID WHERE SC.accountID = ?");
      $shoppingCartCount->execute(array($readAccount["id"]));
      $shoppingCartCount = $shoppingCartCount->rowCount();
    }
  ?>
  <?php if (themeSettings("announcementBar") == 1) : ?>
    <?php $broadcast = $db->query("SELECT * FROM Broadcast ORDER BY id DESC"); ?>
    <?php if ($broadcast->rowCount() > 0) : ?>
      <ul class="broadcast">
        <?php foreach ($broadcast as $readBroadcast) : ?>
          <li class="broadcast-item">
            <a class="broadcast-link" href="<?php echo $readBroadcast["url"]; ?>"><?php echo $readBroadcast["title"]; ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  <?php endif; ?>
  <div class="header-banner" style="margin-top: <?php echo themeSettings("announcementBar") == 1 ? "44px" : "0"; ?>">
    <div id="parallax-header-image">
      <img data-depth="0.25" class="header-banner-image" src="/apps/main/public/assets/img/extras/header-bg.png?cache=<?php echo $readTheme["updatedAt"]; ?>">
    </div>
    <div class="header-banner-content">
      <div class="container">
        <div class="row g-0 w-100 align-items-center">
          <div class="col-md-4 d-flex justify-content-start">
            <a href="#!" data-toggle="copyip" data-clipboard-action="copy" data-clipboard-text="<?php echo $serverIP; ?>" class="d-none d-lg-flex text-uppercase">
              <div class="me-3">
                <i class="shi-gamepad text-white" style="font-size: 3rem;"></i>
              </div>
              <div>
                <div style="color: #bbb;">
                  <span data-toggle="onlinetext" server-ip="<?php echo $serverIP; ?>">-/-</span>
                  <span><?php e__('players online') ?></span>
                </div>
                <div>
                  <span class="text-white"><?php echo $serverIP; ?></span>
                </div>
              </div>
            </a>
          </div>
          <div class="col-md-4 d-flex flex-column justify-content-center">
            <div class="zoom-hover text-center">
              <a href="/">
                <img src="/apps/main/public/assets/img/extras/header-logo.png?cache=<?php echo $readTheme["updatedAt"]; ?>" class="header-banner-logo" alt="<?php echo $serverName; ?> Logo">
              </a>
            </div>
            <div class="text-center mt-4">
              <a href="/play" class="btn-play">
                <span><?php e__('Play Now') ?></span>
              </a>
            </div>
          </div>
          <div class="col-md-4 d-none d-lg-flex justify-content-end">
            <a href="<?php echo $readSettings["footerDiscord"]; ?>" class="d-flex text-end text-uppercase" target="_blank">
              <div>
                <div>
                  <span style="color: #bbb;"><?php e__('Click to Join!') ?></span>
                </div>
                <div>
                  <span class="text-white"><?php e__('Discord Server') ?></span>
                </div>
              </div>
              <div class="ms-3">
                <i class="shi-discord text-white" style="font-size: 3rem;"></i>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <header class="header navbar navbar-expand-lg navbar-light bg-light navbar-shadow navbar-sticky" data-scroll-header data-fixed-element>
    <div class="container px-0 px-xl-3">
      <button class="navbar-toggler ms-n2 me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#primaryMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="d-flex align-items-center order-lg-3 ms-lg-auto">
        <?php if (isset($_SESSION["login"])) : ?>
          <div class="btn-group dropdown">
            <button type="button" class="btn btn-profile btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <?php echo $readAccount["realname"]; ?>
            </button>
            <div class="dropdown-menu dropdown-menu-end" style="z-index: 99999">
              <a href="/profile" class="dropdown-item">
                <i class="shi-user me-2"></i>
                <?php e__('Profile') ?>
              </a>
              <a href="/credit/buy" class="dropdown-item">
                <img class="me-2" src="<?php echo themePath(); ?>/public/assets/img/icons/coin.svg" width="16">
                <?php e__('Balance') ?>: <span class="fw-bold"><?php echo $readAccount["credit"]; ?></span> <i class="shi-plus-circle text-success"></i>
              </a>
              <?php if (!moduleIsDisabled("store")): ?>
                <a class="dropdown-item" href="/checkout">
                  <i class="shi-shopping-cart me-2"></i>
                  <span><?php e__('Cart') ?> (<span class="shopping-cart-count"><?php echo $shoppingCartCount; ?></span>)</span>
                </a>
              <?php endif; ?>
              <?php if (!moduleIsDisabled("chest")): ?>
                <a href="/chest" class="dropdown-item">
                  <i class="shi-archive me-2"></i>
                  <?php e__('Chest') ?> (<?php echo $chestCount; ?>)
                </a>
              <?php endif; ?>
              <?php if (!moduleIsDisabled("bazaar")): ?>
                <a href="/manage-bazaar" class="dropdown-item">
                  <i class="shi-package me-2"></i>
                  <?php e__('Bazaar Storage') ?>
                </a>
              <?php endif; ?>
              <?php if (!moduleIsDisabled("lottery")): ?>
                <a href="/fortune-wheel" class="dropdown-item">
                  <i class="shi-pie-chart me-2"></i>
                  <?php e__('Wheel of Fortune') ?>
                </a>
              <?php endif; ?>
              <?php if (!moduleIsDisabled("gift")): ?>
                <a href="/gift" class="dropdown-item">
                  <i class="shi-gift me-2"></i>
                  <?php e__('Gift') ?>
                </a>
              <?php endif; ?>
              <?php if (checkStaff($readAccount)): ?>
                <a href="/dashboard" class="dropdown-item" rel="external">
                  <i class="shi-activity me-2"></i>
                  <?php e__('Dashboard') ?>
                </a>
              <?php endif; ?>
              <div class="dropdown-divider"></div>
              <a href="/logout" class="dropdown-item" onclick="return confirm('<?php e__('Are you sure want to logout?') ?>');">
                <i class="shi-log-out me-2"></i>
                <?php e__('Logout') ?>
              </a>
            </div>
          </div>
        <?php else : ?>
          <a class="nav-link-style text-nowrap" href="/login">
            <i class="shi-user fs-xl me-2"></i>
            <?php e__('Login') ?>
          </a>
          <a class="btn btn-primary ms-grid-gutter d-none d-lg-inline-block" href="/register">
            <?php e__('Register') ?>
          </a>
        <?php endif; ?>
        <?php if (!moduleIsDisabled("store")): ?>
          <a href="/checkout" class="btn btn-icon btn-primary ms-2 position-relative">
            <span class="shopping-cart-count shopping-cart-count-circle"><?php echo $shoppingCartCount; ?></span>
            <i class="shi shi-shopping-cart"></i>
          </a>
        <?php endif; ?>
      </div>
      <div class="offcanvas offcanvas-collapse order-lg-2" id="primaryMenu">
        <div class="offcanvas-header navbar-shadow">
          <h5 class="mt-1 mb-0">Menu</h5>
          <button class="btn-close lead" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <!-- Menu-->
          <ul class="navbar-nav" style="margin-left: -18px">
            <?php
              $activatedStatus = false;
              $headerJSON = json_decode($readTheme["header"], true);
            ?>
            <?php foreach ($headerJSON as $readHeader) : ?>
              <?php if (moduleIsDisabled($readHeader["pagetype"])) continue; ?>
              <?php $readHeader["title"] = t__($readHeader["title"]); ?>
              <?php if ($readHeader["pagetype"] == "support") : ?>
                <?php if (isset($_SESSION["login"])) : ?>
                  <?php
                  $unreadMessages = $db->prepare("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id WHERE S.statusID = ? AND S.readStatus = ? AND S.accountID = ?");
                  $unreadMessages->execute(array(2, 0, $readAccount["id"]));
                  ?>
                  <?php if ($unreadMessages->rowCount() > 0) : ?>
                    <?php $readHeader["title"] .= " <span>(" . $unreadMessages->rowCount() . ")</span>"; ?>
                  <?php endif; ?>
                <?php endif; ?>
              <?php endif; ?>
              <?php if ($readHeader["pagetype"] == "chest") : ?>
                <?php if (isset($_SESSION["login"])) : ?>
                  <?php if ($chestCount > 0) : ?>
                    <?php $readHeader["title"] .= " <span>(" . $chestCount . ")</span>"; ?>
                  <?php endif; ?>
                <?php endif; ?>
              <?php endif; ?>
              <?php if (isset($readHeader["children"])) : ?>
                <li class="nav-item dropdown <?php echo (((get("route") == $readHeader["pagetype"]) && ($activatedStatus == false)) ? "active" : null); ?>">
                  <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <?php echo $readHeader["title"]; ?>
                  </a>
                  <ul class="dropdown-menu">
                    <?php foreach ($readHeader["children"] as $readHeaderChildren) : ?>
                      <?php if (moduleIsDisabled($readHeaderChildren["pagetype"])) continue; ?>
                      <?php $readHeaderChildren["title"] = t__($readHeaderChildren["title"]); ?>
                      <li>
                        <a class="dropdown-item" href="<?php echo $readHeaderChildren["url"]; ?>" <?php echo (($readHeaderChildren["tabstatus"] == 1) ? "rel=\"external\"" : null); ?>><?php echo $readHeaderChildren["title"]; ?></a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </li>
              <?php else : ?>
                <li class="nav-item <?php echo (((get("route") == $readHeader["pagetype"]) && ($activatedStatus == false)) ? "active" : null); ?>">
                  <a class="nav-link" href="<?php echo $readHeader["url"]; ?>" <?php echo (($readHeader["tabstatus"] == 1) ? "rel=\"external\"" : null); ?>>
                    <?php echo $readHeader["title"]; ?>
                  </a>
                </li>
              <?php endif; ?>
              <?php if (get("route") == $readHeader["pagetype"]) : ?>
                <?php $activatedStatus = true; ?>
              <?php endif; ?>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php if (isset($_SESSION["login"])) : ?>
          <div class="offcanvas-footer border-top">
            <a href="/profile" class="btn btn-profile btn-outline-secondary w-100 me-2">
              <i class="shi-user me-1"></i>
              <?php e__('Profile') ?>
            </a>
            <?php if (!moduleIsDisabled("store")): ?>
              <a href="/checkout" class="btn btn-primary w-100">
                <i class="shi-shopping-cart me-1"></i>
                <?php e__('Cart') ?> (<span class="shopping-cart-count"><?php echo $shoppingCartCount; ?></span>)
              </a>
            <?php endif; ?>
          </div>
        <?php else : ?>
          <div class="offcanvas-footer border-top">
            <a href="/login" class="btn btn-profile btn-outline-secondary w-100 me-2">
              <i class="shi-log-in me-1"></i>
              <?php e__('Login') ?>
            </a>
            <a href="/register" class="btn btn-primary w-100">
              <i class="shi-user me-1"></i>
              <?php e__('Register') ?>
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </header>
<?php endif; ?>