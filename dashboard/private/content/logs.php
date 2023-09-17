<?php
  if (!checkPerm($readAdmin, 'MANAGE_LOGS')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  if (get("target") == 'logs' && get("action") == 'getAll') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  }
  
  $actions = array(
    'GENERAL_SETTINGS_UPDATED' => 'General Settings updated',
    'SYSTEM_SETTINGS_UPDATED' => 'System Settings updated',
    'SEO_SETTINGS_UPDATED' => 'SEO Settings updated',
    'SMTP_SETTINGS_UPDATED' => 'SMTP Settings updated',
    'WEBHOOK_SETTINGS_UPDATED' => 'Webhook Settings updated',
    'LANGUAGE_ADDED' => 'Language added',
    'LANGUAGE_UPDATED' => 'Language updated',
    'SYSTEM_UPDATED' => 'LeaderOS updated',
    'THEME_SETTINGS_UPDATED' => 'Theme Settings updated',
    'THEME_HEADER_UPDATED' => 'Theme Header updated',
    'THEME_CSS_UPDATED' => 'Theme CSS updated',
    'GAMING_NIGHT_UPDATED' => 'Gaming Night Settings updated',
    'MODULES_UPDATED' => 'Module Settings updated',
  );

?>
<?php if (get("target") == 'logs'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Logs') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Logs') ?></li>
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
            if (isset($_GET["page"])) {
              if (!is_numeric($_GET["page"])) {
                $_GET["page"] = 1;
              }
              $page = intval(get("page"));
            }
            else {
              $page = 1;
            }
        
            $visiblePageCount = 5;
            $limit = 50;
        
            $logs = $db->query("SELECT id FROM Logs");
            $itemsCount = $logs->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $logs = $db->query("SELECT L.*, A.realname FROM Logs L INNER JOIN Accounts A ON L.accountID = A.id ORDER BY L.id DESC LIMIT $visibleItemsCount, $limit");
        
            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $logs = $db->prepare("SELECT L.*, A.realname FROM Logs L INNER JOIN Accounts A ON L.accountID = A.id WHERE A.realname LIKE :search OR L.ip LIKE :search ORDER BY L.id DESC");
                $logs->execute(array(
                  "search" => '%'.post("query").'%'
                ));
              }
            }
          ?>
          <?php if ($logs->rowCount() > 0): ?>
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
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="<?php e__('Search (Username or IP Address)') ?>" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success"><?php e__('Search') ?></button>
                      <a class="btn btn-sm btn-danger clickdelete" href="/dashboard/logs/delete-all"><?php e__('Delete All') ?></a>
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
                      <th><?php e__('Username') ?></th>
                      <th><?php e__('Action') ?></th>
                      <th><?php e__('IP Address') ?></th>
                      <th><?php e__('Date') ?></th>
                      <th class="text-right">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    <?php foreach ($logs as $readLogs): ?>
                      <tr>
                        <td class="text-center" style="width: 40px;">
                          #<?php echo $readLogs["id"]; ?>
                        </td>
                        <td>
                          <a href="/dashboard/users/view/<?php echo $readLogs["accountID"]; ?>">
                            <?php echo $readLogs["realname"]; ?>
                          </a>
                        </td>
                        <td>
                          <?php e__($actions[$readLogs["action"]]); ?>
                        </td>
                        <td>
                          <?php echo $readLogs["ip"]; ?>
                        </td>
                        <td>
                          <?php echo convertTime($readLogs["creationDate"], 2, true); ?>
                        </td>
                        <td class="text-right">
                          <?php if (checkPerm($readAdmin, 'SUPER_ADMIN')): ?>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/logs/delete/<?php echo $readLogs["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          <?php endif; ?>
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
                    <a class="page-link" href="/dashboard/logs/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/dashboard/logs/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/logs/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
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
  <?php elseif (get("action") == 'deleteAll'): ?>
    <?php
      if (checkPerm($readAdmin, 'SUPER_ADMIN')) {
        $deleteLogs = $db->prepare("TRUNCATE TABLE Logs");
        $deleteLogs->execute();
      }
      go("/dashboard/logs");
    ?>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      if (checkPerm($readAdmin, 'SUPER_ADMIN')) {
        $deleteLogs = $db->prepare("DELETE FROM Logs WHERE id = ?");
        $deleteLogs->execute(array(get("id")));
      }
      go("/dashboard/logs");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
