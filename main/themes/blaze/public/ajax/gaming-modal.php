<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  if (moduleIsDisabled('gaming-night')) die(false);
?>
<?php if (get("action") == "buy"): ?>
  <?php
    $products = $db->prepare("SELECT P.id, P.categoryID, P.name, P.price, P.duration, P.imageID, P.imageType, GNP.price as discountedPrice, GNP.stock, S.name as serverName FROM GamingNightProducts GNP INNER JOIN Products P ON GNP.productID = P.id INNER JOIN Servers S ON S.id = P.serverID WHERE GNP.productID = ?");
    $products->execute(array(get("id")));
    $readProducts = $products->fetch();
  ?>
  <?php if ($products->rowCount() > 0): ?>
    <?php
      $productPrice = $readProducts["discountedPrice"];
    ?>
    <!-- Modal -->
    <div class="modal fade" id="buyModal" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-labelledby="buyModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title d-flex align-items-center h2 card-title" id="buyModalLabel"><?php e__('Gaming Night') ?> <i class="shi-chevron-right mx-1"></i> <?php echo $readProducts["name"]; ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div class="title background mt-0"><span><?php e__('Product') ?></span></div>
              </div>
              <div class="col-4">
                <div class="card-product position-relative d-flex h-auto">
                  <?php if ($readProducts["stock"] != -1): ?>
                    <div class="stock <?php echo ($readProducts["stock"] == 0) ? "stock-out" : "have-stock"; ?>">
                      <?php if ($readProducts["stock"] == 0): ?>
                        <?php e__('Out of stock!') ?>
                      <?php else : ?>
                        <?php e__('Limited Stock!') ?>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                  <?php $discountPercent = round((($readProducts["price"]-$readProducts["discountedPrice"])*100)/($readProducts["price"])); ?>
                  <div class="discount discount-sm">
                    <span>%<?php echo $discountPercent; ?></span>
                  </div>
                  <img class="store-card-img" src="/apps/main/public/assets/img/store/products/<?php echo $readProducts["imageID"].'.'.$readProducts["imageType"]; ?>" alt="<?php echo $serverName." Ürün - ".$readProducts["name"]." Satın Al"; ?>">
                </div>
              </div>
              <div class="col-8">
                <div class="row">
                  <span class="col-sm-4 fw-bold"><?php e__('Name') ?>:</span>
                  <span class="col-sm-8"><?php echo $readProducts["name"]; ?></span>
                </div>
                <div class="row">
                  <span class="col-sm-4 fw-bold"><?php e__('Server') ?>:</span>
                  <span class="col-sm-8">
                    <?php echo $readProducts["serverName"]; ?>
                  </span>
                </div>
                <div class="row">
                  <span class="col-sm-4 fw-bold"><?php e__('Category') ?>:</span>
                  <span class="col-sm-8">
                    <?php if ($readProducts["categoryID"] == 0): ?>
                      -
                    <?php else : ?>
                      <?php
                        $productCategory = $db->prepare("SELECT name FROM ProductCategories WHERE id = ?");
                        $productCategory->execute(array($readProducts["categoryID"]));
                        $readProductCategory = $productCategory->fetch();
                      ?>
                      <?php if ($productCategory->rowCount() > 0): ?>
                        <?php echo $readProductCategory["name"]; ?>
                      <?php else : ?>
                        -
                      <?php endif; ?>
                    <?php endif; ?>
                  </span>
                </div>
                <div class="row">
                  <span class="col-sm-4 fw-bold"><?php e__('Price') ?>:</span>
                  <span class="col-sm-8">
                    <?php e__('%credit% credit(s)', ['%credit%' => $productPrice]); ?>
                  </span>
                </div>
                <div class="row">
                  <span class="col-sm-4 fw-bold"><?php e__('Duration') ?>:</span>
                  <span class="col-sm-8">
                    <?php if ($readProducts["duration"] == 0): ?>
                      <?php e__('Lifetime') ?>
                    <?php elseif ($readProducts["duration"] == -1): ?>
                      <?php e__('One-Time') ?>
                    <?php else : ?>
                      <?php e__('%day% day(s)', ['%day%' => $readProducts["duration"]]) ?>
                    <?php endif; ?>
                  </span>
                </div>
                <?php if ($readProducts["stock"] != -1): ?>
                  <div class="row">
                    <span class="col-sm-4 fw-bold"><?php e__('Stock') ?>:</span>
                    <span class="col-sm-8">
                      <?php if ($readProducts["stock"] == 0): ?>
                        <span class="text-danger">
                          <?php e__('Out of stock!') ?>
                        </span>
                      <?php else : ?>
                        <span class="text-success"><?php e__('%stock% in stock', ['%stock%' => $readProducts["stock"]]) ?></span>
                      <?php endif; ?>
                    </span>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" id="inputProduct" name="product" value="<?php echo $readProducts["id"]; ?>">
            <?php if (isset($_SESSION["login"])): ?>
              <button type="button" id="buyProductButton" class="btn btn-rounded btn-success"><?php e__('Buy Now') ?></button>
            <?php else: ?>
              <a href="/login" class="btn btn-rounded btn-success"><?php e__('Login') ?></a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      var buyModal = $("#buyModal");
      var couponBox = $("#couponBox");
      var alertCoupon = $("#alertCoupon");
      var inputCoupon = $("#inputCoupon");
      var inputProduct = $("#inputProduct");
      var oldPrice = $("#oldPrice");
      var newPrice = $("#newPrice");
      var addCouponButton = $("#addCouponButton");
      var deleteCouponButton = $("#deleteCouponButton");
      var buyProductButton = $("#buyProductButton");
      var addToCartButton = $("#addToCartButton");

      buyProductButton.on("click", function() {
        $.ajax({
          type: "POST",
          url: "/apps/main/public/ajax/gaming-buy.php",
          data: {productID: inputProduct.val()},
          success: function(result) {
            if (result == "error") {
              swal.fire({
                title: "<?php e__('Error!'); ?>",
                text: "<?php e__('Something went wrong! Please try again later.'); ?>",
                type: "error",
                confirmButtonColor: "#02b875",
                confirmButtonText: "<?php e__('OK'); ?>"
              }).then(function() {
                buyModal.modal("hide");
              });
            }
            if (result == "error_login") {
              swal.fire({
                title: "<?php e__('Error!'); ?>",
                text: "Please login to buy this product!",
                type: "error",
                confirmButtonColor: "#02b875",
                confirmButtonText: "<?php e__('OK'); ?>"
              }).then(function() {
                buyModal.modal("hide");
              });
            }
            else if (result == "unsuccessful") {
              swal.fire({
                title: "<?php e__('Error!'); ?>",
                text: "<?php e__("You don't have enough credit."); ?>",
                type: "error",
                confirmButtonColor: "#02b875",
                confirmButtonText: "<?php e__('OK'); ?>"
              }).then(function() {
                window.location = '/credit/buy';
              });
            }
            else if (result == "stock_error") {
              swal.fire({
                title: "<?php e__('Error!'); ?>",
                text: "<?php e__('The product you selected is out of stock.'); ?>",
                type: "error",
                confirmButtonColor: "#02b875",
                confirmButtonText: "<?php e__('OK'); ?>"
              }).then(function() {
                buyModal.modal("hide");
              });
            }
            else {
              swal.fire({
                title: "<?php e__('Success!'); ?>",
                text: "<?php e__('Your purchase has been completed and added to your chest.'); ?>",
                type: "success",
                confirmButtonColor: "#02b875",
                confirmButtonText: "<?php e__('OK'); ?>"
              }).then(function() {
                window.location = '/chest';
              });
            }
          }
        });
      });
    </script>
  <?php else : ?>
    <?php die(false); ?>
  <?php endif; ?>
<?php else : ?>
  <?php die(false); ?>
<?php endif; ?>
