<footer class="footer">
  <div class="footer-top">
    <div class="container">
      <div class="row">
        <div class="col-md-5 mb-5 mb-md-0">
          <h5 class="footer-title"><?php e__('About') ?></h5>
          <p class="mb-0">
            <?php if ($readSettings["footerAboutText"] == '0'): ?>
              <?php e__('You can edit this text from the Dashboard.') ?>
            <?php else: ?>
              <?php echo $readSettings["footerAboutText"]; ?>
            <?php endif; ?>
          </p>
          <?php
            $availableLanguages = $db->query("SELECT * FROM Languages");
            $readAvailableLanguages = $availableLanguages->fetchAll();
          ?>
          <?php if ($availableLanguages->rowCount() > 0): ?>
            <div class="mt-4">
              <div class="dropdown dropup">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="languageMenu" data-toggle="dropdown" aria-expanded="false">
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
            </div>
          <?php endif; ?>
        </div>
        <div class="col-6 col-md-2">
          <h5 class="footer-title"><?php e__('Quick Menu') ?></h5>
          <ul class="list-unstyled mb-0">
            <li class="mb-2">
              <a href="/"><?php e__('Home') ?></a>
            </li>
            <?php if (!moduleIsDisabled('store')): ?>
              <li class="mb-2">
                <a href="/store"><?php e__('Store') ?></a>
              </li>
            <?php endif; ?>
            <li class="mb-2">
              <a href="/credit/buy"><?php e__('Buy Credits') ?></a>
            </li>
            <?php if (isset($_SESSION["login"])): ?>
              <li class="mb-2">
                <a href="/profile"><?php e__('Profile') ?></a>
              </li>
            <?php else: ?>
              <li class="mb-2">
                <a href="/login"><?php e__('Login') ?></a>
              </li>
              <li class="mb-2">
                <a href="/register"><?php e__('Register') ?></a>
              </li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="col-6 col-md-2">
          <h5 class="footer-title"><?php e__('Social Media') ?></h5>
          <ul class="list-unstyled mb-0">
            <?php if (($readSettings["footerFacebook"] != '0') || ($readSettings["footerTwitter"] != '0') || ($readSettings["footerInstagram"] != '0') || ($readSettings["footerYoutube"] != '0') || ($readSettings["footerDiscord"] != '0')): ?>
              <?php if ($readSettings["footerFacebook"] != '0'): ?>
                <li class="mb-2">
                  <a href="<?php echo $readSettings["footerFacebook"]; ?>" rel="external">
                    <i class="fab fa-facebook-square text-white mr-1"></i> Facebook
                  </a>
                </li>
              <?php endif; ?>
              <?php if ($readSettings["footerTwitter"] != '0'): ?>
                <li class="mb-2">
                  <a href="<?php echo $readSettings["footerTwitter"]; ?>" rel="external">
                    <i class="fab fa-twitter text-white mr-1"></i> Twitter
                  </a>
                </li>
              <?php endif; ?>
              <?php if ($readSettings["footerInstagram"] != '0'): ?>
                <li class="mb-2">
                  <a href="<?php echo $readSettings["footerInstagram"]; ?>" rel="external">
                    <i class="fab fa-instagram text-white mr-1"></i> Instagram
                  </a>
                </li>
              <?php endif; ?>
              <?php if ($readSettings["footerYoutube"] != '0'): ?>
                <li class="mb-2">
                  <a href="<?php echo $readSettings["footerYoutube"]; ?>" rel="external">
                    <i class="fab fa-youtube-play text-white mr-1"></i> Youtube
                  </a>
                </li>
              <?php endif; ?>
              <?php if ($readSettings["footerDiscord"] != '0'): ?>
                <li class="mb-2">
                  <a href="<?php echo $readSettings["footerDiscord"]; ?>" rel="external">
                    <i class="fab fa-discord text-white mr-1"></i> Discord
                  </a>
                </li>
              <?php endif; ?>
            <?php else: ?>
              <li><?php e__('You can edit social media details from the Dashboard.') ?></li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="col-md-3 mt-5 mt-md-0">
          <h5 class="footer-title"><?php e__('Contact') ?></h5>
          <ul class="list-unstyled mb-0">
            <?php if (($readSettings["footerEmail"] != '0') || ($readSettings["footerPhone"] != '0') || ($readSettings["footerWhatsapp"] != '0')): ?>
              <?php if ($readSettings["footerEmail"] != '0'): ?>
                <li class="mb-2">
                  <a href="mailto:<?php echo $readSettings["footerEmail"]; ?>" rel="external">
                    <i class="fa fa-envelope text-white mr-1"></i> <?php echo $readSettings["footerEmail"]; ?>
                  </a>
                </li>
              <?php endif; ?>
              <?php if ($readSettings["footerPhone"] != '0'): ?>
                <li class="mb-2">
                  <a href="tel:<?php echo $readSettings["footerPhone"]; ?>" rel="external">
                    <i class="fa fa-phone text-white mr-1"></i> <?php echo $readSettings["footerPhone"]; ?>
                  </a>
                </li>
              <?php endif; ?>
              <?php if ($readSettings["footerWhatsapp"] != '0'): ?>
                <li class="mb-2">
                  <a href="https://wa.me/<?php echo str_replace(array("+", " "), array('', ''), $readSettings["footerWhatsapp"]); ?>" rel="external">
                    <i class="fab fa-whatsapp text-white mr-1"></i> <?php echo $readSettings["footerWhatsapp"]; ?>
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
  <div class="footer-bottom">
    <div class="container">
      <div class="row">
        <div class="col-12 col-md text-center text-md-left mb-2 mb-md-0">
          <?php e__('All rights reserved. &copy; %year%', ['%year%' => date("Y")]) ?>
        </div>
        <div class="col-12 col-md-auto text-center text-md-left">
          <copyright data-toggle="tooltip" data-placement="top" title="Powered by VEXUS">
            <a href="https://www.leaderos.net/" rel="external">
              LEADEROS <?php echo ("v".VERSION); ?>
            </a>
          </copyright>
        </div>
      </div>
    </div>
  </div>
</footer>
