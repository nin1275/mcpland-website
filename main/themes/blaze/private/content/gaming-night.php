<?php
  require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource(themePath().'/public/assets/js/gaming-night.js');
  $extraResourcesJS->addResource('https://unpkg.com/@pqina/flip/dist/flip.min.js');
?>
<link href="https://unpkg.com/@pqina/flip/dist/flip.min.css" rel="stylesheet">
<section class="section store-section">
  <div class="container">
    <?php if ($readSettings["gamingNightDay"] == date("l") && date("Hi") >= $readSettings["gamingNightStart"] && date("Hi") <= $readSettings["gamingNightEnd"]): ?>
      <div class="row">
        <div class="col-md-12">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="/"><?php e__('Home') ?></a></li>
              <li class="breadcrumb-item active" aria-current="page">
                <?php e__('Gaming Night') ?>
              </li>
            </ol>
          </nav>
        </div>
      </div>
      <div class="row">
        <div id="modalBox"></div>
        <div class="col-md-12">
          <?php
            $products = $db->query("SELECT P.id, P.name, P.price, P.imageID, P.imageType, GNP.price as discountedPrice, GNP.stock FROM GamingNightProducts GNP INNER JOIN Products P ON GNP.productID = P.id");
          ?>
          <h2 class="h4 mb-3"><?php e__('Products') ?></h2>
          <?php if ($products->rowCount() > 0): ?>
            <div class="row">
              <?php foreach ($products as $readProducts): ?>
                <div class="col-md-3 col-sm-6 mb-grid-gutter">
                  <div class="card card-product card-hover">
                    <?php if ($readProducts["stock"] != -1): ?>
                      <div class="stock <?php echo ($readProducts["stock"] == 0) ? "stock-out" : "have-stock"; ?>">
                        <?php if ($readProducts["stock"] == 0): ?>
                          <?php e__('Out of Stock!') ?>
                        <?php else : ?>
                          <?php e__('Limited Stock!') ?>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
  
                    <?php $discountPercent = round((($readProducts["price"]-$readProducts["discountedPrice"])*100)/($readProducts["price"])); ?>
                    <div class="discount">
                      <span><?php echo $discountPercent; ?>%</span>
                    </div>
                    <img class="card-img-top lazyload" data-src="/apps/main/public/assets/img/store/products/<?php echo $readProducts["imageID"].'.'.$readProducts["imageType"]; ?>" src="/apps/main/public/assets/img/loaders/store.png" alt="<?php echo $serverName." Ürün - ".$readProducts["name"]." Satın Al"; ?>">
                    <div class="card-body">
                      <h3 class="fs-lg fw-medium my-2">
                        <?php echo $readProducts["name"]; ?>
                      </h3>
                      <span class="text-heading fw-semibold">
                        <span class="old-price"><?php echo $readProducts["price"]; ?></span>
                        <small>/</small>
                        <?php $newPrice = $readProducts["discountedPrice"]; ?>
                        <span class="price"><?php echo $newPrice; ?> <i class="fa fa-coins"></i></span>
                      </span>
          
                      <div class="mt-auto">
                        <?php if ($readProducts["stock"] != -1): ?>
                          <div class="mb-2">
                            <?php if ($readProducts["stock"] == 0): ?>
                              <span class="text-danger small"><?php e__('Out of Stock!') ?></span>
                            <?php else : ?>
                              <span class="text-success small"><?php e__('%stock% in stock', ['%stock%' => $readProducts["stock"]]) ?></span>
                            <?php endif; ?>
                          </div>
                        <?php endif; ?>
                        <?php if ($readProducts["stock"] == 0): ?>
                          <button class="btn btn-danger w-100 stretched-link disabled"><?php e__('Out of Stock!') ?></button>
                        <?php else: ?>
                          <button class="btn btn-success w-100 stretched-link openBuyModal" product-id="<?php echo $readProducts["id"]; ?>">
                            <i class="shi-shopping-cart me-1"></i>
                            <?php e__('Buy Now') ?>
                          </button>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data were found!')); ?>
          <?php endif; ?>
        </div>
      </div>
    <?php else: ?>
      <?php
      $gamingNightStart = $readSettings["gamingNightStart"];
      $gamingNightStartFormatted = $gamingNightStart[0].$gamingNightStart[1].":".$gamingNightStart[2].$gamingNightStart[3];
  
      $gamingNightEnd = $readSettings["gamingNightEnd"];
      $gamingNightEndFormatted = $gamingNightEnd[0].$gamingNightEnd[1].":".$gamingNightEnd[2].$gamingNightEnd[3];
      
      if ($readSettings["gamingNightDay"] == date("l")) {
        $tick = date("Y-m-d")."T".$gamingNightStartFormatted.":00+03:00";
      }
      else {
        $tick = date("Y-m-d", strtotime("next ".$readSettings["gamingNightDay"]))."T".$gamingNightStartFormatted.":00+03:00";
      }
      ?>
      <div class="text-center pt-5">
        <h1 class="text-white"><?php e__('Gaming Night') ?></h1>
        <p class="mb-5 text-white">
          <?php e__('The Gaming Night is on every %day% between %start%-%end%!', [
              '%day%' => $readSettings["gamingNightDay"],
              '%start%' => $gamingNightStartFormatted,
              '%end%' => $gamingNightEndFormatted
          ]) ?>
        <style>
          body {
            background-color: black;
            background-image: url(/apps/main/public/assets/img/extras/gaming-bg.png);
          }
          .tick {
            font-size:1rem; white-space:nowrap; font-family:arial,sans-serif; max-width: 38rem; margin: auto;
          }
          .tick-flip,.tick-text-inline {
            font-size:2.5em;
          }
          .tick-label {
            margin-top:1em;font-size:0.825em;
            font-weight: 600;
          }
          .tick-char {
            width:1.5em;
          }
          .tick-text-inline {
            display:inline-block;text-align:center;min-width:1em;
          }
          .tick-text-inline+.tick-text-inline {
            margin-left:-.325em;
          }
          .tick-group {
            margin:0 .5em;text-align:center;
          }
          .tick-text-inline {
            color: rgb(90, 93, 99) !important;
          }
          .tick-label {
            color: #fff !important;
          }
          .tick-flip-panel {
            color: rgb(255, 255, 255) !important;
          }
          .tick-flip {
            font-family: !important;
          }
          .tick-flip-panel-text-wrapper {
            line-height: 1.45 !important;
          }
          .tick-flip-panel {
            background-color: rgb(59, 61, 59) !important;
          }
          .tick-flip {
            border-radius:0.12em !important;
          }
        </style>
  
        <div class="tick" data-did-init="handleTickInit">
          <div data-repeat="true" data-layout="horizontal fit" data-transform="preset(d, h, m, s) -> delay">
            <div class="tick-group">
              <div data-key="value" data-repeat="true" data-transform="pad(00) -> split -> delay">
                <span data-view="flip"></span>
              </div>
              <span data-key="label" data-view="text" class="tick-label"></span>
            </div>
          </div>
        </div>
      </div>
      <script>
        function handleTickInit(tick) {
      
          // uncomment to set labels to different language
          
          var locale = {
              YEAR_PLURAL: '<?php e__('Years') ?>',
              YEAR_SINGULAR: '<?php e__('Year') ?>',
              MONTH_PLURAL: '<?php e__('Months') ?>',
              MONTH_SINGULAR: '<?php e__('Month') ?>',
              WEEK_PLURAL: '<?php e__('Weeks') ?>',
              WEEK_SINGULAR: '<?php e__('Week') ?>',
              DAY_PLURAL: '<?php e__('Days') ?>',
              DAY_SINGULAR: '<?php e__('Day') ?>',
              HOUR_PLURAL: '<?php e__('Hours') ?>',
              HOUR_SINGULAR: '<?php e__('Hour') ?>',
              MINUTE_PLURAL: '<?php e__('Minutes') ?>',
              MINUTE_SINGULAR: '<?php e__('Minute') ?>',
              SECOND_PLURAL: '<?php e__('Seconds') ?>',
              SECOND_SINGULAR: '<?php e__('Second') ?>',
          };
  
          for (var key in locale) {
              if (!locale.hasOwnProperty(key)) { continue; }
              tick.setConstant(key, locale[key]);
          }
          
          // create the countdown counter
          var counter = Tick.count.down('<?php echo $tick; ?>');
      
          counter.onupdate = function(value) {
            tick.value = value;
          };
      
          counter.onended = function() {
            window.location = '/gaming-night';
          };
        }
      </script>
    <?php endif; ?>
  </div>
</section>
