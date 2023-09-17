<?php
  function recursiveCategoryList($id = 0) {
    global $db;
    
    $list = [];
    
    $categories = $db->prepare("SELECT * FROM ForumCategories WHERE id = ?");
    $categories->execute(array($id));
    $categories = $categories->fetchAll();
    foreach ($categories as $category) {
      $list[$category['id']] = $category;
      $list[$category['id']]['children'] = recursiveCategoryList($category['parentID']);
    }
    return $list;
  }
  function recursiveCategoryBreadcrumbHtml($list) {
    $array = [];
    foreach ($list as $category) {
      $array[] = '<li class="breadcrumb-item"><a href="/forum/categories/' . $category['id'] . '/' . $category['slug'] . '">' . $category['name'] . '</a></li>';
      if (count($category['children']) > 0) {
        $array[] = recursiveCategoryBreadcrumbHtml($category['children']);
      }
    }
    return implode('', array_reverse($array));
  }
  
  include __ROOT__ . '/apps/dashboard/private/packages/class/slugify/autoload.php';
  $slugify = new \Cocur\Slugify\Slugify();
  
  if (get("target") == "replies") {
    if (get("action") == "update" && get("id")) {
      if (!isset($_SESSION["login"])) {
        go("/login");
      }
      if (checkPerm($readAccount, 'MANAGE_FORUM')) {
        $reply = $db->prepare("SELECT * FROM ForumReplies WHERE id = ?");
        $reply->execute(array(get("id")));
      }
      else {
        $reply = $db->prepare("SELECT * FROM ForumReplies WHERE id = ? AND accountID = ?");
        $reply->execute(array(get("id"), $readAccount["id"]));
      }
      $readReply = $reply->fetch();
    }
    if (get("action") == "delete" && get("id")) {
      if (!isset($_SESSION["login"])) {
        go("/login");
      }
      $reply = $db->prepare("SELECT FR.threadID, FT.slug as threadSlug FROM ForumReplies FR INNER JOIN ForumThreads FT ON FT.id = FR.threadID WHERE FR.id = ?");
      $reply->execute(array(get("id")));
      $readReply = $reply->fetch();
      if ($reply->rowCount() > 0) {
        if (checkPerm($readAccount, 'MANAGE_FORUM')) {
          $deleteReply = $db->prepare("DELETE FROM ForumReplies WHERE id = ?");
          $deleteReply->execute(array(get("id")));
        }
        else {
          $deleteReply = $db->prepare("DELETE FROM ForumReplies WHERE id = ? AND accountID = ?");
          $deleteReply->execute(array(get("id"), $readAccount["id"]));
        }
        go("/forum/threads/".$readReply["threadID"]."/".$readReply["threadSlug"]);
      }
    }
  }
  if (get("target") == "threads") {
    if (get("action") == "get" && get("id")) {
      $thread = $db->prepare("SELECT FT.*, A.realname FROM ForumThreads FT INNER JOIN Accounts A ON A.id = FT.accountID WHERE FT.id = ?");
      $thread->execute(array(get("id")));
      $readThread = $thread->fetch();
      $categoryList = [];
      if ($thread->rowCount() > 0) {
        $categoryList = recursiveCategoryList($readThread["categoryID"]);
        $replies = $db->prepare("SELECT FR.*, A.realname FROM ForumReplies FR INNER JOIN Accounts A ON A.id = FR.accountID WHERE FR.threadID = ?");
        $replies->execute(array($readThread["id"]));
        
        if (!isset($_COOKIE["threadID"])) {
          $updateThread = $db->prepare("UPDATE ForumThreads SET views = views + 1 WHERE id = ?");
          $updateThread->execute(array($readThread["id"]));
          setcookie("threadID", $readThread["id"]);
        }
      }
    }
    if (get("action") == "insert" && get("category")) {
      if (!isset($_SESSION["login"])) {
        go("/login");
      }
      $category = $db->prepare("SELECT * FROM ForumCategories WHERE id = ?");
      $category->execute(array(get("category")));
      $readCategory = $category->fetch();
    }
    if (get("action") == "update" && get("id")) {
      if (!isset($_SESSION["login"])) {
        go("/login");
      }
      if (checkPerm($readAccount, 'MANAGE_FORUM')) {
        $thread = $db->prepare("SELECT * FROM ForumThreads WHERE id = ?");
        $thread->execute(array(get("id")));
      }
      else {
        $thread = $db->prepare("SELECT * FROM ForumThreads WHERE id = ? AND accountID = ?");
        $thread->execute(array(get("id"), $readAccount["id"]));
      }
      $readThread = $thread->fetch();
    }
    if (get("action") == "delete" && get("id")) {
      if (!isset($_SESSION["login"])) {
        go("/login");
      }
      if (checkPerm($readAccount, 'MANAGE_FORUM')) {
        $deleteThread = $db->prepare("DELETE FROM ForumThreads WHERE id = ?");
        $deleteThread->execute(array(get("id")));
      }
      else {
        $deleteThread = $db->prepare("DELETE FROM ForumThreads WHERE id = ? AND accountID = ?");
        $deleteThread->execute(array(get("id"), $readAccount["id"]));
      }
      go("/forum");
    }
  }
  if (get("target") == "categories") {
    if (get("action") == "get" && get("id")) {
      $categoryList = [];
      $category = $db->prepare("SELECT * FROM ForumCategories WHERE id = ?");
      $category->execute(array(get("id")));
      if ($category->rowCount() > 0) {
        $readCategory = $category->fetch();
        $categoryList = recursiveCategoryList($readCategory["id"]);
      }
    }
  }
  
  require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource(themePath().'/public/assets/js/plugins/ckeditor/translations/tr.js');
  $extraResourcesJS->addResource(themePath().'/public/assets/js/plugins/ckeditor/ckeditor.js');
  $extraResourcesJS->addResource(themePath().'/public/assets/js/ckeditor5.js');
