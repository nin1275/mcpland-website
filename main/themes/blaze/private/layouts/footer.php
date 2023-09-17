<?php if (get("route") != "login" && get("route") != "register" && get("route") != "recover"): ?>
  <!-- Back to top button-->
  <a id="scrollUp" class="btn-scroll-top" href="#" data-scroll data-fixed-element>
    <i class="btn-scroll-top-icon shi-arrow-up"></i>
  </a>
  <!-- Footer-->
  <?php if (!isset($_SESSION["login"])) : ?>
    <section class="footer-banner jarallax bg-dark py-5" data-jarallax data-speed="0.25">
      <span class="position-absolute top-0 start-0 w-100 h-100 bg-dark" style="opacity: .7;"></span>
      <div class="jarallax-img" style="background-image: url(<?php echo themePath(); ?>/public/assets/img/extras/footer-bg.png);"></div>
      <div class="container position-relative zindex-5 text-white">
        <div class="row align-items-center">
          <div class="col-12 col-md">
            <h3 class="h3 mb-0 text-white text-center text-md-start">
              <?php e__('Are you ready to be best?') ?>
            </h3>
          </div>
          <div class="col-12 col-md-auto mt-4 mt-md-0">
            <a href="/play" class="btn btn-lg btn-light w-100">
              <img class="me-2" src="<?php echo themePath(); ?>/public/assets/img/icons/sword.svg" width="24" height="24">
              <?php e__('Play Now') ?>
            </a>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>
  <footer class="footer bg-dark pt-5 pt-md-6">
    <div class="container pt-3 pt-md-0">
      <div class="row mb-4">
        <div class="col-md-5 mb-3">
          <div class="d-sm-flex justify-content-between">
            <div class="widget widget-light pb-1 mb-4">
              <h4 class="widget-title"><?php e__('About') ?></h4>
              <p class="text-light opacity-70 mb-2">
                <?php if ($readSettings["footerAboutText"] == '0'): ?>
                  <?php e__('You can edit this text from the Dashboard.') ?>
                <?php else: ?>
                  <?php echo $readSettings["footerAboutText"]; ?>
                <?php endif; ?>
              </p>
              <div class="mt-4">
                <?php
                  $availableLanguages = $db->query("SELECT * FROM Languages");
                  $readAvailableLanguages = $availableLanguages->fetchAll();
                ?>
                <?php if ($availableLanguages->rowCount() > 0): ?>
                  <div class="btn-group dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="languageMenu" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <?php echo $readAvailableLanguages[array_search($lang, array_column($readAvailableLanguages, 'code'))]["name"]; ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="languageMenu">
                      <?php foreach ($readAvailableLanguages as $readAvailableLanguage): ?>
                        <a class="dropdown-item <?php echo ($readAvailableLanguage["code"] == $lang) ? "active" : null; ?>" href="?lang=<?php echo $readAvailableLanguage["code"] ?>">
                          <?php echo $readAvailableLanguage["name"] ?>
                        </a>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                <?php endif; ?>
                <button id="changeMode" class="btn btn-icon btn-secondary ms-1">
                  <i class="shi shi-moon"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="widget widget-light pb-1 mb-4">
            <h4 class="widget-title"><?php e__('Quick Menu') ?></h4>
            <ul>
              <li>
                <a href="/" class="widget-link"><?php e__('Home') ?></a>
              </li>
              <li>
                <a href="/store" class="widget-link"><?php e__('Store') ?></a>
              </li>
              <li>
                <a href="/credit/buy" class="widget-link"><?php e__('Buy Credits') ?></a>
              </li>
              <?php if (isset($_SESSION["login"])): ?>
                <li>
                  <a href="/profile" class="widget-link"><?php e__('Profile') ?></a>
                </li>
              <?php else: ?>
                <li>
                  <a href="/login" class="widget-link"><?php e__('Login') ?></a>
                </li>
                <li>
                  <a href="/register" class="widget-link"><?php e__('Register') ?></a>
                </li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
        <div class="col-md-2">
          <div class="widget widget-light pb-1 mb-4">
            <h4 class="widget-title"><?php e__('Social Media') ?></h4>
            <i class="custom-icon"></i>
            <ul>
              <?php if (($readSettings["footerFacebook"] != '0') || ($readSettings["footerTwitter"] != '0') || ($readSettings["footerInstagram"] != '0') || ($readSettings["footerYoutube"] != '0') || ($readSettings["footerDiscord"] != '0')) : ?>
                <?php if ($readSettings["footerFacebook"] != '0') : ?>
                  <li>
                    <a class="widget-link" href="<?php echo $readSettings["footerFacebook"]; ?>" rel="external">
                      <i class="shi-facebook text-white me-1"></i> Facebook
                    </a>
                  </li>
                <?php endif; ?>
                <?php if ($readSettings["footerTwitter"] != '0') : ?>
                  <li>
                    <a class="widget-link" href="<?php echo $readSettings["footerTwitter"]; ?>" rel="external">
                      <i class="shi-twitter text-white me-1"></i> Twitter
                    </a>
                  </li>
                <?php endif; ?>
                <?php if ($readSettings["footerInstagram"] != '0') : ?>
                  <li>
                    <a class="widget-link" href="<?php echo $readSettings["footerInstagram"]; ?>" rel="external">
                      <i class="shi-instagram text-white me-1"></i> Instagram
                    </a>
                  </li>
                <?php endif; ?>
                <?php if ($readSettings["footerYoutube"] != '0') : ?>
                  <li>
                    <a class="widget-link" href="<?php echo $readSettings["footerYoutube"]; ?>" rel="external">
                      <i class="shi-youtube text-white me-1"></i> Youtube
                    </a>
                  </li>
                <?php endif; ?>
                <?php if ($readSettings["footerDiscord"] != '0') : ?>
                  <li>
                    <a class="widget-link" href="<?php echo $readSettings["footerDiscord"]; ?>" rel="external">
                      <i class="shi-discord text-white me-1"></i> Discord
                    </a>
                  </li>
                <?php endif; ?>
              <?php else : ?>
                <li><?php e__('You can edit social media details from the Dashboard.') ?></li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
        <div class="col-md-2">
          <div class="widget widget-light pb-1 mb-4">
            <h4 class="widget-title"><?php e__('Contact') ?></h4>
            <ul>
              <?php if (($readSettings["footerEmail"] != '0') || ($readSettings["footerPhone"] != '0') || ($readSettings["footerWhatsapp"] != '0')): ?>
                <?php if ($readSettings["footerEmail"] != '0'): ?>
                  <li>
                    <a class="widget-link" href="mailto:<?php echo $readSettings["footerEmail"]; ?>" rel="external">
                      <i class="shi-mail text-white me-1"></i> <?php echo $readSettings["footerEmail"]; ?>
                    </a>
                  </li>
                <?php endif; ?>
                <?php if ($readSettings["footerPhone"] != '0'): ?>
                  <li>
                    <a class="widget-link" href="tel:<?php echo $readSettings["footerPhone"]; ?>" rel="external">
                      <i class="shi-phone text-white me-1"></i> <?php echo $readSettings["footerPhone"]; ?>
                    </a>
                  </li>
                <?php endif; ?>
                <?php if ($readSettings["footerWhatsapp"] != '0'): ?>
                  <li>
                    <a class="widget-link" href="https://wa.me/<?php echo str_replace(array("+", " "), array('', ''), $readSettings["footerWhatsapp"]); ?>" rel="external">
                      <i class="bi bi-whatsapp text-white me-1"></i> <?php echo $readSettings["footerWhatsapp"]; ?>
                    </a>
                  </li>
                <?php endif; ?>
              <?php else: ?>
                <li><?php e__('You can edit contact details from the Dashboard.') ?></li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="bg-dark py-4 py-md-3">
      <div class="container">
        <div class="row">
          <div class="col-12 col-md text-center text-md-start mb-2 mb-md-0 text-light opacity-50 me-1">
            <?php e__('All rights reserved. &copy; %year%', ['%year%' => date("Y")]) ?>
          </div>
          <div class="col-12 col-md-auto text-center text-md-start">
            <copyright data-bs-toggle="tooltip" data-bs-placement="top" title="Theme: LeaderOS">
              <a class="text-white" href="https://www.leaderos.net/" rel="external">
                LEADEROS <?php echo ("v".VERSION); ?>
              </a>
            </copyright>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">
<?php endif; ?>
