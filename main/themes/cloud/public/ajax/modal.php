<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  if (moduleIsDisabled('store')) die(false);
?>
<?php if (get("action") == "buy"): ?>
  <?php
    $products = $db->prepare("SELECT * FROM Products WHERE id = ?");
    $products->execute(array(get("id")));
    $readProducts = $products->fetch();
    $discountProducts = explode(",", $readSettings["storeDiscountProducts"]);
  ?>
  <?php if ($products->rowCount() > 0): ?>
    <?php $discountedPriceStatus = ($readProducts["discountedPrice"] != 0 && ($readProducts["discountExpiryDate"] > date("Y-m-d H:i:s") || $readProducts["discountExpiryDate"] == '1000-01-01 00:00:00')); ?>
    <?php $storeDiscountStatus = ($readSettings["storeDiscount"] != 0 && (in_array($readProducts["id"], $discountProducts) || $readSettings["storeDiscountProducts"] == '0') && ($readSettings["storeDiscountExpiryDate"] > date("Y-m-d H:i:s") || $readSettings["storeDiscountExpiryDate"] == '1000-01-01 00:00:00')); ?>
    <?php
      if ($discountedPriceStatus == true || $storeDiscountStatus == true) {
        $productPrice = (($storeDiscountStatus == true) ? round(($readProducts["price"]*(100-$readSettings["storeDiscount"]))/100) : $readProducts["discountedPrice"]);
      }
      else {
        $productPrice = $readProducts["price"];
      }
    ?>
    <!-- Modal -->
    <div class="modal fade" id="buyModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="buyModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title" id="buyModalLabel"><?php e__('Store') ?> <i class="fa fa-angle-double-right"></i> <?php echo $readProducts["name"]; ?></div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div class="title background mt-0"><span><?php e__('Product') ?></span></div>
              </div>
              <div class="col-4">
                <div class="store-card">
                  <?php if ($readProducts["stock"] != -1): ?>
                    <div class="store-card-stock <?php echo ($readProducts["stock"] == 0) ? "stock-out" : "have-stock"; ?>">
                      <?php if ($readProducts["stock"] == 0): ?>
                        <?php e__('Out of stock!') ?>
                      <?php else : ?>
                        <?php e__('Limited Stock!') ?>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                  <?php if ($discountedPriceStatus == true || $storeDiscountStatus == true): ?>
                    <?php $discountPercent = (($storeDiscountStatus == true) ? $readSettings["storeDiscount"] : round((($readProducts["price"]-$readProducts["discountedPrice"])*100)/($readProducts["price"]))); ?>
                    <div class="store-card-discount">
                      <span>%<?php echo $discountPercent; ?></span>
                    </div>
                  <?php endif; ?>
                  <img class="store-card-img" src="/apps/main/public/assets/img/store/products/<?php echo $readProducts["imageID"].'.'.$readProducts["imageType"]; ?>" alt="<?php echo $serverName." Ürün - ".$readProducts["name"]." Satın Al"; ?>">
                </div>
              </div>
              <div class="col-8">
                <div class="row">
                  <span class="col-sm-4 font-weight-bold"><?php e__('Name') ?>:</span>
                  <span class="col-sm-8"><?php echo $readProducts["name"]; ?></span>
                </div>
                <div class="row">
                  <span class="col-sm-4 font-weight-bold"><?php e__('Category') ?>:</span>
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
                  <span class="col-sm-4 font-weight-bold"><?php e__('Price') ?>:</span>
                  <span class="col-sm-8">
                    <?php e__('%credit% credit(s)', ['%credit%' => $productPrice]) ?>
                  </span>
                </div>
                <div class="row">
                  <span class="col-sm-4 font-weight-bold"><?php e__('Duration') ?>:</span>
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
                    <span class="col-sm-4 font-weight-bold"><?php e__('Stock') ?>:</span>
                    <span class="col-sm-8">
                      <?php if ($readProducts["stock"] == 0): ?>
                        <span class="text-danger"><?php e__('Out of stock!') ?></span>
                      <?php else : ?>
                        <span class="text-success"><?php e__('%stock% in stock', ['%stock%' => $readProducts["stock"]]) ?></span>
                      <?php endif; ?>
                    </span>
                  </div>
                <?php endif; ?>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="title background"><span><?php e__('Description') ?></span></div>
                <div class="product-details">
                  <?php echo $readProducts["details"]; ?>
                </div>
              </div>
            </div>
            <?php if ($discountedPriceStatus == true || $storeDiscountStatus == true): ?>
              <div class="row pt-3">
                <div class="col">
                  <span class="font-weight-bold"><?php e__('Discount') ?>:</span>
                </div>
                <div class="col-auto text-right">
                  <span id="oldPrice" class="text-danger">
                    -<?php e__('%credit% credit(s)', ['%credit%' => $readProducts["price"]]) ?>
                  </span>
                </div>
              </div>
            <?php endif; ?>
            <div class="row pt-3">
              <div class="col">
                <span class="font-weight-bold"><?php e__('Subtotal') ?>:</span>
              </div>
              <div class="col-auto text-right">
                <span id="newPrice" class="text-success" value="<?php echo $productPrice; ?>">
                  <?php e__('%credit% credit(s)', ['%credit%' => $productPrice]) ?>
                </span>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" id="inputProduct" name="product" value="<?php echo $readProducts["id"]; ?>">
            <?php if (isset($_SESSION["login"])): ?>
              <button type="button" class="btn btn-rounded btn-primary addToCartButton" data-buynow="false"><?php e__('Add to Cart') ?></button>
              <button type="button" class="btn btn-rounded btn-success addToCartButton" data-buynow="true"><?php e__('Buy Now') ?></button>
            <?php else: ?>
              <a href="/login" class="btn btn-rounded btn-success"><?php e__('Login') ?></a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      var buyModal = $("#buyModal");
      var inputProduct = $("#inputProduct");
      var buyProductButton = $("#buyProductButton");
      var addToCartButton = $(".addToCartButton");
      
      addToCartButton.on("click", function() {
        var button = $(this);
        $.ajax({
          type: "POST",
          url: "/apps/main/public/ajax/shopping-cart.php?action=add&productID=" + inputProduct.val(),
          success: function(result) {
            if (result == "error" || result == "error_product") {
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
            else if (result == "error_login") {
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
            else {
              if (button.attr("data-buynow") === "true") {
                window.location = '/checkout';
              }
              else {
                if (result == "error_credit") {
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
                else if (result == "error_stock") {
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
                    text: "<?php e__('The product has been added to your shopping cart.'); ?>",
                    type: "success",
                    showCancelButton: true,
                    cancelButtonColor: "#02b875",
                    cancelButtonText: "<?php e__('Continue Shopping'); ?>",
                    confirmButtonColor: "#5e72e4",
                    confirmButtonText: "<?php e__('Go to Cart'); ?>"
                  }).then(function(isAccepted) {
                    if (isAccepted.value) {
                      window.location = '/checkout';
                    }
                  });
                  var response = JSON.parse(result);
                  $(".shopping-cart-count").text(response.items.length);
                }
              }
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