?>
<style>
  .ck-editor__editable {
    min-height: 150px !important;
  }
  .forum-content img {
    max-width: 100%;
    height: auto;
  }
  .role__default {
    display: block;
    width: 100%;
    margin-bottom: 0.25rem;
    padding: 0.5rem 0;
    border-radius: .3rem;
    text-align: center;
    font-size: 80%;
    font-weight: 700;
    color: #343a40;
    background-color: #f8f9fa;
  }
</style>
<section class="section forum-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><?php e__('Home') ?></a></li>
            <?php if (get("target") == "threads"): ?>
              <?php if (get("action") == "get" && get("id")): ?>
                <li class="breadcrumb-item"><a href="/forum"><?php e__('Forum') ?></a></li>
                <?php echo recursiveCategoryBreadcrumbHtml($categoryList); ?>
              <?php elseif (get("action") == "insert"): ?>
                <li class="breadcrumb-item"><a href="/forum"><?php e__('Forum') ?></a></li>
                <?php if ($category->rowCount() > 0): ?>
                  <li class="breadcrumb-item"><a href="/forum/categories/<?php echo $readCategory["id"]."/".$readCategory["slug"]; ?>"><?php echo $readCategory["name"] ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?php e__('Post Thread') ?></li>
              <?php elseif (get("action") == "update"): ?>
                <li class="breadcrumb-item"><a href="/forum"><?php e__('Forum') ?></a></li>
              <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page"><?php e__('Error!') ?></li>
              <?php endif; ?>
            <?php elseif (get("target") == "categories"): ?>
              <?php if (get("action") == "getAll"): ?>
                <li class="breadcrumb-item active" aria-current="page"><?php e__('Forum') ?></li>
              <?php elseif (get("action") == "get" && get("id")): ?>
                <li class="breadcrumb-item"><a href="/forum"><?php e__('Forum') ?></a></li>
                <?php echo recursiveCategoryBreadcrumbHtml($categoryList); ?>
              <?php endif; ?>
            <?php elseif (get("target") == "replies"): ?>
              <?php if (get("action") == "update"): ?>
                <li class="breadcrumb-item"><a href="/forum"><?php e__('Forum') ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php e__('Edit Reply') ?></li>
              <?php endif; ?>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
    </div>

    <?php if (get("target") == "categories"): ?>
      <?php if (get("action") == "getAll"): ?>
        <div class="row">
          <div class="col-md-8">
            <?php
              $rootCategories = $db->prepare("SELECT * FROM ForumCategories WHERE parentID = ?");
              $rootCategories->execute(array(0));
            ?>
            <?php if ($rootCategories->rowCount() > 0): ?>
              <?php foreach ($rootCategories as $readRootCategories): ?>
                <div class="card">
                  <div class="card-header">
                    <a href="/forum/categories/<?php echo $readRootCategories["id"]."/".$readRootCategories["slug"]; ?>" class="text-white">
                      <?php echo $readRootCategories["name"]; ?>
                    </a>
                  </div>
                  <div class="card-body">
                    <?php
                      $childCategories = $db->prepare("SELECT * FROM ForumCategories WHERE parentID = ?");
                      $childCategories->execute(array($readRootCategories["id"]));
                    ?>
                    <?php foreach ($childCategories as $readChildCategories): ?>
                      <div class="row border-bottom align-items-center mb-2 pb-3">
                        <div class="col-auto pr-1">
                          <img src="/apps/main/public/assets/img/forum/categories/<?php echo $readChildCategories["imageID"].".".$readChildCategories["imageType"]; ?>" alt="<?php echo $readChildCategories["name"]; ?>" width="32" height="32">
                        </div>
                        <div class="col">
                          <a href="/forum/categories/<?php echo $readChildCategories["id"]."/".$readChildCategories["slug"]; ?>" class="font-weight-bold">
                            <?php echo $readChildCategories["name"]; ?>
                          </a>
                          <?php
                            $subChildCategories = $db->prepare("SELECT * FROM ForumCategories WHERE parentID = ?");
                            $subChildCategories->execute(array($readChildCategories["id"]));
                          ?>
                          <?php if ($subChildCategories->rowCount() > 0): ?>
                            <div>
                              <?php foreach ($subChildCategories as $readSubChildCategories): ?>
                                <a class="small mr-2 text-dark" href="/forum/categories/<?php echo $readSubChildCategories["id"]."/".$readSubChildCategories["slug"]; ?>">
                                  <i class="fa fa-link"></i>
                                  <?php echo $readSubChildCategories["name"]; ?>
                                </a>
                              <?php endforeach; ?>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="col-md-12">
                <?php echo alertError(t__('No data were found!')); ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-header"><?php e__('Latest Threads') ?></div>
              <div class="card-body pt-3 pb-0">
                <?php
                  $lastThreads = $db->query("SELECT FT.*, A.realname FROM ForumThreads FT INNER JOIN Accounts A ON A.id = FT.accountID ORDER BY FT.id DESC LIMIT 5");
                ?>
                <?php if ($lastThreads->rowCount() > 0): ?>
                  <?php foreach ($lastThreads as $readLastThreads): ?>
                    <div class="row border-bottom align-items-center mb-2 pb-3">
                      <div class="col-auto pr-0">
                        <?php echo minecraftHead($readSettings["avatarAPI"], $readLastThreads["realname"], 26); ?>
                      </div>
                      <div class="col">
                        <a href="/forum/threads/<?php echo $readLastThreads["id"]."/".$readLastThreads["slug"]; ?>" class="font-weight-bold">
                          <?php echo $readLastThreads["title"]; ?>
                        </a>
                        <div class="small">
                          <span><?php echo $readLastThreads["realname"]; ?></span>
                          <span>•</span>
                          <span><?php echo convertTime($readLastThreads["creationDate"]); ?></span>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <?php echo alertError(t__('No threads were found!')); ?>
                <?php endif; ?>
              </div>
            </div>
  
            <div class="card">
              <div class="card-header"><?php e__('Latest Replies') ?></div>
              <div class="card-body pt-3 pb-0">
                <?php
                  $lastReplies = $db->query("SELECT FT.id, FT.slug, FT.title, FR.creationDate, A.realname FROM ForumReplies FR INNER JOIN Accounts A ON A.id = FR.accountID INNER JOIN ForumThreads FT ON FT.id = FR.threadID ORDER BY FR.id DESC LIMIT 5");
                ?>
                <?php if ($lastReplies->rowCount() > 0): ?>
                  <?php foreach ($lastReplies as $readLastReplies): ?>
                    <div class="row border-bottom align-items-center mb-2 pb-3">
                      <div class="col-auto pr-0">
                        <?php echo minecraftHead($readSettings["avatarAPI"], $readLastReplies["realname"], 26); ?>
                      </div>
                      <div class="col">
                        <a href="/forum/threads/<?php echo $readLastReplies["id"]."/".$readLastReplies["slug"]; ?>" class="font-weight-bold">
                          <?php echo $readLastReplies["title"]; ?>
                        </a>
                        <div class="small">
                          <span><?php echo $readLastReplies["realname"]; ?></span>
                          <span>•</span>
                          <span><?php echo convertTime($readLastReplies["creationDate"]); ?></span>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <?php echo alertError(t__("No data were found!")); ?>
                <?php endif; ?>
              </div>
            </div>
            
            <div class="card">
              <div class="card-header"><?php e__('Members Online') ?></div>
              <div class="card-body">
                <?php
                  $onlineAccountsHistory = $db->prepare("SELECT A.realname FROM OnlineAccountsHistory OAH INNER JOIN Accounts A ON OAH.accountID = A.id WHERE OAH.expiryDate > ?");
                  $onlineAccountsHistory->execute(array(date("Y-m-d H:i:s")));
                  $onlineAccountsHistoryList = [];
                ?>
                <?php if ($onlineAccountsHistory->rowCount() > 0): ?>
                  <?php foreach ($onlineAccountsHistory as $onlineAccount): ?>
                    <?php $onlineAccountsHistoryList[] = '<a href="/player/'.$onlineAccount["realname"].'">'.$onlineAccount["realname"].'</a>'; ?>
                  <?php endforeach; ?>
                  <?php echo implode(', ', $onlineAccountsHistoryList); ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php elseif (get("action") == "get" && get("id")): ?>
        <div class="row">
          <div class="col-md-12">
            <?php if ($category->rowCount() > 0): ?>
              <div class="row align-items-center mb-3">
                <div class="col">
                  <h2 class="h4 font-weight-bold text-dark mb-0">
                    <?php echo $readCategory["name"]; ?>
                  </h2>
                </div>
                <div class="col-auto">
                  <?php if (isset($_SESSION["login"])): ?>
                    <a href="/forum/threads/create?category=<?php echo $readCategory["id"]; ?>" class="btn btn-banner-bg">
                      <i class="fa fa-plus mr-1"></i>
                      <?php e__('Post Thread') ?>
                    </a>
                  <?php endif; ?>
                </div>
              </div>
              
              <?php
              $childCategories = $db->prepare("SELECT * FROM ForumCategories WHERE parentID = ?");
              $childCategories->execute(array($readCategory["id"]));
              ?>
              <?php if ($childCategories->rowCount() > 0): ?>
                <div class="card">
                  <div class="card-header"><?php e__('Subcategories') ?></div>
                  <div class="card-body">
                    <?php foreach ($childCategories as $readChildCategories): ?>
                      <div class="row border-bottom align-items-center mb-2 pb-3">
                        <div class="col-auto pr-1">
                          <img src="/apps/main/public/assets/img/forum/categories/<?php echo $readChildCategories["imageID"].".".$readChildCategories["imageType"]; ?>" alt="<?php echo $readChildCategories["name"]; ?>" width="32" height="32">
                        </div>
                        <div class="col">
                          <a href="/forum/categories/<?php echo $readChildCategories["id"]."/".$readChildCategories["slug"]; ?>" class="font-weight-bold">
                            <?php echo $readChildCategories["name"]; ?>
                          </a>
                          <div>
                            <span style="font-size: 15px;">
                              <?php echo $readChildCategories["description"]; ?>
                            </span>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>
              
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
                $threadLimit = 20;
                $itemsCount = $db->prepare("SELECT id from ForumThreads WHERE categoryID = ?");
                $itemsCount->execute(array($readCategory["id"]));
                $itemsCount = $itemsCount->rowCount();
                $pageCount = ceil($itemsCount/$threadLimit);
                if ($page > $pageCount) {
                  $page = 1;
                }
                $visibleItemsCount = $page * $threadLimit - $threadLimit;
                $visiblePageCount = 5;
                $threads = $db->prepare("SELECT FT.*, A.realname FROM ForumThreads FT INNER JOIN Accounts A ON A.id = FT.accountID WHERE FT.categoryID = ? ORDER BY FT.id DESC LIMIT $visibleItemsCount, $threadLimit");
                $threads->execute(array($readCategory["id"]));
              ?>
              <?php if ($threads->rowCount() > 0): ?>
                <div class="card">
                  <div class="card-header">
                    <?php e__('Threads') ?>
                  </div>
                  <div class="card-body">
                    <?php foreach ($threads as $readThreads): ?>
                      <div class="row border-bottom align-items-center mb-2 pb-3">
                        <div class="col-auto pr-0">
                          <?php echo minecraftHead($readSettings["avatarAPI"], $readThreads["realname"], 32); ?>
                        </div>
                        <div class="col">
                          <a href="/forum/threads/<?php echo $readThreads["id"]."/".$readThreads["slug"]; ?>" class="font-weight-bold">
                            <?php echo $readThreads["title"]; ?>
                          </a>
                          <div class="small">
                            <a href="/player/<?php echo $readThreads["realname"]; ?>" class="text-muted"><?php echo $readThreads["realname"]; ?></a>
                            <span>•</span>
                            <span><?php echo convertTime($readThreads["creationDate"], 2, true); ?></span>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
    
                <?php $requestURL = "/forum/categories/".$readCategory["id"]."/".$readCategory["slug"]; ?>
                <div class="col-md-12 d-flex justify-content-center">
                  <nav class="pages" aria-label="Pages">
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
              <?php else: ?>
                <?php echo alertError(t__('No threads were found!')); ?>
              <?php endif; ?>
            <?php else: ?>
              <?php echo alertError(t__('No data were found!')); ?>
            <?php endif; ?>
          </div>
        </div>
      <?php else: ?>
        <?php go("/404"); ?>
      <?php endif; ?>
    <?php elseif (get("target") == "threads"): ?>
      <?php if (get("action") == "get" && get("id")): ?>
        <?php if ($thread->rowCount() > 0): ?>
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/commonmark/autoload.php");
            $converter = new \League\CommonMark\GithubFlavoredMarkdownConverter();
          ?>
          <?php
            $defaultRole = $db->prepare("SELECT * FROM Roles WHERE slug = ?");
            $defaultRole->execute(array('default'));
            $readDefaultRole = $defaultRole->fetch();
            
            $userRoles = getRoles($readThread["accountID"], true, [$readDefaultRole]);
          ?>
          <div class="mb-3 mt-2">
            <h1 class="h3 text-dark font-weight-bold"><?php echo $readThread["title"]; ?></h1>
            <div class="d-flex" style="font-size: 14px;">
              <span class="mr-3">
                <i class="fa fa-user"></i>
                <?php echo styledUsername($readThread["realname"], $userRoles[0]["slug"], true, "text-muted"); ?>
              </span>
              <span class="mr-3">
                <i class="fa fa-clock"></i>
                <?php echo convertTime($readThread["creationDate"], 2, true); ?>
              </span>
              <span class="mr-3">
                <i class="fa fa-eye"></i>
                <?php echo $readThread["views"]; ?>
              </span>
            </div>
          </div>
          <style>
            .forum-avatar img {
              border-radius: 0.25rem !important;
            }
          </style>
          <div class="mb-4">
            <div class="row">
              <div class="col-md-3">
                <div class="card mb-0">
                  <div class="card-body text-center">
                    <a href="/player/<?php echo $readThread["realname"]; ?>" class="d-block">
                      <div class="forum-avatar mb-2">
                        <?php echo minecraftHead($readSettings["avatarAPI"], $readThread["realname"], 64); ?>
                      </div>
                      <?php echo styledUsername($readThread["realname"], $userRoles[0]["slug"], false, "d-block mb-2 font-weight-bold text-dark"); ?>
                    </a>
                    <?php foreach ($userRoles as $role): ?>
                      <span class="role__default <?php echo "role__".$role["slug"] ?>">
                        <?php echo $role["name"]; ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
              <div class="col-md-9 pl-0">
                <div class="card mb-0 h-100">
                  <div class="card-body pb-5">
                    <div class="forum-content h-100">
                      <?php echo $converter->convertToHtml($readThread["content"]); ?>
                    </div>
                    <div class="border-top small pt-2">
                      <div class="row">
                        <div class="col">
                          <?php echo convertTime($readThread["creationDate"], 2, true); ?>
                        </div>
                        <div class="col-auto">
                          <?php if (isset($_SESSION["login"]) && ($readThread["accountID"] == $readAccount["id"] || checkPerm($readAccount, 'MANAGE_FORUM'))): ?>
                            <a href="/forum/threads/edit/<?php echo $readThread["id"]; ?>" class="mr-2">
                              <i class="far fa-edit"></i> <?php e__('Edit') ?>
                            </a>
                            <a href="/forum/threads/delete/<?php echo $readThread["id"]; ?>" class="text-danger clickdelete">
                              <i class="far fa-trash-alt"></i> <?php e__('Delete') ?>
                            </a>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php foreach ($replies as $readReplies): ?>
            <?php $userRoles = getRoles($readReplies["accountID"], true, [$readDefaultRole]); ?>
            <div class="mb-2">
              <div class="row">
                <div class="col-md-3">
                  <div class="card mb-0">
                    <div class="card-body text-center">
                      <a href="/player/<?php echo $readReplies["realname"]; ?>" class="d-block">
                        <div class="forum-avatar mb-2">
                          <?php echo minecraftHead($readSettings["avatarAPI"], $readReplies["realname"], 64); ?>
                        </div>
                        <?php echo styledUsername($readReplies["realname"], $userRoles[0]["slug"], false, "d-block mb-2 font-weight-bold text-dark"); ?>
                      </a>
                      <?php foreach ($userRoles as $role): ?>
                        <span class="role__default <?php echo "role__".$role["slug"] ?>">
                          <?php echo $role["name"]; ?>
                        </span>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
                <div class="col-md-9 pl-0">
                  <div class="card mb-0 h-100">
                    <div class="card-body pb-5">
                      <div class="forum-content h-100">
                        <?php echo $converter->convertToHtml($readReplies["message"]); ?>
                      </div>
                      <div class="border-top small pt-2">
                        <div class="row">
                          <div class="col">
                            <?php echo convertTime($readReplies["creationDate"], 2, true); ?>
                          </div>
                          <div class="col-auto">
                            <?php if (isset($_SESSION["login"]) && ($readReplies["accountID"] == $readAccount["id"] || checkPerm($readAccount, 'MANAGE_FORUM'))): ?>
                              <a href="/forum/replies/edit/<?php echo $readReplies["id"]; ?>" class="mr-2">
                                <i class="far fa-edit"></i> <?php e__('Edit') ?>
                              </a>
                              <a href="/forum/replies/delete/<?php echo $readReplies["id"]; ?>" class="text-danger clickdelete">
                                <i class="far fa-trash-alt"></i> <?php e__('Delete') ?>
                              </a>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
          <?php if (isset($_SESSION["login"])): ?>
            <div class="row mt-4">
              <div class="col-md-3">
                <div class="card mb-0 h-100">
                  <div class="card-body text-center">
                    <div class="flex">
                      <a href="/profile" class="d-block">
                        <div class="forum-avatar mb-2">
                          <?php echo minecraftHead($readSettings["avatarAPI"], $readAccount["realname"], 64); ?>
                        </div>
                        <span class="d-block mb-2 font-weight-bold text-dark"><?php echo $readAccount["realname"]; ?></span>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <section id="replyArea"></section>
              <div class="col-md-9 pl-0">
                <div class="card mb-0">
                  <div class="card-body">
                    <?php
                      require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
                      $csrf = new CSRF('csrf-sessions', 'csrf-token');
                      if (isset($_POST["sendReply"])) {
                        if (!$csrf->validate('sendReply')) {
                          echo alertError(t__('Something went wrong! Please try again later.'));
                        }
                        else if (post("message") == null) {
                          echo alertError(t__('Please fill all the fields!'));
                        }
                        else {
                          echo '<script>window.location = "#replyArea"</script>';
                          $insertReply = $db->prepare("INSERT INTO ForumReplies (message, accountID, threadID, creationDate, updatedDate) VALUES (?, ?, ?, ?, ?)");
                          $insertReply->execute(array(post("message"), $readAccount["id"], $readThread["id"], date("Y-m-d H:i:s"), date("Y-m-d H:i:s")));
                          echo alertSuccess(t__('Your reply has been successfully sent.'));
                          echo goDelay("/forum/threads/".$readThread["id"]."/".$readThread["slug"], 1);
                        }
                      }
                    ?>
                    <div class="forum-content">
                      <form action="" method="post">
                      <textarea
                          id="ckeditor"
                          name="message"
                          placeholder="<?php e__('Write your reply here...') ?>"
                          class="form-control"
                          data-upload="/apps/main/public/ajax/forum-upload.php"
                          data-csrf="<?php echo $csrf->string('sendReply'); ?>"></textarea>
                        <?php echo $csrf->input('sendReply'); ?>
                        <div class="text-right mt-3">
                          <button type="submit" name="sendReply" class="btn btn-primary">
                            <?php e__('Send') ?>
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertWarning(t__('You need to be signed in to reply.')); ?>
          <?php endif; ?>
        <?php else: ?>
          <?php echo alertError(t__('Thread not found!')); ?>
        <?php endif; ?>
      <?php elseif (get("action") == "insert" && get("category")): ?>
        <?php if ($category->rowCount() > 0): ?>
          <div class="card mb-0">
            <div class="card-header"><?php e__('Post Thread') ?></div>
            <div class="card-body">
              <?php
                require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
                $csrf = new CSRF('csrf-sessions', 'csrf-token');
                if (isset($_POST["sendReply"])) {
                  if (!$csrf->validate('sendReply')) {
                    echo alertError(t__('Something went wrong! Please try again later.'));
                  }
                  else if (post("title") == null || post("message") == null) {
                    echo alertError(t__('Please fill all the fields!'));
                  }
                  else {
                    $slug = $slugify->slugify(post("title"));
                    $insertThread = $db->prepare("INSERT INTO ForumThreads (title, slug, content, categoryID, accountID, views, creationDate, updatedDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertThread->execute(array(post("title"), $slug, post("message"), $readCategory["id"], $readAccount["id"], 0, date("Y-m-d H:i:s"), date("Y-m-d H:i:s")));
                    $threadID = $db->lastInsertId();
                    
                    go("/forum/threads/".$threadID."/".$slug);
                  }
                }
              ?>
              <div class="forum-content">
                <form action="" method="post">
                  <div class="form-group">
                    <label for="inputTitle"><?php e__('Title') ?></label>
                    <input type="text" class="form-control" id="inputTitle" name="title">
                  </div>
                  <div class="form-group">
                    <textarea
                        id="ckeditor"
                        name="message"
                        placeholder=""
                        class="form-control"
                        style="min-height: 500px;"
                        data-upload="/apps/main/public/ajax/forum-upload.php"
                        data-csrf="<?php echo $csrf->string('sendReply'); ?>"></textarea>
                  </div>
                  <?php echo $csrf->input('sendReply'); ?>
                  <div class="text-right">
                    <button type="submit" name="sendReply" class="btn btn-banner-bg">
                      <?php e__('Post Thread') ?>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php else: ?>
          <?php echo alertError(t__('Category not found!')); ?>
        <?php endif; ?>
      <?php elseif (get("action") == "update" && get("id")): ?>
        <?php if ($thread->rowCount() > 0): ?>
          <div class="card mb-0">
            <div class="card-header"><?php e__('Edit the thread') ?></div>
            <div class="card-body">
              <?php
                require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
                $csrf = new CSRF('csrf-sessions', 'csrf-token');
                if (isset($_POST["sendReply"])) {
                  if (!$csrf->validate('sendReply')) {
                    echo alertError(t__('Something went wrong! Please try again later.'));
                  }
                  else if (post("title") == null || post("message") == null) {
                    echo alertError(t__('Please fill all the fields!'));
                  }
                  else {
                    $slug = $slugify->slugify(post("title"));
                    $updateThread = $db->prepare("UPDATE ForumThreads SET title = ?, slug = ?, content = ?, updatedDate = ? WHERE id = ?");
                    $updateThread->execute(array(post("title"), $slug, post("message"), date("Y-m-d H:i:s"), $readThread["id"]));
                
                    go("/forum/threads/".$readThread["id"]."/".$slug);
                  }
                }
              ?>
              <div class="forum-content">
                <form action="" method="post">
                  <div class="form-group">
                    <label for="inputTitle"><?php e__('Title') ?></label>
                    <input type="text" class="form-control" id="inputTitle" name="title" value="<?php echo $readThread["title"]; ?>">
                  </div>
                  <div class="form-group">
                    <textarea
                        id="ckeditor"
                        name="message"
                        placeholder=""
                        class="form-control"
                        style="min-height: 500px;"
                        data-upload="/apps/main/public/ajax/forum-upload.php"
                        data-csrf="<?php echo $csrf->string('sendReply'); ?>"><?php echo $readThread["content"]; ?></textarea>
                  </div>
                  <?php echo $csrf->input('sendReply'); ?>
                  <div class="text-right">
                    <button type="submit" name="sendReply" class="btn btn-banner-bg">
                      <?php e__('Save Changes') ?>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php else: ?>
          <?php echo alertError("Konu bulunanadı!"); ?>
        <?php endif; ?>
      <?php endif; ?>
    <?php elseif (get("target") == "replies"): ?>
      <?php if (get("action") == "update" && get("id")): ?>
        <?php if ($reply->rowCount() > 0): ?>
          <div class="card mb-0">
            <div class="card-body">
              <?php
                require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
                $csrf = new CSRF('csrf-sessions', 'csrf-token');
                if (isset($_POST["sendReply"])) {
                  if (!$csrf->validate('sendReply')) {
                    echo alertError(t__('Something went wrong! Please try again later.'));
                  }
                  else if (post("message") == null) {
                    echo alertError(t__('Please fill all the fields!'));
                  }
                  else {
                    $updateReply = $db->prepare("UPDATE ForumReplies SET message = ?, updatedDate = ? WHERE id = ?");
                    $updateReply->execute(array(post("message"), date("Y-m-d H:i:s"), $readReply["id"]));
                    echo alertSuccess(t__('Your reply has been updated successfully!'));
                  }
                }
              ?>
              <div class="forum-content">
                <form action="" method="post">
                <textarea
                    id="ckeditor"
                    name="message"
                    placeholder="<?php e__('Write your reply here...') ?>"
                    class="form-control"
                    data-upload="/apps/main/public/ajax/forum-upload.php"
                    data-csrf="<?php echo $csrf->string('sendReply'); ?>"><?php echo $readReply["message"]; ?></textarea>
                  <?php echo $csrf->input('sendReply'); ?>
                  <div class="text-right mt-3">
                    <button type="submit" name="sendReply" class="btn btn-primary">
                      <?php e__('Save Changes') ?>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php else: ?>
          <?php echo alertError(t__('Reply not found!')); ?>
        <?php endif; ?>
      <?php endif; ?>
    <?php else: ?>
      <?php go("/404"); ?>
    <?php endif; ?>
  </div>
</section>
