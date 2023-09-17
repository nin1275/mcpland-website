<?php
  if (!checkPerm($readAdmin, 'MANAGE_SUPPORT')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  if (get("target") == 'support' && get("action") == 'get' && get("id")) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/support.js');
  }
  if (get("target") == 'support' && get("action") == 'getAll') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/support.delete.js');
  }
?>
<?php if (get("target") == 'support'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Support') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/support"><?php e__('Support') ?></a></li>
                      <?php if (get("category")): ?>
                        <?php if (get("category") == 'unread'): ?>
                          <li class="breadcrumb-item active" aria-current="page"><?php e__('Waiting Reply') ?></li>
                        <?php elseif (get("category") == 'readed'): ?>
                          <li class="breadcrumb-item active" aria-current="page"><?php e__('Answered') ?></li>
                        <?php elseif (get("category") == 'closed'): ?>
                          <li class="breadcrumb-item active" aria-current="page"><?php e__('Closed') ?></li>
                        <?php else: ?>
                          <li class="breadcrumb-item active" aria-current="page"><?php e__('Error!') ?></li>
                        <?php endif; ?>
                      <?php else: ?>
                        <li class="breadcrumb-item active" aria-current="page"><?php e__('All') ?></li>
                      <?php endif; ?>
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
            if (get("category")) {
              if (get("category") == 'unread') {
                $supports = $db->prepare("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.statusID IN (?, ?) ORDER BY S.id DESC");
                $supports->execute(array(1, 3));
              }
              else if (get("category") == 'readed') {
                $supports = $db->prepare("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.statusID = ? ORDER BY S.id DESC");
                $supports->execute(array(2));
              }
              else if (get("category") == 'closed') {
                $supports = $db->prepare("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.statusID = ? ORDER BY S.id DESC");
                $supports->execute(array(4));
              }
              else {
                $supports = $db->query("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id ORDER BY S.id DESC");
              }
            }
            else {
              $supports = $db->query("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id ORDER BY S.id DESC");
            }
          ?>
          <?php if ($supports->rowCount() > 0): ?>
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

              $itemsCount = $supports->rowCount();
              $pageCount = ceil($itemsCount/$limit);
              if ($page > $pageCount) {
                $page = 1;
              }
              $visibleItemsCount = $page * $limit - $limit;
              $requestURL = 'destek';
              if (get("category")) {
                if (get("category") == 'unread') {
                  $supports = $db->prepare("SELECT S.*, SC.name as categoryName, Se.name as serverName, A.realname FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.statusID IN (?, ?) ORDER BY S.id DESC LIMIT $visibleItemsCount, $limit");
                  $supports->execute(array(1, 3));
                  $requestURL = 'support/waiting-reply';
                }
                else if (get("category") == 'readed') {
                  $supports = $db->prepare("SELECT S.*, SC.name as categoryName, Se.name as serverName, A.realname FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.statusID = ? ORDER BY S.id DESC LIMIT $visibleItemsCount, $limit");
                  $supports->execute(array(2));
                  $requestURL = 'support/answered';
                }
                else if (get("category") == 'closed') {
                  $supports = $db->prepare("SELECT S.*, SC.name as categoryName, Se.name as serverName, A.realname FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.statusID = ? ORDER BY S.id DESC LIMIT $visibleItemsCount, $limit");
                  $supports->execute(array(4));
                  $requestURL = 'support/closed';
                }
                else {
                  $supports = $db->query("SELECT S.*, SC.name as categoryName, Se.name as serverName, A.realname FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id ORDER BY S.id DESC LIMIT $visibleItemsCount, $limit");
                }
              }
              else {
                $supports = $db->query("SELECT S.*, SC.name as categoryName, Se.name as serverName, A.realname FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id ORDER BY S.id DESC LIMIT $visibleItemsCount, $limit");
              }

              if (isset($_POST["query"])) {
                if (post("query") != null) {
                  $supports = $db->prepare("SELECT S.*, SC.name as categoryName, Se.name as serverName, A.realname FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.title LIKE :search OR SC.name LIKE :search OR Se.name LIKE :search ORDER BY S.id DESC");
                  $supports->execute(array(
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
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="<?php e__('Search (Title, Category or Server)') ?>" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success"><?php e__('Server') ?></button>
                      <button type="button" class="btn btn-sm btn-danger clickdelete" onclick='document.getElementById("deleteSelected").submit();'><?php e__("Delete Choosen's") ?></button>
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
                        <th class="text-center" style="width: 40px;">
                          <div class="custom-control custom-checkbox table-checkbox">
                            <input type="checkbox" class="custom-control-input" name="ordersSelect" id="ordersSelectAll">
                            <label class="custom-control-label" for="ordersSelectAll">
                              &nbsp;
                            </label>
                          </div>
                        </th>
                        <th class="text-center" style="width: 40px;">#ID</th>
                        <th><?php e__('Title') ?></th>
                        <th><?php e__('Username') ?></th>
                        <th><?php e__('Server') ?></th>
                        <th><?php e__('Category') ?></th>
                        <th><?php e__('Date') ?></th>
                        <th class="text-center"><?php e__('Status') ?></th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <form action="/dashboard/support/delete-selected" method="post" id="deleteSelected">
                        <?php foreach ($supports as $readSupports): ?>
                          <tr>
                            <td class="text-center" style="width: 40px;">
                              <div class="custom-control custom-checkbox table-checkbox">
                                <input type="checkbox" class="custom-control-input" name="ordersSelect[]" value="<?php echo $readSupports["id"]; ?>" id="ordersSelect-<?php echo $readSupports["id"]; ?>">
                                <label class="custom-control-label" for="ordersSelect-<?php echo $readSupports["id"]; ?>">
                                  &nbsp;
                                </label>
                              </div>
                            </td>
                            <td class="text-center" style="width: 40px;">
                              <a href="/dashboard/support/view/<?php echo $readSupports["id"]; ?>">
                                #<?php echo $readSupports["id"]; ?>
                              </a>
                            </td>
                            <td>
                              <a href="/dashboard/support/view/<?php echo $readSupports["id"]; ?>">
                                <?php echo $readSupports["title"]; ?>
                              </a>
                            </td>
                            <td>
                              <a href="/dashboard/users/view/<?php echo $readSupports["accountID"]; ?>">
                                <?php echo $readSupports["realname"]; ?>
                              </a>
                            </td>
                            <td>
                              <?php echo $readSupports["serverName"]; ?>
                            </td>
                            <td>
                              <?php echo $readSupports["categoryName"]; ?>
                            </td>
                            <td>
                              <?php echo convertTime($readSupports["creationDate"], 2, true); ?>
                            </td>
                            <td class="text-center">
                              <?php if ($readSupports["statusID"] == 1): ?>
                                <span class="badge badge-pill badge-info"><?php e__('Open') ?></span>
                              <?php elseif ($readSupports["statusID"] == 2): ?>
                                <span class="badge badge-pill badge-success"><?php e__('Answered') ?></span>
                              <?php elseif ($readSupports["statusID"] == 3): ?>
                                <span class="badge badge-pill badge-warning"><?php e__('User-Reply') ?></span>
                              <?php elseif ($readSupports["statusID"] == 4): ?>
                                <span class="badge badge-pill badge-danger"><?php e__('Closed') ?></span>
                              <?php else: ?>
                                <span class="badge badge-pill badge-danger"><?php e__('Error!') ?></span>
                              <?php endif; ?>
                            </td>
                            <td class="text-right">
                              <a class="btn btn-sm btn-rounded-circle btn-primary" href="/dashboard/support/view/<?php echo $readSupports["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                                <i class="fe fe-eye"></i>
                              </a>
                              <a class="btn btn-sm btn-rounded-circle btn-warning" href="/dashboard/support/close/<?php echo $readSupports["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Close') ?>">
                                <i class="fe fe-x"></i>
                              </a>
                              <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/support/delete/<?php echo $readSupports["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                                <i class="fe fe-trash-2"></i>
                              </a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </form>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php if (post("query") == false): ?>
              <nav class="pt-3 pb-5" aria-label="Page Navigation">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo ($page == 1) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/<?php echo $requestURL; ?>/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/dashboard/<?php echo $requestURL; ?>/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/<?php echo $requestURL; ?>/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
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
      $support = $db->prepare("SELECT S.*, A.realname, Se.name as serverName, SC.name as categoryName FROM Supports S INNER JOIN Accounts A ON S.accountID = A.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN SupportCategories SC ON S.categoryID = SC.id WHERE S.id = ?");
      $support->execute(array(get("id")));
      $readSupport = $support->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('View Support') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/support"><?php e__('Support') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/support"><?php e__('View Support') ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo ($support->rowCount() > 0) ? substr($readSupport["title"], 0, 50): "Bulunamadı!"; ?></li>
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
          <?php if ($support->rowCount() > 0): ?>
            <?php
            require_once(__ROOT__."/apps/main/private/packages/class/commonmark/autoload.php");
            $converter = new \League\CommonMark\GithubFlavoredMarkdownConverter();
            ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["insertSupportMessages"])) {
                if (!$csrf->validate('insertSupportMessages')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if (post("message") == null) {
                  echo alertError(t__('Please fill all the fields!'));
                }
                else {
                  $insertSupportMessages = $db->prepare("INSERT INTO SupportMessages (accountID,supportID, message, writeLocation, creationDate) VALUES (?, ?, ?, ?, ?)");
                  $insertSupportMessages->execute(array($readAdmin["id"], $readSupport["id"], filteredContent($_POST["message"]), 2, date("Y-m-d H:i:s")));
                  $updateSupports = $db->prepare("UPDATE Supports SET updateDate = ?, statusID = ?, readStatus = ? WHERE id = ? AND accountID = ?");
                  $updateSupports->execute(array(date("Y-m-d H:i:s"), 2, 0, get("id"), $readSupport["accountID"]));
                  echo alertSuccess(t__('Message has been sent successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <h4 class="card-header-title">
                      <?php echo $readSupport["title"]; ?>
                    </h4>
                  </div>
                  <div class="col-auto">
                    <span class="badge badge-pill badge-primary" data-toggle="tooltip" data-placement="top" data-original-title="<?php e__('Server') ?>">
                      <?php echo $readSupport["serverName"]; ?>
                    </span>
                    <span class="badge badge-pill badge-primary" data-toggle="tooltip" data-placement="top" data-original-title="<?php e__('Category') ?>">
                      <?php echo $readSupport["categoryName"]; ?>
                    </span>
                    <span class="badge badge-pill badge-primary" data-toggle="tooltip" data-placement="top" data-original-title="<?php e__('Date') ?>">
                      <?php echo convertTime($readSupport["creationDate"], 2, true); ?>
                    </span>
                    <?php if ($readSupport["statusID"] == 1): ?>
                      <span class="badge badge-pill badge-info" data-toggle="tooltip" data-placement="top" data-original-title="<?php e__('Status') ?>"><?php e__('Open') ?></span>
                    <?php elseif ($readSupport["statusID"] == 2): ?>
                      <span class="badge badge-pill badge-success" data-toggle="tooltip" data-placement="top" data-original-title="<?php e__('Status') ?>"><?php e__('Answered') ?></span>
                    <?php elseif ($readSupport["statusID"] == 3): ?>
                      <span class="badge badge-pill badge-warning" data-toggle="tooltip" data-placement="top" data-original-title="<?php e__('Status') ?>"><?php e__('User-Reply') ?></span>
                    <?php elseif ($readSupport["statusID"] == 4): ?>
                      <span class="badge badge-pill badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="<?php e__('Status') ?>"><?php e__('Closed') ?></span>
                    <?php else: ?>
                      <span class="badge badge-pill badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="<?php e__('Status') ?>"><?php e__('Error!') ?></span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <div id="messagesBox" class="card-body pb-0" style="overflow: auto; max-height: 500px;">
                <div class="message">
                  <div class="message-img">
                    <a href="/dashboard/users/view/<?php echo $readSupport["accountID"]; ?>">
                      <img class="float-left rounded-circle" src="https://minotar.net/avatar/<?php echo $readSupport["realname"]; ?>/40.png" alt="<?php echo $serverName. e__('Player') ." - ".$readSupport["realname"] . e__('Message'); ?>">
                    </a>
                  </div>
                  <div class="message-content">
                    <div class="message-header">
                      <div class="message-username">
                        <a href="/dashboard/users/view/<?php echo $readSupport["accountID"]; ?>">
                          <?php echo $readSupport["realname"]; ?>
                        </a>
                      </div>
                      <div class="message-date">
                        <?php echo convertTime($readSupport["creationDate"], 2, true); ?>
                      </div>
                    </div>
                    <div class="message-body">
                      <p>
                        <?php echo $converter->convertToHtml($readSupport["message"]); ?>
                      </p>
                    </div>
                  </div>
                </div>
                <?php
                  $supportMessages = $db->prepare("SELECT SM.*, A.realname FROM SupportMessages SM INNER JOIN Accounts A ON SM.accountID = A.id WHERE SM.supportID = ? ORDER BY SM.id ASC");
                  $supportMessages->execute(array(get("id")));
                ?>
                <?php if ($supportMessages->rowCount() > 0): ?>
                  <?php foreach ($supportMessages as $readSupportMessages): ?>
                    <div class="message">
                      <div class="message-img">
                        <a href="/dashboard/users/view/<?php echo $readSupportMessages["accountID"]; ?>">
                          <img class="float-left rounded-circle" src="https://minotar.net/avatar/<?php echo $readSupportMessages["realname"]; ?>/40.png" alt="<?php echo $serverName. e__('Player')."  - ".$readSupportMessages["realname"] . e__('Message'); ?>">
                        </a>
                      </div>
                      <div class="message-content">
                        <div class="message-header">
                          <div class="message-username">
                            <a href="/dashboard/users/view/<?php echo $readSupportMessages["accountID"]; ?>">
                              <?php echo $readSupportMessages["realname"]; ?>
                            </a>
                          </div>
                          <div class="message-date">
                            <?php echo convertTime($readSupportMessages["creationDate"]); ?>
                          </div>
                        </div>
                        <div class="message-body">
                          <p>
                            <?php
                              if ($readSupportMessages["writeLocation"] == 1) {
                                echo $converter->convertToHtml($readSupportMessages["message"]);
                              }
                              else {
                                $message = showEmoji(hashtag(hashtag($readSupportMessages["message"], "@", "/player"), "#", "/tags"));
                                $search = array("%username%", "%message%", "%servername%", "%serverip%", "%serverversion%");
                                $replace = array($readSupport["realname"], $message, $serverName, $serverIP, $serverVersion);
                                $template = $readSettings["supportMessageTemplate"];
                                echo str_replace($search, $replace, $template);
                              }
                            ?>
                          </p>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
              <div class="card-footer">
                <form action="" method="post">
                  <div class="message">
                    <div class="message-img">
                      <img class="float-left rounded-circle" src="https://minotar.net/avatar/<?php echo $readAdmin["realname"]; ?>/40.png" alt="<?php echo $serverName.e__('Player')." - ".$readAdmin["realname"].e__('Message'); ?>">
                    </div>
                    <div class="message-content">
                      <div class="message-body">
                        <div class="mb-3">
                          <select id="selectAnswer" class="form-control" data-toggle="select" data-minimum-results-for-search="-1">
                            <?php $supportAnswers = $db->query("SELECT * FROM SupportAnswers"); ?>
                            <?php if ($supportAnswers->rowCount() > 0): ?>
                              <option value=""><?php e__('You can choose a quick reply') ?>.</option>
                              <?php foreach ($supportAnswers as $readSupportAnswers): ?>
                                <option value="<?php echo htmlentities($readSupportAnswers["content"]); ?>">
                                  <?php echo $readSupportAnswers["title"]; ?>
                                </option>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <option value=""><?php e__('No quick reply found!') ?></option>
                            <?php endif; ?>
                          </select>
                        </div>
                        <textarea id="textareaMessage" class="form-control" data-toggle="textEditor" name="message" placeholder="<?php e__('Enter the message') ?>."></textarea>
                      </div>
                      <div class="message-footer">
                        <?php echo $csrf->input('insertSupportMessages'); ?>
                        <div class="clearfix">
                          <div class="float-right">
                            <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/support/delete/<?php echo $readSupport["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                              <i class="fe fe-trash-2"></i>
                            </a>
                            <a class="btn btn-rounded-circle btn-warning" href="/dashboard/support/close/<?php echo $readSupport["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Close') ?>">
                              <i class="fe fe-x"></i>
                            </a>
                            <button type="submit" class="btn btn-rounded btn-success" name="insertSupportMessages"><?php e__('Send') ?></button>
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
  <?php elseif (get("action") == 'close' && get("id")): ?>
    <?php
      $closeSupport = $db->prepare("UPDATE Supports SET statusID = ?, updateDate = ? WHERE id = ?");
      $closeSupport->execute(array(4, date("Y-m-d H:i:s"), get("id")));
      go("/dashboard/support");
    ?>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteSupport = $db->prepare("DELETE FROM Supports WHERE id = ?");
      $deleteSupport->execute(array(get("id")));
      $deleteSupportMessages = $db->prepare("DELETE FROM SupportMessages WHERE supportID = ?");
      $deleteSupportMessages->execute(array(get("id")));
      go("/dashboard/support");
    ?>
  <?php elseif (get("action") == 'delete-selected' && count($_POST["ordersSelect"])): ?>
    <?php
      foreach ($_POST["ordersSelect"] as $supportID) {
        $deleteSupport = $db->prepare("DELETE FROM Supports WHERE id = ?");
        $deleteSupport->execute(array($supportID));
        $deleteSupportMessages = $db->prepare("DELETE FROM SupportMessages WHERE supportID = ?");
        $deleteSupportMessages->execute(array($supportID));
      }
      go("/dashboard/support");
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
                      <li class="breadcrumb-item"><a href="/dashboard/support"><?php e__('Support') ?></a></li>
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
          <?php $supportCategories = $db->query("SELECT * FROM SupportCategories ORDER BY id DESC"); ?>
          <?php if ($supportCategories->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["supportCategoryID", "suportCategoryName"]'>
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
                    <a class="btn btn-sm btn-white" href="/dashboard/support/categories/create"><?php e__('Add Category') ?></a>
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
                          <a href="#" class="text-muted sort" data-sort="supportCategoryID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="suportCategoryName">
                              <?php e__('Category Name') ?>
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($supportCategories as $readSupportCategories): ?>
                        <tr>
                          <td class="supportCategoryID text-center" style="width: 40px;">
                            <a href="/dashboard/support/categories/edit/<?php echo $readSupportCategories["id"]; ?>">
                              #<?php echo $readSupportCategories["id"]; ?>
                            </a>
                          </td>
                          <td class="suportCategoryName">
                            <a href="/dashboard/support/categories/edit/<?php echo $readSupportCategories["id"]; ?>">
                              <?php echo $readSupportCategories["name"]; ?>
                            </a>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/support/categories/edit/<?php echo $readSupportCategories["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/support/categories/delete/<?php echo $readSupportCategories["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
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
                      <li class="breadcrumb-item"><a href="/dashboard/support"><?php e__('Support') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/support/categories"><?php e__('Category') ?></a></li>
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
            if (isset($_POST["insertSupportCategories"])) {
              if (!$csrf->validate('insertSupportCategories')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("name") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else {
                $insertSupportCategories = $db->prepare("INSERT INTO SupportCategories (name, userTemplate) VALUES (?, ?)");
                $insertSupportCategories->execute(array(post("name"), post("userTemplate")));
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
                <div class="form-group row">
                  <label for="inputUserTemplate" class="col-sm-2 col-form-label"><?php e__('User Message Template (Optional)') ?>:</label>
                  <div class="col-sm-10">
                    <textarea cols="3" id="inputUserTemplate" class="form-control" name="userTemplate" placeholder="<?php e__('Enter the message to be filled automatically when users open a new ticket in this category.') ?>."></textarea>
                  </div>
                </div>
                <?php echo $csrf->input('insertSupportCategories'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertSupportCategories"><?php e__('Add') ?></button>
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
      $supportCategory = $db->prepare("SELECT * FROM SupportCategories WHERE id = ?");
      $supportCategory->execute(array(get("id")));
      $readSupportCategory = $supportCategory->fetch();
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
                      <li class="breadcrumb-item"><a href="/dashboard/support"><?php e__('Support') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/support/categories"><?php e__('Category') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/support/categories"><?php e__('Edit Category') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($supportCategory->rowCount() > 0) ? $readSupportCategory["name"] : "Bulunamadı!"; ?></li>
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
          <?php if ($supportCategory->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateSupportCategories"])) {
                if (!$csrf->validate('updateSupportCategories')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if (post("name") == null) {
                  echo alertError(t__('Please fill all the fields!'));
                }
                else {
                  $updateSupportCategories = $db->prepare("UPDATE SupportCategories SET name = ?, userTemplate = ? WHERE id = ?");
                  $updateSupportCategories->execute(array(post("name"), post("userTemplate"), get("id")));
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
                      <input type="text" id="inputName" class="form-control" name="name" placeholder="<?php e__('Enter the category name') ?>." value="<?php echo $readSupportCategory["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputUserTemplate" class="col-sm-2 col-form-label"><?php e__('User Message Template (Optional)') ?>:</label>
                    <div class="col-sm-10">
                      <textarea cols="3" id="inputUserTemplate" class="form-control" name="userTemplate" placeholder="<?php e__('Enter the message to be filled automatically when users open a new ticket in this category.') ?>."><?php echo $readSupportCategory["userTemplate"]; ?></textarea>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateSupportCategories'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/support/categories/delete/<?php echo $readSupportCategory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateSupportCategories"><?php e__('Save Changes') ?></button>
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
      $deleteSupportCategory = $db->prepare("DELETE FROM SupportCategories WHERE id = ?");
      $deleteSupportCategory->execute(array(get("id")));
      go("/dashboard/support/categories");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'answer'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Answers') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/support"><?php e__('Support') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Answers') ?></li>
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
          <?php $supportAnswers = $db->query("SELECT * FROM SupportAnswers ORDER BY id DESC"); ?>
          <?php if ($supportAnswers->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["supportAnswerID", "supportAnswerTitle"]'>
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
                    <a class="btn btn-sm btn-white" href="/dashboard/support/quick-answers/create"><?php e__('Add Answer') ?></a>
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
                          <a href="#" class="text-muted sort" data-sort="supportAnswerID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="supportAnswerTitle">
                              <?php e__('Title') ?>
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($supportAnswers as $readSupportAnswers): ?>
                        <tr>
                          <td class="supportAnswerID text-center" style="width: 40px;">
                            <a href="/dashboard/support/quick-answers/edit/<?php echo $readSupportAnswers["id"]; ?>">
                              #<?php echo $readSupportAnswers["id"]; ?>
                            </a>
                          </td>
                          <td class="supportAnswerTitle">
                            <a href="/dashboard/support/quick-answers/edit/<?php echo $readSupportAnswers["id"]; ?>">
                              <?php echo $readSupportAnswers["title"]; ?>
                            </a>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/support/quick-answers/edit/<?php echo $readSupportAnswers["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/support/quick-answers/delete/<?php echo $readSupportAnswers["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
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
                  <h2 class="header-title"><?php e__('Add Answer') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/support"><?php e__('Support') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/support/quick-answers"><?php e__('Answers') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Add Answer') ?></li>
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
            if (isset($_POST["insertSupportAnswers"])) {
              if (!$csrf->validate('insertSupportAnswers')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("title") == null || post("content") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else {
                $insertSupportAnswers = $db->prepare("INSERT INTO SupportAnswers (title, content) VALUES (?, ?)");
                $insertSupportAnswers->execute(array(post("title"), filteredContent($_POST["content"])));
                echo alertSuccess(t__('Answer has been added successfully!'));
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputTitle" class="col-sm-2 col-form-label"><?php e__('Title') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="<?php e__('Enter the answer title') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputContent" class="col-sm-2 col-form-label"><?php e__('Content') ?>:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="<?php e__('Enter the answer content') ?>."></textarea>
                  </div>
                </div>
                <?php echo $csrf->input('insertSupportAnswers'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertSupportAnswers"><?php e__('Add') ?></button>
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
      $supportAnswers = $db->prepare("SELECT * FROM SupportAnswers WHERE id = ?");
      $supportAnswers->execute(array(get("id")));
      $readSupportAnswers = $supportAnswers->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Edit Answer') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/support"><?php e__('Support') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/support/quick-answers"><?php e__('Answer') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/support/quick-answers"><?php e__('Edit Answer') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($supportAnswers->rowCount() > 0) ? $readSupportAnswers["title"] : "Bulunamadı!"; ?></li>
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
          <?php if ($supportAnswers->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateSupportAnswers"])) {
                if (!$csrf->validate('updateSupportAnswers')) {
                  echo alertError(t__('A terribly system error happened1'));
                }
                else if (post("title") == null || post("content") == null) {
                  echo alertError(t__('Please fill all the fields!'));
                }
                else {
                  $updateSupportAnswers = $db->prepare("UPDATE SupportAnswers SET title = ?, content = ? WHERE id = ?");
                  $updateSupportAnswers->execute(array(post("title"), filteredContent($_POST["content"]), get("id")));
                  echo alertSuccess(t__('Changes has been saved successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputTitle" class="col-sm-2 col-form-label"><?php e__('Title') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="<?php e__('Enter the answer title') ?>." value="<?php echo $readSupportAnswers["title"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputContent" class="col-sm-2 col-form-label"><?php e__('Content') ?>:</label>
                    <div class="col-sm-10">
                      <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="<?php e__('Enter the answer content') ?>."><?php echo $readSupportAnswers["content"]; ?></textarea>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateSupportAnswers'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/support/quick-answers/delete/<?php echo $readSupportAnswers["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateSupportAnswers"><?php e__('Save Changes') ?></button>
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
      $deleteSupportAnswer = $db->prepare("DELETE FROM SupportAnswers WHERE id = ?");
      $deleteSupportAnswer->execute(array(get("id")));
      go("/dashboard/support/quick-answers");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
