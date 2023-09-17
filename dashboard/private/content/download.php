<?php
  if (!checkPerm($readAdmin, 'MANAGE_DOWNLOADS')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
?>
<?php if (get("target") == 'download'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("Files") ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard") ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Files") ?></li>
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
          <?php $download = $db->query("SELECT * FROM Downloads ORDER BY id DESC"); ?>
          <?php if ($download->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["downloadID", "downloadName", "downloadURL"]'>
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
                    <a class="btn btn-sm btn-white" href="/dashboard/files/create"><?php e__("Add Page") ?></a>
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
                          <a href="#" class="text-muted sort" data-sort="downloadID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="downloadName">
                              <?php e__("File Name") ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="downloadURL">
                              <?php e__("Download URL") ?>
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($download as $readDownload): ?>
                        <tr>
                          <td class="downloadID text-center" style="width: 40px;">
                            <a href="/dashboard/files/edit/<?php echo $readDownload["id"]; ?>">
                              #<?php echo $readDownload["id"]; ?>
                            </a>
                          </td>
                          <td class="downloadName">
                            <a href="/dashboard/files/edit/<?php echo $readDownload["id"]; ?>">
                              <?php echo substr($readDownload["name"], 0, 30); ?>
                            </a>
                          </td>
                          <td class="downloadURL">
                            <?php echo substr($readDownload["downloadURL"], 0, 70); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/files/edit/<?php echo $readDownload["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Edit") ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/download/<?php echo $readDownload["id"]; ?>/<?php echo $readDownload["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__("View") ?>">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/files/delete/<?php echo $readDownload["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete") ?>">
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
                  <h2 class="header-title"><?php e__("Add File") ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard") ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/files"><?php e__("Files") ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("New File") ?></li>
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
            if (isset($_POST["insertDownloads"])) {
              if (!$csrf->validate('insertDownloads')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("name") == null || post("content") == null || post("downloadURL") == null) {
                echo alertError(t__('Please fill all the fields.'));
              }
              else {
                $insertDownloads = $db->prepare("INSERT INTO Downloads (name, slug, content, downloadURL) VALUES (?, ?, ?, ?)");
                $insertDownloads->execute(array(post("name"), $slugify->slugify(post("name")), filteredContent($_POST["content"]), post("downloadURL")));
                echo alertSuccess(t__('The Download File has been added successfully!'));
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                    <label for="inputname" class="col-sm-2 col-form-label"><?php e__("File Name") ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputname" class="form-control" name="name" placeholder="<?php e__("Enter the file name.") ?>">
                  </div>
                </div>
                <div class="form-group row">
                    <label for="inputdownloadURL" class="col-sm-2 col-form-label"><?php e__("File Download URL") ?></label>
                  <div class="col-sm-10">
                    <input type="text" id="inputdownloadURL" class="form-control" name="downloadURL" placeholder="<?php e__("Enter the download url.") ?>">
                  </div>
                </div>
                <div class="form-group row">
                    <label for="textareacontent" class="col-sm-2 col-form-label"><?php e__("File Content") ?>:</label>
                  <div class="col-sm-10">
                    <textarea id="textareacontent" class="form-control" data-toggle="textEditor" name="content" placeholder="<?php e__("Enter the content of downloading file.") ?>"></textarea>
                  </div>
                </div>
                <?php echo $csrf->input('insertDownloads'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertDownloads"><?php e__("Add") ?></button>
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
      $download = $db->prepare("SELECT * FROM Downloads WHERE id = ?");
      $download->execute(array(get("id")));
      $readDownload = $download->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("Edit File") ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard") ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/files"><?php e__("Files") ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/files"><?php e__("Edit File") ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($download->rowCount() > 0) ? substr($readDownload["name"], 0, 30): t__('Not found!'); ?></li>
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
          <?php if ($download->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateDownloads"])) {
                if (!$csrf->validate('updateDownloads')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if (post("name") == null || post("content") == null || post("downloadURL") == null) {
                  echo alertError(t__('Please fill all the fields.'));
                }
                else {
                  $updateDownloads = $db->prepare("UPDATE Downloads SET name = ?, slug = ?, content = ?, downloadURL = ? WHERE id = ?");
                  $updateDownloads->execute(array(post("name"), $slugify->slugify(post("name")), filteredContent($_POST["content"]), post("downloadURL"), get("id")));
                  echo alertSuccess(t__('Changes has been saved successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputname" class="col-sm-2 col-form-label"><?php e__("File Name") ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputname" class="form-control" name="name" placeholder="<?php e__("Enter the file name") ?>" value="<?php echo $readDownload["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputdownloadURL" class="col-sm-2 col-form-label"><?php e__("File Download URL") ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputdownloadURL" class="form-control" name="downloadURL" placeholder="<?php e__("Enter the download url") ?>" value="<?php echo $readDownload["downloadURL"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="textareacontent" class="col-sm-2 col-form-label"><?php e__("File Content") ?>:</label>
                    <div class="col-sm-10">
                      <textarea id="textareacontent" class="form-control" data-toggle="textEditor" name="content" placeholder="<?php e__("Enter the content of downloading file") ?>"><?php echo $readDownload["content"]; ?></textarea>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateDownloads'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/files/delete/<?php echo $readDownload["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete") ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/download/<?php echo $readDownload["id"]; ?>/<?php echo $readDownload["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__("View") ?>">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateDownloads"><?php e__("Save Changes") ?></button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page.')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteDownload = $db->prepare("DELETE FROM Downloads WHERE id = ?");
      $deleteDownload->execute(array(get("id")));
      go("/dashboard/files");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
