<?php
  use Phelium\Component\reCAPTCHA;
  require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource(themePath().'/public/assets/js/loader.js');
  $recaptchaPagesStatusJSON = $readSettings["recaptchaPagesStatus"];
  $recaptchaPagesStatus = json_decode($recaptchaPagesStatusJSON, true);
  $recaptchaStatus = $readSettings["recaptchaPublicKey"] != '0' && $readSettings["recaptchaPrivateKey"] != '0' && $recaptchaPagesStatus["newsPage"] == 1;
  if ($recaptchaStatus) {
    require_once(__ROOT__.'/apps/main/private/packages/class/recaptcha/recaptcha.php');
    $reCAPTCHA = new reCAPTCHA($readSettings["recaptchaPublicKey"], $readSettings["recaptchaPrivateKey"]);
    $reCAPTCHA->setRemoteIp(getIP());
    $reCAPTCHA->setLanguage($lang);
    $reCAPTCHA->setTheme((themeSettings("recaptchaTheme") == "light") ? "light" : ((themeSettings("recaptchaTheme") == "dark") ? "dark" : "light"));
    $extraResourcesJS->addResource($reCAPTCHA->getScriptURL(), true, true);
  }
  $news = $db->prepare("SELECT N.*, A.realname, NC.name as categoryName, NC.slug as categorySlug FROM News N INNER JOIN Accounts A ON N.accountID = A.id INNER JOIN NewsCategories NC ON N.categoryID = NC.id WHERE N.id = ?");
  $news->execute(array(get("id")));
  $readNews = $news->fetch();
