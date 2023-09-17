<?php
  header('HTTP/1.1 404 Not Found');
  header('Status: 404 Not Found');
?>
<section class="section error-404-section">
  <div class="container">
    <div class="container d-flex flex-column justify-content-center pt-6 mt-n6" style="flex: 1 0 auto;">
      <div class="pt-7 pb-5">
        <div class="text-center mb-2 pb-4">
          <h1 class="mb-5">
            <img class="d-inline-block" src="<?php echo themePath(); ?>/public/assets/img/extras/404-text.svg" alt="Hata 404">
            <span class="visually-hidden"><?php e__('Page not found!') ?></span>
          </h1>
          <h2><?php e__('Page not found!') ?></h2>
          <a class="btn btn-primary" href="/"><?php e__('Home') ?></a>
        </div>
      </div>
    </div>
  </div>
</section>

