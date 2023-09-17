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
	if (get("category")) {
		$itemsCount = $db->prepare("SELECT N.id from News N INNER JOIN NewsCategories NC ON N.categoryID = NC.id WHERE NC.slug = ?");
		$itemsCount->execute(array(get("category")));
		$itemsCount = $itemsCount->rowCount();
		$requestURL = '/categories/'.get("category");
	}
	else if (get("tag")) {
		$itemsCount = $db->prepare("SELECT N.id from News N INNER JOIN NewsTags NT ON N.id = NT.newsID WHERE NT.slug = ?");
		$itemsCount->execute(array(get("tag")));
		$itemsCount = $itemsCount->rowCount();
		$requestURL = '/tags/'.get("tag");
	}
	else {
		$itemsCount = $db->query("SELECT id from News");
		$itemsCount = $itemsCount->rowCount();
		$requestURL = '/blog';
	}
	$pageCount = ceil($itemsCount/$newsLimit);
	if ($page > $pageCount) {
		$page = 1;
	}
	$visibleItemsCount = $page * $newsLimit - $newsLimit;
	$visiblePageCount = 5;
  
  require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource(themePath().'/public/assets/js/store.js');
?>
<style type="text/css">
  <?php if (themeSettings("sliderType") == "wide"): ?>
		.news-section {
			margin-top: 2rem !important;
		}
		.carousel-inner, .carousel-item img {
    <?php if (themeSettings("serverOnlineBar") == 0): ?>
				border-radius: 1rem;
			<?php else: ?>
				border-radius: 1rem 1rem 0 0;
			<?php endif; ?>
		}
    <?php if (themeSettings("serverOnlineBar") == 1): ?>
			.server-online-info {
				border-radius: 0 0 1rem 1rem;
			}
		<?php endif; ?>
	<?php endif; ?>