?>
<section class="section news-section">
  <div class="container">
    <div class="row justify-content-center">
      <!-- Content-->
      <div class="col-lg-10">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><?php e__('Home') ?></a></li>
            <?php if (isset($_GET["id"])): ?>
              <?php if ($news->rowCount() > 0): ?>
                <li class="breadcrumb-item"><a href="/categories/<?php echo $readNews["categorySlug"]; ?>"><?php echo $readNews["categoryName"]; ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $readNews["title"]; ?></li>
              <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page"><?php e__('Not Found!') ?></li>
              <?php endif; ?>
            <?php else: ?>
              <li class="breadcrumb-item active" aria-current="page"><?php e__('Blog') ?></li>
            <?php endif; ?>
          </ol>
        </nav>
        
        <?php if ($news->rowCount() > 0): ?>
          <?php if (!isset($_COOKIE["newsID"])): ?>
            <?php
            $updateNews = $db->prepare("UPDATE News SET views = views + 1 WHERE id = ?");
            $updateNews->execute(array($readNews["id"]));
            setcookie("newsID", $readNews["id"]);
            ?>
          <?php endif; ?>
          <?php
          $newsComments = $db->prepare("SELECT NC.*, A.realname FROM NewsComments NC INNER JOIN Accounts A ON NC.accountID = A.id WHERE NC.newsID = ? AND NC.status = ? ORDER BY NC.id DESC");
          $newsComments->execute(array($readNews["id"], 1));
          ?>
          <h1 class="h2 mb-3">
            <?php echo $readNews["title"]; ?>
          </h1>
          <div class="card mb-5">
            <div class="card-body">
              <div class="row position-relative g-0 align-items-center">
                <div class="col-md-6 pb-4">
                  <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center me-grid-gutter">
                      <a class="d-block" href="/player/<?php echo $readNews["realname"]; ?>">
                        <?php echo minecraftHead($readSettings["avatarAPI"], $readNews["realname"], 34); ?>
                      </a>
                      <div class="ps-2">
                        <h6 class="nav-heading mb-0">
                          <a href="/player/<?php echo $readNews["realname"]; ?>">
                            <?php echo $readNews["realname"]; ?>
                          </a>
                        </h6>
                        <div class="text-nowrap">
                          <div class="meta-link fs-xs">
                            <i class="shi-calendar me-1 align-vertical"></i> <?php echo convertTime($readNews["creationDate"], 2, true); ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 ps-md-3 py-3 d-none d-md-block">
                  <div class="d-flex align-items-center justify-content-center justify-content-md-end">
                    <span class="me-3">
                      <i class="shi-eye"></i> <?php echo $readNews["views"]; ?>
                    </span>
                    <span>
                      <i class="shi-message-square"></i> <?php echo $newsComments->rowCount(); ?>
                    </span>
                  </div>
                </div>
              </div>
              <!-- Post content-->
              <div class="article-content">
                <?php echo showEmoji(hashtag(hashtag($readNews["content"], "@", "/player"), "#", "/tags")); ?>
              </div>
              <!-- Tags-->
              <div class="tags">
                <?php
                  $newsTags = $db->prepare("SELECT NT.* FROM NewsTags NT INNER JOIN News N ON NT.newsID = N.id WHERE NT.newsID = ?");
                  $newsTags->execute(array($readNews["id"]));
                  if ($newsTags->rowCount() > 0) {
                    foreach ($newsTags as $readNewsTags) {
                      echo '<a class="btn-tag me-2 my-2" href="/tags/'.$readNewsTags["slug"].'">'.$readNewsTags["name"].'</a>';
                    }
                  }
                  else {
                    echo "-";
                  }
                ?>
              </div>
            </div>
          </div>
          <?php if ($readSettings["commentsStatus"] == 1 && $readNews["commentsStatus"] == 1): ?>
            <?php if (isset($_SESSION["login"])): ?>
              <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["insertNewsComments"])) {
                if (!$csrf->validate('insertNewsComments')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if ($recaptchaStatus && post("g-recaptcha-response") == null) {
                  echo alertError(t__('Please verify you are not a robot.'));
                }
                else if ($recaptchaStatus && !$reCAPTCHA->isValid(post("g-recaptcha-response"))) {
                  // Hata Tespit
                  //var_dump($reCAPTCHA->getErrorCodes());
                  echo alertError(t__('Spam detected!'));
                }
                else if (post("message") == null) {
                  echo alertError(t__('Please fill all the fields!'));
                }
                else {
                  $commentBannedStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?)");
                  $commentBannedStatus->execute(array($readAccount["id"], 3, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
                  if ($commentBannedStatus->rowCount() > 0) {
                    echo alertError(t__('You are banned from commenting.'));
                  }
                  else {
                    if (checkStaff($readAccount)) {
                      $status = 1;
                      echo alertSuccess(t__('Your comment has been successfully sent.'));
                    }
                    else {
                      $status = 0;
                      echo alertSuccess(t__('Your comment will be visible to public after a mod-check.'));
                    }
                    $insertNewsComments = $db->prepare("INSERT INTO NewsComments (accountID, message, newsID, status, creationDate) VALUES (?, ?, ?, ?, ?)");
                    $insertNewsComments->execute(array($readAccount["id"], post("message"), get("id"), $status, date("Y-m-d H:i:s")));
                    $notificationsVariables = $db->lastInsertId();
                    $insertNotifications = $db->prepare("INSERT INTO Notifications (accountID, type, variables, creationDate) VALUES (?, ?, ?, ?)");
                    $insertNotifications->execute(array($readAccount["id"], 2, $notificationsVariables, date("Y-m-d H:i:s")));
            
                    $websiteURL = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"]);
                    if ($readSettings["webhookNewsURL"] != '0') {
                      require_once(__ROOT__."/apps/main/private/packages/class/webhook/webhook.php");
                      $search = array("%username%", "%panelurl%", "%posturl%");
                      $replace = array($readAccount["realname"], "$websiteURL/dashboard/blog/comments/edit/$notificationsVariables", "$websiteURL/posts/$readNews[id]/$readNews[slug]");
                      $webhookMessage = $readSettings["webhookNewsMessage"];
                      $webhookEmbed = $readSettings["webhookNewsEmbed"];
                      $postFields = (array(
                        'content'     => ($webhookMessage != '0') ? str_replace($search, $replace, $webhookMessage) : null,
                        'avatar_url'  => 'https://minotar.net/avatar/'.$readAccount["realname"].'/256.png',
                        'tts'         => false,
                        'embeds'      => array(
                          array(
                            'type'        => 'rich',
                            'title'       => $readSettings["webhookNewsTitle"],
                            'color'       => hexdec($readSettings["webhookNewsColor"]),
                            'description' => str_replace($search, $replace, $webhookEmbed),
                            'image'       => array(
                              'url' => ($readSettings["webhookNewsImage"] != '0') ? $readSettings["webhookNewsImage"] : null
                            ),
                            'footer'      =>
                              ($readSettings["webhookNewsAdStatus"] == 1) ? array(
                                'text'      => 'Powered by LeaderOS',
                                'icon_url'  => 'https://i.ibb.co/wNHKQ7B/leaderos-logo.png'
                              ) : array()
                          )
                        )
                      ));
                      $curl = new \LeaderOS\Http\Webhook($readSettings["webhookNewsURL"]);
                      $curl(json_encode($postFields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                    }
            
                    if ($readSettings["oneSignalAppID"] != '0' && $readSettings["oneSignalAPIKey"] != '0') {
                      require_once(__ROOT__."/apps/main/private/packages/class/onesignal/onesignal.php");
                      $adminAccounts = $db->prepare("SELECT AOSI.oneSignalID FROM Accounts A INNER JOIN AccountOneSignalInfo AOSI ON A.id = AOSI.accountID WHERE A.permission IN (?, ?, ?, ?)");
                      $adminAccounts->execute(array(1, 2, 3, 4));
                      if ($adminAccounts->rowCount() > 0) {
                        $oneSignalIDList = array();
                        foreach ($adminAccounts as $readAdminAccounts) {
                          array_push($oneSignalIDList, $readAdminAccounts["oneSignalID"]);
                        }
                        $oneSignal = new OneSignal($readSettings["oneSignalAppID"], $readSettings["oneSignalAPIKey"], $oneSignalIDList);
                        $oneSignal->sendMessage(t__('LeaderOS Notifications'), t__('%username% left a comment.', ['%username%' => $readAccount["realname"]]), '/dashboard/blog/comments/edit/'.$notificationsVariables);
                      }
                    }
                  }
                }
              }
              ?>
              <div class="mb-5">
                <h2 class="h4 mb-3"><?php e__('Leave a Reply') ?></h2>
                <div class="card">
                  <div class="card-body">
                    <form action="" method="post">
                      <div class="row">
                        <div class="col-auto">
                          <?php echo minecraftHead($readSettings["avatarAPI"], $readAccount["realname"], 40, "float-left"); ?>
                        </div>
                        <div class="col ps-0">
                          <textarea name="message" class="form-control" rows="3" placeholder="<?php e__('Write your comment.') ?>"></textarea>
                          <?php if ($recaptchaStatus): ?>
                            <div class="d-flex justify-content-end mt-3">
                              <?php echo $reCAPTCHA->getHtml(); ?>
                            </div>
                          <?php endif; ?>
                          <?php echo $csrf->input('insertNewsComments'); ?>
                          <div class="d-flex justify-content-end">
                            <button name="insertNewsComments" type="submit" class="btn btn-success mt-3"><?php e__('Send') ?></button>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            <?php else: ?>
              <?php echo alertError(t__('You need to be signed in to comment.')); ?>
            <?php endif; ?>
            <?php if ($newsComments->rowCount() > 0): ?>
              <div class="mb-5">
                <h2 class="h4 mb-3"><?php e__('Comments') ?></h2>
                <?php foreach ($newsComments as $readNewsComments): ?>
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-auto">
                          <a href="/player/<?php echo $readNewsComments["realname"]; ?>">
                            <?php echo minecraftHead($readSettings["avatarAPI"], $readNewsComments["realname"], 40, "float-left"); ?>
                          </a>
                        </div>
                        <div class="col ps-0">
                          <div class="row align-items-center mb-2">
                            <div class="col">
                              <a href="/player/<?php echo $readNewsComments["realname"]; ?>">
                                <span class="h6">
                                  <?php echo $readNewsComments["realname"]; ?>
                                </span>
                              </a>
                            </div>
                            <div class="col-auto">
                              <span class="small"><?php echo convertTime($readNewsComments["creationDate"]); ?></span>
                            </div>
                          </div>
                          <p>
                            <?php echo showEmoji(urlContent(hashtag(hashtag($readNewsComments["message"], "@", "/player"), "#", "/tags"))); ?>
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <?php if (isset($_SESSION["login"])): ?>
                <?php echo alertWarning(t__('Hey, no comments yet! Would you like to comment first?')); ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php else: ?>
            <?php echo alertWarning(t__('Comments are closed!')); ?>
          <?php endif; ?>
        <?php else: ?>
          <?php echo alertError(t__('Post not found!')); ?>
        <?php endif; ?>
        
        <!-- Related posts -->
        <div class="mt-5 mb-5">
          <div class="row align-items-center pb-4">
            <div class="col">
              <h2 class="h4 mb-0"><?php e__('Related Posts') ?></h2>
            </div>
            <div class="col-auto">
              <a href="/blog/1" class="btn btn-light"><?php e__('View All') ?></a>
            </div>
          </div>
          <div class="row">
            <?php
              $otherNews = $db->prepare("SELECT N.id, N.title, N.slug, N.content, N.views, N.imageID, N.imageType, N.creationDate, NC.name as categoryName, NC.slug as categorySlug from News N INNER JOIN Accounts A ON N.accountID = A.id INNER JOIN NewsCategories NC ON N.categoryID = NC.id WHERE N.id != ? ORDER BY N.id DESC LIMIT 3");
              $otherNews->execute(array($readNews["id"]));
            ?>
            <?php if ($otherNews->rowCount() > 0): ?>
              <?php foreach ($otherNews as $readOtherNews): ?>
                <?php
                $otherNewsComments = $db->prepare("SELECT NC.id FROM NewsComments NC INNER JOIN Accounts A ON NC.accountID = A.id WHERE NC.newsID = ? AND NC.status = ?");
                $otherNewsComments->execute(array($readOtherNews["id"], 1));
                ?>
                <div class="col-md-4">
                  <article class="card card-hover">
                    <a class="card-img-top" href="/posts/<?php echo $readOtherNews["id"]; ?>/<?php echo $readOtherNews["slug"]; ?>">
                      <img class="lazyload" data-src="/apps/main/public/assets/img/news/<?php echo $readOtherNews["imageID"].'.'.$readOtherNews["imageType"]; ?>" src="/apps/main/public/assets/img/loaders/news.png" alt="<?php echo $serverName." Haber - ".$readOtherNews["title"]; ?>">
                    </a>
                    <div class="card-body">
                      <a class="meta-link fs-sm mb-2" href="/categories/<?php echo $readOtherNews["categorySlug"]; ?>">
                        <?php echo $readOtherNews["categoryName"]; ?>
                      </a>
                      <span class="meta-link fs-sm mb-2">-</span>
                      <span class="meta-link fs-sm mb-2">
                        <?php echo convertTime($readOtherNews["creationDate"], 1); ?>
                      </span>
                      <h2 class="h5 nav-heading mb-3">
                        <a href="/posts/<?php echo $readOtherNews["id"]; ?>/<?php echo $readOtherNews["slug"]; ?>">
                          <?php echo $readOtherNews["title"]; ?>
                        </a>
                      </h2>
                    </div>
                  </article>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="col-12">
                <?php echo alertError(t__('No related posts!')); ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
