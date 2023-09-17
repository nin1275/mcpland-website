<?php
  $servers = $db->query("SELECT * FROM Servers ORDER BY priority DESC");

  if (get("action") == "getAll") {
    if ($servers->rowCount() == 1) {
      go("/bazaar/".$servers->fetch()["slug"]);
    }
  }
  if (get("action") == "get") {
    if (get("server")) {
      $thisServer = $db->prepare("SELECT * FROM Servers WHERE slug = ?");
      $thisServer->execute(array(get("server")));
      $readThisServer = $thisServer->fetch();
    }

    $discountProducts = explode(",", $readSettings["storeDiscountProducts"]);
    require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
    $extraResourcesJS = new ExtraResources('js');
    $extraResourcesJS->addResource(themePath().'/public/assets/js/bazaar.js');
  }
?>
<section class="section store-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><?php e__('Home') ?></a></li>
            <?php if (get("server")): ?>
              <?php if ($thisServer->rowCount() > 0): ?>
                <li class="breadcrumb-item"><a href="/bazaar"><?php e__('Bazaar') ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $readThisServer["name"]; ?></li>
              <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page"><?php e__('Bazaar') ?></li>
              <?php endif; ?>
            <?php else: ?>
              <li class="breadcrumb-item active" aria-current="page"><?php e__('Bazaar') ?></li>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
    </div>
    <?php if (get("action") == "getAll"): ?>
      <div class="row">
        <?php if ($servers->rowCount() > 0): ?>
          <?php foreach ($servers as $readServers): ?>
            <div class="col-md-3">
              <div class="img-card-wrapper">
                <div class="img-container">
                  <a class="img-card" href="/bazaar/<?php echo $readServers["slug"]; ?>">
                    <img class="card-img-top lazyload" data-src="/apps/main/public/assets/img/servers/<?php echo $readServers["imageID"].'.'.$readServers["imageType"]; ?>" src="/apps/main/public/assets/img/loaders/server.png" alt="<?php echo $serverName." Sunucu - ".$readServers["name"]; ?>">
                  </a>
                  <div class="img-card-bottom">
                    <h5 class="mb-0">
                      <a class="text-white" href="/bazaar/<?php echo $readServers["slug"]; ?>">
                        <?php echo $readServers["name"]; ?>
                      </a>
                    </h5>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-md-12">
            <?php echo alertError(t__('No server were found!')); ?>
          </div>
        <?php endif; ?>
      </div>
    <?php elseif (get("action") == "get" && get("server")): ?>
      <div class="row">
        <div id="modalBox"></div>
        <div class="col-md-3">
          <?php if ($servers->rowCount() > 0): ?>
            <div class="card">
              <div class="card-header">
                <?php e__('Servers') ?>
              </div>
              <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                  <?php foreach ($servers as $readServers): ?>
                    <li class="list-group-item <?php echo ($readServers["slug"] == get("server")) ? "active":null; ?>">
                      <a href="/bazaar/<?php echo $readServers["slug"]; ?>">
                        <?php echo $readServers["name"]; ?>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No server were found!')); ?>
          <?php endif; ?>
        </div>
        <div class="col-md-9">
          <?php if (get("server") && $thisServer->rowCount() > 0): ?>
            <?php
              if (get("page")) {
                if (!is_numeric(get("page"))) {
                  $_GET["page"] = 1;
                }
                $page = intval(get("page"));
              }
              else {
                $page = 1;
              }
              $productLimit = 20;
              $itemsCount = $db->prepare("SELECT id from BazaarItems WHERE serverID = ? AND price > ? AND sold = ?");
              $itemsCount->execute(array($readThisServer["id"], 0, 0));
              $itemsCount = $itemsCount->rowCount();
              $pageCount = ceil($itemsCount/$productLimit);
              if ($page > $pageCount) {
                $page = 1;
              }
              $visibleItemsCount = $page * $productLimit - $productLimit;
              $visiblePageCount = 5;

              $products = $db->prepare("SELECT * FROM BazaarItems WHERE serverID = ? AND price > ? AND sold = ? ORDER BY id DESC LIMIT $visibleItemsCount, $productLimit");
              $products->execute(array($readThisServer["id"], 0, 0));
            ?>
            <div class="card">
              <div class="card-header">
                <div class="row">
                  <div class="col"><?php e__('Items') ?></div>
                  <div class="col-auto">
                    <a href="/help-bazaar" class="text-white">
                      <?php e__('How to sell?') ?>
                    </a>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <?php if ($products->rowCount() > 0): ?>
                  <div class="row store-cards">
                    <?php foreach ($products as $readProducts): ?>
                      <div class="col-md-3">
                        <div class="store-card">
                          <img class="store-card-img lazyload" data-src="/apps/main/public/assets/img/items/<?php echo strtolower($readProducts["itemID"]).'.png'; ?>" src="/apps/main/public/assets/img/loaders/store.png">
                          <div class="row store-card-text">
                            <div class="col">
                              <span><?php echo $readProducts["name"]; ?></span>
                            </div>
                            <div class="col-auto">
                              <span class="price"><?php echo $readProducts["price"]; ?> <i class="fa fa-coins"></i></span>
                            </div>
                          </div>
                          <div class="store-card-button">
                            <button class="btn btn-success w-100 stretched-link openBuyModal" product-id="<?php echo $readProducts["id"]; ?>">
                              <?php e__('Buy Now') ?>
                            </button>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <?php echo alertError(t__('No product were found!')); ?>
                <?php endif; ?>
              </div>
            </div>
  
            <?php if ($products->rowCount() > 0): ?>
              <div class="col-md-12 d-flex justify-content-center">
                <nav class="pages" aria-label="Pages">
                  <ul class="pagination">
                    <li class="page-item <?php echo ($page == 1) ? "disabled" : null; ?>">
                      <a class="page-link" href="<?php echo '?page='.($page-1); ?>" tabindex="-1">
                        <i class="fa fa-angle-double-left"></i>
                      </a>
                    </li>
                    <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                      <?php if ($i > 0 and $i <= $pageCount): ?>
                        <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                          <a class="page-link" href="<?php echo '?page='.$i; ?>"><?php echo $i; ?></a>
                        </li>
                      <?php endif; ?>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                      <a class="page-link" href="<?php echo '?page='.($page+1); ?>">
                        <i class="fa fa-angle-double-right"></i>
                      </a>
                    </li>
                  </ul>
                </nav>
              </div>
            <?php endif; ?>
          <?php else: ?>
            <?php echo alertError("No data were found!"); ?>
          <?php endif; ?>
        </div>
      </div>
    <?php else: ?>
      <?php go("/404"); ?>
    <?php endif; ?>
  </div>
</section>