</style>
<section class="section home-section">
  <div class="container">
    <?php if (!get("category") && !get("tag") && $page == 1): ?>
      <?php if (themeSettings("slider") == '1'): ?>
        <?php $slider = $db->query("SELECT * FROM Slider ORDER BY id DESC LIMIT 5"); ?>
        <?php if ($slider->rowCount() > 0): ?>
          <div class="home-slider mb-5">
            <div class="tns-carousel-wrapper tns-nav-inside tns-nav-light tns-controls-inside rounded-3">
              <div class="tns-carousel-inner" data-carousel-options='{"gutter": 15, "lazyload": true, "lazyloadSelector": ".tns-lazy"}'>
                <?php foreach ($slider as $i => $readSlider): ?>
                  <div>
                    <a href="<?php echo $readSlider["url"]; ?>">
                      <img class="w-100 rounded-3 tns-lazy" data-src="/apps/main/public/assets/img/slider/<?php echo $readSlider["imageID"].'.'.$readSlider["imageType"]; ?>" src="/apps/main/public/assets/img/loaders/slider.png" alt="<?php echo $serverName." Slider - Afiş"; ?>" />
                    </a>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    <?php endif; ?>
  
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><?php e__('Home') ?></a></li>
            <?php if (get("category")): ?>
              <?php
              $newsCategory = $db->prepare("SELECT name FROM NewsCategories WHERE slug = ?");
              $newsCategory->execute(array(get("category")));
              $readNewsCategory = $newsCategory->fetch();
              ?>
              <li class="breadcrumb-item"><a href="/"><?php e__('Blog') ?></a></li>
              <li class="breadcrumb-item"><a href="/"><?php e__('Category') ?></a></li>
              <li class="breadcrumb-item active" aria-current="page"><?php echo (($newsCategory->rowCount() > 0) ? $readNewsCategory["name"] : t__('Not Found!')); ?></li>
            <?php elseif (get("tag")): ?>
              <?php
              $newsTag = $db->prepare("SELECT name FROM NewsTags WHERE slug = ?");
              $newsTag->execute(array(get("tag")));
              $readNewsTag = $newsTag->fetch();
              ?>
              <li class="breadcrumb-item"><a href="/"><?php e__('Blog') ?></a></li>
              <li class="breadcrumb-item"><a href="/"><?php e__('Tag') ?></a></li>
              <li class="breadcrumb-item active" aria-current="page"><?php echo (($newsTag->rowCount() > 0) ? $readNewsTag["name"] : t__('Not Found!')); ?></li>
            <?php else: ?>
              <li class="breadcrumb-item active" aria-current="page"><?php e__('Blog') ?></li>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
    </div>
    <h2 class="h3 mb-3"><?php e__('Blog') ?></h2>
    <div class="row">
      <div class="<?php echo (themeSettings("sidebar") == 0) ? 'col-md-12' : 'col-md-8'; ?>">
        <?php
          if (get("category")) {
            $news = $db->prepare("SELECT N.*, NC.name as categoryName, NC.slug as categorySlug from News N INNER JOIN NewsCategories NC ON N.categoryID = NC.id INNER JOIN Accounts A ON N.accountID = A.id WHERE NC.slug = ? ORDER BY N.id DESC LIMIT $visibleItemsCount, $newsLimit");
            $news->execute(array(get("category")));
          }
          else if (get("tag")) {
            $news = $db->prepare("SELECT N.*, NC.name as categoryName, NC.slug as categorySlug from News N INNER JOIN NewsCategories NC ON N.categoryID = NC.id INNER JOIN NewsTags NT ON N.id = NT.newsID INNER JOIN Accounts A ON N.accountID = A.id WHERE NT.slug = ? ORDER BY N.id DESC LIMIT $visibleItemsCount, $newsLimit");
            $news->execute(array(get("tag")));
          }
          else {
            $news = $db->query("SELECT N.*, NC.name as categoryName, NC.slug as categorySlug from News N INNER JOIN NewsCategories NC ON N.categoryID = NC.id INNER JOIN Accounts A ON N.accountID = A.id ORDER BY N.id DESC LIMIT $visibleItemsCount, $newsLimit");
            $news->execute();
          }
        ?>
        <?php if ($news->rowCount() > 0): ?>
          <?php foreach ($news as $readNews): ?>
            <?php
              $newsComments = $db->prepare("SELECT * FROM NewsComments WHERE newsID = ? AND status = ? ORDER BY id DESC");
              $newsComments->execute(array($readNews["id"], 1));
            ?>
            <div class="card card-hover">
              <div class="row g-0">
                <div class="col-md-5">
                  <a class="d-flex h-100" href="/posts/<?php echo $readNews["id"]; ?>/<?php echo $readNews["slug"]; ?>">
                    <img class="lazyload card-img-right" data-src="/apps/main/public/assets/img/news/<?php echo $readNews["imageID"].'.'.$readNews["imageType"]; ?>" src="/apps/main/public/assets/img/loaders/news.png" alt="<?php echo $serverName." Posts - ".$readNews["title"]; ?>">
                  </a>
                </div>
                <div class="col-md-7">
                  <div class="news-card-body">
                    <a class="meta-link fs-sm mb-2" href="/categories/<?php echo $readNews["categorySlug"]; ?>">
                      <?php echo $readNews["categoryName"]; ?>
                    </a>
                    <span class="meta-link fs-sm mb-2">-</span>
                    <span class="meta-link fs-sm mb-2">
                      <?php echo convertTime($readNews["creationDate"], 1); ?>
                    </span>
                    <h3 class="h4 nav-heading mb-3">
                      <a href="/posts/<?php echo $readNews["id"]; ?>/<?php echo $readNews["slug"]; ?>">
                        <?php echo $readNews["title"]; ?>
                      </a>
                    </h3>
                    <p class="mb-3"><?php echo showEmoji(substr(strip_tags($readNews["content"]), 0, 250)); ?></p>
                    <div class="d-flex justify-content-end">
                      <a href="/posts/<?php echo $readNews["id"]; ?>/<?php echo $readNews["slug"]; ?>" class="btn btn-primary"><?php e__('Read More') ?></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
          <div class="d-md-flex justify-content-center align-items-center pt-5 pb-2 <?php echo (themeSettings("sidebar") == 1) ? 'mb-4' : null; ?>">
            <nav class="mb-4" aria-label="Sayfalar">
              <ul class="pagination justify-content-center">
                <?php if ($page != 1): ?>
                  <li class="page-item">
                    <a class="page-link" href="<?php echo $requestURL.'/'.($page-1); ?>" aria-label="Önceki Sayfa">
                      <i class="shi-chevron-left"></i>
                    </a>
                  </li>
                <?php endif ?>
                <li class="page-item d-sm-none">
                  <span class="page-link page-link-static"><?php echo $page." / ".$pageCount ?></span>
                </li>
                <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                  <?php if ($i > 0 and $i <= $pageCount): ?>
                    <li class="page-item <?php echo (($page == $i) ? "active" : null); ?> d-none d-sm-block" <?php echo ($page == $i) ? 'aria-current="page"' : null ?> >
                      <a class="page-link" href="<?php echo $requestURL.'/'.$i; ?>"><?php echo $i; ?></a>
                    </li>
                  <?php endif; ?>
                <?php endfor; ?>
                <?php if ($page != $pageCount): ?>
                  <li class="page-item">
                    <a class="page-link" href="<?php echo $requestURL.'/'.($page+1); ?>" aria-label="Sonraki Sayfa">
                      <i class="shi-chevron-right"></i>
                    </a>
                  </li>
                <?php endif ?>
              </ul>
            </nav>
          </div>
        <?php else : ?>
          <div class="col-md-12">
            <?php echo alertError(t__('No posts were found!')); ?>
          </div>
        <?php endif; ?>
      </div>
      <?php if (themeSettings("sidebar") == 1): ?>
        <div class="col-md-4">
          <?php if (themeSettings("featuredProduct") != 0 && themeSettings("featuredProductWidget") == 1): ?>
            <div id="modalBox"></div>
            <?php
            $featuredProduct = $db->prepare("SELECT * FROM Products WHERE id = ?");
            $featuredProduct->execute(array(themeSettings("featuredProduct")));
            $readFeaturedProduct = $featuredProduct->fetch();
            ?>
            <?php if ($featuredProduct->rowCount() > 0): ?>
              <?php $discountProducts = explode(",", $readSettings["storeDiscountProducts"]); ?>
              <?php $discountedPriceStatus = ($readFeaturedProduct["discountedPrice"] != 0 && ($readFeaturedProduct["discountExpiryDate"] > date("Y-m-d H:i:s") || $readFeaturedProduct["discountExpiryDate"] == '1000-01-01 00:00:00')); ?>
              <?php $storeDiscountStatus = ($readSettings["storeDiscount"] != 0 && (in_array($readFeaturedProduct["id"], $discountProducts) || $readSettings["storeDiscountProducts"] == '0') && ($readSettings["storeDiscountExpiryDate"] > date("Y-m-d H:i:s") || $readSettings["storeDiscountExpiryDate"] == '1000-01-01 00:00:00')); ?>
              <div class="sidebar-store-card mb-4 openBuyModal" product-id="<?php echo $readFeaturedProduct["id"]; ?>">
                <div class="sidebar-store-card-content">
                  <div class="sidebar-store-card-title">
                    <?php e__('Featured Product') ?>
                  </div>
                  <div class="sidebar-store-card-product">
                    <img class="mb-3" src="/apps/main/public/assets/img/store/products/<?php echo $readFeaturedProduct["imageID"].'.'.$readFeaturedProduct["imageType"]; ?>" alt="<?php echo $serverName." Ürün - ".$readFeaturedProduct["name"]." Satın Al"; ?>" width="190px">
                  </div>
                  <div class="sidebar-store-card-detail">
                    <div class="row">
                      <div class="col">
                        <span class="product-name">
                          <?php echo $readFeaturedProduct["name"]; ?>
                        </span>
                      </div>
                      <div class="col-auto">
                        <div class="price">
                          <?php if ($discountedPriceStatus == true || $storeDiscountStatus == true): ?>
                            <span class="old-price"><?php echo $readFeaturedProduct["price"]; ?></span>
                            <small>/</small>
                            <?php $newPrice = (($storeDiscountStatus == true) ? round(($readFeaturedProduct["price"]*(100-$readSettings["storeDiscount"]))/100) : $readFeaturedProduct["discountedPrice"]); ?>
                            <span class="price"><?php e__('%credit% credit(s)', ['%credit%' => $newPrice]); ?></span>
                          <?php else: ?>
                            <span class="price"><?php e__('%credit% credit(s)', ['%credit%' => $readFeaturedProduct["price"]]); ?></span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif ?>
          <?php endif; ?>
          
          <?php if (themeSettings("discordWidget") == 1): ?>
            <?php if (themeSettings("discordServerID") != '0'): ?>
              <div class="card discord-widget mb-4">
                <a href="<?php echo $readSettings["footerDiscord"]; ?>" rel="external">
                  <div class="card-body">
                    <img class="discord-logo mb-3" src="<?php echo themePath(); ?>/public/assets/img/extras/discord-sidebar.svg" />
          
                    <div class="row align-items-center">
                      <div class="col">
                        <span class="h4 text-white" data-toggle="discordonline" data-discord-id="<?php echo themeSettings("discordServerID"); ?>">0</span>
                        <span class="d-block"><?php e__('Members Online') ?></span>
                      </div>
                      <div class="col-auto">
                        <button type="button" class="btn btn-light rounded-pill btn-icon">
                          <i class="shi-arrow-right"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            <?php endif; ?>
          <?php endif; ?>
  
          <?php if (themeSettings("storeHistoryWidget") == 1): ?>
            <?php $storeHistory = $db->query("SELECT P.name as productName, S.name as serverName, A.realname FROM OrderProducts OP INNER JOIN Orders O ON OP.orderID = O.id INNER JOIN Products P ON OP.productID = P.id INNER JOIN Servers S ON P.serverID = S.id INNER JOIN Accounts A ON O.accountID = A.id ORDER BY O.id DESC LIMIT 5"); ?>
            <?php if ($storeHistory->rowCount() > 0): ?>
              <div class="card mb-3">
                <div class="card-header">
                  <h2 class="card-title last-storebuyers"><?php e__('Recent Purchases') ?></h2>
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                      <tr>
                        <th class="text-center">#</th>
                        <th><?php e__('Username') ?></th>
                        <th class="text-center"><?php e__('Server') ?></th>
                        <th class="text-center"><?php e__('Product') ?></th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php foreach ($storeHistory as $readStoreHistory): ?>
                        <tr>
                          <td class="text-center">
                            <?php echo minecraftHead($readSettings["avatarAPI"], $readStoreHistory["realname"], 20); ?>
                          </td>
                          <td>
                            <?php echo $readStoreHistory["realname"]; ?>
                          </td>
                          <td class="text-center"><?php echo $readStoreHistory["serverName"]; ?></td>
                          <td class="text-center"><?php echo $readStoreHistory["productName"]; ?></td>
                        </tr>
                      <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            <?php else : ?>
              <?php echo alertError(t__('History not found!')); ?>
            <?php endif; ?>
          <?php endif; ?>
  
          <?php if (themeSettings("creditHistoryWidget") == 1): ?>
            <?php
            $creditHistory = $db->prepare("SELECT CH.type, CH.price, A.realname FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE CH.type IN (?, ?) AND CH.paymentStatus = ? ORDER BY CH.id DESC LIMIT 5");
            $creditHistory->execute(array(1, 2, 1));
            ?>
            <?php if ($creditHistory->rowCount() > 0): ?>
              <div class="card mb-3">
                <div class="card-header">
                  <h2 class="card-title last-donators"><?php e__('Recent Donations') ?></h2>
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                      <tr>
                        <th class="text-center">#</th>
                        <th><?php e__('Username') ?></th>
                        <th class="text-center"><?php e__('Amount') ?></th>
                        <th class="text-center"><?php e__('Type') ?></th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php foreach ($creditHistory as $readCreditHistory): ?>
                        <tr>
                          <td class="text-center">
                            <?php echo minecraftHead($readSettings["avatarAPI"], $readCreditHistory["realname"], 20); ?>
                          </td>
                          <td>
                            <?php echo $readCreditHistory["realname"]; ?>
                          </td>
                          <td class="text-center"><?php echo ($readCreditHistory["type"] == 3 || $readCreditHistory["type"] == 5) ? '<span class="text-danger">-'.$readCreditHistory["price"].'</span>' : '<span class="text-success">+'.$readCreditHistory["price"].'</span>'; ?></td>
                          <td class="text-center">
                            <?php if ($readCreditHistory["type"] == 1): ?>
                              <i class="fa fa-mobile" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('Mobile Payment') ?>"></i>
                            <?php elseif ($readCreditHistory["type"] == 2): ?>
                              <i class="fa fa-credit-card" data-bs-toggle="tooltip" data-placement="top" title="<?php e__('Credit Card') ?>"></i>
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
                        </tr>
                      <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            <?php else : ?>
              <?php echo alertError(t__('History not found!')); ?>
            <?php endif; ?>
          <?php endif; ?>
  
          <?php if (themeSettings("topCreditHistoryWidget") == 1): ?>
            <?php
            $topCreditHistory = $db->prepare("SELECT SUM(CH.price) as totalPrice, COUNT(CH.id) as totalProcess, A.realname FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE CH.type IN (?, ?) AND CH.paymentStatus = ? AND CH.creationDate LIKE ? GROUP BY CH.accountID HAVING totalProcess > 0 ORDER BY totalPrice DESC LIMIT 5");
            $topCreditHistory->execute(array(1, 2, 1, '%'.date("Y-m").'%'));
            ?>
            <?php if ($topCreditHistory->rowCount() > 0): ?>
              <div class="card mb-3">
                <div class="card-header">
                  <div class="row">
                    <div class="col">
                      <h2 class="card-title top-donators"><?php e__('Top Donators') ?></h2>
                    </div>
                    <div class="col-auto">
                      <span>(<?php e__('This Month') ?>)</span>
                    </div>
                  </div>
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                      <tr>
                        <th class="text-center">#</th>
                        <th><?php e__('Username') ?></th>
                        <th class="text-center"><?php e__('Total') ?></th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php foreach ($topCreditHistory as $topCreditHistoryRead): ?>
                        <tr>
                          <td class="text-center">
                            <?php echo minecraftHead($readSettings["avatarAPI"], $topCreditHistoryRead["realname"], 20); ?>
                          </td>
                          <td>
                            <?php echo $topCreditHistoryRead["realname"]; ?>
                          </td>
                          <td class="text-center"><?php echo $topCreditHistoryRead["totalPrice"]; ?></td>
                        </tr>
                      <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
