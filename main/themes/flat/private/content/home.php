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
<?php if (!get("category") && !get("tag") && $page == 1): ?>
	<?php if (themeSettings("slider") == 1): ?>
		<?php $slider = $db->query("SELECT * FROM Slider"); ?>
		<?php if ($slider->rowCount() > 0): ?>
			<div class="<?php echo (themeSettings("sliderType") == "wide") ? 'container mt-4 mt-md-5' : null; ?>">
				<div id="carouselSlider" class="carousel slide" data-ride="carousel">
					<ol class="carousel-indicators">
						<?php for ($i=0; $i < $slider->rowCount(); $i++): ?>
							<li <?php echo ($i == 0) ? 'class="active"' : null; ?> data-target="#carouselSlider" data-slide-to="<?php echo $i; ?>"></li>
						<?php endfor; ?>
					</ol>
					<div class="carousel-inner">
						<?php foreach ($slider as $i => $readSlider): ?>
							<div class="carousel-item <?php echo ($i == 0) ? "active" : null; ?>">
								<a href="<?php echo $readSlider["url"]; ?>">
									<img class="d-block w-100 lazyload" data-src="/apps/main/public/assets/img/slider/<?php echo $readSlider["imageID"].'.'.$readSlider["imageType"]; ?>" src="/apps/main/public/assets/img/loaders/slider.png" alt="<?php echo $serverName." Slider - Afiş"; ?>">
									<div class="carousel-caption d-md-block">
										<h1><?php echo $readSlider["title"]; ?></h1>
										<p><?php echo $readSlider["content"]; ?></p>
									</div>
								</a>
							</div>
						<?php endforeach; ?>
					</div>
					<a class="carousel-control-prev" href="#carouselSlider" role="button" data-slide="prev">
						<span class="fa fa-angle-left" aria-hidden="true"></span>
						<span class="sr-only"><?php e__('Prev') ?></span>
					</a>
					<a class="carousel-control-next" href="#carouselSlider" role="button" data-slide="next">
						<span class="fa fa-angle-right" aria-hidden="true"></span>
						<span class="sr-only"><?php e__('Next') ?></span>
					</a>
				</div>
				<?php if (themeSettings("serverOnlineBar") == 1): ?>
					<div class="server-online-info" data-toggle="onlinebox"><strong data-toggle="onlinetext" server-ip="<?php echo $serverIP; ?>">-/-</strong> <?php e__('players online') ?></div>
				<?php endif; ?>
			</div>
		<?php else: ?>
			<section class="section">
				<div class="container">
					<div class="row">
						<div class="col-12">
							<?php echo alertError(t__('Slider not found!')); ?>
						</div>
					</div>
				</div>
			</section>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>
