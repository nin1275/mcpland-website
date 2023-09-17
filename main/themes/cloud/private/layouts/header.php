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
<style type="text/css">
  @media all and (min-width: 992px) {
    .navbar-dark .navbar-nav .nav-item {
      margin: .375rem .125rem;
    }
  }
</style>
<?php if (themeSettings("announcementBar") == 1): ?>
  <?php $broadcast = $db->query("SELECT * FROM Broadcast ORDER BY id DESC"); ?>
  <?php if ($broadcast->rowCount() > 0): ?>
    <ul class="broadcast">
      <?php foreach ($broadcast as $readBroadcast): ?>
        <li class="broadcast-item">
          <a class="broadcast-link" href="<?php echo $readBroadcast["url"]; ?>"><?php echo $readBroadcast["title"]; ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
<?php endif; ?>
  <div class="header-banner position-relative">
    <div class="sfx-header-container position-relative <?php echo (themeSettings("headerType") == "small") ? 'container' : 'container-fluid'; ?>">
      <div class="header-banner-content flex-lg-row flex-column">
        <div class="d-flex flex-column text-center text-uppercase mt-lg-0 mt-4">
          <div>
            <a href="#!" data-toggle="copyip" data-clipboard-action="copy" data-clipboard-text="<?php echo $serverIP; ?>">
              <span class="text-white">IP:</span>
              <span class="text-yellow"><?php echo $serverIP; ?></span>
            </a>
          </div>
          <div class="d-lg-block d-none my-3">
            <button type="button" class="btn secondary-bg-color btn-rounded btn-header-ipcopy px-4 py-2" data-toggle="copyip" data-clipboard-action="copy" data-clipboard-text="<?php echo $serverIP; ?>">
              <i class="fa fa-gamepad mr-1"></i>
              <?php e__('Copy to clipboard') ?>
            </button>
          </div>
          <div>
            <span class="text-yellow" data-toggle="onlinetext" server-ip="<?php echo $serverIP; ?>">-/-</span>
            <span class="text-white"><?php e__('players online') ?></span>
          </div>
        </div>
        <div class="d-flex flex-column order-lg-2 order-1">
          <div class="zoom-hover text-center">
            <a href="/">
              <img src="/apps/main/public/assets/img/extras/header-logo.png?cache=<?php echo $readTheme["updatedAt"] ?>" class="header-banner-logo" alt="<?php echo $serverName; ?> Logo">
            </a>
          </div>
        </div>
        <div class="d-lg-flex d-none flex-column order-3 text-center">
          <div><span class="text-white"><?php e__('Browse the Store') ?></span></div>
          <div class="my-3 mb-0">
            <a class="btn secondary-bg-color btn-rounded btn-header-store px-5 py-2" href="/store">
              <i class="fa fa-shopping-cart mr-1"></i>
              <?php e__('Store') ?>
            </a>
          </div>
        </div>
      </div>
    </div>
    <svg fill="rgba(var(--body-color))" class="wave-header " preserveAspectRatio="none" viewBox="0 0 1440 83" xmlns="http://www.w3.org/2000/svg">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M47.4694 20.8187C122.804 42.296 222.186 70.6289 409.5 49C726.618 12.3829 789 9.50001 1086 43.5C1271.47 64.732 1385.22 31.8972 1440 5.44763V83H0V8.11478C15.1007 11.5906 30.6885 16.0345 47.4694 20.8187Z"></path>
    </svg>
  </div>
