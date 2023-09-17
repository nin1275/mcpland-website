<?php
  header('HTTP/1.1 404 Not Found');
  header('Status: 404 Not Found');
?>
<section class="section error-404-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12 text-center">
        <h1>404</h1>
        <p><?php e__('Page not found!') ?></p>
        <a class="btn btn-rounded btn-primary" href="/"><?php e__('Home') ?></a>
      </div>
    </div>
  </div>
</section>
