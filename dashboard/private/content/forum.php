<?php
  if (!checkPerm($readAdmin, 'MANAGE_FORUM')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
?>
<?php if (get("target") == 'thread'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("Threads")?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard")?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Forum")?></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Threads")?></li>
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
          <?php $threads = $db->query("SELECT FT.id FROM ForumThreads FT INNER JOIN Accounts A ON FT.accountID = A.id INNER JOIN ForumCategories FC ON FT.categoryID = FC.id ORDER BY FT.id DESC"); ?>
          <?php if ($threads->rowCount() > 0): ?>
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

              $itemsCount = $threads->rowCount();
              $pageCount = ceil($itemsCount/$limit);
              if ($page > $pageCount) {
                $page = 1;
              }
              $visibleItemsCount = $page * $limit - $limit;
              $threads = $db->query("SELECT FT.*, A.realname, FC.name as categoryName FROM ForumThreads FT INNER JOIN Accounts A ON FT.accountID = A.id INNER JOIN ForumCategories FC ON FT.categoryID = FC.id ORDER BY FT.id DESC LIMIT $visibleItemsCount, $limit");

              if (isset($_POST["query"])) {
                if (post("query") != null) {
                  $threads = $db->prepare("SELECT FT.*, A.realname, FC.name as categoryName FROM ForumThreads FT INNER JOIN Accounts A ON FT.accountID = A.id INNER JOIN ForumCategories FC ON FT.categoryID = FC.id WHERE FT.title LIKE :search OR A.realname LIKE :search OR FC.name LIKE :search ORDER BY FT.id DESC");
                  $threads->execute(array(
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
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="<?php e__("Search (Title, Author or Category)")?>" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success"><?php e__("Search")?></button>
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
                        <th class="text-center" style="width: 40px;">#<?php e__("ID")?></th>
                        <th><?php e__("Title")?></th>
                        <th><?php e__("Author")?></th>
                        <th><?php e__("Category")?></th>
                        <th><?php e__("View")?></th>
                        <th><?php e__("Date")?></th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($threads as $readThreads): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            <a href="/forum/threads/edit/<?php echo $readThreads["id"]; ?>">
                              #<?php echo $readThreads["id"]; ?>
                            </a>
                          </td>
                          <td>
                            <a href="/forum/threads/edit/<?php echo $readThreads["id"]; ?>">
                              <?php echo $readThreads["title"]; ?>
                            </a>
                          </td>
                          <td>
                            <a href="/dashboard/users/view/<?php echo $readThreads["accountID"]; ?>">
                              <?php echo $readThreads["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php echo $readThreads["categoryName"]; ?>
                          </td>
                          <td>
                            <?php echo $readThreads["views"]; ?>
                          </td>
                          <td>
                            <?php echo convertTime($readThreads["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/forum/threads/edit/<?php echo $readThreads["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Edit")?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/forum/threads/<?php echo $readThreads["id"]; ?>/<?php echo $readThreads["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__("View")?>">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/forum/threads/delete/<?php echo $readThreads["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete")?>">
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
                    <a class="page-link" href="/dashboard/forum/threads/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/dashboard/forum/threads/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/forum/threads/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
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
                  <h2 class="header-title"><?php e__("Categories")?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard")?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/forum/threads"><?php e__("Forum")?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Category")?></li>
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
          <?php $forumCategories = $db->query("SELECT * FROM ForumCategories ORDER BY id DESC"); ?>
          <?php if ($forumCategories->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["forumCategoryID", "forumCategoryName"]'>
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <div class="row align-items-center">
                      <div class="col-auto pr-0">
                        <span class="fe fe-search text-muted"></span>
                      </div>
                      <div class="col">
                        <input type="search" class="form-control form-control-flush search" name="search" placeholder="<?php e__("Search")?>">
                      </div>
                    </div>
                  </div>
                  <div class="col-auto">
                    <a class="btn btn-sm btn-white" href="/dashboard/forum/categories/ekle"><?php e__("Add Category")?></a>
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
                          <a href="#" class="text-muted sort" data-sort="forumCategoryID">
                            #<?php e__("ID")?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="forumCategoryName">
                              <?php e__("Category Name")?>
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($forumCategories as $readForumCategories): ?>
                        <tr>
                          <td class="newsCategoryID text-center" style="width: 40px;">
                            <a href="/dashboard/forum/categories/edit/<?php echo $readForumCategories["id"]; ?>">
                              #<?php echo $readForumCategories["id"]; ?>
                            </a>
                          </td>
                          <td class="newsCategoryName">
                            <a href="/dashboard/forum/categories/edit/<?php echo $readForumCategories["id"]; ?>">
                              <?php echo $readForumCategories["name"]; ?>
                            </a>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/forum/categories/edit/<?php echo $readForumCategories["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Edit")?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/forum/categories/<?php echo $readForumCategories["id"]."/".$readForumCategories["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__("View")?>">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/forum/categories/delete/<?php echo $readForumCategories["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete")?>">
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
                  <h2 class="header-title"><?php e__("Add Category")?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard")?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/forum/threads"><?php e__("Forum")?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/forum/categories"><?php e__("Category")?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Add Category")?></li>
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
            if (isset($_POST["insertForumCategories"])) {
              if (!$csrf->validate('insertForumCategories')) {
                echo alertError(t__('A system error happened!'));
              }
              else if (post("name") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else if ($_FILES["image"]["size"] == null) {
                echo alertError(t__('Please choose a picture!'));
              }
              else {
                require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                $upload = new \Verot\Upload\Upload($_FILES["image"]);
                $imageID = md5(uniqid(rand(0, 9999)));
                if ($upload->uploaded) {
                  $upload->allowed = array("image/*");
                  $upload->file_new_name_body = $imageID;
                  $upload->process(__ROOT__."/apps/main/public/assets/img/forum/categories/");
                  if ($upload->processed) {
                    $insertForumCategories = $db->prepare("INSERT INTO ForumCategories (parentID, name, slug, description, imageID, imageType) VALUES (?, ?, ?, ?, ?, ?)");
                    $insertForumCategories->execute(array(post("parentID"), post("name"), $slugify->slugify(post("name")), post("description"), $imageID, $upload->file_dst_name_ext));
                    echo alertSuccess(t__('Category has been added successfully!'));
                  }
                  else {
                    echo alertError(t__("An error while uploading a picture: ").$upload->error);
                  }
                }
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group row">
                  <label for="inputName" class="col-sm-2 col-form-label"><?php e__("Name")?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputName" class="form-control" name="name" placeholder="<?php e__("Enter the category name")?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputDescription" class="col-sm-2 col-form-label"><?php e__("Description")?>:</label>
                  <div class="col-sm-10">
                    <textarea id="inputDescription" class="form-control" rows="3" name="description" placeholder="<?php e__("Enter the category description")?>."></textarea>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectParentID" class="col-sm-2 col-form-label"><?php e__("Parent Category")?>:</label>
                  <div class="col-sm-10">
                    <?php
                      $forumCategories = $db->query("SELECT * FROM ForumCategories");
                    ?>
                    <select id="selectParentID" class="form-control" data-toggle="select" name="parentID">
                      <option value="0"><?php e__("Uncategorized")?></option>
                      <?php if ($forumCategories->rowCount() > 0): ?>
                        <?php foreach ($forumCategories as $readForumCategories): ?>
                          <option value="<?php echo $readForumCategories["id"]; ?>"><?php echo $readForumCategories["name"]; ?></option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="fileImage" class="col-sm-2 col-form-label"><?php e__("Picture")?>:</label>
                  <div class="col-sm-10">
                    <div data-toggle="dropimage" class="dropimage">
                      <div class="di-thumbnail">
                        <img src="" alt="<?php e__("Preview")?>">
                      </div>
                      <div class="di-select">
                        <label for="fileImage"><?php e__("Choose a Picture")?></label>
                        <input type="file" id="fileImage" name="image" accept="image/*">
                      </div>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('insertForumCategories'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertForumCategories"><?php e__("Add")?></button>
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
      $forumCategory = $db->prepare("SELECT * FROM ForumCategories WHERE id = ?");
      $forumCategory->execute(array(get("id")));
      $readForumCategory = $forumCategory->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("Update Category")?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard")?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/forum/threads"><?php e__("Forum")?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/forum/categories"><?php e__("Category")?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/forum/categories"><?php e__("Edit Category")?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($forumCategory->rowCount() > 0) ? $readForumCategory["name"] : "Bulunamadı!"; ?></li>
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
          <?php if ($forumCategory->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateForumCategories"])) {
                if (!$csrf->validate('updateForumCategories')) {
                  echo alertError(t__('A system error happened!'));
                }
                else if (post("name") == null) {
                  echo alertError(t__('Please fill all the fields!'));
                }
                else {
                  if ($_FILES["image"]["size"] != null) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                    $upload = new \Verot\Upload\Upload($_FILES["image"]);
                    $imageID = $readForumCategory["imageID"];
                    if ($upload->uploaded) {
                      $upload->allowed = array("image/*");
                      $upload->file_overwrite = true;
                      $upload->file_new_name_body = $imageID;
                      $upload->process(__ROOT__."/apps/main/public/assets/img/forum/categories/");
                      if ($upload->processed) {
                        $updateServers = $db->prepare("UPDATE ForumCategories SET imageType = ? WHERE id = ?");
                        $updateServers->execute(array($upload->file_dst_name_ext, $readForumCategory["id"]));
                      }
                      else {
                        echo alertError(t__("An error while uploading a picture: ").$upload->error);
                      }
                    }
                  }
                  
                  $updateForumCategories = $db->prepare("UPDATE ForumCategories SET parentID = ?, name = ?, slug = ?, description = ? WHERE id = ?");
                  $updateForumCategories->execute(array(post("parentID"), post("name"), $slugify->slugify(post("name")), post("description"), get("id")));
                  echo alertSuccess(t__('Changes has been saved successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label"><?php e__("Name")?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputName" class="form-control" name="name" placeholder="<?php e__("Enter the category name")?>." value="<?php echo $readForumCategory["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputDescription" class="col-sm-2 col-form-label"><?php e__("Description")?>:</label>
                    <div class="col-sm-10">
                      <textarea id="inputDescription" class="form-control" rows="3" name="description" placeholder="<?php e__("Enter the category description")?>."><?php echo $readForumCategory["description"]; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectParentID" class="col-sm-2 col-form-label"><?php e__("Parent Category")?>:</label>
                    <div class="col-sm-10">
                      <?php
                        $forumCategories = $db->query("SELECT * FROM ForumCategories");
                      ?>
                      <select id="selectParentID" class="form-control" data-toggle="select" name="parentID">
                        <option value="0"><?php e__("Uncategorized")?></option>
                        <?php if ($forumCategories->rowCount() > 0): ?>
                          <?php foreach ($forumCategories as $readForumCategories): ?>
                            <option value="<?php echo $readForumCategories["id"]; ?>" <?php echo (($readForumCategories["id"] == $readForumCategory["parentID"]) ? 'selected="selected"' : null); ?>><?php echo $readForumCategories["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="fileImage" class="col-sm-2 col-form-label"><?php e__("Picture")?>:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage active">
                        <div class="di-thumbnail">
                          <img src="/apps/main/public/assets/img/forum/categories/<?php echo $readForumCategory["imageID"].'.'.$readForumCategory["imageType"]; ?>" alt="Ön İzleme">
                        </div>
                        <div class="di-select">
                          <label for="fileImage"><?php e__("Choose a Picture")?></label>
                          <input type="file" id="fileImage" name="image" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateForumCategories'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/forum/categories/delete/<?php echo $readForumCategory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete")?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/forum/categories/<?php echo $readForumCategory["id"]."/".$readForumCategory["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__("View")?>">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateForumCategories"><?php e__("Save Changes")?></button>
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
      $deleteForumCategory = $db->prepare("DELETE FROM ForumCategories WHERE id = ?");
    $deleteForumCategory->execute(array(get("id")));
      go("/dashboard/forum/categories");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
