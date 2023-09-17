<?php
  if (!checkPerm($readAdmin, 'MANAGE_GAMES')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
?>
<?php if (get("target") == 'game'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("Games") ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard") ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Games") ?></li>
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
          <?php $games = $db->query("SELECT * FROM Games ORDER BY id DESC"); ?>
          <?php if ($games->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["gameID", "gameTitle", "gameCreationDate"]'>
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <div class="row align-items-center">
                      <div class="col-auto pr-0">
                        <span class="fe fe-search text-muted"></span>
                      </div>
                      <div class="col">
                        <input type="search" class="form-control form-control-flush search" name="search" placeholder="<?php e__("Search") ?>">
                      </div>
                    </div>
                  </div>
                  <div class="col-auto">
                    <a class="btn btn-sm btn-white" href="/dashboard/games/create"><?php e__("Add Game") ?></a>
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
                          <a href="#" class="text-muted sort" data-sort="gameID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="gameTitle">
                              <?php e__("Title") ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="gameCreationDate">
                              <?php e__("Date") ?>
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($games as $readGames): ?>
                        <tr>
                          <td class="gameID text-center" style="width: 40px;">
                            <a href="/dashboard/games/edit/<?php echo $readGames["id"]; ?>">
                              #<?php echo $readGames["id"]; ?>
                            </a>
                          </td>
                          <td class="gameTitle">
                            <a href="/dashboard/games/edit/<?php echo $readGames["id"]; ?>">
                              <?php echo $readGames["title"]; ?>
                            </a>
                          </td>
                          <td class="gameCreationDate">
                            <?php echo convertTime($readGames["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/games/edit/<?php echo $readGames["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Edit") ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/games/<?php echo $readGames["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__("View") ?>">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/games/delete/<?php echo $readGames["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete") ?>">
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
                  <h2 class="header-title"><?php e__("Add Game") ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard") ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/games"><?php e__("Games") ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Add Game") ?></li>
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
            if (isset($_POST["insertGames"])) {
              if (!$csrf->validate('insertGames')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("title") == null || post("content") == null) {
                echo alertError(t__('Please fill all the fields.'));
              }
              else if ($_FILES["image"]["size"] == null) {
                echo alertError(t__('Please select an image!'));
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
                  $upload->process(__ROOT__."/apps/main/public/assets/img/games/");
                  if ($upload->processed) {
                    $insertGames = $db->prepare("INSERT INTO Games (title, slug, imageID, imageType, content, creationDate) VALUES (?, ?, ?, ?, ?, ?)");
                    $insertGames->execute(array(post("title"), $slugify->slugify(post("title")), $imageID, $upload->file_dst_name_ext, filteredContent($_POST["content"]), date("Y-m-d H:i:s")));
                    echo alertSuccess(t__('Game has been added successfully!'));
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
                  <label for="inputTitle" class="col-sm-2 col-form-label"><?php e__("Title") ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="<?php e__("Enter the game title") ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaContent" class="col-sm-2 col-form-label"><?php e__("Content") ?>:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="<?php e__("Enter the game content") ?>."></textarea>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="fileImage" class="col-sm-2 col-form-label"><?php e__("Image") ?>:</label>
                  <div class="col-sm-10">
                    <div data-toggle="dropimage" class="dropimage">
                      <div class="di-thumbnail">
                        <img src="" alt="Preview">
                      </div>
                      <div class="di-select">
                        <label for="fileImage"><?php e__("Select Image") ?></label>
                        <input type="file" id="fileImage" name="image" accept="image/*">
                      </div>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('insertGames'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertGames"><?php e__("Add") ?></button>
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
      $game = $db->prepare("SELECT * FROM Games WHERE id = ?");
      $game->execute(array(get("id")));
      $readGame = $game->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("Edit Game") ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard") ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/games"><?php e__("Games") ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/games"><?php e__("Edit Game") ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($game->rowCount() > 0) ? substr($readGame["title"], 0, 30): t__('Not found!'); ?></li>
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
          <?php if ($game->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateGames"])) {
                if (!$csrf->validate('updateGames')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if (post("title") == null || post("content") == null) {
                  echo alertError(t__('Please fill all the fields.'));
                }
                else {
                  if ($_FILES["image"]["size"] != null) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                    $upload = new \Verot\Upload\Upload($_FILES["image"]);
                    $imageID = $readGame["imageID"];
                    if ($upload->uploaded) {
                      $upload->allowed = array("image/*");
                      $upload->file_overwrite = true;
                      $upload->file_new_name_body = $imageID;
                      $upload->image_resize = true;
                      $upload->image_ratio_crop = true;
                      $upload->image_x = 640;
                      $upload->image_y = 360;
                      $upload->process(__ROOT__."/apps/main/public/assets/img/games/");
                      if ($upload->processed) {
                        $updateGames = $db->prepare("UPDATE Games SET imageType = ? WHERE id = ?");
                        $updateGames->execute(array($upload->file_dst_name_ext, $readGame["id"]));
                      }
                      else {
                        echo alertError(t__('An error occupied while uploading an image: %error%', ['%error%' => $upload->error]));
                      }
                    }
                  }

                  $updateGames = $db->prepare("UPDATE Games SET title = ?, slug = ?, content = ? WHERE id = ?");
                  $updateGames->execute(array(post("title"), $slugify->slugify(post("title")), filteredContent($_POST["content"]), get("id")));
                  echo alertSuccess(t__('Changes has been saved successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label for="inputTitle" class="col-sm-2 col-form-label"><?php e__("Title") ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="<?php e__("Enter the game title") ?>." value="<?php echo $readGame["title"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="textareaContent" class="col-sm-2 col-form-label"><?php e__("Content") ?>:</label>
                    <div class="col-sm-10">
                      <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="<?php e__("Enter the game content") ?>."><?php echo $readGame["content"]; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="fileImage" class="col-sm-2 col-form-label"><?php e__('Image') ?>:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage active">
                        <div class="di-thumbnail">
                          <img src="/apps/main/public/assets/img/games/<?php echo $readGame["imageID"].'.'.$readGame["imageType"]; ?>" alt="<?php e__("Preview") ?>">
                        </div>
                        <div class="di-select">
                          <label for="fileImage"><?php e__("Select Image") ?></label>
                          <input type="file" id="fileImage" name="image" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateGames'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/games/delete/<?php echo $readGame["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete") ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/games/<?php echo $readGame["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__("View") ?>">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateGames"><?php e__("Save Changes") ?></button>
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
      $deleteGame = $db->prepare("DELETE FROM Games WHERE id = ?");
      $deleteGame->execute(array(get("id")));
      go("/dashboard/games");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
