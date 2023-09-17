<?php
  if (!checkPerm($readAdmin, 'MANAGE_BROADCAST')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  if (get("target") == 'broadcast' && get("action") == 'getAll') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  }
?>
<?php if (get("target") == 'broadcast'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("Announcement") ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard") ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Announcement") ?></li>
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
          <?php $broadcast = $db->query("SELECT * FROM Broadcast ORDER BY id ASC"); ?>
          <?php if ($broadcast->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["broadcastID", "broadcastTitle", "broadcastURL"]'>
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
                    <a class="btn btn-sm btn-white" href="/dashboard/announcements/create"><?php e__("Add an Announcement") ?></a>
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
                          <a href="#" class="text-muted sort" data-sort="broadcastID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="broadcastTitle">
                              <?php e__("Title") ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="broadcastURL">
                              <?php e__("URL") ?>
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($broadcast as $readBroadcast): ?>
                        <tr>
                          <td class="broadcastID text-center" style="width: 40px;">
                            <a href="/dashboard/announcements/edit/<?php echo $readBroadcast["id"]; ?>">
                              #<?php echo $readBroadcast["id"]; ?>
                            </a>
                          </td>
                          <td class="broadcastTitle">
                            <a href="/dashboard/announcements/edit/<?php echo $readBroadcast["id"]; ?>">
                              <?php echo substr($readBroadcast["title"], 0, 30); ?>
                            </a>
                          </td>
                          <td class="broadcastURL">
                            <?php echo substr($readBroadcast["url"], 0, 30); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/announcements/edit/<?php echo $readBroadcast["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Edit") ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/announcements/delete/<?php echo $readBroadcast["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete") ?>">
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
                  <h2 class="header-title"><?php e__("Add an Announcement") ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard") ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/announcements"><?php e__("Announcement") ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Add") ?></li>
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
            if (isset($_POST["insertBroadcast"])) {
              if (!$csrf->validate('insertBroadcast')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("title") == null || post("url") == null) {
                echo alertError(t__('Please fill all the fields.'));
              }
              else {
                $insertBroadcast = $db->prepare("INSERT INTO Broadcast (title, url) VALUES (?, ?)");
                $insertBroadcast->execute(array(post("title"), post("url")));
                echo alertSuccess(t__('Announcement has been added successfully!'));
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputTitle" class="col-sm-2 col-form-label"><?php e__("Title") ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="<?php e__("Enter the title.") ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputURL" class="col-sm-2 col-form-label"><?php e__("URL (Link)") ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputURL" class="form-control" name="url" placeholder="<?php e__("Enter the URL to go on click.") ?>">
                  </div>
                </div>
                <?php echo $csrf->input('insertBroadcast'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertBroadcast"><?php e__("Add") ?></button>
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
      $broadcast = $db->prepare("SELECT * FROM Broadcast WHERE id = ?");
      $broadcast->execute(array(get("id")));
      $readBroadcast = $broadcast->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("Edit an Announcement") ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard") ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/announcements"><?php e__("Announcement") ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/announcements"><?php e__("Edit") ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($broadcast->rowCount() > 0) ? substr($readBroadcast["title"], 0, 30) : t__('Not found!'); ?></li>
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
          <?php if ($broadcast->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateBroadcast"])) {
                if (!$csrf->validate('updateBroadcast')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if (post("title") == null || post("url") == null) {
                  echo alertError(t__('Please fill all the fields.'));
                }
                else {
                  $updateBroadcast = $db->prepare("UPDATE Broadcast SET title = ?, url = ? WHERE id = ?");
                  $updateBroadcast->execute(array(post("title"), post("url"), get("id")));
                  echo alertSuccess(t__('Changes has been saved successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputTitle" class="col-sm-2 col-form-label"><?php e__("Title") ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="<?php e__("Enter the title.") ?>" value="<?php echo $readBroadcast["title"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputURL" class="col-sm-2 col-form-label"><?php e__("URL (Link)") ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputURL" class="form-control" name="url" placeholder="<?php e__("Enter the URL to go on click.") ?>" value="<?php echo $readBroadcast["url"]; ?>">
                    </div>
                  </div>
                  <?php echo $csrf->input('updateBroadcast'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/announcements/delete/<?php echo $readBroadcast["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete") ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateBroadcast"><?php e__("Save Changes") ?></button>
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
      $deleteBroadcast = $db->prepare("DELETE FROM Broadcast WHERE id = ?");
      $deleteBroadcast->execute(array(get("id")));
      go("/dashboard/announcements");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
