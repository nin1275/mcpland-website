<?php
  if (!checkPerm($readAdmin, 'MANAGE_ACCOUNTS')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  if (get("target") == 'account' && (get("action") == 'insert' || get("action") == 'update')) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/account.roles.js');
  }
?>
<?php if (get("target") == 'account'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Accounts') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Accounts') ?></li>
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
          <?php if ($readSettings["totalAccountCount"] > 0): ?>
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

              $itemsCount = $readSettings["totalAccountCount"];
              $pageCount = ceil($itemsCount/$limit);
              if ($page > $pageCount) {
                $page = 1;
              }
              $visibleItemsCount = $page * $limit - $limit;
              $accounts = $db->query("SELECT * FROM Accounts ORDER BY id DESC LIMIT $visibleItemsCount, $limit");

              if (isset($_POST["query"])) {
                if (post("query") != null) {
                  $accounts = $db->prepare("SELECT * FROM Accounts WHERE id = :searchEqual OR realname LIKE :search OR email LIKE :search ORDER BY id DESC");
                  $accounts->execute(array(
                    "search"      => '%'.post("query").'%',
                    "searchEqual" => post("query")
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
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="<?php e__('Search (User ID, Nickname or E-mail)') ?>" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success"><?php e__('Search') ?></button>
                      <a class="btn btn-sm btn-white" href="/dashboard/users/create"><?php e__('New Account') ?></a>
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
                        <th><?php e__('Nickname') ?></th>
                        <th><?php e__('E-mail') ?><th><?php e__('Credit') ?></th>
                        <th><?php e__('Role') ?></th>
                        <th><?php e__('Last Login') ?></th>
                        <th><?php e__('Creation Date') ?></th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($accounts as $readAccounts): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            <a href="/dashboard/users/view/<?php echo $readAccounts["id"]; ?>">
                              #<?php echo $readAccounts["id"]; ?>
                            </a>
                          </td>
                          <td>
                            <a href="/dashboard/users/view/<?php echo $readAccounts["id"]; ?>">
                              <?php echo $readAccounts["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php echo $readAccounts["email"]; ?>
                          </td>
                          <td>
                            <?php echo $readAccounts["credit"]; ?>
                          </td>
                          <td>
                            <?php echo styledRoles(getRoles($readAccounts["id"])); ?>
                          </td>
                          <td>
                            <?php if ($readAccounts["lastlogin"] == 0): ?>
                                <?php e__('Not Logged In') ?>
                            <?php else: ?>
                              <?php echo convertTime(date("Y-m-d H:i:s", ($readAccounts["lastlogin"]/1000)), 2, true); ?>
                            <?php endif; ?>
                          </td>
                          <td>
                            <?php if ($readAccounts["creationDate"] == "1000-01-01 00:00:00"): ?>
                                <?php e__('Unknown') ?>
                            <?php else: ?>
                              <?php echo convertTime($readAccounts["creationDate"], 2, true); ?>
                            <?php endif; ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/users/edit/<?php echo $readAccounts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/dashboard/users/view/<?php echo $readAccounts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-warning" href="/dashboard/bans/create/<?php echo $readAccounts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Block') ?>">
                              <i class="fe fe-slash"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-secondary" href="/dashboard/store/chest-item/send/<?php echo $readAccounts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Send Chest Item') ?>">
                              <i class="fe fe-archive"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-info" href="/dashboard/store/credit/send/<?php echo $readAccounts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Send Credit') ?>">
                              <i class="fe fe-dollar-sign"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/users/delete/<?php echo $readAccounts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
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
                    <a class="page-link" href="/dashboard/users/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/dashboard/users/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/users/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
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
                  <h2 class="header-title"><?php e__('Add an Account') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/users"><?php e__('Accounts') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Add an Account') ?></li>
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
            if (isset($_POST["insertAccounts"])) {
              if (!$csrf->validate('insertAccounts')) {
                echo alertError(t__("Something went wrong! Please try again later."));
              }
              else if (post("username") == null || post("email") == null) {
                echo alertError(t__("Please fill all the fields"));
              }
              else {
                $usernameValid = $db->prepare("SELECT * FROM Accounts WHERE realname = ?");
                $usernameValid->execute(array(post("username")));

                $emailValid = $db->prepare("SELECT * FROM Accounts WHERE email = ?");
                $emailValid->execute(array(post("email")));

                if (post("credit") == null) {
                  $_POST["credit"] = 0;
                }
                if ($readSettings["authStatus"] == 0) {
                  $_POST["authStatus"] = 0;
                }

                if (checkUsername(post("username"))) {
                  echo alertError(t__("Please enter a valid username"));
                }
                else if (strlen(post("username")) < 3) {
                  echo alertError(t__('Nickname must be more than 3 character.'));
                }
                else if (strlen(post("username")) > 16) {
                  echo alertError(t__("Nickname must be lesser than 16 characters."));
                }
                else if ($usernameValid->rowCount() > 0) {
                  echo alertError(t__('<strong>%username%</strong> is using by someone else', ['%username%' => post("username")]));
                }
                else if (checkEmail(post("email"))) {
                  echo alertError(t__("Please enter a valid email."));
                }
                else if ($emailValid->rowCount() > 0) {
                  echo alertError(t__('<strong>%email%</strong> is using by someone else', ['%email%' => post("email")]));
                }
                else if (strlen(post("password")) < 4) {
                  echo alertError(t__("Password shouldn't be less than 4 characters!"));
                }
                else if (post("password") != post("passwordRe")) {
                  echo alertError(t__("Password does not match!"));
                }
                else if (checkBadPassword(post("password"))) {
                  echo alertError(t__("Simply passwords cant be used"));
                }
                else {
                  $password = createPassword($readSettings["passwordType"], post("password"));
                  
                  $insertAccounts = $db->prepare("INSERT INTO Accounts (username, realname, email, password, credit, authStatus, creationIP, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                  $insertAccounts->execute(array(strtolower(post("username")), post("username"), post("email"), $password, post("credit"), post("authStatus"), getIP(), date("Y-m-d H:i:s")));
                  $accountID = $db->lastInsertId();
                  
                  if (checkPerm($readAdmin, 'MANAGE_ROLES')) {
                    if (isset($_POST["roleID"])) {
                      foreach ($_POST["roleID"] as $role) {
                        if ($role == 1) continue;
                        $addRoleToUser = $db->prepare("INSERT INTO AccountRoles (accountID, roleID) VALUES (?, ?)");
                        $addRoleToUser->execute(array($accountID, $role));
                      }
                    }
    
                    if (isset($_POST["permissions"])) {
                      foreach ($_POST["permissions"] as $permission) {
                        $permission = strip_tags($permission);
                        $addPermToUser = $db->prepare("INSERT INTO AccountPermissions (accountID, permissionID) VALUES (?, ?)");
                        $addPermToUser->execute(array($accountID, $permission));
                      }
                    }
                  }
                  
                  echo alertSuccess(t__("Account added successfully."));
                }
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputUsername" class="col-sm-2 col-form-label"><?php e__('Username') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputUsername" class="form-control" name="username" placeholder="<?php e__('Enter your minecraft username.') ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputUsername" class="col-sm-2 col-form-label"><?php e__('Email') ?>:</label>
                  <div class="col-sm-10">
                    <input type="email" id="inputEmail" class="form-control" name="email" placeholder="<?php e__('Enter your email.') ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputUsername" class="col-sm-2 col-form-label"><?php e__('Password') ?>:</label>
                  <div class="col-sm-10">
                    <input type="password" id="inputEmail" class="form-control" name="password" placeholder="<?php e__('Enter the password.') ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputUsername" class="col-sm-2 col-form-label"><?php e__('Re-Password') ?>:</label>
                  <div class="col-sm-10">
                    <input type="password" id="inputEmail" class="form-control" name="passwordRe" placeholder="<?php e__('Enter the password for safety.') ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputUsername" class="col-sm-2 col-form-label"><?php e__('Credit') ?>:</label>
                  <div class="col-sm-10">
                    <div class="input-group input-group-merge">
                      <input type="number" id="inputPrice" class="form-control form-control-prepended" name="credit" placeholder="<?php e__('Enter the credit amount.') ?>">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <span class="fa fa-coins"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php if ($readSettings["authStatus"] == 1): ?>
                  <div class="form-group row">
                    <label for="selectAuthStatus" class="col-sm-2 col-form-label">
                        <?php e__('2FA:') ?>
                      <a href="https://help.leaderos.net/google-authenticator" rel="external">
                        <i class="fa fa-question-circle text-primary" data-toggle="tooltip" data-placement="top" title="<?php e__('Two-Factor Authentication') ?>"></i>
                      </a>
                    </label>
                    <div class="col-sm-10">
                      <select id="selectAuthStatus" class="form-control" name="authStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0"><?php e__('Disabled') ?></option>
                        <option value="1"><?php e__('Active') ?></option>
                      </select>
                    </div>
                  </div>
                <?php endif; ?>
                <?php if (checkPerm($readAdmin, 'MANAGE_ROLES')): ?>
                  <div class="form-group row">
                    <label for="selectPermission" class="col-sm-2 col-form-label"><?php e__('Role') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectPermission" class="form-control" name="roleID[]" data-toggle="select" multiple>
                        <?php $roles = $db->query("SELECT * FROM Roles ORDER BY priority DESC"); ?>
                        <?php foreach ($roles as $role): ?>
                          <?php if ($role["id"] == 1): ?>
                            <option disabled selected><?php echo $role["name"]; ?></option>
                          <?php else: ?>
                            <?php
                            $rolePermissions = $db->prepare("SELECT permissionID FROM RolePermissions WHERE roleID = ?");
                            $rolePermissions->execute(array($role["id"]));
                            $rolePermissionList = implode(",", $rolePermissions->fetchAll(PDO::FETCH_COLUMN));
                            ?>
                            <option value="<?php echo $role["id"] ?>" data-permissions="<?php echo $rolePermissionList; ?>"><?php echo $role["name"]; ?></option>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div id="extraPermBlock" style="display: none;">
                    <div class="form-group row">
                      <label for="selectExtraPermission" class="col-sm-2 col-form-label"><?php e__('Extra Perm') ?>:</label>
                      <div class="col-sm-10">
                        <div class="row">
                          <?php $permissions = $db->query("SELECT * FROM Permissions"); ?>
                          <?php foreach ($permissions as $permission): ?>
                            <div class="col-sm-3">
                              <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="permissions[]" class="custom-control-input" id="permission_<?php echo $permission["id"] ?>" value="<?php echo $permission["id"] ?>">
                                <label class="custom-control-label" for="permission_<?php echo $permission["id"] ?>"><?php echo $permission["description"] ?></label>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <?php echo $csrf->input('insertAccounts'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertAccounts"><?php e__('Add') ?></button>
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
      $account = $db->prepare("SELECT * FROM Accounts WHERE id = ?");
      $account->execute(array(get("id")));
      $readAccount = $account->fetch();

      //Show unauthorized action message if not admin and admin tries to edit.
      if ($account->rowCount() > 0) {
        $readAccount["permissions"] = getPermissions($readAccount["id"]);
        if (!checkPerm($readAdmin, 'SUPER_ADMIN') && checkPerm($readAccount, 'SUPER_ADMIN')) {
          go('/dashboard/error/001');
        }
      }
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Edit Account') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/users"><?php e__('Accounts') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/users"><?php e__('Edit Account') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($account->rowCount() > 0) ? $readAccount["realname"] : t__("Not found"); ?></li>
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
          <?php if ($account->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateAccounts"])) {
                if (!$csrf->validate('updateAccounts')) {
                  echo alertError(t__("A terribly system error happened."));
                }
                else if (post("username") == null || post("email") == null) {
                  echo alertError(t__("Please fill all the fields!"));
                }
                else {
                  $usernameValid = $db->prepare("SELECT * FROM Accounts WHERE realname = ?");
                  $usernameValid->execute(array(post("username")));
                  $emailValid = $db->prepare("SELECT * FROM Accounts WHERE email = ?");
                  $emailValid->execute(array(post("email")));

                  if (post("credit") == null) {
                    $_POST["credit"] = $readAccount["credit"];
                  }

                  if (checkUsername(post("username"))) {
                    echo alertError(t__("Please enter a valid username"));
                  }
                  else if (strlen(post("username")) < 3) {
                    echo alertError(t__("Username should be more than 3 characters."));
                  }
                  else if (strlen(post("username")) > 16) {
                    echo alertError(t__("Username shouldn't be lesser than 16 characters."));
                  }
                  else if (post("username") != $readAccount["realname"] && $usernameValid->rowCount() > 0) {
                    echo alertError(t__("<strong>%username%</strong> is using by someone else.", ['%username%' => post("username")]));
                  }
                  else if (checkEmail(post("email"))) {
                    echo alertError(t__("Please enter a valid e-mail"));
                  }
                  else if (post("email") != $readAccount["email"] && $emailValid->rowCount() > 0) {
                    echo alertError(t__('<strong>%email%</strong> is using by someone else.', ['%email%' => post("email")]));
                  }
                  else if (strlen(post("password")) < 4 && (post("password") != null && post("passwordRe") != null)) {
                    echo alertError(t__("Password shouldn't be lesser than 4 characters!"));
                  }
                  else if (post("password") != post("passwordRe") && (post("password") != null && post("passwordRe") != null)) {
                    echo alertError(t__("Password does not match."));
                  }
                  else if (checkBadPassword(post("password")) && (post("password") != null && post("passwordRe") != null)) {
                    echo alertError(t__("You cant use simply passwords."));
                  }
                  else {
                    if (post("password") != null && post("passwordRe") != null) {
                      $password = createPassword($readSettings["passwordType"], post("password"));
                    }
                    else {
                      $password = $readAccount["password"];
                    }
                    if ((post("username") != $readAccount["realname"]) || (post("email") != $readAccount["email"]) || (post("password") != null && post("passwordRe") != null)) {
                      $deleteAccountSessions = $db->prepare("DELETE FROM AccountSessions WHERE accountID = ?");
                      $deleteAccountSessions->execute(array(get("id")));
                    }
                    $updateAccounts = $db->prepare("UPDATE Accounts SET username = ?, realname = ?, email = ?, password = ?, credit = ?, permission = ? WHERE id = ?");
                    $updateAccounts->execute(array(strtolower(post("username")), post("username"), post("email"), $password, post("credit"), post("permission"), get("id")));
                    if ($readSettings["authStatus"] == 1) {
                      if ($readAccount["authStatus"] == 1 && post("authStatus") == 0) {
                        $deleteAccountAuths = $db->prepare("DELETE FROM AccountAuths WHERE accountID = ?");
                        $deleteAccountAuths->execute(array(get("id")));
                      }
                      $updateAccountAuthStatus = $db->prepare("UPDATE Accounts SET authStatus = ? WHERE id = ?");
                      $updateAccountAuthStatus->execute(array(post("authStatus"), get("id")));
                    }
  
                    if (checkPerm($readAdmin, 'MANAGE_ROLES')) {
                      $removeRolesFromUser = $db->prepare("DELETE FROM AccountRoles WHERE accountID = ? AND expiryDate = ?");
                      $removeRolesFromUser->execute(array($readAccount["id"], '1000-01-01 00:00:00'));
                      if (isset($_POST["roleID"])) {
                        foreach ($_POST["roleID"] as $role) {
                          if ($role == 1) continue;
                          $addRoleToUser = $db->prepare("INSERT INTO AccountRoles (accountID, roleID) VALUES (?, ?)");
                          $addRoleToUser->execute(array($readAccount["id"], $role));
                        }
                      }
    
                      $removePermsFromUser = $db->prepare("DELETE FROM AccountPermissions WHERE accountID = ?");
                      $removePermsFromUser->execute(array($readAccount["id"]));
                      if (isset($_POST["permissions"])) {
                        foreach ($_POST["permissions"] as $permission) {
                          $permission = strip_tags($permission);
                          $addPermToUser = $db->prepare("INSERT INTO AccountPermissions (accountID, permissionID) VALUES (?, ?)");
                          $addPermToUser->execute(array($readAccount["id"], $permission));
                        }
                      }
                    }
                    
                    echo alertSuccess(t__("Changes has been saved successfully!"));
                  }
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputUsername" class="col-sm-2 col-form-label"><?php e__("Username") ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputUsername" class="form-control" name="username" placeholder="<?php e__("Enter your username.") ?>" value="<?php echo $readAccount["realname"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputEmail" class="col-sm-2 col-form-label"><?php e__("Email") ?>:</label>
                    <div class="col-sm-10">
                      <input type="email" id="inputEmail" class="form-control" name="email" placeholder="<?php e__("Enter your email.") ?>" value="<?php echo $readAccount["email"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPassword" class="col-sm-2 col-form-label"><?php e__("Password") ?>:</label>
                    <div class="col-sm-10">
                      <input type="password" id="inputPassword" class="form-control" name="password" placeholder="<?php e__("Enter the password.") ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPasswordRe" class="col-sm-2 col-form-label"><?php e__("Re-Password") ?>:</label>
                    <div class="col-sm-10">
                      <input type="password" id="inputPasswordRe" class="form-control" name="passwordRe" placeholder="<?php e__("Enter the password again for safety.") ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputCredit" class="col-sm-2 col-form-label"><?php e__("Credit") ?>:</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputCredit" class="form-control form-control-prepended" name="credit" placeholder="<?php e__("Enter the credit amount") ?>." value="<?php echo $readAccount["credit"]; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fa fa-coins"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php if ($readSettings["authStatus"] == 1): ?>
                    <div class="form-group row">
                      <label for="selectAuthStatus" class="col-sm-2 col-form-label">
                          <?php e__("2FA:") ?>
                        <a href="https://help.leaderos.net/google-authenticator" rel="external">
                          <i class="fa fa-question-circle text-primary" data-toggle="tooltip" data-placement="top" title="<?php e__("Two-Factor Authentication") ?>"></i>
                        </a>
                      </label>
                      <div class="col-sm-10">
                        <select id="selectAuthStatus" class="form-control" name="authStatus" data-toggle="select" data-minimum-results-for-search="-1">
                          <option value="0" <?php echo ($readAccount["authStatus"] == 0) ? 'selected="selected"' : null; ?>><?php e__("Disabled") ?></option>
                          <option value="1" <?php echo ($readAccount["authStatus"] == 1) ? 'selected="selected"' : null; ?>><?php e__("Active") ?></option>
                        </select>
                      </div>
                    </div>
                  <?php endif; ?>
                  <?php if (checkPerm($readAdmin, 'MANAGE_ROLES')): ?>
                    <div class="form-group row">
                      <label for="selectPermission" class="col-sm-2 col-form-label">Rol:</label>
                      <div class="col-sm-10">
                        <select id="selectPermission" class="form-control" name="roleID[]" data-toggle="select" multiple>
                          <?php
                            $accountRoles = $db->prepare("SELECT * FROM AccountRoles WHERE accountID = ?");
                            $accountRoles->execute(array($readAccount["id"]));
                            $accountRoleList = [];
                            foreach ($accountRoles as $accountRole) {
                              $accountRoleList[] = $accountRole["roleID"];
                            }
                          ?>
                          <?php $roles = $db->query("SELECT * FROM Roles ORDER BY priority DESC"); ?>
                          <?php foreach ($roles as $role): ?>
                            <?php if ($role["id"] == 1): ?>
                              <option disabled selected><?php echo $role["name"]; ?></option>
                            <?php else: ?>
                              <?php
                              $rolePermissions = $db->prepare("SELECT permissionID FROM RolePermissions WHERE roleID = ?");
                              $rolePermissions->execute(array($role["id"]));
                              $rolePermissionList = implode(",", $rolePermissions->fetchAll(PDO::FETCH_COLUMN));
                              ?>
                              <option value="<?php echo $role["id"] ?>" data-permissions="<?php echo $rolePermissionList; ?>" <?php echo (in_array($role["id"], $accountRoleList)) ? "selected" : null; ?>><?php echo $role["name"]; ?></option>
                            <?php endif; ?>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <?php
                    $accountPermissionList = [];
                    $accountPermissions = $db->prepare("SELECT permissionID FROM AccountPermissions WHERE accountID = ?");
                    $accountPermissions->execute(array($readAccount["id"]));
                    $accountPermissionList = $accountPermissions->fetchAll(PDO::FETCH_COLUMN);
                    ?>
                    <input type="hidden" id="accountExtraPermissions" value="<?php echo implode(",", $accountPermissionList); ?>">
                    <div id="extraPermBlock">
                      <div class="form-group row">
                        <label for="selectExtraPermission" class="col-sm-2 col-form-label">Ekstra Yetki:</label>
                        <div class="col-sm-10">
                          <div class="row">
                            <?php
                              $rolePermissionList = [];
                              $rolePermissions = $db->prepare("SELECT permissionID FROM RolePermissions WHERE roleID = ?");
                              foreach ($accountRoleList as $roleID) {
                                $rolePermissions->execute(array($roleID));
                                foreach ($rolePermissions as $readRolePermission) {
                                  $rolePermissionList[] = $readRolePermission["permissionID"];
                                }
                              }
                            ?>
                            <?php $permissions = $db->query("SELECT * FROM Permissions"); ?>
                            <?php foreach ($permissions as $permission): ?>
                              <div class="col-sm-3">
                                <div class="custom-control custom-checkbox">
                                  <input type="checkbox" name="permissions[]" class="custom-control-input" id="permission_<?php echo $permission["id"] ?>" value="<?php echo $permission["id"] ?>" <?php echo (in_array($permission["id"], $rolePermissionList)) ? "checked disabled" : ((in_array($permission["id"], $accountPermissionList)) ? "checked" : null); ?>>
                                  <label class="custom-control-label" for="permission_<?php echo $permission["id"] ?>"><?php echo $permission["description"] ?></label>
                                </div>
                              </div>
                            <?php endforeach; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>
                  <?php echo $csrf->input('updateAccounts'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/users/delete/<?php echo $readAccount["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete") ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/dashboard/users/view/<?php echo $readAccount["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("View") ?>">
                        <i class="fe fe-eye"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-warning" href="/dashboard/bans/create/<?php echo $readAccount["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Block") ?>">
                        <i class="fe fe-slash"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-secondary" href="/dashboard/store/chest-item/send/<?php echo $readAccount["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Send Chest Item") ?>">
                        <i class="fe fe-archive"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-info" href="/dashboard/store/credit/send/<?php echo $readAccount["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Send Credit") ?>">
                        <i class="fe fe-dollar-sign"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateAccounts"><?php e__("Save Changes") ?></button>
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
  <?php elseif (get("action") == 'get' && get("id")): ?>
    <?php
      $account = $db->prepare("SELECT * FROM Accounts WHERE id = :user OR realname = :user");
      $account->execute(array(
        'user' => get("id")
      ));
      $readAccount = $account->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("View an Account") ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard") ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/users"><?php e__("Accounts") ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/users"><?php e__("View an Account") ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($account->rowCount() > 0) ? $readAccount["realname"] : t__('Not found'); ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <?php if ($account->rowCount() > 0): ?>
          <?php $readAccount["roles"] = getRoles($readAccount["id"]); ?>
          <div class="col-md-4">
            <div class="card">
              <div class="card-img-profile">
                <a href="/profile">
                  <?php echo minecraftHead($readSettings["avatarAPI"], $readAccount["realname"], 70); ?>
                </a>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <label class="col-sm-5"><?php e__("Username") ?>:</label>
                  <label class="col-sm-7">
                    <?php echo $readAccount["realname"]; ?>
                  </label>
                </div>
                <div class="form-group row">
                  <label class="col-sm-5"><?php e__("Email") ?>:</label>
                  <label class="col-sm-7">
                    <?php echo $readAccount["email"]; ?>
                  </label>
                </div>
                <div class="form-group row">
                  <label class="col-sm-5"><?php e__("Role") ?>:</label>
                  <label class="col-sm-7">
                    <?php echo styledRoles($readAccount["roles"]); ?>
                  </label>
                </div>
                <div class="form-group row">
                  <label class="col-sm-5"><?php e__("Credit") ?>:</label>
                  <label class="col-sm-7">
                    <?php echo $readAccount["credit"]; ?>
                  </label>
                </div>
                <div class="form-group row">
                  <label class="col-sm-5"><?php e__("Last Login") ?>:</label>
                  <label class="col-sm-7">
                    <?php if ($readAccount["lastlogin"] == 0): ?>
                        <?php e__("Not Logged In") ?>
                    <?php else: ?>
                      <?php echo convertTime(date("Y-m-d H:i:s", ($readAccount["lastlogin"]/1000)), 2, true); ?>
                    <?php endif; ?>
                  </label>
                </div>
                <div class="form-group row">
                  <label class="col-sm-5"><?php e__("Creation Date") ?>:</label>
                  <label class="col-sm-7">
                    <?php if ($readAccount["creationDate"] == "1000-01-01 00:00:00"): ?>
                        <?php e__("Unknown") ?>
                    <?php else: ?>
                      <?php echo convertTime($readAccount["creationDate"], 2, true); ?>
                    <?php endif; ?>
                  </label>
                </div>
                <div class="form-group row">
                  <label class="col-sm-5"><?php e__("Ip Address") ?>:</label>
                  <label class="col-sm-7">
                    <?php if ($readAccount["creationIP"] == "127.0.0.1"): ?>
                        <?php e__("Unknown") ?>
                    <?php else: ?>
                      <?php echo $readAccount["creationIP"]; ?>
                    <?php endif; ?>
                  </label>
                </div>
                <?php if ($readSettings["authStatus"] == 1): ?>
                  <div class="form-group row">
                    <label class="col-sm-5">
                        <?php e__("2FA:") ?>
                      <a href="https://help.leaderos.net/google-authenticator" rel="external">
                        <i class="fa fa-question-circle text-primary" data-toggle="tooltip" data-placement="top" title="<?php e__("Two-Factor Authentication") ?>"></i>
                      </a>
                    </label>
                    <label class="col-sm-7">
                      <?php echo ($readAccount["authStatus"] == 0) ? t__("Disabled") : t__('Active'); ?>
                    </label>
                  </div>
                <?php endif; ?>
                <?php
                  $accountSocialMedia = $db->prepare("SELECT * FROM AccountSocialMedia WHERE accountID = ?");
                  $accountSocialMedia->execute(array($readAccount["id"]));
                  $readAccountSocialMedia = $accountSocialMedia->fetch();
                ?>
                <div class="form-group row">
                  <label class="col-sm-5"><?php e__("Skype") ?>:</label>
                  <label class="col-sm-7">
                    <?php if ($accountSocialMedia->rowCount() > 0): ?>
                      <?php echo (($readAccountSocialMedia["skype"] != '0') ? $readAccountSocialMedia["skype"] : "-"); ?>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </label>
                </div>
                <div class="form-group row">
                  <label class="col-sm-5"><?php e__("Discord") ?>:</label>
                  <label class="col-sm-7">
                    <?php if ($accountSocialMedia->rowCount() > 0): ?>
                      <?php echo (($readAccountSocialMedia["discord"] != '0') ? $readAccountSocialMedia["discord"] : "-"); ?>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </label>
                </div>
                <?php
                  $siteBannedAccountStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?) ORDER BY expiryDate DESC LIMIT 1");
                  $siteBannedAccountStatus->execute(array($readAccount["id"], 1, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
                  $readSiteBannedAccountStatus = $siteBannedAccountStatus->fetch();
                ?>
                <?php if ($siteBannedAccountStatus->rowCount() > 0): ?>
                  <div class="form-group row">
                    <label class="col-sm-5"><?php e__("Site Block") ?>:</label>
                    <label class="col-sm-7">
                      <?php echo ($readSiteBannedAccountStatus["expiryDate"] == '1000-01-01 00:00:00') ? t__('Lifetime') : getDuration($readSiteBannedAccountStatus["expiryDate"]). ' '. t__("day"); ?>
                    </label>
                  </div>
                <?php endif; ?>
                <?php
                  $supportBannedAccountStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?) ORDER BY expiryDate DESC LIMIT 1");
                  $supportBannedAccountStatus->execute(array($readAccount["id"], 2, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
                  $readSupportBannedAccountStatus = $supportBannedAccountStatus->fetch();
                ?>
                <?php if ($supportBannedAccountStatus->rowCount() > 0): ?>
                  <div class="form-group row">
                    <label class="col-sm-5"><?php e__("Support Block") ?>:</label>
                    <label class="col-sm-7">
                      <?php echo ($readSupportBannedAccountStatus["expiryDate"] == '1000-01-01 00:00:00') ? t__('Lifetime') : getDuration($readSupportBannedAccountStatus["expiryDate"]).' '. t__("day"); ?>
                    </label>
                  </div>
                <?php endif; ?>
                <?php
                  $commentBannedAccountStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?) ORDER BY expiryDate DESC LIMIT 1");
                  $commentBannedAccountStatus->execute(array($readAccount["id"], 3, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
                  $readCommentBannedAccountStatus = $commentBannedAccountStatus->fetch();
                ?>
                <?php if ($commentBannedAccountStatus->rowCount() > 0): ?>
                  <div class="form-group row">
                    <label class="col-sm-5"><?php e__("Comment Block") ?>:</label>
                    <label class="col-sm-7">
                      <?php echo ($readCommentBannedAccountStatus["expiryDate"] == '1000-01-01 00:00:00') ? t__('Lifetime') : getDuration($readCommentBannedAccountStatus["expiryDate"]).' '.t__("day"); ?>
                    </label>
                  </div>
                <?php endif; ?>
                <div class="row justify-content-between">
                  <div class="col-md-12 mb-3">
                    <a class="btn btn-success w-100" href="/dashboard/users/edit/<?php echo $readAccount["id"]; ?>"><?php e__("Edit") ?></a>
                  </div>
                  <div class="col-md-12 mb-3">
                    <a class="btn btn-secondary w-100" href="/dashboard/store/chest-item/send/<?php echo $readAccount["id"]; ?>"><?php e__("Send Chest Item") ?></a>
                  </div>
                  <div class="col-md-12 mb-3">
                    <a class="btn btn-info w-100" href="/dashboard/store/credit/send/<?php echo $readAccount["id"]; ?>"><?php e__("Send Credit") ?></a>
                  </div>
                  <div class="col-md-6 btn-account-ban">
                    <a class="btn btn-warning w-100" href="/dashboard/bans/create/<?php echo $readAccount["id"]; ?>"><?php e__("Block") ?></a>
                  </div>
                  <div class="col-md-6 btn-account-delete">
                    <a class="btn btn-danger clickdelete w-100" href="/dashboard/users/delete/<?php echo $readAccount["id"]; ?>"><?php e__("Delete") ?></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <?php
              $statServers = $db->query("SELECT serverName, serverSlug FROM Leaderboards");
              $statServers->execute();
            ?>
            <?php if ($statServers->rowCount() > 0): ?>
              <div class="card">
                <div class="card-body p-0">
                  <nav>
                    <div class="nav nav-tabs nav-fill">
                      <?php foreach ($statServers as $readStatServers): ?>
                        <?php
                          if (!get("siralama")) {
                            $_GET["siralama"] = $readStatServers["serverSlug"];
                          }
                        ?>
                        <a class="nav-item nav-link <?php echo (get("siralama") == $readStatServers["serverSlug"]) ? t__('active') : null; ?>" id="nav-<?php echo $readStatServers["serverSlug"]; ?>-tab" href="?siralama=<?php echo $readStatServers["serverSlug"]; ?>">
                          <?php echo $readStatServers["serverName"]; ?>
                        </a>
                      <?php endforeach; ?>
                    </div>
                  </nav>
                  <div class="tab-content" id="nav-tabContent">
                    <?php
                      $statServer = $db->query("SELECT * FROM Leaderboards");
                      $statServer->execute();
                    ?>
                    <?php foreach ($statServer as $readStatServer): ?>
                      <?php
                        $usernameColumn = $readStatServer["usernameColumn"];
                        $mysqlTable = $readStatServer["mysqlTable"];
                        $sorter = $readStatServer["sorter"];
                        $tableTitles = $readStatServer["tableTitles"];
                        $tableData = $readStatServer["tableData"];
                        $tableTitlesArray = explode(",", $tableTitles);
                        $tableDataArray = explode(",", $tableData);

                        if ($readStatServer["mysqlServer"] == '0') {
                          $accountOrder = $db->prepare("SELECT $usernameColumn,$tableData FROM $mysqlTable WHERE $usernameColumn = ? ORDER BY $sorter DESC LIMIT 1");
                          $accountOrder->execute(array($readAccount["realname"]));
                        }
                        else {
                          try {
                            $newDB = new PDO("mysql:host=".$readStatServer["mysqlServer"]."; port=".$readStatServer["mysqlPort"]."; dbname=".$readStatServer["mysqlDatabase"]."; charset=utf8", $readStatServer["mysqlUsername"], $readStatServer["mysqlPassword"]);
                          }
                          catch (PDOException $e) {
                            die("<strong>MySQL connection error:</strong> ".utf8_encode($e->getMessage()));
                          }
                          $accountOrder = $newDB->prepare("SELECT $usernameColumn,$tableData FROM $mysqlTable WHERE $usernameColumn = ? ORDER BY $sorter DESC LIMIT 1");
                          $accountOrder->execute(array($readAccount["realname"]));
                        }
                      ?>
                      <div class="tab-pane fade <?php echo (get("siralama") == $readStatServer["serverSlug"]) ? t__('show active') : null; ?>" id="nav-<?php echo $readStatServer["serverSlug"] ?>">
                        <?php if ($accountOrder->rowCount() > 0): ?>
                          <div class="table-responsive">
                            <table class="table table-sm table-nowrap card-table">
                              <thead>
                                <tr>
                                  <th class="text-center" style="width: 40px;"><?php e__("Rank") ?></th>
                                  <th class="text-center" style="width: 20px;">#</th>
                                  <th><?php e__("Username") ?></th>
                                  <?php
                                    foreach ($tableTitlesArray as $readTableTitles) {
                                      echo '<th class="text-center">'.$readTableTitles.'</th>';
                                    }
                                  ?>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($accountOrder as $readAccountOrder): ?>
                                  <tr>
                                    <td class="text-center" style="width: 40px;">
                                      <?php
                                        if ($readStatServer["mysqlServer"] == '0') {
                                          $userPosition = $db->prepare("SELECT $usernameColumn FROM $mysqlTable ORDER BY $sorter DESC");
                                          $userPosition->execute();
                                        }
                                        else {
                                          $userPosition = $newDB->prepare("SELECT $usernameColumn FROM $mysqlTable ORDER BY $sorter DESC");
                                          $userPosition->execute();
                                        }
                                      ?>
                                      <?php $rank = 1; ?>
                                      <?php foreach ($userPosition as $readUserPosition): ?>
                                        <?php if ($readUserPosition[$usernameColumn] == $readAccount["realname"]): ?>
                                          <?php if ($rank == 1): ?>
                                            <strong class="text-success">1</strong>
                                          <?php elseif ($rank == 2): ?>
                                            <strong class="text-warning">2</strong>
                                          <?php elseif ($rank == 3): ?>
                                            <strong class="text-danger">3</strong>
                                          <?php else: ?>
                                            <?php echo $rank; ?>
                                          <?php endif; ?>
                                          <?php break; ?>
                                        <?php endif; ?>
                                        <?php $rank++; ?>
                                      <?php endforeach; ?>
                                    </td>
                                    <td class="text-center" style="width: 20px;">
                                      <?php echo minecraftHead($readSettings["avatarAPI"], $readAccount["realname"], 20); ?>
                                    </td>
                                    <td>
                                      <?php echo $readAccount["realname"]; ?>
                                    </td>
                                    <?php foreach ($tableDataArray as $readTableData): ?>
                                      <td class="text-center"><?php echo $readAccountOrder[$readTableData]; ?></td>
                                    <?php endforeach; ?>
                                  </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                          </div>
                        <?php else: ?>
                          <div class="p-4"><?php echo alertError(t__('Theres no data for this page!'), false); ?></div>
                        <?php endif; ?>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <?php
              $chests = $db->prepare("SELECT C.*, P.name as productName, S.name as serverName FROM Chests C INNER JOIN Products P ON C.productID = P.id INNER JOIN Servers S ON P.serverID = S.id WHERE C.accountID = ? AND C.status = ? ORDER BY C.id DESC");
              $chests->execute(array($readAccount["id"], 0));
            ?>
            <div class="card">
              <div class="card-header">
                  <?php e__("Chest") ?> (<?php echo $chests->rowCount(); ?>)
              </div>
              <div class="card-body p-0">
                <?php if ($chests->rowCount() > 0): ?>
                  <div class="table-responsive" <?php echo ($chests->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                    <table class="table table-sm table-nowrap card-table">
                      <thead>
                        <tr>
                          <th class="text-center" style="width: 40px;">#ID</th>
                          <th><?php e__("Product") ?></th>
                          <th><?php e__("Server") ?></th>
                          <th><?php e__("Date") ?></th>
                          <th class="text-center">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($chests as $readChests): ?>
                          <tr>
                            <td class="text-center" style="width: 40px;">
                              #<?php echo $readChests["id"]; ?>
                            </td>
                            <td>
                              <?php echo $readChests["productName"]; ?>
                            </td>
                            <td>
                              <?php echo $readChests["serverName"]; ?>
                            </td>
                            <td>
                              <?php echo convertTime($readChests["creationDate"], 2, true); ?>
                            </td>
                            <td class="text-center">
                              <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/chest/delete/<?php echo $readChests["id"]; ?>/<?php echo $readAccount["id"]; ?>" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php e__("Delete") ?>">
                                <i class="fe fe-trash-2"></i>
                              </a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php else: ?>
                  <div class="p-4"><?php echo alertError(t__('Theres no storage data for this user!'), false); ?></div>
                <?php endif; ?>
              </div>
            </div>

            <div class="card">
              <div class="card-body p-0">
                <nav>
                  <div class="nav nav-tabs nav-fill" id="nav-profile-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-support-tab" data-toggle="tab" href="#nav-support" role="tab" aria-controls="nav-support" aria-selected="true"><?php e__("Support Messages") ?></a>
                    <a class="nav-item nav-link" id="nav-credit-history-tab" data-toggle="tab" href="#nav-credit-history" role="tab" aria-controls="nav-credit-history" aria-selected="false"><?php e__("Credit History") ?></a>
                    <a class="nav-item nav-link" id="nav-store-history-tab" data-toggle="tab" href="#nav-store-history" role="tab" aria-controls="nav-store-history" aria-selected="false"><?php e__("Store History") ?></a>
                  </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                  <div class="tab-pane fade show active" id="nav-support" role="tabpanel" aria-labelledby="nav-support-tab">
                    <?php
                      $supports = $db->prepare("SELECT S.*, SC.name as categoryName, Se.name as serverName FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id WHERE S.accountID = ? ORDER BY S.updateDate DESC LIMIT 50");
                      $supports->execute(array($readAccount["id"]));
                    ?>
                    <?php if ($supports->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($supports->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-sm table-nowrap card-table">
                          <thead>
                            <tr>
                              <th class="text-center" style="width: 40px;">ID</th>
                              <th><?php e__("Title") ?></th>
                              <th><?php e__("Category") ?></th>
                              <th><?php e__("Last Updated") ?></th>
                              <th class="text-center"><?php e__("Status") ?></th>
                              <th class="text-center">&nbsp;</th>

                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($supports as $readSupports): ?>
                              <tr>
                                <td class="text-center" style="width: 40px;">
                                  <a href="/dashboard/support/view/<?php echo $readSupports["id"]; ?>/">
                                    #<?php echo $readSupports["id"]; ?>
                                  </a>
                                </td>
                                <td>
                                  <a href="/dashboard/support/view/<?php echo $readSupports["id"]; ?>/">
                                    <?php echo $readSupports["title"]; ?>
                                  </a>
                                </td>
                                <td>
                                  <?php echo $readSupports["categoryName"]; ?>
                                </td>
                                <td>
                                  <?php echo convertTime($readSupports["updateDate"]); ?>
                                </td>
                                <td class="text-center">
                                  <?php if ($readSupports["statusID"] == 1): ?>
                                    <span class="badge badge-pill badge-info"><?php e__("Open") ?></span>
                                  <?php elseif ($readSupports["statusID"] == 2): ?>
                                    <span class="badge badge-pill badge-success"><?php e__("Answered") ?></span>
                                  <?php elseif ($readSupports["statusID"] == 3): ?>
                                    <span class="badge badge-pill badge-warning"><?php e__("User-Reply") ?></span>
                                  <?php elseif ($readSupports["statusID"] == 4): ?>
                                    <span class="badge badge-pill badge-danger"><?php e__("Closed") ?></span>
                                  <?php else: ?>
                                    <span class="badge badge-pill badge-danger"><?php e__("Error!") ?></span>
                                  <?php endif; ?>
                                </td>
                                <td class="text-center">
                                  <a class="btn btn-sm btn-rounded-circle btn-primary" href="/dashboard/support/view/<?php echo $readSupports["id"]; ?>/" data-toggle="tooltip" data-placement="top" title="<?php e__("Read the Message") ?>">
                                    <i class="fa fa-eye"></i>
                                  </a>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError(t__("Theres no support message for this user!"), false); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="tab-pane fade" id="nav-credit-history" role="tabpanel" aria-labelledby="nav-credit-history-tab">
                    <?php
                      $creditHistory = $db->prepare("SELECT * FROM CreditHistory CH WHERE accountID = ? AND paymentStatus = ? ORDER BY id DESC LIMIT 50");
                      $creditHistory->execute(array($readAccount["id"], 1));
                    ?>
                    <?php if ($creditHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($creditHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-sm table-nowrap card-table">
                          <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th class="text-center"><?php e__("Amount") ?></th>
                              <th class="text-center"><?php e__("Payment") ?></th>
                              <th><?php e__("Date") ?></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($creditHistory as $readCreditHistory): ?>
                              <tr>
                                <td class="text-center">#<?php echo $readCreditHistory["id"]; ?></td>
                                <td class="text-center"><?php echo ($readCreditHistory["type"] == 3 || $readCreditHistory["type"] == 5) ? '<span class="text-danger">-'.$readCreditHistory["price"].'</span>' : '<span class="text-success">+'.$readCreditHistory["price"].'</span>'; ?></td>
                                <td class="text-center">
                                  <?php if ($readCreditHistory["type"] == 1): ?>
                                    <i class="fa fa-mobile" data-toggle="tooltip" data-placement="top" title="<?php e__("Mobile Payment") ?>"></i>
                                  <?php elseif ($readCreditHistory["type"] == 2): ?>
                                    <i class="fa fa-credit-card" data-toggle="tooltip" data-placement="top" title="<?php e__("Credit Card Payment") ?>"></i>
                                  <?php elseif ($readCreditHistory["type"] == 3): ?>
                                    <i class="fa fa-paper-plane" data-toggle="tooltip" data-placement="top" title="<?php e__("Transfer (Sender)") ?>"></i>
                                  <?php elseif ($readCreditHistory["type"] == 4): ?>
                                    <i class="fa fa-paper-plane" data-toggle="tooltip" data-placement="top" title="<?php e__("Transfer (Receiver)") ?>"></i>
                                  <?php elseif ($readCreditHistory["type"] == 5): ?>
                                    <i class="fa fa-ticket-alt" data-toggle="tooltip" data-placement="top" title="<?php e__("Wheel of Fortune (Ticket)") ?>"></i>
                                  <?php elseif ($readCreditHistory["type"] == 6): ?>
                                    <i class="fa fa-ticket-alt" data-toggle="tooltip" data-placement="top" title="<?php e__("Wheel of Fortune (Earning)") ?>"></i>
                                  <?php else: ?>
                                    <i class="fa fa-paper-plane"></i>
                                  <?php endif; ?>
                                </td>
                                <td><?php echo convertTime($readCreditHistory["creationDate"], 2, true); ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError(t__("Theres no credit history for this user!"), false); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="tab-pane fade" id="nav-store-history" role="tabpanel" aria-labelledby="nav-store-history-tab">
                    <?php
                      $storeHistory = $db->prepare("SELECT * FROM Orders WHERE accountID = ? ORDER BY id DESC LIMIT 50");
                      $storeHistory->execute(array($readAccount["id"]));
                    ?>
                    <?php if ($storeHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($storeHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-sm table-nowrap card-table">
                          <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th><?php e__("Amount") ?></th>
                              <th><?php e__("Date") ?></th>
                              <th class="text-right">&nbsp;</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($storeHistory as $readStoreHistory): ?>
                              <tr>
                                <td class="text-center">#<?php echo $readStoreHistory["id"]; ?></td>
                                <td>
                                  <?php e__('%credit% credit(s)', ['%credit%' => $readStoreHistory["subtotal"]]); ?>
                                </td>
                                <td><?php echo convertTime($readStoreHistory["creationDate"], 2, true); ?></td>
                                <td class="text-right">
                                  <a class="btn btn-sm btn-rounded-circle btn-primary" href="/dashboard/store/store-logs/view/<?php echo $readStoreHistory["id"]; ?>/" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                                    <i class="fa fa-eye"></i>
                                  </a>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError(t__("Theres no story history for this user!"), false); ?></div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-body p-0">
                <nav>
                  <div class="nav nav-tabs nav-fill" id="nav-profile-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-lottery-history-tab" data-toggle="tab" href="#nav-lottery-history" role="tab" aria-controls="nav-lottery-history" aria-selected="false"><?php e__("Wheel of Fortune History") ?></a>
                    <a class="nav-item nav-link" id="nav-gift-history-tab" data-toggle="tab" href="#nav-gift-history" role="tab" aria-controls="nav-gift-history" aria-selected="false"><?php e__("Gift History") ?></a>
                    <a class="nav-item nav-link" id="nav-chest-history-tab" data-toggle="tab" href="#nav-chest-history" role="tab" aria-controls="nav-chest-history" aria-selected="false"><?php e__("Storage History") ?></a>
                  </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                  <div class="tab-pane fade show active" id="nav-lottery-history" role="tabpanel" aria-labelledby="nav-lottery-history-tab">
                    <?php
                      $lotteryHistory = $db->prepare("SELECT LH.*, L.title as lotteryTitle, LA.title, LA.awardType, LA.award FROM LotteryHistory LH INNER JOIN LotteryAwards LA ON LH.lotteryAwardID = LA.id INNER JOIN Lotteries L ON LA.lotteryID = L.id WHERE LH.accountID = ? AND LA.awardType != ? ORDER by LH.id DESC LIMIT 50");
                      $lotteryHistory->execute(array($readAccount["id"], 3));
                    ?>
                    <?php if ($lotteryHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($lotteryHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-sm table-nowrap card-table">
                          <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th><?php e__("Wheel of Fortune") ?></th>
                              <th><?php e__("Prize") ?></th>
                              <th><?php e__("Date") ?></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($lotteryHistory as $readLotteryHistory): ?>
                              <tr>
                                <td class="text-center">#<?php echo $readLotteryHistory["id"]; ?></td>
                                <td>
                                  <?php echo $readLotteryHistory["lotteryTitle"]; ?>
                                </td>
                                <td>
                                  <?php echo $readLotteryHistory["title"]; ?>
                                </td>
                                <td><?php echo convertTime($readLotteryHistory["creationDate"], 2, true); ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError(t__("Theres no wheel of fortune for this user!"), false); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="tab-pane fade" id="nav-gift-history" role="tabpanel" aria-labelledby="nav-gift-history-tab">
                    <?php
                      $giftHistory = $db->prepare("SELECT PGH.*, PG.name, PG.giftType, PG.gift FROM ProductGiftsHistory PGH INNER JOIN ProductGifts PG ON PGH.giftID = PG.id WHERE PGH.accountID = ? ORDER by PGH.id DESC LIMIT 50");
                      $giftHistory->execute(array($readAccount["id"]));
                    ?>
                    <?php if ($giftHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($giftHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-sm table-nowrap card-table">
                          <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th><?php e__("Code") ?></th>
                              <th><?php e__("Gift") ?></th>
                              <th><?php e__("Date") ?></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($giftHistory as $readGiftHistory): ?>
                              <tr>
                                <td class="text-center">#<?php echo $readGiftHistory["id"]; ?></td>
                                <td>
                                  <?php echo $readGiftHistory["name"]; ?>
                                </td>
                                <td>
                                  <?php if ($readGiftHistory["giftType"] == 1): ?>
                                    <?php
                                      $product = $db->prepare("SELECT name FROM Products WHERE id = ?");
                                      $product->execute(array($readGiftHistory["gift"]));
                                      $readProduct = $product->fetch();
                                      echo $readProduct["name"];
                                    ?>
                                  <?php else: ?>
                                    <?php echo $readGiftHistory["gift"]; ?> <?php e__("credit") ?>
                                  <?php endif; ?>
                                </td>
                                <td><?php echo convertTime($readGiftHistory["creationDate"], 2, true); ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError(t__("Theres no gift history for this user!"), false); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="tab-pane fade" id="nav-chest-history" role="tabpanel" aria-labelledby="nav-chest-history-tab">
                    <?php
                      $chestsHistory = $db->prepare("SELECT CH.*, P.name as productName, S.name as serverName FROM ChestsHistory CH INNER JOIN Chests C ON CH.chestID = C.id INNER JOIN Products P ON C.productID = P.id INNER JOIN Servers S ON P.serverID = S.id WHERE CH.accountID = ? ORDER BY CH.id DESC LIMIT 50");
                      $chestsHistory->execute(array($readAccount["id"]));
                    ?>
                    <?php if ($chestsHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($chestsHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-sm table-nowrap card-table">
                          <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th><?php e__("Product") ?></th>
                              <th><?php e__("Server") ?></th>
                              <th class="text-center"><?php e__("Process") ?></th>
                              <th><?php e__("Date") ?></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($chestsHistory as $readChestsHistory): ?>
                              <tr>
                                <td class="text-center">
                                  #<?php echo $readChestsHistory["id"]; ?>
                                </td>
                                <td><?php echo $readChestsHistory["productName"]; ?></td>
                                <td><?php echo $readChestsHistory["serverName"]; ?></td>
                                <td class="text-center">
                                  <?php if ($readChestsHistory["type"] == 1): ?>
                                    <i class="fa fa-check" data-toggle="tooltip" data-placement="top" title="<?php e__("Delivery") ?>"></i>
                                  <?php elseif ($readChestsHistory["type"] == 2): ?>
                                    <i class="fa fa-gift" data-toggle="tooltip" data-placement="top" title="<?php e__("Gift (Sender)") ?>"></i>
                                  <?php elseif ($readChestsHistory["type"] == 3): ?>
                                    <i class="fa fa-gift" data-toggle="tooltip" data-placement="top" title="<?php e__("Gift (Receiver)") ?>"></i>
                                  <?php else: ?>
                                    <i class="fa fa-check"></i>
                                  <?php endif; ?>
                                </td>
                                <td><?php echo convertTime($readChestsHistory["creationDate"], 2, true); ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError(t__("Theres no storage history for this user!"), false); ?></div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
  
            <div class="card">
              <div class="card-body p-0">
                <nav>
                  <div class="nav nav-tabs nav-fill" role="tablist">
                    <a class="nav-item nav-link active" href="#!" role="tab"><?php e__('Bazaar History') ?></a>
                  </div>
                </nav>
                <div class="tab-content">
                  <div class="tab-pane fade show active" id="nav-bazaar-history" role="tabpanel" aria-labelledby="nav-lottery-bazaar-tab">
                    <?php
                      $bazaarHistory = $db->prepare("SELECT BH.*, BI.name as itemName, BI.price as itemPrice FROM BazaarHistory BH INNER JOIN BazaarItems BI ON BH.itemID = BI.id WHERE BH.accountID = ? ORDER BY BH.id DESC LIMIT 50");
                      $bazaarHistory->execute(array($readAccount["id"]));
                    ?>
                    <?php if ($bazaarHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($bazaarHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-sm table-nowrap card-table">
                          <thead>
                          <tr>
                            <th class="text-center">ID</th>
                            <th><?php e__('Product') ?></th>
                            <th><?php e__('Price') ?></th>
                            <th class="text-center"><?php e__('Type') ?></th>
                            <th><?php e__('Date') ?></th>
                          </tr>
                          </thead>
                          <tbody>
                          <?php foreach ($bazaarHistory as $readBazaarHistory): ?>
                            <tr>
                              <td class="text-center">#<?php echo $readBazaarHistory["id"]; ?></td>
                              <td>
                                <?php echo $readBazaarHistory["itemName"]; ?>
                              </td>
                              <td>
                                <?php echo $readBazaarHistory["itemPrice"]; ?>
                              </td>
                              <td class="text-center">
                                <?php if ($readBazaarHistory["type"] == 0): ?>
                                  <span class="text-danger" data-toggle="tooltip" data-placement="top" title="<?php e__('Purchase') ?>">-<i class="fa fa-coins"></i></span>
                                <?php elseif ($readBazaarHistory["type"] == 1): ?>
                                  <span class="text-success" data-toggle="tooltip" data-placement="top" title="<?php e__('Sell') ?>">+<i class="fa fa-coins"></i></span>
                                <?php else: ?>
                                  <i class="fa fa-check"></i>
                                <?php endif; ?>
                              </td>
                              <td><?php echo convertTime($readBazaarHistory["creationDate"], 2, true); ?></td>
                            </tr>
                          <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError(t__('Theres no bazaar history for this user!'), false); ?></div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            <?php
              $applications = $db->prepare("SELECT AP.id, AF.title, AP.reason, AP.status FROM Applications AP INNER JOIN Accounts A ON A.id = AP.accountID INNER JOIN ApplicationForms AF ON AF.id = AP.formID WHERE AP.accountID = ? ORDER BY AP.id DESC LIMIT 50");
              $applications->execute(array($readAccount["id"]));
            ?>
            <?php if ($applications->rowCount() > 0): ?>
              <div class="card">
                <div class="card-body p-0">
                  <nav>
                    <div class="nav nav-tabs nav-fill" role="tablist">
                      <a class="nav-item nav-link active" href="#!" role="tab"><?php e__('Applications') ?></a>
                    </div>
                  </nav>
                  <div class="tab-content">
                    <div class="tab-pane fade show active" role="tabpanel">
                      <div class="table-responsive" <?php echo ($applications->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-sm table-nowrap card-table">
                          <thead>
                          <tr>
                            <th class="text-center" style="width: 40px;">ID</th>
                            <th><?php e__('Form') ?></th>
                            <th><?php e__('Reason') ?></th>
                            <th class="text-center"><?php e__('Status') ?></th>
                            <th class="text-right"> </th>
                          </tr>
                          </thead>
                          <tbody>
                          <?php foreach ($applications as $readApplications): ?>
                            <tr>
                              <td class="text-center" style="width: 40px;">
                                <a href="/applications/<?php echo $readApplications["id"]; ?>">
                                  #<?php echo $readApplications["id"]; ?>
                                </a>
                              </td>
                              <td>
                                <a href="/applications/<?php echo $readApplications["id"]; ?>">
                                  <?php echo $readApplications["title"]; ?>
                                </a>
                              </td>
                              <td>
                                <?php echo ($readApplications["reason"] == '') ? '-' : $readApplications["reason"]; ?>
                              </td>
                              <td class="text-center">
                                <?php if ($readApplications["status"] == 0): ?>
                                  <span class="badge badge-pill badge-danger"><?php e__('Rejeceted') ?></span>
                                <?php elseif ($readApplications["status"] == 1): ?>
                                  <span class="badge badge-pill badge-success"><?php e__('Approved') ?></span>
                                <?php elseif ($readApplications["status"] == 2): ?>
                                  <span class="badge badge-pill badge-warning"><?php e__('Pending Approval') ?></span>
                                <?php else: ?>
                                  <span class="badge badge-pill badge-danger"><?php e__('Error!') ?></span>
                                <?php endif; ?>
                              </td>
                              <td class="text-right">
                                <a class="btn btn-primary btn-sm btn-rounded-circle" href="/dashboard/applications/view/<?php echo $readApplications["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                                  <i class="fe fe-eye"></i>
                                </a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="col-md-12"><?php echo alertError(t__("Theres no data for this page!")) ?></div>
        <?php endif; ?>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      if ($readAdmin["id"] == get("id")) {
        go('/dashboard/error/101');
      }
      else {
        if (!checkPerm($readAdmin, 'SUPER_ADMIN')) {
          go('/dashboard/error/001');
        }
        else {
          $deleteAccount = $db->prepare("DELETE FROM Accounts WHERE id = ?");
          $deleteAccount->execute(array(get("id")));
          go("/dashboard/users");
        }
      }
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'authorized'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("Staff Accounts") ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard") ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Staff Accounts") ?></li>
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
            $viewDashboardPerm = $db->prepare("SELECT id FROM Permissions WHERE name = ?");
            $viewDashboardPerm->execute(array('VIEW_DASHBOARD'));
            $readViewDashboardPerm = $viewDashboardPerm->fetch();
    
            $superAdminPerm = $db->prepare("SELECT id FROM Permissions WHERE name = ?");
            $superAdminPerm->execute(array('SUPER_ADMIN'));
            $readSuperAdminPerm = $superAdminPerm->fetch();
          ?>
          <?php if ($viewDashboardPerm->rowCount() > 0 && $superAdminPerm->rowCount() > 0): ?>
            <?php
              $accounts = $db->prepare("SELECT A.*, GROUP_CONCAT(R.name) as roles FROM Accounts A LEFT JOIN AccountRoles AR ON AR.accountID = A.id INNER JOIN Roles R ON AR.roleID = R.id INNER JOIN RolePermissions RP ON RP.roleID = R.id LEFT JOIN AccountPermissions AP ON AP.accountID = A.id WHERE AP.permissionID IN (:superperm, :viewperm) OR RP.permissionID IN (:superperm, :viewperm) GROUP BY A.id");
              $accounts->execute(array(
                "viewperm" => $readViewDashboardPerm["id"],
                "superperm" => $readSuperAdminPerm["id"]
              ));
            ?>
            <?php if ($accounts->rowCount() > 0): ?>
              <div class="card" data-toggle="lists" data-lists-values='["accountID", "accountRealname", "accountEmail", "accountCredit", "accountPermission", "accountLastLogin", "accountCreationDate"]'>
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
                          <a href="#" class="text-muted sort" data-sort="accountID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="accountRealname">
                            <?php e__("Username") ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="accountEmail">
                            <?php e__("Email") ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="accountCredit">
                            <?php e__("Credit") ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="accountPermission">
                            <?php e__("Role") ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="accountLastLogin">
                            <?php e__("Last Login") ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="accountCreationDate">
                            <?php e__("Date") ?>
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                      </thead>
                      <tbody class="list">
                      <?php foreach ($accounts as $readAccounts): ?>
                        <tr>
                          <td class="accountID text-center" style="width: 40px;">
                            <a href="/dashboard/users/view/<?php echo $readAccounts["id"]; ?>">
                              #<?php echo $readAccounts["id"]; ?>
                            </a>
                          </td>
                          <td class="accountRealname">
                            <a href="/dashboard/users/view/<?php echo $readAccounts["id"]; ?>">
                              <?php echo $readAccounts["realname"]; ?>
                            </a>
                          </td>
                          <td class="accountEmail">
                            <?php echo $readAccounts["email"]; ?>
                          </td>
                          <td class="accountCredit">
                            <?php echo $readAccounts["credit"]; ?>
                          </td>
                          <td class="accountPermission">
                            <?php echo $readAccounts["roles"]; ?>
                          </td>
                          <td class="accountLastLogin">
                            <?php if ($readAccounts["lastlogin"] == 0): ?>
                              <?php e__("Not Logged In") ?>
                            <?php else: ?>
                              <?php echo convertTime(date("Y-m-d H:i:s", ($readAccounts["lastlogin"]/1000)), 2, true); ?>
                            <?php endif; ?>
                          </td>
                          <td class="accountCreationDate">
                            <?php echo convertTime($readAccounts["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/users/edit/<?php echo $readAccounts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Edit") ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/dashboard/users/view/<?php echo $readAccounts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("View") ?>">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-warning" href="/dashboard/bans/create/<?php echo $readAccounts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Block") ?>">
                              <i class="fe fe-slash"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-secondary" href="/dashboard/store/chest-item/send/<?php echo $readAccounts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Send Chest Item") ?>">
                              <i class="fe fe-archive"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-info" href="/dashboard/store/credit/send/<?php echo $readAccounts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Send Credit") ?>">
                              <i class="fe fe-dollar-sign"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/users/delete/<?php echo $readAccounts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete") ?>">
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
          <?php else: ?>
            <?php echo alertError(t__("No data for this page!")); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      if ($readAdmin["id"] == get("id")) {
        go('/dashboard/error/101');
      }
      else {
        if (!checkPerm($readAdmin, 'SUPER_ADMIN')) {
          go('/dashboard/error/001');
        }
        else {
          $deleteAccount = $db->prepare("DELETE FROM Accounts WHERE id = ?");
          $deleteAccount->execute(array(get("id")));
          go("/dashboard/users/staffs");
        }
      }
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
