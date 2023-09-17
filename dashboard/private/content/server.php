<?php
  if (!checkPerm($readAdmin, 'MANAGE_SERVERS')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  if (get("target") == 'server') {
    if (get("action") == 'insert' || get("action") == 'update') {
      $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/server.js');
      $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/server.check.js');
      $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.minecraft-items.js');
    }
    if (get("action") == 'get' && get("id")) {
      $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/server.console.js');
    }
  }
?>
<?php if (get("target") == 'server'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Servers') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Servers') ?></li>
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
          <?php $servers = $db->query("SELECT * FROM Servers ORDER BY priority DESC, id DESC"); ?>
          <?php if ($servers->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["serverID", "servername", "serverIP", "serverConsoleID", "serverConsolePort", "serverPriority"]'>
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
                    <a class="btn btn-sm btn-white" href="/dashboard/servers/create"><?php e__('Add Server') ?></a>
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
                          <a href="#" class="text-muted sort" data-sort="serverID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="servername">
                              <?php e__('Server Name') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="serverIP">
                              <?php e__('Server IP:Port') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="serverConsoleID">
                              <?php e__('Console Type') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="serverConsolePort">
                              <?php e__('Console Port') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="serverPriority">
                            <?php e__('Priority') ?>
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($servers as $readServers): ?>
                        <tr>
                          <td class="serverID text-center" style="width: 40px;">
                            <a href="/dashboard/servers/view/<?php echo $readServers["id"]; ?>">
                              #<?php echo $readServers["id"]; ?>
                            </a>
                          </td>
                          <td class="servername">
                            <a href="/dashboard/servers/view/<?php echo $readServers["id"]; ?>">
                              <?php echo $readServers["name"]; ?>
                            </a>
                          </td>
                          <td class="serverIP">
                            <?php echo $readServers["ip"].":".$readServers["port"]; ?>
                          </td>
                          <td class="serverConsoleID">
                            <?php echo ($readServers["consoleID"] == 1 ? 'Websend' : (($readServers["consoleID"] == 2) ? 'RCON' : (($readServers["consoleID"] == 3) ? 'Websender' : t__('Error!')))); ?>
                          </td>
                          <td class="serverConsolePort">
                            <?php echo $readServers["consolePort"]; ?>
                          </td>
                          <td class="serverPriority">
                            <?php echo $readServers["priority"]; ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/servers/edit/<?php echo $readServers["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/dashboard/servers/view/<?php echo $readServers["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Console & Summary') ?>">
                              <i class="fe fe-activity"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/servers/delete/<?php echo $readServers["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
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
                  <h2 class="header-title"><?php e__('Add Server') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/servers"><?php e__('Servers') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Add Server') ?></li>
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
            if (isset($_POST["insertServers"])) {
              if (!$csrf->validate('insertServers')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("name") == null || post("ip") == null || post("port") == null || post("consoleID") == null || post("consolePort") == null || post("consolePassword") == null || post("minecraftStatus") == null || post("priority") == null) {
                echo alertError(t__('Please fill all the fields!'));
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
                  $upload->process(__ROOT__."/apps/main/public/assets/img/servers/");
                  if ($upload->processed) {
                    $insertServers = $db->prepare("INSERT INTO Servers (name, slug, ip, port, consoleID, consolePort, consolePassword, imageID, imageType, priority, minecraftStatus, minecraftTitle, minecraftDescription, minecraftItem) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertServers->execute(array(post("name"), $slugify->slugify(post("name")), post("ip"), post("port"), post("consoleID"), post("consolePort"), post("consolePassword"), $imageID, $upload->file_dst_name_ext, post("priority"), post("minecraftStatus"), post("minecraftTitle"), post("minecraftDescription"), post("minecraftItem")));
                    echo alertSuccess(t__('Server has been added successfully!'));
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
                  <label for="inputName" class="col-sm-2 col-form-label"><?php e__('Server Name') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputName" class="form-control" name="name" placeholder="<?php e__('Enter the server name') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputIP" class="col-sm-2 col-form-label"><?php e__('Server IP') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputIP" class="form-control" name="ip" placeholder="<?php e__('Enter the server IP') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputPort" class="col-sm-2 col-form-label"><?php e__('Server Port') ?>:</label>
                  <div class="col-sm-10">
                    <input type="number" id="inputPort" class="form-control" name="port" placeholder="<?php e__('Enter the server port') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectConsoleID" class="col-sm-2 col-form-label"><?php e__('Console Type') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectConsoleID" class="form-control" name="consoleID" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="2"><?php e__('Rcon') ?></option>
                      <option value="3"><?php e__('Websender (recommended)') ?></option>
                      <option value="1"><?php e__('Websend') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputConsolePort" class="col-sm-2 col-form-label"><?php e__('Console Port') ?>:</label>
                  <div class="col-sm-10">
                    <input type="number" id="inputConsolePort" class="form-control" name="consolePort" placeholder="<?php e__('Enter the console port') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputConsolePassword" class="col-sm-2 col-form-label"><?php e__('Console Password') ?>:</label>
                  <div class="col-sm-10">
                    <input type="password" id="inputConsolePassword" class="form-control" name="consolePassword" placeholder="<?php e__('Enter the console password') ?>.">
                    <div id="checkConnect" class="mt-3">
                      <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                        <span class="sr-only">-/-</span>
                      </div>
                      <a href="javascript:void(0);"><?php e__('Check The Console Connection') ?></a>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputPriority" class="col-sm-2 col-form-label"><?php e__('Priority') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputPriority" class="form-control" name="priority" placeholder="<?php e__('Enter the priority') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectMinecraftStatus" class="col-sm-2 col-form-label"><?php e__('Show in Game') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectMinecraftStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="minecraftStatus">
                      <option value="0"><?php e__('No') ?></option>
                      <option value="1"><?php e__('Yes') ?></option>
                    </select>
                  </div>
                </div>
                <div id="minecraftBlock" style="display: none;">
                  <div class="form-group row">
                    <label for="input-minecrafttitle" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Title') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="input-minecrafttitle" class="form-control" name="minecraftTitle" placeholder="<?php e__('If you leave it blank, the product name will be used. Color codes (&) are acceptable.') ?>.">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="input-minecraftdesc" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Description (lore)') ?>:</label>
                    <div class="col-sm-10">
                      <textarea id="input-minecraftdesc" class="form-control" name="minecraftDescription" placeholder="<?php e__('You can leave it blank. Color codes (&) are acceptable.') ?>."></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectMinecraftItem" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Icon') ?>:</label>
                    <div class="col-sm-10">
                      <div id="mi-loading" style="margin-top: 7px"><?php e__('Loading...') ?></div>
                      <div id="product-minecraftitems" style="display: none;" data-type="insert">
                        <select id="selectMinecraftItem" class="form-control" name="minecraftItem">
                          <option value=""><?php e__('Default') ?></option>
                        </select>
                      </div>
                    </div>
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
                <?php echo $csrf->input('insertServers'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertServers"><?php e__('Add') ?></button>
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
      $server = $db->prepare("SELECT * FROM Servers WHERE id = ?");
      $server->execute(array(get("id")));
      $readServer = $server->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Edit Server') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/servers"><?php e__('Servers') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/servers"><?php e__('Edit Server') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($server->rowCount() > 0) ? $readServer["name"] : t__('Not found!'); ?></li>
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
          <?php if ($server->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateServers"])) {
                if (!$csrf->validate('updateServers')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if (post("name") == null || post("ip") == null || post("port") == null || post("consoleID") == null || post("consolePort") == null || post("consolePassword") == null || post("minecraftStatus") == null || post("priority") == null) {
                  echo alertError(t__('Please fill all the fields!'));
                }
                else {
                  if ($_FILES["image"]["size"] != null) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                    $upload = new \Verot\Upload\Upload($_FILES["image"]);
                    $imageID = $readServer["imageID"];
                    if ($upload->uploaded) {
                      $upload->allowed = array("image/*");
                      $upload->file_overwrite = true;
                      $upload->file_new_name_body = $imageID;
                      $upload->image_resize = true;
                      $upload->image_ratio_crop = true;
                      $upload->image_x = 640;
                      $upload->image_y = 360;
                      $upload->process(__ROOT__."/apps/main/public/assets/img/servers/");
                      if ($upload->processed) {
                        $updateServers = $db->prepare("UPDATE Servers SET imageType = ? WHERE id = ?");
                        $updateServers->execute(array($upload->file_dst_name_ext, $readServer["id"]));
                      }
                      else {
                        echo alertError(t__('An error occupied while uploading an image: %error%', ['%error%' => $upload->error]));
                      }
                    }
                  }

                  $updateServers = $db->prepare("UPDATE Servers SET name = ?, slug = ?, ip = ?, port = ?, consoleID = ?, consolePort = ?, consolePassword = ?, priority = ?, minecraftStatus = ?, minecraftTitle = ?, minecraftDescription = ?, minecraftItem = ? WHERE id = ?");
                  $updateServers->execute(array(post("name"), $slugify->slugify(post("name")), post("ip"), post("port"), post("consoleID"), post("consolePort"), post("consolePassword"), post("priority"), post("minecraftStatus"), post("minecraftTitle"), post("minecraftDescription"), post("minecraftItem"), get("id")));
                  echo alertSuccess(t__('Changes has been saved successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label"><?php e__('Server Name') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputName" class="form-control" name="name" placeholder="<?php e__('Enter the server name') ?>." value="<?php echo $readServer["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputIP" class="col-sm-2 col-form-label"><?php e__('Server IP') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputIP" class="form-control" name="ip" placeholder="<?php e__('Enter the server ip') ?>." value="<?php echo $readServer["ip"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPort" class="col-sm-2 col-form-label"><?php e__('Server Port') ?>:</label>
                    <div class="col-sm-10">
                      <input type="number" id="inputPort" class="form-control" name="port" placeholder="<?php e__('Enter the server port') ?>." value="<?php echo $readServer["port"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectConsoleID" class="col-sm-2 col-form-label"><?php e__('Console Type') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectConsoleID" class="form-control" name="consoleID" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="1" <?php echo ($readServer["consoleID"] == 1) ? 'selected="selected"' : null; ?>>Websend</option>
                        <option value="2" <?php echo ($readServer["consoleID"] == 2) ? 'selected="selected"' : null; ?>>RCON</option>
                        <option value="3" <?php echo ($readServer["consoleID"] == 3) ? 'selected="selected"' : null; ?>>Websender</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputConsolePort" class="col-sm-2 col-form-label"><?php e__('Console Port') ?>:</label>
                    <div class="col-sm-10">
                      <input type="number" id="inputConsolePort" class="form-control" name="consolePort" placeholder="<?php e__('Enter the console port') ?>." value="<?php echo $readServer["consolePort"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputConsolePassword" class="col-sm-2 col-form-label"><?php e__('Console Password') ?>:</label>
                    <div class="col-sm-10">
                      <input type="password" id="inputConsolePassword" class="form-control" name="consolePassword" placeholder="<?php e__('Enter the console password') ?>." value="<?php echo $readServer["consolePassword"]; ?>">
                      <div id="checkConnect" class="mt-3">
                        <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                          <span class="sr-only">-/-</span>
                        </div>
                        <a href="javascript:void(0);"><?php e__('Check the console connection.') ?></a>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPriority" class="col-sm-2 col-form-label"><?php e__('Priority') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputPriority" class="form-control" name="priority" placeholder="<?php e__('Enter the priority') ?>." value="<?php echo $readServer["priority"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectMinecraftStatus" class="col-sm-2 col-form-label"><?php e__('Show in GUI') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectMinecraftStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="minecraftStatus">
                        <option value="0" <?php echo ($readServer["minecraftStatus"] == 0) ? 'selected="selected"' : null; ?>><?php e__('No') ?></option>
                        <option value="1" <?php echo ($readServer["minecraftStatus"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Yes') ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="minecraftBlock" style="<?php echo ($readServer["minecraftStatus"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <label for="input-minecrafttitle" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Title') ?>:</label>
                      <div class="col-sm-10">
                        <input type="text" id="input-minecrafttitle" class="form-control" name="minecraftTitle" placeholder="<?php e__('If you leave it blank, the product name will be used. Color codes (&) are acceptable.') ?>." value="<?php echo $readServer["minecraftTitle"]; ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="input-minecraftdesc" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Description (lore)') ?>:</label>
                      <div class="col-sm-10">
                        <textarea id="input-minecraftdesc" class="form-control" name="minecraftDescription" placeholder="<?php e__('You can leave it blank. Color codes (&) are acceptable.') ?>."><?php echo $readServer["minecraftDescription"]; ?></textarea>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectMinecraftItem" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Icon') ?>:</label>
                      <div class="col-sm-10">
                        <div id="mi-loading" style="margin-top: 7px"><?php e__('Loading...') ?></div>
                        <div id="product-minecraftitems" style="display: none;" data-type="update" data-selected="<?php echo $readServer["minecraftItem"]; ?>">
                          <select id="selectMinecraftItem" class="form-control" name="minecraftItem">
                            <option value=""><?php e__('None') ?></option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="fileImage" class="col-sm-2 col-form-label"><?php e__('Image') ?>:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage active">
                        <div class="di-thumbnail">
                          <img src="/apps/main/public/assets/img/servers/<?php echo $readServer["imageID"].'.'.$readServer["imageType"]; ?>" alt="<?php e__('Preview') ?>">
                        </div>
                        <div class="di-select">
                          <label for="fileImage"><?php e__('Select Image') ?></label>
                          <input type="file" id="fileImage" name="image" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateServers'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/servers/delete/<?php echo $readServer["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/dashboard/servers/view/<?php echo $readServer["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Console & Summary') ?>">
                        <i class="fe fe-activity"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateServers"><?php e__('Save Changes') ?></button>
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
  <?php elseif (get("action") == 'get' && get("id")): ?>
    <?php
      $server = $db->prepare("SELECT * FROM Servers WHERE id = ?");
      $server->execute(array(get("id")));
      $readServer = $server->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Server Summary') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/servers"><?php e__('Servers') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/servers"><?php e__('Server Summary') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($server->rowCount() > 0) ? $readServer["name"] : t__('Not found!'); ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <?php if ($server->rowCount() > 0): ?>
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <h4 class="card-header-title">
                        <?php e__('Console') ?>
                    </h4>
                  </div>
                  <div class="col-auto">
                    <a id="consoleRefresh" class="small text-muted" href="#">
                      <i class="fe fe-refresh-cw"></i>
                    </a>
                  </div>
                </div>
              </div>
              <div id="consoleBox" class="card-body" style="height: 200px !important; overflow: auto;">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <?php
                  $consoleIP = $readServer["ip"];
                  $consoleID = $readServer["consoleID"];
                  $consolePort = $readServer["consolePort"];
                  $consolePassword = $readServer["consolePassword"];
                  $consoleTimeout = 3;

                  if ($consoleID == 1) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/websend/websend.php");
                    $console = new Websend($consoleIP, $consolePort);
                    $console->password = $consolePassword;
                  }
                  else if ($consoleID == 2) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/rcon/rcon.php");
                    $console = new Rcon($consoleIP, $consolePort, $consolePassword, $consoleTimeout);
                  }
                  else if ($consoleID == 3) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/websender/websender.php");
                    $console = new Websender($consoleIP, $consolePassword, $consolePort);
                  }
                  else {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/websend/websend.php");
                    $console = new Websend($consoleIP, $consolePort);
                    $console->password = $consolePassword;
                  }
                ?>
                <div class="row mb-3">
                  <div class="col d-flex align-items-center">
                    <span class="badge badge-pill badge-secondary mr-2"><?php e__('Console') ?></span>
                    <?php if (@$console->connect()): ?>
                      <strong class="text-success mr-1"><?php e__('SUCCESS') ?>:</strong>
                      <span class="text-success"><?php e__('Connected!') ?>!</span>
                      <?php $console->disconnect(); ?>
                    <?php else: ?>
                      <strong class="text-danger mr-1"><?php e__('ERROR') ?>:</strong>
                      <span class="text-danger"><?php e__('Cant connected!') ?>!</span>
                    <?php endif; ?>
                  </div>
                  <div class="col-auto small">
                    <span><?php echo date("H:i"); ?></span>
                  </div>
                </div>
                <div id="consoleHistory"></div>
              </div>
              <div class="card-footer p-0">
                <input type="text" id="consoleCommand" class="form-control border-0" style="padding: .75rem 1.5rem; border-radius: 0 0 .375rem .375rem;" name="command" placeholder="<?php e__('Enter your command') ?>.">
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col">
                        <h6 class="card-title text-uppercase text-muted mb-2">
                            <?php e__('Total Credit Spent') ?>
                        </h6>
                        <span class="h2 mb-0">
                          <?php
                            $earnedMoney = $db->prepare("SELECT SUM(price) AS price FROM StoreHistory WHERE serverID = ? AND creationDate LIKE ?");
                            $earnedMoney->execute(array($readServer["id"], "%".date("Y-m")."%"));
                            $readEarnedMoney = $earnedMoney->fetch();
                            if ($readEarnedMoney["price"] == null) {
                              $readEarnedMoney["price"] = 0;
                            }
                            echo $readEarnedMoney["price"];
                          ?>
                        </span>
                        <?php
                          $lastMonthEarnedMoney = $db->prepare("SELECT SUM(price) AS price FROM StoreHistory WHERE serverID = ? AND creationDate LIKE ?");
                          $lastMonthEarnedMoney->execute(array($readServer["id"], "%".date("Y-m", strtotime("first day of last month"))."%"));
                          $readLastMonthEarnedMoney = $lastMonthEarnedMoney->fetch();
                          if ($readLastMonthEarnedMoney["price"] == null) {
                            $readLastMonthEarnedMoney["price"] = 0;
                          }
                          $calculate = floor(((100*($readEarnedMoney["price"]-$readLastMonthEarnedMoney["price"])) / (max(1, $readLastMonthEarnedMoney["price"]))));
                        ?>
                        <?php if ($calculate > 0): ?>
                          <span class="badge badge-soft-success mt--1">+<?php echo $calculate; ?>%</span>
                        <?php elseif ($calculate < 0): ?>
                          <span class="badge badge-soft-danger mt--1"><?php echo $calculate; ?>%</span>
                        <?php elseif ($calculate == 0): ?>
                          <span class="badge badge-soft-secondary mt--1"><?php echo $calculate; ?>%</span>
                        <?php else: ?>
                          <?php e__('HATA!'); ?>
                        <?php endif; ?>
                      </div>
                      <div class="col-auto">
                        <span class="h2 fe fe-dollar-sign text-muted mb-0"></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col">
                        <h6 class="card-title text-uppercase text-muted mb-2">
                            <?php e__('Online Players') ?>
                        </h6>
                        <span data-toggle="onlinetext" server-ip="<?php echo $readServer["ip"].':'.$readServer["port"]; ?>">-/-</span>
                      </div>
                      <div class="col-auto">
                        <span class="h2 fe fe-globe text-muted mb-0"></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <?php
                  $storeHistory = $db->prepare("SELECT SH.*, A.realname, P.name as productName, S.name as serverName FROM StoreHistory SH INNER JOIN Accounts A ON SH.accountID = A.id INNER JOIN Products P ON SH.productID = P.id INNER JOIN Servers S ON SH.serverID = S.id WHERE SH.serverID = ? ORDER BY SH.id DESC LIMIT 50");
                  $storeHistory->execute(array($readServer["id"]));
                ?>
                <?php if ($storeHistory->rowCount() > 0): ?>
                  <div class="card">
                    <div class="card-header">
                      <div class="row align-items-center">
                        <div class="col">
                          <h4 class="card-header-title">
                              <?php e__('Store History') ?>
                          </h4>
                        </div>
                        <div class="col-auto">
                          <span class="text-primary small">(<?php e__('Last 50 Activity') ?>)</span>
                        </div>
                      </div>
                    </div>
                    <div class="card-body p-0">
                      <div class="table-responsive mb-0">
                        <table class="table table-sm table-no-wrap card-table">
                          <thead>
                            <tr>
                              <th><?php e__('User') ?></th>
                              <th class="text-center"><?php e__('Product') ?></th>
                              <th class="text-center"><?php e__('Server') ?></th>
                              <th class="text-center"><?php e__('Amount') ?></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($storeHistory as $readStoreHistory): ?>
                              <tr>
                                <td>
                                  <a class="avatar avatar-xs d-inline-block" href="/dashboard/users/view/<?php echo $readStoreHistory["realname"]; ?>">
                                    <img src="https://minotar.net/avatar/<?php echo $readStoreHistory["realname"]; ?>/20.png" alt="<?php e__('Manager Account') ?>" class="rounded-circle">
                                  </a>
                                  <a href="/dashboard/users/view/<?php echo $readStoreHistory["realname"]; ?>"><?php echo $readStoreHistory["realname"]; ?></a>
                                </td>
                                <td class="text-center"><?php echo $readStoreHistory["productName"]; ?></td>
                                <td class="text-center"><?php echo $readStoreHistory["serverName"]; ?></td>
                                <td class="text-center"><?php echo $readStoreHistory["price"]; ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                <?php else: ?>
                  <?php echo alertError(t__('No store history found!')); ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <script type="text/javascript">
            var serverID      = <?php echo $readServer["id"]; ?>;
            var username        = '<?php echo $readAdmin["realname"]; ?>';
            var creationDate    = '<?php echo date('H:i'); ?>';
          </script>
        <?php else : ?>
          <div class="col-md-12"><?php echo alertError(t__('No data for this page!')); ?></div>
        <?php endif; ?>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteServer = $db->prepare("DELETE FROM Servers WHERE id = ?");
      $deleteServer->execute(array(get("id")));
      go("/dashboard/servers");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
