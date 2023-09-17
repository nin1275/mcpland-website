<?php
  if (!checkPerm($readAdmin, 'MANAGE_LEADERBOARDS')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/leaderboards.js');
?>
<?php if (get("target") == 'leaderboards'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Rank Table') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/leaderboards"><?php e__('Leaderboard') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Rank Tables') ?></li>
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
          <?php $leaderboards = $db->query("SELECT * FROM Leaderboards ORDER BY id DESC"); ?>
          <?php if ($leaderboards->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["leaderboardsID", "leaderboardsServerName", "leaderboardsMySQLTable", "leaderboardsSorter", "leaderboardsDataLimit"]'>
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
                    <a class="btn btn-sm btn-white" href="/dashboard/leaderboards/create"><?php e__('Add Rank Table') ?></a>
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
                          <a href="#" class="text-muted sort" data-sort="leaderboardsID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="leaderboardsServerName">
                              <?php e__('Server Name') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="leaderboardsMySQLTable">
                              <?php e__('MySQL Table') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="leaderboardsSorter">
                            Sorter
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="leaderboardsDataLimit">
                            Sorter Limit
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($leaderboards as $readLeaderboards): ?>
                        <tr>
                          <td class="leaderboardsID text-center" style="width: 40px;">
                            <a href="/dashboard/leaderboards/edit/<?php echo $readLeaderboards["id"]; ?>">
                              #<?php echo $readLeaderboards["id"]; ?>
                            </a>
                          </td>
                          <td class="leaderboardsServerName">
                            <a href="/dashboard/leaderboards/edit/<?php echo $readLeaderboards["id"]; ?>">
                              <?php echo $readLeaderboards["serverName"]; ?>
                            </a>
                          </td>
                          <td class="leaderboardsMySQLTable">
                            <?php echo $readLeaderboards["mysqlTable"]; ?>
                          </td>
                          <td class="leaderboardsSorter">
                            <?php echo $readLeaderboards["sorter"]; ?>
                          </td>
                          <td class="leaderboardsDataLimit">
                            <?php echo $readLeaderboards["dataLimit"]; ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/leaderboards/edit/<?php echo $readLeaderboards["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/leaderboards/<?php echo $readLeaderboards["serverSlug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/leaderboards/delete/<?php echo $readLeaderboards["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
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
            <?php echo alertError(t__("No data for this page!")); ?>
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
                  <h2 class="header-title"><?php e__('Add Rank Table') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/leaderboards"><?php e__('Rank') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/leaderboards"><?php e__('Rank Tables') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Add Rank Table') ?></li>
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
            if (isset($_POST["insertLeaderboards"])) {
              if (post("mysqlServerStatus") == 0) {
                $_POST["mysqlServer"] = 0;
                $_POST["mysqlPort"] = 0;
                $_POST["mysqlUsername"] = 0;
                $_POST["mysqlPassword"] = 0;
                $_POST["mysqlDatabase"] = 0;
              }
              $tableTitles = rtrim(strip_tags(implode(",", str_replace(",", "", $_POST["tableTitles"]))), ",");
              $tableData = rtrim(strip_tags(implode(",", str_replace(",", "", $_POST["tableData"]))), ",");
              if (!$csrf->validate('insertLeaderboards')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("serverName") == null || post("mysqlServerStatus") == null || post("mysqlServer") == null || post("mysqlPort") == null || post("mysqlUsername") == null || post("mysqlPassword") == null || post("mysqlDatabase") == null || post("mysqlTable") == null || post("usernameColumn") == null || $tableTitles == null || $tableData == null || post("sorter") == null || post("dataLimit") == null) {
                echo alertError(t__("Please fill all the fields"));
              }
              else {
                $insertLeaderboards = $db->prepare("INSERT INTO Leaderboards (serverName, serverSlug, mysqlServer, mysqlPort, mysqlUsername, mysqlPassword, mysqlDatabase, mysqlTable, usernameColumn, tableTitles, tableData, sorter, dataLimit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $insertLeaderboards->execute(array(post("serverName"), $slugify->slugify(post("serverName")), post("mysqlServer"), post("mysqlPort"), post("mysqlUsername"), post("mysqlPassword"), post("mysqlDatabase"), post("mysqlTable"), post("usernameColumn"), $tableTitles, $tableData, post("sorter"), post("dataLimit")));
                echo alertSuccess(t__("Rank server has been added successfully!"));
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputServerName" class="col-sm-2 col-form-label"><?php e__('Server Name') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputServerName" class="form-control" name="serverName" placeholder="<?php e__("Enter the rank server's name") ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectMySQLServerStatus" class="col-sm-2 col-form-label"><?php e__('MySQL Server') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectMySQLServerStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="mysqlServerStatus">
                      <option value="0"><?php e__('Connect to this server') ?></option>
                      <option value="1"><?php e__('Connect to another server') ?></option>
                    </select>
                  </div>
                </div>
                <div id="mysqlServerInfo" class="row" style="display: none;">
                  <div class="col-sm-10 offset-2">
                    <div class="form-group row">
                      <div class="col-md-6">
                        <label for="inputMySQLServer"><?php e__('Server Address') ?>:</label>
                        <input type="text" id="inputMySQLServer" class="form-control" name="mysqlServer" placeholder="<?php e__('Enter the server address of database') ?>.">
                      </div>
                      <div class="col-md-6">
                        <label for="inputMySQLPort"><?php e__('Port') ?>:</label>
                        <input type="number" id="inputMySQLPort" class="form-control" name="mysqlPort" placeholder="<?php e__('Enter the port of database') ?>.">
                      </div>
                    </div>
                    <div class="form-group row">
                      <div class="col-md-6">
                        <label for="inputMySQLUsername"><?php e__('Username') ?>:</label>
                        <input type="text" id="inputMySQLUsername" class="form-control" name="mysqlUsername" placeholder="<?php e__('Enter the user of database') ?>.">
                      </div>
                      <div class="col-md-6">
                        <label for="inputMySQLPassword"><?php e__('Password') ?>:</label>
                        <input type="password" id="inputMySQLPassword" class="form-control" name="mysqlPassword" placeholder="<?php e__('Enter the password of database') ?>.">
                      </div>
                    </div>
                    <div class="form-group row">
                      <div class="col-md-12">
                        <label for="inputMySQLDatabase"><?php e__('Database') ?>:</label>
                        <input type="text" id="inputMySQLDatabase" class="form-control" name="mysqlDatabase" placeholder="<?php e__('Enter the name of database') ?>.">
                        <div id="checkConnect" class="mt-3">
                          <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                            <span class="sr-only">-/-</span>
                          </div>
                          <a href="javascript:void(0);"><?php e__('Check the connection') ?></a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputMySQLTable" class="col-sm-2 col-form-label"><?php e__('MySQL Table') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputMySQLTable" class="form-control" name="mysqlTable" placeholder="<?php e__('Enter the name of database table to make rank system') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputUsernameColumn" class="col-sm-2 col-form-label"><?php e__('Username Column') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputUsernameColumn" class="form-control" name="usernameColumn" placeholder="<?php e__('Enter the column of user in database') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputSorter" class="col-sm-2 col-form-label"><?php e__('Sorter Column') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputSorter" class="form-control" name="sorter" placeholder="<?php e__('Enter the name of that column which will made sorter') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selecetDataLimit" class="col-sm-2 col-form-label"><?php e__('Data Limit') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selecetDataLimit" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="dataLimit">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputTable" class="col-sm-2 col-form-label"><?php e__('Table Infos') ?>:</label>
                  <div class="col-sm-10">
                    <div class="table-responsive">
                      <table id="tableitems" class="table table-sm table-hover table-nowrap array-table">
                        <thead>
                          <tr>
                            <th><?php e__('Title') ?></th>
                            <th><?php e__('Database Column name') ?></th>
                            <th class="text-center pt-0 pb-0 align-middle">
                              <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                                <i class="fe fe-plus"></i>
                              </button>
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>
                              <input type="text" class="form-control" name="tableTitles[]" placeholder="<?php e__('Enter the title which will be show in table') ?>.">
                            </td>
                            <td>
                              <input type="text" class="form-control" name="tableData[]" placeholder="<?php e__('Enter the column name of MySQL') ?>.">
                            </td>
                            <td class="text-center align-middle">
                              <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                <i class="fe fe-trash-2"></i>
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('insertLeaderboards'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertLeaderboards"><?php e__('Add') ?></button>
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
      $leaderboards = $db->prepare("SELECT * FROM Leaderboards WHERE id = ?");
      $leaderboards->execute(array(get("id")));
      $readLeaderboards = $leaderboards->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Edit Rank Table') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/leaderboards"><?php e__('Rank') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/leaderboards"><?php e__('Rank Tables') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/leaderboards"><?php e__('Edit Rank Table') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($leaderboards->rowCount() > 0) ? $readLeaderboards["serverName"] : t__('Not found!'); ?></li>
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
          <?php if ($leaderboards->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateLeaderboards"])) {
                if (post("mysqlServerStatus") == 0) {
                  $_POST["mysqlServer"] = 0;
                  $_POST["mysqlPort"] = 0;
                  $_POST["mysqlUsername"] = 0;
                  $_POST["mysqlPassword"] = 0;
                  $_POST["mysqlDatabase"] = 0;
                }
                $tableTitles = rtrim(strip_tags(implode(",", str_replace(",", "", $_POST["tableTitles"]))), ",");
                $tableData = rtrim(strip_tags(implode(",", str_replace(",", "", $_POST["tableData"]))), ",");
                if (!$csrf->validate('updateLeaderboards')) {
                  echo alertError(t__("Something went wrong! Please try again later."));
                }
                else if (post("serverName") == null || post("mysqlServerStatus") == null || post("mysqlServer") == null || post("mysqlPort") == null || post("mysqlUsername") == null || post("mysqlPassword") == null || post("mysqlDatabase") == null || post("mysqlTable") == null || post("usernameColumn") == null || $tableTitles == null || $tableData == null || post("sorter") == null || post("dataLimit") == null) {
                  echo alertError(t__("Please fill all the fields!"));
                }
                else {
                  $updateLeaderboards = $db->prepare("UPDATE Leaderboards SET serverName = ?, serverSlug = ?, mysqlServer = ?, mysqlPort = ?, mysqlUsername = ?, mysqlPassword = ?, mysqlDatabase = ?, mysqlTable = ?, usernameColumn = ?, tableTitles = ?, tableData = ?, sorter = ?, dataLimit = ? WHERE id = ?");
                  $updateLeaderboards->execute(array(post("serverName"), $slugify->slugify(post("serverName")), post("mysqlServer"), post("mysqlPort"), post("mysqlUsername"), post("mysqlPassword"), post("mysqlDatabase"), post("mysqlTable"), post("usernameColumn"), $tableTitles, $tableData, post("sorter"), post("dataLimit"), get("id")));
                  echo alertSuccess(t__("Changes has been saved successfully!"));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputServerName" class="col-sm-2 col-form-label"><?php e__('Server Name') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputServerName" class="form-control" name="serverName" placeholder="<?php e__('Enter the name of rank server') ?>." value="<?php echo $readLeaderboards["serverName"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectMySQLServerStatus" class="col-sm-2 col-form-label"><?php e__('MySQL Server') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectMySQLServerStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="mysqlServerStatus">
                        <option value="0" <?php echo ($readLeaderboards["mysqlServer"] == '0') ? 'selected="selected"' : null; ?>><?php e__('Connect to this server') ?></option>
                        <option value="1" <?php echo ($readLeaderboards["mysqlServer"] != '0') ? 'selected="selected"' : null; ?>><?php e__('Connect to another server') ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="mysqlServerInfo" class="row" style="<?php echo ($readLeaderboards["mysqlServer"] == '0') ? "display: none;" : "display: block;"; ?>">
                    <div class="col-sm-10 offset-2">
                      <div class="form-group row">
                        <div class="col-md-6">
                          <label for="inputMySQLServer"><?php e__('Server Address') ?>:</label>
                          <input type="text" id="inputMySQLServer" class="form-control" name="mysqlServer" placeholder="<?php e__('Enter the address of database server') ?>." value="<?php echo ($readLeaderboards["mysqlServer"] != '0') ? $readLeaderboards["mysqlServer"] : null; ?>">
                        </div>
                        <div class="col-md-6">
                          <label for="inputMySQLPort"><?php e__('Port') ?>:</label>
                          <input type="number" id="inputMySQLPort" class="form-control" name="mysqlPort" placeholder="<?php e__('Enter the port of database server') ?>." value="<?php echo ($readLeaderboards["mysqlServer"] != '0') ? $readLeaderboards["mysqlPort"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="col-md-6">
                          <label for="inputMySQLUsername"><?php e__('Username') ?>:</label>
                          <input type="text" id="inputMySQLUsername" class="form-control" name="mysqlUsername" placeholder="<?php e__('Enter the username of database') ?>." value="<?php echo ($readLeaderboards["mysqlServer"] != '0') ? $readLeaderboards["mysqlUsername"] : null; ?>">
                        </div>
                        <div class="col-md-6">
                          <label for="inputMySQLPassword"><?php e__('Password') ?>:</label>
                          <input type="password" id="inputMySQLPassword" class="form-control" name="mysqlPassword" placeholder="<?php e__('Enter the password of database') ?>." value="<?php echo ($readLeaderboards["mysqlServer"] != '0') ? $readLeaderboards["mysqlPassword"] : null; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="col-md-12">
                          <label for="inputMySQLDatabase"><?php e__('Database') ?>:</label>
                          <input type="text" id="inputMySQLDatabase" class="form-control" name="mysqlDatabase" placeholder="<?php e__('Enter the name of database') ?>." value="<?php echo ($readLeaderboards["mysqlServer"] != '0') ? $readLeaderboards["mysqlDatabase"] : null; ?>">
                          <div id="checkConnect" class="mt-3">
                            <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                              <span class="sr-only">-/-</span>
                            </div>
                            <a href="javascript:void(0);"><?php e__('Check the connection') ?></a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputMySQLTable" class="col-sm-2 col-form-label"><?php e__('MySQL Table') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputMySQLTable" class="form-control" name="mysqlTable" placeholder="<?php e__('Enter the name of database table to make rank system') ?>." value="<?php echo $readLeaderboards["mysqlTable"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputUsernameColumn" class="col-sm-2 col-form-label"><?php e__('Username Column') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputUsernameColumn" class="form-control" name="usernameColumn" placeholder="<?php e__('Enter the column of user in database') ?>." value="<?php echo $readLeaderboards["usernameColumn"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputSorter" class="col-sm-2 col-form-label"><?php e__("Sorter Column") ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputSorter" class="form-control" name="sorter" placeholder="<?php e__('Enter the name of that column which will made sorter') ?>." value="<?php echo $readLeaderboards["sorter"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selecetDataLimit" class="col-sm-2 col-form-label"><?php e__('Data Limit') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selecetDataLimit" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="dataLimit">
                        <option value="10" <?php echo ($readLeaderboards["dataLimit"] == 10) ? 'selected="selected"' : null; ?>>10</option>
                        <option value="25" <?php echo ($readLeaderboards["dataLimit"] == 25) ? 'selected="selected"' : null; ?>>25</option>
                        <option value="50" <?php echo ($readLeaderboards["dataLimit"] == 50) ? 'selected="selected"' : null; ?>>50</option>
                        <option value="100" <?php echo ($readLeaderboards["dataLimit"] == 100) ? 'selected="selected"' : null; ?>>100</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputTable" class="col-sm-2 col-form-label"><?php e__('Table Infos') ?>:</label>
                    <div class="col-sm-10">
                      <div class="table-responsive">
                        <table id="tableitems" class="table table-sm table-hover table-nowrap array-table">
                          <thead>
                            <tr>
                              <th><?php e__('Title') ?></th>
                              <th><?php e__('Database Column Name') ?></th>
                              <th class="text-center pt-0 pb-0 align-middle">
                                <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                                  <i class="fe fe-plus"></i>
                                </button>
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              $tableTitlesArray = explode(",", $readLeaderboards["tableTitles"]);
                              $tableDataArray = explode(",", $readLeaderboards["tableData"]);
                            ?>
                            <?php foreach (array_combine($tableTitlesArray, $tableDataArray) as $tableTitleValue => $tableDataValue): ?>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="tableTitles[]" placeholder="<?php e__('Enter the title which will be show in table') ?>." value="<?php echo $tableTitleValue; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="tableData[]" placeholder="<?php e__('Enter the column name of MySQL') ?>." value="<?php echo $tableDataValue; ?>">
                                </td>
                                <td class="text-center align-middle" style="">
                                  <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                    <i class="fe fe-trash-2"></i>
                                  </button>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateLeaderboards'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/leaderboards/delete/<?php echo $readLeaderboards["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/leaderboards/<?php echo $readLeaderboards["serverSlug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateLeaderboards"><?php e__('Save Changes') ?></button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__("No data for this page!")); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteLeaderboard = $db->prepare("DELETE FROM Leaderboards WHERE id = ?");
      $deleteLeaderboard->execute(array(get("id")));
      go("/dashboard/leaderboards");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