<header class="header sticky-top">
  <div class="container sfx-container-navbar">
    <div class="row justify-content-center sfx-navbar-row">
      <nav class="navbar navbar-expand-lg navbar-light py-2 shadow-none">
        <div class="<?php echo (themeSettings("headerType") == "small") ? 'container' : 'container-fluid'; ?> sfx-container-navbar">
          <a class="navbar-brand image d-inline-block d-lg-none" href="/">
            <img src="/apps/main/public/assets/img/extras/header-logo.png?cache=<?php echo $readTheme["updatedAt"] ?>" alt="<?php echo $serverName; ?> Logo">
          </a>
          <button class="navbar-toggler p-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="navbar-collapse collapse justify-content-between align-items-center w-100" id="navbarSupportedContent">
            <ul id="navbarMainContent" class="nav navbar-nav text-center justify-content-between w-100">
              <?php
              $activatedStatus = false;
              $headerJSON = json_decode($readTheme["header"], true);
              ?>
              <?php foreach ($headerJSON as $readHeader): ?>
                <?php if (moduleIsDisabled($readHeader["pagetype"])) continue; ?>
                <?php $readHeader["title"] = t__($readHeader["title"]); ?>
                <?php if ($readHeader["pagetype"] == "support"): ?>
                  <?php if (isset($_SESSION["login"])): ?>
                    <?php
                    $unreadMessages = $db->prepare("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id WHERE S.statusID = ? AND S.readStatus = ? AND S.accountID = ?");
                    $unreadMessages->execute(array(2, 0, $readAccount["id"]));
                    ?>
                    <?php if ($unreadMessages->rowCount() > 0): ?>
                      <?php $readHeader["title"].=" <span>(".$unreadMessages->rowCount().")</span>"; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                <?php endif; ?>
                <?php if ($readHeader["pagetype"] == "chest"): ?>
                  <?php if (isset($_SESSION["login"])): ?>
                    <?php if ($chestCount > 0): ?>
                      <?php $readHeader["title"].=" <span>(".$chestCount.")</span>"; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                <?php endif; ?>
                <?php if (isset($readHeader["children"])): ?>
                  <li class="nav-item dropdown m-auto <?php echo (((get("route") == $readHeader["pagetype"]) && ($activatedStatus == false)) ? "active" : null); ?>">
                    <a class="nav-link dropdown-toggle p-2 font-weight-bold" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <?php echo $readHeader["title"]; ?>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                      <?php foreach ($readHeader["children"] as $readHeaderChildren): ?>
                        <?php if (moduleIsDisabled($readHeaderChildren["pagetype"])) continue; ?>
                        <?php $readHeaderChildren["title"] = t__($readHeaderChildren["title"]); ?>
                        <a class="dropdown-item" href="<?php echo $readHeaderChildren["url"]; ?>" <?php echo (($readHeaderChildren["tabstatus"] == 1) ? "rel=\"external\"" : null); ?>><?php echo $readHeaderChildren["title"]; ?></a>
                      <?php endforeach; ?>
                    </div>
                  </li>
                <?php else: ?>
                  <li class="nav-item p-2 font-weight-bold <?php echo (((get("route") == $readHeader["pagetype"]) && ($activatedStatus == false)) ? "active" : null); ?>">
                    <a class="nav-link" href="<?php echo $readHeader["url"]; ?>" <?php echo (($readHeader["tabstatus"] == 1) ? "rel=\"external\"" : null); ?>> <?php echo $readHeader["title"]; ?></a>
                  </li>
                <?php endif; ?>
                <?php if (get("route") == $readHeader["pagetype"]): ?>
                  <?php $activatedStatus = true; ?>
                <?php endif; ?>
              <?php endforeach; ?>
              <?php if (isset($_SESSION["login"])): ?>
                <li class="nav-item dropdown pc <?php echo ((get("route") == "profile") ? "active" : null); ?>">
                  <a id="profileDropdown" class="nav-link dropdown-toggle p-3 font-weight-bold <?php echo ((get("route") == "profile") ? "active" : null); ?>" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
                    <div class="d-inline-flex align-items-center">
                      <?php echo minecraftHead($readSettings["avatarAPI"], $readAccount["realname"], 14, "mr-1"); ?>
                      <?php echo $readAccount["realname"]; ?>
                    </div>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                    <div class="w-100 d-flex justify-content-center" style="
                    ">
                    <div class="w-100 text-center">
                      <?php echo minecraftHead($readSettings["avatarAPI"], $readAccount["realname"], 48, "w-20 rounded:full"); ?>
                      <p class="sfx-dropdown-profile-name font-weight:550"><?php echo $readAccount["realname"] ?></p>
                    </div>
                  </div>
                  <div class="col-md-12 mt-4">
                    <div class="row">
                      <div class="container">
                        <div class="col-md-6 text-center mb-2">
                          <a class="font-weight-bold sfx-profile-dropdown secondary-text-color" href="/profile">
                            <div class="sfx-dropdown-icon-bg:1">
                              <i class="bi bi-person sfx-profile-dropdown-sub sfx-dropdown-icon:1"></i>
                            </div>
                            <span class="sfx-dropdown-text:1"><?php e__('Profile') ?></span>
                          </a>
                        </div>
                        <div class="col-md-6 text-center mb-2">
                          <a class="font-weight-bold sfx-profile-dropdown" href="/credit/buy">
                            <div class="sfx-dropdown-icon-bg:2">
                              <i class="bi bi-cash sfx-profile-dropdown-sub sfx-dropdown-icon:2"></i>
                            </div>
                            <span class="sfx-dropdown-text:2"><?php echo $readAccount["credit"]; ?> <i class="bi bi-coin mt-2"></i></span>
                          </a>
                        </div>
                        <?php if (!moduleIsDisabled('chest')): ?>
                          <div class="col-md-6 text-center mb-2">
                            <a class="font-weight-bold sfx-profile-dropdown" href="/chest">
                              <div class="sfx-dropdown-icon-bg:3">
                                <i class="bi bi-archive sfx-profile-dropdown-sub sfx-dropdown-icon:3"></i>
                              </div>
                              <span class="sfx-dropdown-text:3"><?php e__('Chest') ?>(<?php echo $chestCount; ?>)</span>
                            </a>
                          </div>
                        <?php endif; ?>
                        <?php if (!moduleIsDisabled('store')): ?>
                          <div class="col-md-6 text-center mb-2">
                            <a class="font-weight-bold sfx-profile-dropdown position-relative" href="/checkout">
                              <div class="sfx-dropdown-icon-bg:8">
                                <i class="bi bi-cart sfx-profile-dropdown-sub sfx-dropdown-icon:8"></i>
                              </div>
                              <span class="shopping-cart-count shopping-cart-count-circle"><?php echo $shoppingCartCount; ?></span>
                              <span class="sfx-dropdown-text:8"><?php e__('Cart') ?></span>
                            </a>
                          </div>
                        <?php endif; ?>
                        <?php if (!moduleIsDisabled('bazaar')): ?>
                          <div class="col-md-6 text-center mb-2">
                            <a class="font-weight-bold sfx-profile-dropdown" href="/manage-bazaar">
                              <div class="sfx-dropdown-icon-bg:9">
                                <i class="bi bi-box sfx-profile-dropdown-sub sfx-dropdown-icon:9"></i>
                              </div>
                              <span class="sfx-dropdown-text:9"><?php e__('Bazaar Storage') ?></span>
                            </a>
                          </div>
                        <?php endif; ?>
                        <?php if (!moduleIsDisabled('lottery')): ?>
                          <div class="col-md-6 text-center mb-2">
                            <a class="font-weight-bold sfx-profile-dropdown" href="/fortune-wheel">
                              <div class="sfx-dropdown-icon-bg:4">
                                <i class="bi bi-pie-chart sfx-profile-dropdown-sub sfx-dropdown-icon:4"></i>
                              </div>
                              <span class="sfx-dropdown-text:4"><?php e__('Wheel of Fortune') ?></span>
                            </a>
                          </div>
                        <?php endif; ?>
                        <?php if (!moduleIsDisabled('gift')): ?>
                          <div class="col-md-6 text-center mb-2">
                            <a class="font-weight-bold sfx-profile-dropdown" href="/gift">
                              <div class="sfx-dropdown-icon-bg:5">
                                <i class="bi bi-gift sfx-profile-dropdown-sub sfx-dropdown-icon:5"></i>
                              </div>
                              <span class="sfx-dropdown-text:5"><?php e__('Gift') ?></span>
                            </a>
                          </div>
                        <?php endif; ?>
                        <div class="col-md-6 text-center mb-2">
                          <a class="font-weight-bold sfx-profile-dropdown" href="/logout" onclick="return confirm('<?php e__('Are you sure want to logout?') ?>');">
                            <div class="sfx-dropdown-icon-bg:6">
                              <i class="bi bi-x sfx-profile-dropdown-sub sfx-dropdown-icon:6"></i>
                            </div>
                            <span class="sfx-dropdown-text:6"><?php e__('Logout') ?></span>
                          </a>
                        </div>
                        <?php if (checkStaff($readAccount)): ?>
                          <div class="col-md-12 text-center mb-2">
                            <a class="font-weight-bold sfx-profile-dropdown" href="/dashboard" target="blank">
                              <div class="sfx-dropdown-icon-bg:7">
                                <i class="bi bi-speedometer2 sfx-profile-dropdown-sub sfx-dropdown-icon:7"></i>
                              </div>
                              <span class="sfx-dropdown-text:7"><?php e__('Dashboard') ?></span>
                            </a>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>

                  </div>
                </div>
              </li>
              <li class="nav-item mobil <?php echo ((get("route") == 'profile') ? 'active' : null); ?>">
                <a class="nav-link" href="/profile">
                  <i class="fa fa-user-circle"></i>
                  <span><?php e__('Profile') ?></span>
                </a>
              </li>
              <li class="nav-item mobil">
                <a class="nav-link" href="/credit/buy">
                  <i class="fa fa-coins"></i>
                  <span><?php e__('Balance') ?>: <strong><?php echo $readAccount["credit"]; ?></strong></span>
                </a>
              </li>
              <?php if (!moduleIsDisabled('store')): ?>
                <li class="nav-item mobil <?php echo ((get("route") == 'checkout') ? 'active' : null); ?>">
                  <a class="nav-link" href="/chest">
                    <i class="fa fa-shopping-cart"></i>
                    <span><?php e__('Cart') ?> (<span class="shopping-cart-count"><?php echo $shoppingCartCount; ?></span>)</span>
                  </a>
                </li>
              <?php endif; ?>
              <?php if (!moduleIsDisabled('chest')): ?>
                <li class="nav-item mobil <?php echo ((get("route") == 'chest') ? 'active' : null); ?>">
                  <a class="nav-link" href="/chest">
                    <i class="fa fa-archive"></i>
                    <span><?php e__('Chest') ?> (<?php echo $chestCount; ?>)</span>
                  </a>
                </li>
              <?php endif; ?>
              <?php if (!moduleIsDisabled('lottery')): ?>
                  <li class="nav-item mobil <?php echo ((get("route") == 'lottery') ? 'active' : null); ?>">
                    <a class="nav-link" href="/fortune-wheel">
                      <i class="fa fa-chart-pie"></i>
                      <span><?php e__('Wheel of Fortune') ?></span>
                    </a>
                  </li>
              <?php endif; ?>
                <?php if (!moduleIsDisabled('bazaar')): ?>
                  <li class="nav-item mobil <?php echo ((get("route") == 'managa-bazaar') ? 'active' : null); ?>">
                    <a class="nav-link" href="/manage-bazaar">
                      <i class="fa fa-box"></i>
                      <span><?php e__('Bazaar Storage') ?></span>
                    </a>
                  </li>
                <?php endif; ?>
              <?php if (!moduleIsDisabled('gift')): ?>
                <li class="nav-item mobil <?php echo ((get("route") == 'gift') ? 'active' : null); ?>">
                  <a class="nav-link" href="/gift">
                    <i class="fa fa-gift"></i>
                    <span><?php e__('Gift') ?></span>
                  </a>
                </li>
              <?php endif; ?>
              <?php if (checkStaff($readAccount)): ?>
                <li class="nav-item mobil">
                  <a class="nav-link" href="/dashboard">
                    <i class="fa fa-dashboard"></i>
                    <span><?php e__('Dashboard') ?></span>
                  </a>
                </li>
              <?php endif; ?>
              <li class="nav-item mobil">
                <a class="nav-link" href="/logout" onclick="return confirm('<?php e__('Are you sure want to logout?') ?>');">
                  <i class="fa fa-sign-out"></i>
                  <span><?php e__('Logout') ?></span>
                </a>
              </li>
            <?php else : ?>
              <li class="nav-item pc p-2 font-weight-bold <?php echo ((get("route") == 'login') ? 'active' : null); ?>">
                <a class="nav-link" href="/login">
                  <?php e__('Login') ?>
                </a>
              </li>
              <li class="nav-item pc p-2 font-weight-bold <?php echo ((get("route") == 'register') ? 'active' : null); ?>">
                <a class="nav-link" href="/register">
                  <?php e__('Register') ?>
                </a>
              </li>
              <li class="nav-item mobil p-2 font-weight-bold <?php echo ((get("route") == 'login') ? 'active' : null); ?>">
                <a class="nav-link" href="/login">
                  <i class="fa fa-sign-in"></i>
                  <span><?php e__('Login') ?></span>
                </a>
              </li>
              <li class="nav-item mobil p-2 font-weight-bold <?php echo ((get("route") == 'register') ? 'active' : null); ?>">
                <a class="nav-link" href="/register">
                  <i class="fa fa-user-plus"></i>
                  <span><?php e__('Register') ?></span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>
      <a class="position-relative" href="/checkout">
        <div class="cart-button">
          <i class="fa fa-shopping-cart"></i>
        </div>
        <span class="shopping-cart-count shopping-cart-count-circle" style="right: -5px;"><?php echo $shoppingCartCount; ?></span>
      </a>
  </div>
</div>

</header>

<!-- Preloader -->
<?php if ($readSettings["preloaderStatus"] == 1): ?>
  <div id="preloader">
    <div class="spinner-border" role="status">
      <span class="sr-only"><?php e__('Loading...') ?></span>
    </div>
  </div>
<?php endif; ?>