<section class="section news-section">
	<div class="container">
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
		<div class="row">
			<div class="<?php echo (themeSettings("sidebar") == 0) ? 'col-md-12' : 'col-md-8'; ?>">
				<div class="row">
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
							<?php
								$newsCardCol = 'col-md-4';
								$newsLetterLimit = 240;
								if (themeSettings("sidebar") == 0 && themeSettings('newsCardType') == "small") {
									$newsCardCol = 'col-md-4';
									$newsLetterLimit = 240;
								}
								if (themeSettings("sidebar") == 0 && themeSettings('newsCardType') == "wide"
								|| themeSettings("sidebar") == 1 && themeSettings('newsCardType') == "small") {
									$newsCardCol = 'col-md-6';
									$newsLetterLimit = 420;
								}
								if (themeSettings("sidebar") == 1 && themeSettings('newsCardType') == "wide") {
									$newsCardCol = 'col-md-12';
									$newsLetterLimit = 600;
								}
							?>
							<div class="<?php echo $newsCardCol; ?>">
								<article class="news">
									<div class="card">
										<div class="img-container">
											<a class="img-card" href="/posts/<?php echo $readNews["id"]; ?>/<?php echo $readNews["slug"]; ?>">
												<img class="card-img-top lazyload" data-src="/apps/main/public/assets/img/news/<?php echo $readNews["imageID"].'.'.$readNews["imageType"]; ?>" src="/apps/main/public/assets/img/loaders/news.png" alt="<?php echo $serverName." Haber - ".$readNews["title"]; ?>">
											</a>
											<div class="img-card-tl">
												<a href="/categories/<?php echo $readNews["categorySlug"]; ?>">
													<span class="theme-color badge badge-pill badge-primary"><?php echo $readNews["categoryName"]; ?></span>
												</a>
												<a href="/posts/<?php echo $readNews["id"]; ?>/<?php echo $readNews["slug"]; ?>">
													<span class="theme-color badge badge-pill badge-primary"><i class="fa fa-eye"></i> <?php echo $readNews["views"]; ?></span>
												</a>
												<a href="/posts/<?php echo $readNews["id"]; ?>/<?php echo $readNews["slug"]; ?>">
													<span class="theme-color badge badge-pill badge-primary"><i class="fa fa-comments"></i> <?php echo $newsComments->rowCount(); ?></span>
												</a>
											</div>
											<div class="img-card-tr">
												<a href="/posts/<?php echo $readNews["id"]; ?>/<?php echo $readNews["slug"]; ?>">
													<span class="theme-color badge badge-pill badge-primary"><?php echo convertTime($readNews["creationDate"], 1); ?></span>
												</a>
											</div>
											<div class="img-card-bottom">
												<h5 class="mb-0">
													<a class="text-white" href="/posts/<?php echo $readNews["id"]; ?>/<?php echo $readNews["slug"]; ?>">
														<?php echo $readNews["title"]; ?>
													</a>
												</h5>
											</div>
										</div>
										<div class="card-body">
											<p class="card-text" <?php echo ($newsCardCol == 'col-md-12') ? 'style="height: auto !important; max-height: 168px !important;"' : null ?>>
												<?php echo showEmoji(substr(strip_tags($readNews["content"]), 0, $newsLetterLimit)); ?>
											</p>
											<a class="theme-color btn btn-primary w-100" href="/posts/<?php echo $readNews["id"]; ?>/<?php echo $readNews["slug"]; ?>"><?php e__('Read More') ?></a>
										</div>
									</div>
								</article>
							</div>
						<?php endforeach; ?>
						<div class="col-md-12 d-flex justify-content-center <?php echo (themeSettings("sidebar") == 1) ? 'mb-4' : null; ?>">
							<nav class="pages" aria-label="Sayfalar">
								<ul class="pagination">
									<li class="page-item <?php echo ($page == 1) ? "disabled" : null; ?>">
										<a class="page-link" href="<?php echo $requestURL.'/'.($page-1); ?>" tabindex="-1">
											<i class="fa fa-angle-double-left"></i>
										</a>
									</li>
									<?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
										<?php if ($i > 0 and $i <= $pageCount): ?>
											<li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
												<a class="page-link" href="<?php echo $requestURL.'/'.$i; ?>"><?php echo $i; ?></a>
											</li>
										<?php endif; ?>
									<?php endfor; ?>
									<li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
										<a class="page-link" href="<?php echo $requestURL.'/'.($page+1); ?>">
											<i class="fa fa-angle-double-right"></i>
										</a>
									</li>
								</ul>
							</nav>
						</div>
					<?php else : ?>
						<div class="col-md-12">
							<?php echo alertError(t__('No posts were found!')); ?>
						</div>
					<?php endif; ?>
				</div>
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
          
          <?php if (themeSettings("storeHistoryWidget") == 1): ?>
            <?php $storeHistory = $db->query("SELECT P.name as productName, S.name as serverName, A.realname FROM OrderProducts OP INNER JOIN Orders O ON OP.orderID = O.id INNER JOIN Products P ON OP.productID = P.id INNER JOIN Servers S ON P.serverID = S.id INNER JOIN Accounts A ON O.accountID = A.id ORDER BY O.id DESC LIMIT 5"); ?>
            <?php if ($storeHistory->rowCount() > 0): ?>
              <div class="card mb-3">
                <div class="card-header">
                  <span><?php e__('Recent Purchases') ?></span>
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
                  <span><?php e__('Recent Donations') ?></span>
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
                              <i class="fa fa-mobile" data-toggle="tooltip" data-placement="top" title="<?php e__('Mobile Payment') ?>"></i>
                            <?php elseif ($readCreditHistory["type"] == 2): ?>
                              <i class="fa fa-credit-card" data-toggle="tooltip" data-placement="top" title="<?php e__('Credit Card') ?>"></i>
                            <?php elseif ($readCreditHistory["type"] == 3): ?>
                              <i class="fa fa-paper-plane" data-toggle="tooltip" data-placement="top" title="<?php e__('Transfer (Sender)') ?>"></i>
                            <?php elseif ($readCreditHistory["type"] == 4): ?>
                              <i class="fa fa-paper-plane" data-toggle="tooltip" data-placement="top" title="<?php e__('Transfer (Receiver)') ?> "></i>
                            <?php elseif ($readCreditHistory["type"] == 5): ?>
                              <i class="fa fa-ticket-alt" data-toggle="tooltip" data-placement="top" title="<?php e__('Wheel of Fortune (Ticket)') ?>"></i>
                            <?php elseif ($readCreditHistory["type"] == 6): ?>
                              <i class="fa fa-ticket-alt" data-toggle="tooltip" data-placement="top" title="<?php e__('Wheel of Fortune (Prize)') ?>"></i>
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
                      <span><?php e__('Top Donators') ?></span>
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
          
          <?php if (themeSettings("discordWidget") == 1): ?>
            <?php if (themeSettings("discordServerID") != '0'): ?>
              <iframe class="lazyload" data-src="https://discordapp.com/widget?id=<?php echo themeSettings("discordServerID"); ?>&theme=<?php echo (themeSettings("discordServerID") == "light") ? "light" : ((themeSettings("discordServerID") == "dark") ? "dark" : "light"); ?>" width="100%" height="500" allowtransparency="true" frameborder="0"></iframe>
            <?php endif; ?>
          <?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
