<?php
  if (!isset($_SESSION["login"])) {
    go("/login");
  }
  if (moduleIsDisabled("store")) go("/404");
  
  
  require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource(themePath().'/public/assets/js/store.js');
  $extraResourcesJS->addResource(themePath().'/public/assets/js/checkout.js');
?>
<style>
  /* SPINNER */
  .checkoutItemQuantitySpinner {
    width: 40px;
    text-align: center;
    background-color: #fff;
    border-top: 1px solid #cad1d7;
    border-bottom: 1px solid #cad1d7;
  }
  
  .shoppingCartSpinner {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 100%;
  }

  .shopping-cart-loading {
    height: 10rem;
    overflow: visible;
  }

  .shopping-cart-loading> :not(.shoppingCartSpinner) {
    display: none;
  }
</style>
<div id="modalBox"></div>
<section class="section page-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><?php e__('Home') ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php e__('Shopping Cart') ?></li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <?php e__('Shopping Cart') ?>
          </div>
          <div id="shoppingCartTableLoader" class="card-body p-0 shopping-cart-loading">
            <div class="shoppingCartSpinner">
              <div class="spinner-border text-default" role="status">
                <span class="sr-only">-/-</span>
              </div>
            </div>
            <div id="emptyShoppingCart" style="display: none;">
              <div class="mt-3 mx-3">
                <?php echo alertWarning(t__('Your shopping cart is empty!')); ?>
              </div>
            </div>
            <div id="shoppingCartTable" class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th><?php e__('Product') ?></th>
                    <th><?php e__('Price') ?></th>
                    <th><?php e__('Quantity') ?></th>
                    <th><?php e__('Total') ?></th>
                    <th class="text-right">&nbsp;</th>
                  </tr>
                </thead>
                <tbody id="checkoutItems">
                  <tr id="checkoutItemCopy" style="display: none;">
                    <td class="checkoutItemName">-</td>
                    <td class="checkoutItemPrice">0</td>
                    <td class="checkoutItemQuantity">
                      <div class="input-group" style="width: 90px;">
                        <div class="input-group-prepend">
                          <button class="btn btn-sm btn-danger cartItemDecreaseButton" type="button" product-id="0">-</button>
                        </div>
                        <input type="text" class="form-control form-control-sm text-center cartItemAmountUpdateInput" value="0" data-max="0" product-id="0" autocomplete="off">
                        <div class="checkoutItemQuantitySpinner" style="display: none;">
                          <div class="spinner-border spinner-border-sm" role="status">
                            <span class="sr-only">...</span>
                          </div>
                        </div>
                        <div class="input-group-append">
                          <button class="btn btn-sm btn-success cartItemIncreaseButton" type="button" product-id="0">+</button>
                        </div>
                      </div>
                    </td>
                    <td class="checkoutItemTotal">0</td>
                    <td class="checkoutItemButtons text-right">
                      <button class="btn btn-primary btn-circle openBuyModal" data-toggle="tooltip" data-placement="top" title="<?php e__('Details') ?>" product-id="0">
                        <i class="fa fa-info"></i>
                      </button>
                      <button class="btn btn-danger btn-circle removeItemButton" data-toggle="tooltip" data-placement="top" title="<?php e__('Remove') ?>" product-id="0">
                        <i class="fa fa-times"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header"><?php e__('Order Summary') ?></div>
          <div class="card-body">
            <div id="shoppingCartSummary">
              <div class="row">
                <div class="col-md-12">
                  <div class="input-group">
                    <input type="text" id="inputCoupon" class="form-control" name="coupon" placeholder="<?php e__('Coupon') ?>" disabled>
                    <div class="input-group-append">
                      <button type="button" id="addCouponButton" class="btn btn-success" style="pointer-events: none;"><?php e__('Apply') ?></button>
                      <button type="button" id="deleteCouponButton" class="btn btn-danger" style="display: none;"><?php e__('Remove') ?></button>
                    </div>
                  </div>
                  <small id="alertCoupon"></small>
                </div>
              </div>
              <div id="discountBlock" class="row pt-4" style="display: none;">
                <div class="col">
                  <span class="font-weight-bold"><?php e__('Discount') ?>:</span>
                </div>
                <div class="col-auto text-right">
                  <span id="discount" class="text-danger">
                    -
                  </span>
                </div>
              </div>
              <div class="row pt-4">
                <div class="col">
                  <span class="font-weight-bold"><?php e__('Subtotal') ?>:</span>
                </div>
                <div class="col-auto text-right">
                  <span id="subtotal" class="text-success">
                    -
                  </span>
                </div>
              </div>
              <div class="pt-3">
                <button type="button" id="checkoutButton" class="btn btn-banner-bg w-100"><?php e__('Checkout') ?></button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
