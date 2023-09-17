<?php
  if (!checkPerm($readAdmin, 'MANAGE_UPDATES')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/update.js');
?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="header">
        <div class="header-body">
          <div class="row align-items-center">
            <div class="col">
              <h2 class="header-title"><?php e__('Update') ?></h2>
            </div>
            <div class="col-auto">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                  <li class="breadcrumb-item active" aria-current="page"><?php e__('Update') ?></li>
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
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col">
              <h4 class="card-header-title">
                  <?php e__('Update Status') ?>
              </h4>
            </div>
            <div class="col-auto">
              <?php if ($needUpdate == true): ?>
                <span class="badge badge-pill badge-danger"><?php e__('Update Required') ?></span>
              <?php else: ?>
                <span class="badge badge-pill badge-success"><?php e__('Latest') ?></span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div id="loader" class="card-body">
          <div id="spinner" style="display: none;">
            <div class="spinner-border" role="status">
              <span class="sr-only">-/-</span>
            </div>
          </div>
          <?php if ($needUpdate == true): ?>
            <div id="updateBlock">
              <div class="row">
                <div class="col d-flex align-items-center text-muted">
                  <span><?php e__('A new update is out! To update new version <a rel="external" href="https://www.leaderos.net/blog">LeaderOS v%version%</a> click the <strong class="text-success">Update</strong> button.', ['%version%' => $newVersion]) ?></span>
                </div>
                <div class="col-auto d-flex align-items-center">
                  <button type="button" id="updateButton" class="btn btn-rounded btn-success">
                    <?php e__('Update') ?>
                  </button>
                </div>
              </div>
            </div>
          <?php else: ?>
            <span class="text-muted"><?php e__("You're using latest version! You'll be notified when a new version is out.") ?></span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
