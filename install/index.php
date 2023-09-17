<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/status.php");
  require_once(__ROOT__."/apps/install/private/config/functions.php");

  if (INSTALL_STATUS == true) {
    go("/");
  }
?>
<!DOCTYPE html>
<html lang="tr">
  <head>
    <?php require_once(__ROOT__."/apps/install/private/layouts/head.php"); ?>
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3 installBlock">
          <div class="wizard animated fadeIn">
            <div class="progress">
              <div class="progressLine" width="12.5"></div>
            </div>
            <div class="step active">
              <div class="step-icon">
                <i class="fa fa-database"></i>
              </div>
              <p>Database</p>
            </div>
            <div class="step">
              <div class="step-icon">
                <i class="fa fa-sliders"></i>
              </div>
              <p>Website Settings</p>
            </div>
            <div class="step">
              <div class="step-icon">
                <i class="fa fa-user-plus"></i>
              </div>
              <p>Admin</p>
            </div>
            <div class="step">
              <div class="step-icon">
                <i class="fa fa-check"></i>
              </div>
              <p>Completed</p>
            </div>
          </div>
          <div class="alert alert-danger animated fadeIn"></div>
          <form id="installForm" method="post" autocomplete="off">
            <div class="card" style="display: block;">
              <div class="card-header">
                MySQL Database
              </div>
              <div id="loader" class="card-body is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="form-group">
                  <label>MySQL Host:</label>
                  <input type="text" class="form-control" name="mysqlServer" placeholder="Example: localhost">
                </div>
                <div class="form-group">
                  <label>MySQL Port:</label>
                  <input type="number" class="form-control" name="mysqlPort" placeholder="Example: 3306">
                </div>
                <div class="form-group">
                  <label>MySQL Username:</label>
                  <input type="text" class="form-control" name="mysqlUsername" placeholder="Example: root">
                </div>
                <div class="form-group">
                  <label>MySQL Password:</label>
                  <input type="password" class="form-control" name="mysqlPassword" placeholder="Example: 123456">
                </div>
                <div class="form-group">
                  <label>MySQL Database Name:</label>
                  <input type="text" class="form-control" name="mysqlDatabase" placeholder="Example: database">
                </div>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="button" class="btn btn-next" index="0">Next</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                Website Settings
              </div>
              <div id="loader" class="card-body">
                <div id="spinner" style="display: none;">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="form-group">
                  <label>Server Name:</label>
                  <input type="text" class="form-control" name="siteServerName" placeholder="Example: XXX Craft">
                </div>
                <div class="form-group">
                  <label>Website Title:</label>
                  <input type="text" class="form-control" name="siteSlogan" placeholder="Example: Minecraft Server!">
                </div>
                <div class="form-group">
                  <label>Server IP:</label>
                  <input type="text" class="form-control" name="siteServerIP" placeholder="Example: play.xxxcraft.com">
                </div>
                <div class="form-group">
                  <label>Server Version:</label>
                  <input type="text" class="form-control" name="siteServerVersion" placeholder="Example: 1.8.X">
                </div>
                <div class="form-group">
                  <label>Hashing:</label>
                  <select class="form-control" name="sitePasswordType">
                    <option value="1">SHA256</option>
                    <option value="2">MD5</option>
                    <option value="3">Bcrypt</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Maintenance:</label>
                  <select class="form-control" name="siteMaintenance">
                    <option value="0">Disable</option>
                    <option value="1">Active</option>
                  </select>
                </div>
                <div class="row">
                  <div class="col">
                    <button type="button" class="btn btn-prev">Prev</button>
                  </div>
                  <div class="col-auto">
                    <button type="button" class="btn btn-next" index="1">Next</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                Create Admin
              </div>
              <div id="loader" class="card-body">
                <div id="spinner" style="display: none;">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="form-group">
                  <label>Username:</label>
                  <input type="text" class="form-control" name="accountUsername" placeholder="Example: user" autocomplete="username">
                </div>
                <div class="form-group">
                  <label>Email:</label>
                  <input type="email" class="form-control" name="accountEmail" placeholder="Example: user@domain.com" autocomplete="email">
                </div>
                <div class="form-group">
                  <label>Password:</label>
                  <input type="password" class="form-control" name="accountPassword" placeholder="Example: 123456">
                </div>
                <div class="form-group">
                  <label>Confirm Password:</label>
                  <input type="password" class="form-control" name="accountPasswordRe" placeholder="Example: 123456">
                </div>
                <div class="row">
                  <div class="col">
                    <button type="button" class="btn btn-prev">Prev</button>
                  </div>
                  <div class="col-auto">
                    <button type="submit" class="btn btn-submit" index="2">Complete</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                Installation Completed
              </div>
              <div class="card-body">
                <p>Installation completed successfully! You can change the site settings from the dashboard as you wish.</p>
                <div class="clearfix">
                  <div class="float-right">
                    <a href="javascript:void(0)" id="redirect" class="btn btn-submit">
                      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                      <span class="sr-only">Loading...</span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php require_once(__ROOT__."/apps/install/private/layouts/footer.php"); ?>
    <script type="text/javascript">
      $(window).on("load", function() {
        $("#loader").removeClass("is-loading");
        $("#spinner").css("display", "none");
      });
    </script>
  </body>
</html>
