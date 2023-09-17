<?php
  header('HTTP/1.1 503 Service Temporarily Unavailable');
  header('Status: 503 Service Temporarily Unavailable');
  header('Retry-After: 300');
?>
<section class="section error-404-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12 text-center">
        <h1><?php e__('Maintenance') ?></h1>
        <p><?php e__('Our website is currently under maintenance, please try again later!') ?></p>
        <?php if (isset($_SESSION["login"])): ?>
          <?php if (checkStaff($readAccount)): ?>
            <a class="btn btn-rounded btn-success" href="/dashboard"><?php e__('Dashboard') ?></a>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
