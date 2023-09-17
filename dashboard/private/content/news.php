<?php
  if (!checkPerm($readAdmin, 'MANAGE_BLOG')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
?>
<?php if (get("target") == 'news'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('News') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('News') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php $news = $db->query("SELECT N.id FROM News N INNER JOIN Accounts A ON N.accountID = A.id INNER JOIN NewsCategories NC ON N.categoryID = NC.id ORDER BY N.id DESC"); ?>
          <?php if ($news->rowCount() > 0): ?>
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

              $visiblePageCount = 5;
              $limit = 50;

              $itemsCount = $news->rowCount();
              $pageCount = ceil($itemsCount/$limit);
              if ($page > $pageCount) {
                $page = 1;
              }
              $visibleItemsCount = $page * $limit - $limit;
              $news = $db->query("SELECT N.*, A.realname, NC.name as categoryName FROM News N INNER JOIN Accounts A ON N.accountID = A.id INNER JOIN NewsCategories NC ON N.categoryID = NC.id ORDER BY N.id DESC LIMIT $visibleItemsCount, $limit");

              if (isset($_POST["query"])) {
                if (post("query") != null) {
                  $news = $db->prepare("SELECT N.*, A.realname, NC.name as categoryName FROM News N INNER JOIN Accounts A ON N.accountID = A.id INNER JOIN NewsCategories NC ON N.categoryID = NC.id WHERE N.title LIKE :search OR A.realname LIKE :search OR NC.name LIKE :search ORDER BY N.id DESC");
                  $news->execute(array(
                    "search" => '%'.post("query").'%'
                  ));
                }
              }
            ?>
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <form action="" method="post" class="d-flex align-items-center w-100">
                    <div class="col">
                      <div class="row align-items-center">
                        <div class="col-auto pr-0">
                          <span class="fe fe-search text-muted"></span>
                        </div>
                        <div class="col">
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="<?php e__('Search (Title, Author or Category)') ?>" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success"><?php e__('Search') ?></button>
                      <a class="btn btn-sm btn-white" href="/dashboard/blog/create"><?php e__('Add News') ?></a>
                    </div>
                  </form>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 40px;">#ID</th>
                        <th><?php e__('Title') ?></th>
                        <th><?php e__('Author') ?></th>
                        <th><?php e__('Category') ?></th>
                        <th><?php e__('Views') ?></th>
                        <th><?php e__('Comments') ?></th>
                        <th><?php e__('Date') ?></th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($news as $readNews): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            <a href="/dashboard/blog/edit/<?php echo $readNews["id"]; ?>">
                              #<?php echo $readNews["id"]; ?>
                            </a>
                          </td>
                          <td>
                            <a href="/dashboard/blog/edit/<?php echo $readNews["id"]; ?>">
                              <?php echo $readNews["title"]; ?>
                            </a>
                          </td>
                          <td>
                            <a href="/dashboard/users/view/<?php echo $readNews["accountID"]; ?>">
                              <?php echo $readNews["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php echo $readNews["categoryName"]; ?>
                          </td>
                          <td>
                            <?php echo $readNews["views"]; ?>
                          </td>
                          <td>
                            <?php
                              $newsComments = $db->prepare("SELECT NC.id FROM NewsComments NC INNER JOIN Accounts A ON NC.accountID = A.id WHERE NC.newsID = ? AND NC.status = ?");
                              $newsComments->execute(array($readNews["id"], 1));
                              echo $newsComments->rowCount();
                            ?>
                          </td>
                          <td>
                            <?php echo convertTime($readNews["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/blog/edit/<?php echo $readNews["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/posts/<?php echo $readNews["id"]; ?>/<?php echo $readNews["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/blog/delete/<?php echo $readNews["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php if (post("query") == false): ?>
              <nav class="pt-3 pb-5" aria-label="Page Navigation">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo ($page == 1) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/blog/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/dashboard/blog/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/blog/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
                  </li>
                </ul>
              </nav>
            <?php endif; ?>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'insert'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Add News') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/blog"><?php e__('News') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Add News') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["insertNews"])) {
              if (!$csrf->validate('insertNews')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("title") == null || post("categoryID") == null || post("content") == null || post("commentsStatus") == null) {
                echo alertError(t__('Please fill all the fields.'));
              }
              else if ($_FILES["image"]["size"] == null) {
                echo alertError(t__('Select Image'));
              }
              else {
                require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                $upload = new \Verot\Upload\Upload($_FILES["image"]);
                $imageID = md5(uniqid(rand(0, 9999)));
                if ($upload->uploaded) {
                  $upload->allowed = array("image/*");
                  $upload->file_new_name_body = $imageID;
                  $upload->image_resize = true;
                  $upload->image_ratio_crop = true;
                  $upload->image_x = 640;
                  $upload->image_y = 360;
                  $upload->process(__ROOT__."/apps/main/public/assets/img/news/");
                  if ($upload->processed) {
                    $insertNews = $db->prepare("INSERT INTO News (accountID, title, slug, categoryID, content, imageID, imageType, views, commentsStatus, updateDate, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertNews->execute(array($readAdmin["id"], post("title"), $slugify->slugify(post("title")), post("categoryID"), filteredContent($_POST["content"]), $imageID, $upload->file_dst_name_ext, 0, post("commentsStatus"), date("Y-m-d H:i:s"), date("Y-m-d H:i:s")));
                    $newsLastInsertID = $db->lastInsertId();
                    if (post("tags") != null) {
                      $tags = explode(',', trim(post("tags"), ','));
                      $insertNewsTags = $db->prepare("INSERT INTO NewsTags (newsID, name, slug) VALUES (?, ?, ?)");
                      foreach ($tags as $tag) {
                        $insertNewsTags->execute(array($newsLastInsertID, $tag, $slugify->slugify($tag)));
                      }
                    }
                    echo alertSuccess(t__('News has been published successfully!'));
                  }
                  else {
                    echo alertError(t__('An error occupied while uploading an image: %error%', ['%error%' => $upload->error]));
                  }
                }
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group row">
                  <label for="inputTitle" class="col-sm-2 col-form-label"><?php e__('Title') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="<?php e__('Enter the title of news') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputCategoryID" class="col-sm-2 col-form-label"><?php e__('Category') ?>:</label>
                  <div class="col-sm-10">
                    <?php $newsCategories = $db->query("SELECT * FROM NewsCategories"); ?>
                    <select id="selectCategoryID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="categoryID" <?php echo ($newsCategories->rowCount() == 0) ? "disabled" : null; ?>>
                      <?php if ($newsCategories->rowCount() > 0): ?>
                        <?php foreach ($newsCategories as $readNewsCategories): ?>
                          <option value="<?php echo $readNewsCategories["id"]; ?>">
                            <?php echo $readNewsCategories["name"]; ?>
                          </option>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <option><?php e__('No category found') ?>!</option>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaContent" class="col-sm-2 col-form-label"><?php e__('Content') ?>:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="<?php e__('Enter the content of news') ?>."></textarea>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputTags" class="col-sm-2 col-form-label"><?php e__('Tags') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTags" class="form-control" data-toggle="tagsinput" name="tags" placeholder="<?php e__('Enter the tags') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectCommentsStatus" class="col-sm-2 col-form-label"><?php e__('Comments') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectCommentsStatus" class="form-control" name="commentsStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0"><?php e__('Closed') ?></option>
                      <option value="1" selected="selected"><?php e__('Opened') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="fileImage" class="col-sm-2 col-form-label"><?php e__('Image') ?>:</label>
                  <div class="col-sm-10">
                    <div data-toggle="dropimage" class="dropimage">
                      <div class="di-thumbnail">
                        <img src="" alt="<?php e__('Preview') ?>">
                      </div>
                      <div class="di-select">
                        <label for="fileImage"><?php e__('Select Image') ?></label>
                        <input type="file" id="fileImage" name="image" accept="image/*">
                      </div>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('insertNews'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertNews"><?php e__('Publish') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'update' && get("id")): ?>
    <?php
      $news = $db->prepare("SELECT * FROM News WHERE id = ?");
      $news->execute(array(get("id")));
      $readNews = $news->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Edit News') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/blog"><?php e__('News') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/blog"><?php e__('Edit News') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($news->rowCount() > 0) ? $readNews["title"] : t__('Not found!'); ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php if ($news->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateNews"])) {
                if (!$csrf->validate('updateNews')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if (post("title") == null || post("categoryID") == null || post("content") == null || post("commentsStatus") == null) {
                  echo alertError(t__('Please fill all the fields'));
                }
                else {
                  if ($_FILES["image"]["size"] != null) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                    $upload = new \Verot\Upload\Upload($_FILES["image"]);
                    $imageID = $readNews["imageID"];
                    if ($upload->uploaded) {
                      $upload->allowed = array("image/*");
                      $upload->file_overwrite = true;
                      $upload->file_new_name_body = $imageID;
                      $upload->image_resize = true;
                      $upload->image_ratio_crop = true;
                      $upload->image_x = 640;
                      $upload->image_y = 360;
                      $upload->process(__ROOT__."/apps/main/public/assets/img/news/");
                      if ($upload->processed) {
                        $updateNews = $db->prepare("UPDATE News SET imageType = ? WHERE id = ?");
                        $updateNews->execute(array($upload->file_dst_name_ext, $readNews["id"]));
                      }
                      else {
                        echo alertError(t__('An error occupied while uploading an image: %error%', ['%error%' => $upload->error]));
                      }
                    }
                  }
                  $updateNews = $db->prepare("UPDATE News SET title = ?, slug = ?, categoryID = ?, content = ?, commentsStatus = ? WHERE id = ?");
                  $updateNews->execute(array(post("title"), $slugify->slugify(post("title")), post("categoryID"), filteredContent($_POST["content"]), post("commentsStatus"), $readNews["id"]));
                  if (post("tags") != null) {
                    $tags = explode(',', trim(post("tags"), ','));
                    $deleteNewsTags = $db->prepare("DELETE FROM NewsTags WHERE newsID = ?");
                    $deleteNewsTags->execute(array($readNews["id"]));
                    $insertNewsTags = $db->prepare("INSERT INTO NewsTags (newsID, name, slug) VALUES (?, ?, ?)");
                    foreach ($tags as $tag) {
                      $insertNewsTags->execute(array($readNews["id"], $tag, $slugify->slugify($tag)));
                    }
                  }
                  echo alertSuccess(t__('Changes has been saved successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label for="inputTitle" class="col-sm-2 col-form-label"><?php e__('Title') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="<?php e__('Enter the title of news') ?>." value="<?php echo $readNews["title"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputCategoryID" class="col-sm-2 col-form-label"><?php e__('Category') ?>:</label>
                    <div class="col-sm-10">
                      <?php $newsCategories = $db->query("SELECT * FROM NewsCategories"); ?>
                      <select id="selectCategoryID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="categoryID" <?php echo ($newsCategories->rowCount() == 0) ? "disabled" : null; ?>>
                        <?php if ($newsCategories->rowCount() > 0): ?>
                          <?php foreach ($newsCategories as $readNewsCategories): ?>
                            <option value="<?php echo $readNewsCategories["id"]; ?>" <?php echo (($readNews["categoryID"] == $readNewsCategories["id"]) ? 'selected="selected"' : null); ?>>
                              <?php echo $readNewsCategories["name"]; ?>
                            </option>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <option><?php e__('No category found') ?>!</option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="textareaContent" class="col-sm-2 col-form-label"><?php e__('Content') ?>:</label>
                    <div class="col-sm-10">
                      <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="<?php e__('Enter the content of news') ?>."><?php echo $readNews["content"]; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputTags" class="col-sm-2 col-form-label"><?php e__('Tags') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTags" class="form-control" data-toggle="tagsinput" name="tags" placeholder="<?php e__('Enter the tags') ?>." value="
                        <?php
                          $newsTags = $db->prepare("SELECT NT.* FROM NewsTags NT INNER JOIN News N ON NT.newsID = N.id WHERE NT.newsID = ?");
                          $newsTags->execute(array($readNews["id"]));
                          if ($newsTags->rowCount() > 0) {
                            foreach ($newsTags as $readNewsTags) {
                              echo $readNewsTags["name"].',';
                            }
                          }
                         ?>
                       ">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectCommentsStatus" class="col-sm-2 col-form-label"><?php e__('Comments') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectCommentsStatus" class="form-control" name="commentsStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readNews["commentsStatus"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Closed') ?></option>
                        <option value="1" <?php echo ($readNews["commentsStatus"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Opened') ?></option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="fileImage" class="col-sm-2 col-form-label"><?php e__('Image') ?>:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage active">
                        <div class="di-thumbnail">
                          <img src="/apps/main/public/assets/img/news/<?php echo $readNews["imageID"].'.'.$readNews["imageType"]; ?>" alt="<?php e__('Preview') ?>">
                        </div>
                        <div class="di-select">
                          <label for="fileImage"><?php e__('Select Image') ?></label>
                          <input type="file" id="fileImage" name="image" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateNews'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/blog/delete/<?php echo $readNews["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/posts/<?php echo $readNews["id"]; ?>/<?php echo $readNews["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateNews"><?php e__('Save Changes') ?></button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteNews = $db->prepare("DELETE FROM News WHERE id = ?");
      $deleteNews->execute(array(get("id")));
      go("/dashboard/blog");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'category'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Categories') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/blog"><?php e__('News') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Categories') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php $newsCategories = $db->query("SELECT * FROM NewsCategories ORDER BY id DESC"); ?>
          <?php if ($newsCategories->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["newsCategoryID", "newsCategoryName"]'>
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <div class="row align-items-center">
                      <div class="col-auto pr-0">
                        <span class="fe fe-search text-muted"></span>
                      </div>
                      <div class="col">
                        <input type="search" class="form-control form-control-flush search" name="search" placeholder="<?php e__('Search') ?>">
                      </div>
                    </div>
                  </div>
                  <div class="col-auto">
                    <a class="btn btn-sm btn-white" href="/dashboard/blog/categories/create"><?php e__('Add Category') ?></a>
                  </div>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 40px;">
                          <a href="#" class="text-muted sort" data-sort="newsCategoryID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="newsCategoryName">
                              <?php e__('Category Name') ?>
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($newsCategories as $readNewsCategories): ?>
                        <tr>
                          <td class="newsCategoryID text-center" style="width: 40px;">
                            <a href="/dashboard/blog/categories/edit/<?php echo $readNewsCategories["id"]; ?>">
                              #<?php echo $readNewsCategories["id"]; ?>
                            </a>
                          </td>
                          <td class="newsCategoryName">
                            <a href="/dashboard/blog/categories/edit/<?php echo $readNewsCategories["id"]; ?>">
                              <?php echo $readNewsCategories["name"]; ?>
                            </a>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/blog/categories/edit/<?php echo $readNewsCategories["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/categories/<?php echo $readNewsCategories["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/blog/categories/delete/<?php echo $readNewsCategories["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'insert'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Add Category') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/blog"><?php e__('News') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/blog/categories"><?php e__('Category') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Add Category') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["insertNewsCategories"])) {
              if (!$csrf->validate('insertNewsCategories')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("name") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else {
                $insertNewsCategories = $db->prepare("INSERT INTO NewsCategories (name, slug) VALUES (?, ?)");
                $insertNewsCategories->execute(array(post("name"), $slugify->slugify(post("name"))));
                echo alertSuccess(t__('Category has been added successfully!'));
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputName" class="col-sm-2 col-form-label"><?php e__('Category Name') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputName" class="form-control" name="name" placeholder="<?php e__('Enter the category name') ?>.">
                  </div>
                </div>
                <?php echo $csrf->input('insertNewsCategories'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertNewsCategories"><?php e__('Add') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'update' && get("id")): ?>
    <?php
      $newsCategory = $db->prepare("SELECT * FROM NewsCategories WHERE id = ?");
      $newsCategory->execute(array(get("id")));
      $readNewsCategory = $newsCategory->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Edit Category') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/blog"><?php e__('News') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/blog/categories"><?php e__('Category') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/blog/categories"><?php e__('Edit Category') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($newsCategory->rowCount() > 0) ? $readNewsCategory["name"] : t__('Not found!'); ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php if ($newsCategory->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateNewsCategories"])) {
                if (!$csrf->validate('updateNewsCategories')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if (post("name") == null) {
                  echo alertError(t__('Please fill all the fields!'));
                }
                else {
                  $updateNewsCategories = $db->prepare("UPDATE NewsCategories SET name = ?, slug = ? WHERE id = ?");
                  $updateNewsCategories->execute(array(post("name"), $slugify->slugify(post("name")), get("id")));
                  echo alertSuccess(t__('Changes has been saved successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label"><?php e__('Category Name') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputName" class="form-control" name="name" placeholder="<?php e__('Enter the category name') ?>." value="<?php echo $readNewsCategory["name"]; ?>">
                    </div>
                  </div>
                  <?php echo $csrf->input('updateNewsCategories'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/blog/categories/delete/<?php echo $readNewsCategory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/categories/<?php echo $readNewsCategory["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateNewsCategories"><?php e__('Save Changes') ?></button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteNewsCategory = $db->prepare("DELETE FROM NewsCategories WHERE id = ?");
      $deleteNewsCategory->execute(array(get("id")));
      go("/dashboard/blog/categories");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'comment'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Comments') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/blog"><?php e__('News') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Comments') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php $newsComments = $db->query("SELECT NC.id FROM NewsComments NC INNER JOIN Accounts A ON NC.accountID = A.id INNER JOIN News N ON NC.newsID = N.id ORDER BY NC.id DESC"); ?>
          <?php if ($newsComments->rowCount() > 0): ?>
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

              $visiblePageCount = 5;
              $limit = 50;

              $itemsCount = $newsComments->rowCount();
              $pageCount = ceil($itemsCount/$limit);
              if ($page > $pageCount) {
                $page = 1;
              }
              $visibleItemsCount = $page * $limit - $limit;
              $newsComments = $db->query("SELECT NC.*, A.realname, N.slug as newsSlug FROM NewsComments NC INNER JOIN Accounts A ON NC.accountID = A.id INNER JOIN News N ON NC.newsID = N.id ORDER BY NC.id DESC LIMIT $visibleItemsCount, $limit");

              if (isset($_POST["query"])) {
                if (post("query") != null) {
                  $newsComments = $db->prepare("SELECT NC.*, A.realname, N.slug as newsSlug FROM NewsComments NC INNER JOIN Accounts A ON NC.accountID = A.id INNER JOIN News N ON NC.newsID = N.id WHERE NC.message LIKE :search OR A.realname LIKE :search ORDER BY NC.id DESC");
                  $newsComments->execute(array(
                    "search" => '%'.post("query").'%'
                  ));
                }
              }
            ?>
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <form action="" method="post" class="d-flex align-items-center w-100">
                    <div class="col">
                      <div class="row align-items-center">
                        <div class="col-auto pr-0">
                          <span class="fe fe-search text-muted"></span>
                        </div>
                        <div class="col">
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="<?php e__('Search (Message or Author)') ?>" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success"><?php e__('Search') ?></button>
                    </div>
                  </form>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table id="tableComments" class="table table-sm table-nowrap card-table">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 40px;">#ID</th>
                        <th><?php e__('Message') ?></th>
                        <th><?php e__('Author') ?></th>
                        <th><?php e__('Date') ?></th>
                        <th class="text-center"><?php e__('Status') ?></th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($newsComments as $readNewsComments): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            <a href="/dashboard/blog/comments/edit/<?php echo $readNewsComments["id"]; ?>">
                              #<?php echo $readNewsComments["id"]; ?>
                            </a>
                          </td>
                          <td>
                            <a href="/dashboard/blog/comments/edit/<?php echo $readNewsComments["id"]; ?>">
                              <?php echo substr($readNewsComments["message"], 0, 30); ?>
                            </a>
                          </td>
                          <td>
                            <a href="/dashboard/users/view/<?php echo $readNewsComments["accountID"]; ?>">
                              <?php echo $readNewsComments["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php echo convertTime($readNewsComments["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-center">
                            <?php if ($readNewsComments["status"] == 0): ?>
                              <span class="badge badge-danger"><?php e__('Unconfirmed') ?></span>
                            <?php else: ?>
                              <span class="badge badge-success"><?php e__('Confirmed') ?></span>
                            <?php endif; ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/blog/comments/edit/<?php echo $readNewsComments["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/posts/<?php echo $readNewsComments["newsID"]; ?>/<?php echo $readNewsComments["newsSlug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/blog/comments/delete/<?php echo $readNewsComments["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php if (post("query") == false): ?>
              <nav class="pt-3 pb-5" aria-label="Page Navigation">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo ($page == 1) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/blog/comments/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/dashboard/blog/comments/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/blog/comments/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
                  </li>
                </ul>
              </nav>
            <?php endif; ?>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'get' && get("id")): ?>
    <?php
      $newsComment = $db->prepare("SELECT NC.*, A.realname, N.title as newsTitle, N.slug as newsSlug FROM NewsComments NC INNER JOIN Accounts A ON NC.accountID = A.id INNER JOIN News N ON NC.newsID = N.id WHERE NC.id = ?");
      $newsComment->execute(array(get("id")));
      $readNewsComment = $newsComment->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('View Comment') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/blog"><?php e__('News') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/blog/comments"><?php e__('Comments') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($newsComment->rowCount() > 0) ? substr($readNewsComment["message"], 0, 30): "Bulunamadı!"; ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php if ($newsComment->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateNewsComments"])) {
                if (!$csrf->validate('updateNewsComments')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else {
                  $changeStatus = ($readNewsComment["status"] == 0) ? '1': '0';
                  $updateNewsComments = $db->prepare("UPDATE NewsComments SET status = ? WHERE id = ?");
                  $updateNewsComments->execute(array($changeStatus, get("id")));
                  if ($changeStatus == '0') {
                    echo alertSuccess(t__('The confirmation of the comment has been removed successfully!'));
                  }
                  else {
                    echo alertSuccess(t__('The comment has been confirmed successfully!'));
                  }
                }
              }
            ?>
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <h4 class="card-header-title">
                      <a href="/posts/<?php echo $readNewsComment["newsID"]; ?>/<?php echo $readNewsComment["newsSlug"]; ?>" rel="external"><?php echo substr($readNewsComment["newsTitle"], 0, 30); ?></a>
                    </h4>
                  </div>
                  <div class="col-auto">
                    <span class="badge badge-pill badge-primary" data-toggle="tooltip" data-placement="top" data-original-title="<?php e__('Date') ?>"><?php echo convertTime($readNewsComment["creationDate"]); ?></span>
                    <?php if ($readNewsComment["status"] == 0): ?>
                      <span class="badge badge-pill badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="<?php e__('Status') ?>"><?php e__('Unconfirmed') ?></span>
                    <?php else: ?>
                      <span class="badge badge-pill badge-success" data-toggle="tooltip" data-placement="top" data-original-title="<?php e__('Status') ?>"><?php e__('Confirmed') ?></span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <form action="" method="post">
                  <div class="message">
                    <div class="message-img">
                      <a href="/dashboard/users/view/<?php echo $readNewsComment["accountID"]; ?>">
                        <img class="float-left rounded-circle" src="https://minotar.net/avatar/<?php echo $readNewsComment["realname"]; ?>/40.png" alt="<?php echo $serverName." Player - ".$readNewsComment["realname"]; ?> Message">
                      </a>
                    </div>
                    <div class="message-content">
                      <div class="message-header">
                        <div class="message-username">
                          <a href="/dashboard/users/view/<?php echo $readNewsComment["accountID"]; ?>">
                            <?php echo $readNewsComment["realname"]; ?>
                          </a>
                        </div>
                        <div class="message-date">
                          <?php echo convertTime($readNewsComment["creationDate"]); ?>
                        </div>
                      </div>
                      <div class="message-body">
                        <p>
                          <?php echo showEmoji(urlContent(hashtag(hashtag($readNewsComment["message"], "@", "/dashboard/users/view"), "#", "/tags"))); ?>
                        </p>
                      </div>
                      <div class="message-footer">
                        <?php echo $csrf->input('updateNewsComments'); ?>
                        <div class="clearfix">
                          <div class="float-right">
                            <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/blog/comments/delete/<?php echo $readNewsComment["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                              <i class="fe fe-trash-2"></i>
                            </a>
                            <a class="btn btn-rounded-circle btn-primary" href="/posts/<?php echo $readNewsComment["newsID"]; ?>/<?php echo $readNewsComment["newsSlug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                              <i class="fe fe-eye"></i>
                            </a>
                            <?php if ($readNewsComment["status"] == 0): ?>
                              <button type="submit" class="btn btn-rounded btn-success" name="updateNewsComments"><?php e__('Confirm') ?></button>
                            <?php else: ?>
                              <button type="submit" class="btn btn-rounded btn-warning" name="updateNewsComments"><?php e__('Unconfirm') ?></button>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteNewsComment = $db->prepare("DELETE FROM NewsComments WHERE id = ?");
      $deleteNewsComment->execute(array(get("id")));
      go("/dashboard/blog/comments");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
