<?php
  if (!checkPerm($readAdmin, 'MANAGE_STORE')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  if (get("target") == 'product' && (get("action") == 'insert' || get("action") == 'update')) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.product.js');
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.minecraft-items.js');
  }
  if (get("target") == 'category' && (get("action") == 'insert' || get("action") == 'update')) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.category.js');
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.minecraft-items.js');
  }
  if (get("target") == 'coupon' && (get("action") == 'insert' || get("action") == 'update')) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.coupon.js');
  }
  if (get("target") == 'chest' && get("action") == 'send') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/gift.products.js');
  }
  if (get("target") == 'discount' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.discount.js');
  }
  if (get("target") == 'credit' && get("action") == 'send') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/payment.js');
  }
?>
<?php if (get("target") == 'product'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Products') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/store/urun"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Product') ?></li>
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
          <?php
            $products = $db->prepare("SELECT P.*, PC.name as categoryName, S.name as serverName FROM Products P LEFT JOIN ProductCategories PC ON P.categoryID = PC.id OR P.categoryID = ? INNER JOIN Servers S ON P.serverID = S.id GROUP BY P.id ORDER BY P.priority DESC, P.id DESC");
            $products->execute(array(0));
          ?>
          <?php if ($products->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["productID", "productName", "productCategoryName", "productServerName", "productPrice", "productStock", "productPriority"]'>
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <div class="row align-items-center">
                      <div class="col-auto pr-0">
                        <span class="fe fe-search text-muted"></span>
                      </div>
                      <div class="col">
                        <input type="search" class="form-control form-control-flush search" name="search" placeholder="<?php e__('Search') ?>">
                      </div>
                    </div>
                  </div>
                  <div class="col-auto">
                    <a class="btn btn-sm btn-white" href="/dashboard/store/products/create"><?php e__('Add Product') ?></a>
                  </div>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 40px;">
                          <a href="#" class="text-muted sort" data-sort="productID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productName">
                              <?php e__('Product Name') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productCategoryName">
                              <?php e__('Category Name') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productServerName">
                              <?php e__('Server Name') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productPrice">
                              <?php e__('Price') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productStock">
                              <?php e__('Stock') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productPriority">
                            <?php e__('Priority') ?>
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($products as $readProducts): ?>
                        <tr>
                          <td class="productID text-center" style="width: 40px;">
                            <a href="/dashboard/store/products/edit/<?php echo $readProducts["id"]; ?>">
                              #<?php echo $readProducts["id"]; ?>
                            </a>
                          </td>
                          <td class="productName">
                            <a href="/dashboard/store/products/edit/<?php echo $readProducts["id"]; ?>">
                              <?php echo $readProducts["name"]; ?>
                            </a>
                          </td>
                          <td class="productCategoryName">
                            <?php echo (($readProducts["categoryID"] == 0) ? t__('Uncategorized') : $readProducts["categoryName"]); ?>
                          </td>
                          <td class="productServerName">
                            <?php echo $readProducts["serverName"]; ?>
                          </td>
                          <td class="productPrice">
                            <?php echo ($readProducts["discountedPrice"] > 0) ? $readProducts["discountedPrice"] : $readProducts["price"]; ?> <?php e__('credit'); ?>
                          </td>
                          <td class="productStock">
                            <?php echo ($readProducts["stock"] == -1) ? t__('Unlimited') : $readProducts["stock"]; ?>
                          </td>
                          <td class="productPriority">
                            <?php echo $readProducts["priority"]; ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/store/products/edit/<?php echo $readProducts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/store/products/delete/<?php echo $readProducts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'insert'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Add Product') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Product') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Add Product') ?></li>
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
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["insertProducts"])) {
              if (post("stockStatus") == 0) {
                $_POST["stock"] = -1;
              }
              if (post("durationStatus") == 0) {
                $_POST["duration"] = 0;
              }
              if (post("durationStatus") == -1) {
                $_POST["duration"] = -1;
              }
              if (post("discountStatus") == 0) {
                $_POST["discountedPrice"] = 0;
              }
              if (post("discountDurationStatus") == 0 || post("discountStatus") == 0) {
                $_POST["discountDuration"] = '1000-01-01 00:00:00';
              }
              else {
                $_POST["discountDuration"] = date("Y-m-d H:i:s", strtotime($_POST["discountDuration"]));
              }
              if (!$csrf->validate('insertProducts')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("name") == null || post("categoryID") == null || post("serverID") == null || post("details") == null || !count(array_filter($_POST["commands"])) || post("price") == null || post("discountStatus") == null || post("discountedPrice") == null || post("discountDurationStatus") == null || post("discountDuration") == null || post("durationStatus") == null || post("duration") == null || post("stockStatus") == null || post("stock") == null || post("minecraftStatus") == null || post("giveRoleID") == null || post("priority") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else if ($_FILES["image"]["size"] == null) {
                echo alertError(t__('Please select an image!'));
              }
              else {
                require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                $upload = new \Verot\Upload\Upload($_FILES["image"]);
                $imageID = md5(uniqid(rand(0, 9999)));
                if ($upload->uploaded) {
                  $upload->allowed = array("image/*");
                  $upload->file_new_name_body = $imageID;
                  $upload->image_resize = true;
                  $upload->image_ratio_crop = true;
                  $upload->image_x = 360;
                  $upload->image_y = 360;
                  $upload->process(__ROOT__."/apps/main/public/assets/img/store/products/");
                  if ($upload->processed) {
                    $insertProducts = $db->prepare("INSERT INTO Products (name, imageID, imageType, categoryID, serverID, details, price, discountedPrice, discountExpiryDate, duration, stock, priority, minecraftStatus, minecraftTitle, minecraftDescription, minecraftItem, giveRoleID, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertProducts->execute(array(post("name"), $imageID, $upload->file_dst_name_ext, post("categoryID"), post("serverID"), filteredContent($_POST["details"]), post("price"), post("discountedPrice"), post("discountDuration"), post("duration"), post("stock"), post("priority"), post("minecraftStatus"), post("minecraftTitle"), post("minecraftDescription"), post("minecraftItem"), post("giveRoleID"), date("Y-m-d H:i:s")));
                    $productsLastInsertID = $db->lastInsertId();
                    if (count(array_filter($_POST["commands"]))) {
                      $insertProductCommands = $db->prepare("INSERT INTO ProductCommands (productID, command) VALUES (?, ?)");
                      foreach ($_POST["commands"] as $command) {
                        $command = ltrim(strip_tags($command), '/');
                        $insertProductCommands->execute(array($productsLastInsertID, $command));
                      }
                    }
                    echo alertSuccess(t__('Product has been added successfully!'));
                  }
                  else {
                    echo alertError(t__('An error occupied while uploading an image: %error%', ['%error%' => $upload->error]));
                  }
                }
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group row">
                  <label for="inputname" class="col-sm-2 col-form-label"><?php e__('Product Name') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputname" class="form-control" name="name" placeholder="<?php e__('Enter the product name') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectServerID" class="col-sm-2 col-form-label"><?php e__('Server') ?>:</label>
                  <div class="col-sm-10">
                    <?php $servers = $db->query("SELECT * FROM Servers"); ?>
                    <select id="selectServerID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="serverID" <?php echo ($servers->rowCount() == 0) ? "disabled" : null; ?>>
                      <?php if ($servers->rowCount() > 0): ?>
                        <?php foreach ($servers as $readServers): ?>
                          <option value="<?php echo $readServers["id"]; ?>"><?php echo $readServers["name"]; ?></option>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <option><?php e__('No server found!') ?>!</option>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectCategoryID" class="col-sm-2 col-form-label"><?php e__('Category') ?>:</label>
                  <div class="col-sm-10">
                    <div id="c-loading" style="display: none; margin-top: 7px"><?php e__('Loading') ?>...</div>
                    <div id="product-categories">
                      <?php
                        $firstServer = $db->query("SELECT * FROM Servers ORDER BY id ASC LIMIT 1");
                        $readFirstServer = $firstServer->fetch();

                        $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ?");
                        $productCategories->execute(array($readFirstServer["id"]));
                      ?>
                      <select id="selectCategoryID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="categoryID">
                        <option value="0"><?php e__('Uncategorized') ?></option>
                        <?php if ($productCategories->rowCount() > 0): ?>
                          <?php foreach ($productCategories as $readProductCategories): ?>
                            <option value="<?php echo $readProductCategories["id"]; ?>"><?php echo $readProductCategories["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputPrice" class="col-sm-2 col-form-label"><?php e__('Price') ?>:</label>
                  <div class="col-sm-10">
                    <div class="input-group input-group-merge">
                      <input type="number" id="inputPrice" class="form-control form-control-prepended" name="price" placeholder="<?php e__('Enter the product price') ?>.">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <span class="fa fa-coins"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectDiscountStatus" class="col-sm-2 col-form-label"><?php e__('Discount') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectDiscountStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="discountStatus">
                      <option value="0"><?php e__('No') ?></option>
                      <option value="1"><?php e__('Yes') ?></option>
                    </select>
                  </div>
                </div>
                <div id="discountBlock" style="display: none;">
                  <div class="form-group row">
                    <label for="inputDiscountedPrice" class="col-sm-2 col-form-label"><?php e__('Discounted Price') ?>:</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputDiscountedPrice" class="form-control form-control-prepended" name="discountedPrice" placeholder="<?php e__('Enter the price with discounted') ?>.">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fa fa-coins"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectDiscountDurationStatus" class="col-sm-2 col-form-label"><?php e__('Discount Duration') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectDiscountDurationStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="discountDurationStatus">
                        <option value="0"><?php e__('Lifetime') ?></option>
                        <option value="1"><?php e__('Temporary') ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="discountDurationBlock" style="display: none;">
                    <div class="form-group row">
                      <div class="col-sm-10 offset-sm-2">
                        <div class="input-group input-group-merge">
                          <input type="text" id="inputDiscountDuration" class="form-control form-control-prepended" name="discountDuration" placeholder="<?php e__('Enter expiration date of discount') ?>." data-toggle="flatpickr" data-expirydate="true">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fe fe-clock"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectDurationStatus" class="col-sm-2 col-form-label"><?php e__('Product Duration') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectDurationStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="durationStatus">
                      <option value="0"><?php e__('Lifetime') ?></option>
                      <option value="1"><?php e__('Temporary') ?></option>
                      <option value="-1"><?php e__('One-time') ?></option>
                    </select>
                  </div>
                </div>
                <div id="durationBlock" style="display: none;">
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputDuration" class="form-control form-control-prepended" name="duration" placeholder="Enter an usage duration (day).">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-clock"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectStockStatus" class="col-sm-2 col-form-label"><?php e__('Stock') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectStockStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="stockStatus">
                      <option value="0"><?php e__('Unlimited') ?></option>
                      <option value="1"><?php e__('Limited') ?></option>
                    </select>
                  </div>
                </div>
                <div id="stockBlock" style="display: none;">
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputStock" class="form-control form-control-prepended" name="stock" placeholder="<?php e__('Enter the stock amount') ?>.">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-package"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputPriority" class="col-sm-2 col-form-label"><?php e__('Priority') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputPriority" class="form-control" name="priority" placeholder="<?php e__('Enter the priority') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectMinecraftStatus" class="col-sm-2 col-form-label"><?php e__('Show in Game') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectMinecraftStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="minecraftStatus">
                      <option value="0"><?php e__('No') ?></option>
                      <option value="1"><?php e__('Yes') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectRole" class="col-sm-2 col-form-label"><?php e__('Give Role to Buyers') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectRole" class="form-control" data-toggle="select" name="giveRoleID">
                      <option value="0"><?php e__('None') ?></option>
                      <?php $roles = $db->query("SELECT * FROM Roles ORDER BY priority DESC"); ?>
                      <?php foreach ($roles as $readRoles): ?>
                        <?php if ($readRoles["id"] == 1) continue; ?>
                        <option value="<?php echo $readRoles["id"] ?>"><?php echo $readRoles["name"] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div id="minecraftBlock" style="display: none;">
                  <div class="form-group row">
                    <label for="input-minecrafttitle" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Title') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="input-minecrafttitle" class="form-control" name="minecraftTitle" placeholder="<?php e__('If you leave it blank, the product name will be used. Color codes (&) are acceptable.') ?>.">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="input-minecraftdesc" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Description (lore)') ?>:</label>
                    <div class="col-sm-10">
                      <textarea id="input-minecraftdesc" class="form-control" name="minecraftDescription" placeholder="<?php e__('You can leave it blank. Color codes (&) are acceptable.') ?>."></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectMinecraftItem" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Icon') ?>:</label>
                    <div class="col-sm-10">
                      <div id="mi-loading" style="margin-top: 7px"><?php e__('Loading...') ?></div>
                      <div id="product-minecraftitems" style="display: none;" data-type="insert">
                        <select id="selectMinecraftItem" class="form-control" name="minecraftItem">
                          <option value=""><?php e__('Default') ?></option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaDetails" class="col-sm-2 col-form-label"><?php e__('Details') ?>:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaDetails" class="form-control" data-toggle="textEditor" name="details" placeholder="<?php e__('Enter the products details/features') ?>."></textarea>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputTable" class="col-sm-2 col-form-label"><?php e__('Commands') ?>:</label>
                  <div class="col-sm-10">
                    <div class="table-responsive">
                      <table id="tableitems" class="table table-sm table-hover table-nowrap array-table">
                        <thead>
                          <tr>
                            <th><?php e__('Command') ?></th>
                            <th class="text-center pt-0 pb-0 align-middle">
                              <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                                <i class="fe fe-plus"></i>
                              </button>
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>
                              <div class="input-group input-group-merge">
                                <input type="text" class="form-control form-control-prepended" name="commands[]" placeholder="<?php e__('Enter the command to send console when purchasing the product') ?>.">
                                <div class="input-group-prepend">
                                  <div class="input-group-text">
                                    <span class="fe fe-terminal"></span>
                                  </div>
                                </div>
                              </div>
                            </td>
                            <td class="text-center align-middle">
                              <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                <i class="fe fe-trash-2"></i>
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <small class="form-text text-muted pb-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="fileImage" class="col-sm-2 col-form-label"><?php e__('Image') ?>:</label>
                  <div class="col-sm-10">
                    <div data-toggle="dropimage" class="dropimage">
                      <div class="di-thumbnail">
                        <img src="" alt="<?php e__('Preview') ?>">
                      </div>
                      <div class="di-select">
                        <label for="fileImage"><?php e__('Select Image') ?></label>
                        <input type="file" id="fileImage" name="image" accept="image/*">
                      </div>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('insertProducts'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertProducts"><?php e__('Add') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'update' && get("id")): ?>
    <?php
      $product = $db->prepare("SELECT * FROM Products WHERE id = ?");
      $product->execute(array(get("id")));
      $readProduct = $product->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Edit Product') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Product') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Edit Product') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($product->rowCount() > 0) ? $readProduct["name"] : t__('Not found!'); ?></li>
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
          <?php if ($product->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateProducts"])) {
                if (post("stockStatus") == 0) {
                  $_POST["stock"] = -1;
                }
                if (post("durationStatus") == 0) {
                  $_POST["duration"] = 0;
                }
                if (post("durationStatus") == -1) {
                  $_POST["duration"] = -1;
                }
                if (post("discountStatus") == 0) {
                  $_POST["discountedPrice"] = 0;
                }
                if (post("discountDurationStatus") == 0 || post("discountStatus") == 0) {
                  $_POST["discountDuration"] = '1000-01-01 00:00:00';
                }
                else {
                  $_POST["discountDuration"] = date("Y-m-d H:i:s", strtotime($_POST["discountDuration"]));
                }
                if (!$csrf->validate('updateProducts')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if (post("name") == null || post("categoryID") == null || post("serverID") == null || post("details") == null || !count(array_filter($_POST["commands"])) || post("price") == null || post("discountStatus") == null || post("discountedPrice") == null || post("discountDurationStatus") == null || post("discountDuration") == null || post("durationStatus") == null || post("duration") == null || post("stockStatus") == null || post("stock") == null || post("minecraftStatus") == null || post("giveRoleID") == null || post("priority") == null) {
                  echo alertError(t__('Please fill all the fields!'));
                }
                else {
                  if ($_FILES["image"]["size"] != null) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                    $upload = new \Verot\Upload\Upload($_FILES["image"]);
                    $imageID = $readProduct["imageID"];
                    if ($upload->uploaded) {
                      $upload->allowed = array("image/*");
                      $upload->file_overwrite = true;
                      $upload->file_new_name_body = $imageID;
                      $upload->image_resize = true;
                      $upload->image_ratio_crop = true;
                      $upload->image_x = 360;
                      $upload->image_y = 360;
                      $upload->process(__ROOT__."/apps/main/public/assets/img/store/products/");
                      if ($upload->processed) {
                        $updateProducts = $db->prepare("UPDATE Products SET imageType = ? WHERE id = ?");
                        $updateProducts->execute(array($upload->file_dst_name_ext, get("id")));
                      }
                      else {
                        echo alertError(t__('An error occupied while uploading an image: %error%', ['%error%' => $upload->error]));
                      }
                    }
                  }
                  $updateProducts = $db->prepare("UPDATE Products SET name = ?, categoryID = ?, serverID = ?, details = ?, price = ?, discountedPrice = ?, discountExpiryDate = ?, duration = ?, stock = ?, priority = ?, minecraftStatus = ?, minecraftTitle = ?, minecraftDescription = ?, minecraftItem = ?, giveRoleID = ? WHERE id = ?");
                  $updateProducts->execute(array(post("name"), post("categoryID"), post("serverID"), filteredContent($_POST["details"]), post("price"), post("discountedPrice"),  post("discountDuration"), post("duration"), post("stock"), post("priority"), post("minecraftStatus"), post("minecraftTitle"), post("minecraftDescription"), post("minecraftItem"), post("giveRoleID"), get("id")));
                  if (count(array_filter($_POST["commands"]))) {
                    $deleteProductCommands = $db->prepare("DELETE FROM ProductCommands WHERE productID = ?");
                    $deleteProductCommands->execute(array($readProduct["id"]));
                    $insertProductCommands = $db->prepare("INSERT INTO ProductCommands (productID, command) VALUES (?, ?)");
                    foreach ($_POST["commands"] as $command) {
                      $command = ltrim(strip_tags($command), '/');
                      $insertProductCommands->execute(array($readProduct["id"], $command));
                    }
                  }
                  echo alertSuccess(t__('Changes has been saved successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label for="inputname" class="col-sm-2 col-form-label"><?php e__('Product Name') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputname" class="form-control" name="name" placeholder="<?php e__('Enter the product name') ?>." value="<?php echo $readProduct["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectServerID" class="col-sm-2 col-form-label"><?php e__('Server') ?>:</label>
                    <div class="col-sm-10">
                      <?php $servers = $db->query("SELECT * FROM Servers"); ?>
                      <select id="selectServerID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="serverID" <?php echo ($servers->rowCount() == 0) ? "disabled" : null; ?>>
                        <?php if ($servers->rowCount() > 0): ?>
                          <?php foreach ($servers as $readServers): ?>
                            <option value="<?php echo $readServers["id"]; ?>" <?php echo (($readProduct["serverID"] == $readServers["id"]) ? 'selected="selected"' : null); ?>><?php echo $readServers["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <option><?php e__('No server found!') ?></option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectCategoryID" class="col-sm-2 col-form-label"><?php e__('Category') ?>:</label>
                    <div class="col-sm-10">
                      <div id="c-loading" style="display: none; margin-top: 7px"><?php e__('Loading') ?>...</div>
                      <div id="product-categories">
                        <?php
                          $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ?");
                          $productCategories->execute(array($readProduct["serverID"]));
                        ?>
                        <select id="selectCategoryID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="categoryID">
                          <option value="0"><?php e__('Uncategorizied') ?></option>
                          <?php if ($productCategories->rowCount() > 0): ?>
                            <?php foreach ($productCategories as $readProductCategories): ?>
                              <option value="<?php echo $readProductCategories["id"]; ?>" <?php echo (($readProduct["categoryID"] == $readProductCategories["id"]) ? 'selected="selected"' : null); ?>><?php echo $readProductCategories["name"]; ?></option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPrice" class="col-sm-2 col-form-label"><?php e__('Price') ?>:</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputPrice" class="form-control form-control-prepended" name="price" placeholder="<?php e__('Enter the product price') ?>." value="<?php echo $readProduct["price"]; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fa fa-coins"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectDiscountStatus" class="col-sm-2 col-form-label"><?php e__('Discount') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectDiscountStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="discountStatus">
                        <option value="0" <?php echo ($readProduct["discountedPrice"] == 0) ? 'selected="selected"' : null; ?>><?php e__('No') ?></option>
                        <option value="1" <?php echo ($readProduct["discountedPrice"] != 0) ? 'selected="selected"' : null; ?>><?php e__('Yes') ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="discountBlock" style="<?php echo ($readProduct["discountedPrice"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <label for="inputDiscountedPrice" class="col-sm-2 col-form-label"><?php e__('Discounted Price') ?>:</label>
                      <div class="col-sm-10">
                        <div class="input-group input-group-merge">
                          <input type="number" id="inputDiscountedPrice" class="form-control form-control-prepended" name="discountedPrice" placeholder="<?php e__('Enter the discounted price') ?>." value="<?php echo ($readProduct["discountedPrice"] != 0) ? $readProduct["discountedPrice"] : null; ?>">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fa fa-coins"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectDiscountDurationStatus" class="col-sm-2 col-form-label"><?php e__('Discount Duration') ?>:</label>
                      <div class="col-sm-10">
                        <select id="selectDiscountDurationStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="discountDurationStatus">
                          <option value="0" <?php echo ($readProduct["discountExpiryDate"] == '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>><?php e__('Lifetime') ?></option>
                          <option value="1" <?php echo ($readProduct["discountExpiryDate"] != '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>><?php e__('Temporary') ?></option>
                        </select>
                      </div>
                    </div>
                    <div id="discountDurationBlock" style="<?php echo ($readProduct["discountExpiryDate"] == '1000-01-01 00:00:00') ? "display: none;" : "display: block;"; ?>">
                      <div class="form-group row">
                        <div class="col-sm-10 offset-sm-2">
                          <div class="input-group input-group-merge">
                            <input type="text" id="inputDiscountDuration" class="form-control form-control-prepended" name="discountDuration" placeholder="<?php e__('Enter expiration date of discount') ?>." data-toggle="flatpickr" data-expirydate="true" value="<?php echo ($readProduct["discountExpiryDate"] != '1000-01-01 00:00:00') ? $readProduct["discountExpiryDate"] : null; ?>">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <span class="fe fe-clock"></span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectDurationStatus" class="col-sm-2 col-form-label"><?php e__('Product Duration') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectDurationStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="durationStatus">
                        <option value="0" <?php echo ($readProduct["duration"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Lifetime') ?></option>
                        <option value="1" <?php echo ($readProduct["duration"] != 0 && $readProduct["duration"] != -1) ? 'selected="selected"' : null; ?>><?php e__('Temporary') ?></option>
                        <option value="-1" <?php echo ($readProduct["duration"] == -1) ? 'selected="selected"' : null; ?>><?php e__('One Time') ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="durationBlock" style="<?php echo ($readProduct["duration"] == 0 || $readProduct["duration"] == -1) ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <div class="col-sm-10 offset-sm-2">
                        <div class="input-group input-group-merge">
                          <input type="number" id="inputDuration" class="form-control form-control-prepended" name="duration" placeholder="<?php e__('Product duration (day).') ?>." value="<?php echo ($readProduct["duration"] != 0) ? $readProduct["duration"] : null; ?>">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fe fe-clock"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectStockStatus" class="col-sm-2 col-form-label"><?php e__('Stock') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectStockStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="stockStatus">
                        <option value="0" <?php echo ($readProduct["stock"] == -1) ? 'selected="selected"' : null; ?>><?php e__('Unlimited') ?></option>
                        <option value="1" <?php echo ($readProduct["stock"] != -1) ? 'selected="selected"' : null; ?>><?php e__('Limited') ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="stockBlock" style="<?php echo ($readProduct["stock"] == -1) ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <div class="col-sm-10 offset-sm-2">
                        <div class="input-group input-group-merge">
                          <input type="number" id="inputStock" class="form-control form-control-prepended" name="stock" placeholder="<?php e__('Enter the stock amount') ?>." value="<?php echo ($readProduct["stock"] != -1) ? $readProduct["stock"] : null; ?>">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fe fe-package"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectMinecraftStatus" class="col-sm-2 col-form-label"><?php e__('Show in GUI') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectMinecraftStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="minecraftStatus">
                        <option value="0" <?php echo ($readProduct["minecraftStatus"] == 0) ? 'selected="selected"' : null; ?>><?php e__('No') ?></option>
                        <option value="1" <?php echo ($readProduct["minecraftStatus"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Yes') ?></option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectRole" class="col-sm-2 col-form-label"><?php e__('Give Role to Buyers') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectRole" class="form-control" data-toggle="select" name="giveRoleID">
                        <option value="0"><?php e__('None') ?></option>
                        <?php $roles = $db->query("SELECT * FROM Roles ORDER BY priority DESC"); ?>
                        <?php foreach ($roles as $readRoles): ?>
                          <?php if ($readRoles["id"] == 1) continue; ?>
                          <option value="<?php echo $readRoles["id"] ?>" <?php echo ($readProduct["giveRoleID"] == $readRoles["id"]) ? 'selected="selected"' : null; ?>><?php echo $readRoles["name"] ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPriority" class="col-sm-2 col-form-label"><?php e__('Priority') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputPriority" class="form-control" name="priority" placeholder="<?php e__('Enter the priority') ?>." value="<?php echo $readProduct["priority"]; ?>">
                    </div>
                  </div>
                  <div id="minecraftBlock" style="<?php echo ($readProduct["minecraftStatus"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <label for="input-minecrafttitle" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Title') ?>:</label>
                      <div class="col-sm-10">
                        <input type="text" id="input-minecrafttitle" class="form-control" name="minecraftTitle" placeholder="<?php e__('If you leave it blank, the product name will be used. Color codes (&) are acceptable.') ?>." value="<?php echo $readProduct["minecraftTitle"]; ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="input-minecraftdesc" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Description (lore)') ?>:</label>
                      <div class="col-sm-10">
                        <textarea id="input-minecraftdesc" class="form-control" name="minecraftDescription" placeholder="<?php e__('You can leave it blank. Color codes (&) are acceptable.') ?>."><?php echo $readProduct["minecraftDescription"]; ?></textarea>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectMinecraftItem" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Icon') ?>:</label>
                      <div class="col-sm-10">
                        <div id="mi-loading" style="margin-top: 7px"><?php e__('Loading...') ?></div>
                        <div id="product-minecraftitems" style="display: none;" data-type="update" data-selected="<?php echo $readProduct["minecraftItem"]; ?>">
                          <select id="selectMinecraftItem" class="form-control" name="minecraftItem">
                            <option value=""><?php e__('None') ?></option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="textareaDetails" class="col-sm-2 col-form-label"><?php e__('Details') ?>:</label>
                    <div class="col-sm-10">
                      <textarea id="textareaDetails" class="form-control" data-toggle="textEditor" name="details" placeholder="<?php e__("Enter the product's details/features.") ?>."><?php echo $readProduct["details"]; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputTable" class="col-sm-2 col-form-label"><?php e__('Commands') ?>:</label>
                    <div class="col-sm-10">
                      <div class="table-responsive">
                        <table id="tableitems" class="table table-sm table-hover table-nowrap array-table">
                          <thead>
                            <tr>
                              <th><?php e__('Command') ?></th>
                              <th class="text-center pt-0 pb-0 align-middle">
                                <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                                  <i class="fe fe-plus"></i>
                                </button>
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              $productCommands = $db->prepare("SELECT * FROM ProductCommands WHERE productID = ?");
                              $productCommands->execute(array($readProduct["id"]));
                            ?>
                            <?php foreach ($productCommands as $productCommand): ?>
                              <tr>
                                <td>
                                  <div class="input-group input-group-merge">
                                    <input type="text" class="form-control form-control-prepended" name="commands[]" placeholder="<?php e__('Enter the command to send console when purchasing the product') ?>." value="<?php echo $productCommand["command"]; ?>">
                                    <div class="input-group-prepend">
                                      <div class="input-group-text">
                                        <span class="fe fe-terminal"></span>
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td class="text-center align-middle">
                                  <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                    <i class="fe fe-trash-2"></i>
                                  </button>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                      <small class="form-text text-muted pb-2"><strong><?php e__('Username') ?>:</strong> %username%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="fileImage" class="col-sm-2 col-form-label"><?php e__('Image') ?>:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage active">
                        <div class="di-thumbnail">
                          <img src="/apps/main/public/assets/img/store/products/<?php echo $readProduct["imageID"].'.'.$readProduct["imageType"]; ?>" alt="<?php e__('Preview') ?>">
                        </div>
                        <div class="di-select">
                          <label for="fileImage"><?php e__('Select Image') ?></label>
                          <input type="file" id="fileImage" name="image" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateProducts'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/store/products/delete/<?php echo $readProduct["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateProducts"><?php e__('Save Changes') ?></button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteProduct = $db->prepare("DELETE FROM Products WHERE id = ?");
      $deleteProduct->execute(array(get("id")));
      go("/dashboard/store/products");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'category'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Categories') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Category') ?></li>
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
          <?php $productCategories = $db->query("SELECT PC.*, S.name as serverName, S.slug as serverSlug FROM ProductCategories PC INNER JOIN Servers S ON PC.serverID = S.id ORDER BY PC.priority DESC, PC.id DESC"); ?>
          <?php if ($productCategories->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["productCategoryID", "productCategoryName", "productServerName", "productParentCategoryName", "productPriority"]'>
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <div class="row align-items-center">
                      <div class="col-auto pr-0">
                        <span class="fe fe-search text-muted"></span>
                      </div>
                      <div class="col">
                        <input type="search" class="form-control form-control-flush search" name="search" placeholder="<?php e__('Search') ?>">
                      </div>
                    </div>
                  </div>
                  <div class="col-auto">
                    <a class="btn btn-sm btn-white" href="/dashboard/store/categories/create"><?php e__('Add Category') ?></a>
                  </div>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 40px;">
                          <a href="#" class="text-muted sort" data-sort="productCategoryID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productCategoryName">
                              <?php e__('Category Name') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productServerName">
                              <?php e__('Server') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productParentCategoryName">
                              <?php e__('Parent Category') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productPriority">
                            <?php e__('Priority') ?>
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($productCategories as $readProductCategories): ?>
                        <tr>
                          <td class="productCategoryID text-center" style="width: 40px;">
                            <a href="/dashboard/store/categories/edit/<?php echo $readProductCategories["id"]; ?>">
                              #<?php echo $readProductCategories["id"]; ?>
                            </a>
                          </td>
                          <td class="productCategoryName">
                            <a href="/dashboard/store/categories/edit/<?php echo $readProductCategories["id"]; ?>">
                              <?php echo $readProductCategories["name"]; ?>
                            </a>
                          </td>
                          <td class="productServerName">
                            <?php echo $readProductCategories["serverName"]; ?>
                          </td>
                          <td class="productParentCategoryName">
                            <?php
                              $parentCategory = $db->prepare("SELECT name FROM ProductCategories WHERE id = ?");
                              $parentCategory->execute(array($readProductCategories["parentID"]));
                              $readParentCategory = $parentCategory->fetch();
                            ?>
                            <?php if ($parentCategory->rowCount() > 0): ?>
                              <?php echo $readParentCategory["name"]; ?>
                            <?php else: ?>
                              -
                            <?php endif; ?>
                          </td>
                          <td class="productPriority">
                            <?php echo $readProductCategories["priority"]; ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/store/categories/edit/<?php echo $readProductCategories["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/store/<?php echo $readProductCategories["serverSlug"]; ?>/<?php echo $readProductCategories["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/store/categories/delete/<?php echo $readProductCategories["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'insert'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Add Category') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/categories"><?php e__('Category') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Add Category') ?></li>
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
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["insertProductCategories"])) {
              if (!$csrf->validate('insertProductCategories')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("name") == null || post("serverID") == null || post("parentID") == null || post("minecraftStatus") == null || post("priority") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else if ($_FILES["image"]["size"] == null) {
                echo alertError(t__('Please select an image!'));
              }
              else {
                require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                $upload = new \Verot\Upload\Upload($_FILES["image"]);
                $imageID = md5(uniqid(rand(0, 9999)));
                if ($upload->uploaded) {
                  $upload->allowed = array("image/*");
                  $upload->file_new_name_body = $imageID;
                  $upload->image_resize = true;
                  $upload->image_ratio_crop = true;
                  $upload->image_x = 360;
                  $upload->image_y = 360;
                  $upload->process(__ROOT__."/apps/main/public/assets/img/store/categories/");
                  if ($upload->processed) {
                    $insertProductCategories = $db->prepare("INSERT INTO ProductCategories (serverID, parentID, name, slug, imageID, imageType, priority, minecraftStatus, minecraftTitle, minecraftDescription, minecraftItem) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertProductCategories->execute(array(post("serverID"), post("parentID"), post("name"), $slugify->slugify(post("name")), $imageID, $upload->file_dst_name_ext, post("priority"), post("minecraftStatus"), post("minecraftTitle"), post("minecraftDescription"), post("minecraftItem")));
                    echo alertSuccess(t__('Category has been added successfully!'));
                  }
                  else {
                    echo alertError(t__('An error occupied while uploading an image: %error%', ['%error%' => $upload->error]));
                  }
                }
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group row">
                  <label for="inputName" class="col-sm-2 col-form-label"><?php e__('Category Name') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputName" class="form-control" name="name" placeholder="<?php e__('Enter the category name') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectServerID" class="col-sm-2 col-form-label"><?php e__('Server') ?>:</label>
                  <div class="col-sm-10">
                    <?php $servers = $db->query("SELECT * FROM Servers"); ?>
                    <select id="selectServerID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="serverID" <?php echo ($servers->rowCount() == 0) ? "disabled" : null; ?>>
                      <?php if ($servers->rowCount() > 0): ?>
                        <?php foreach ($servers as $readServers): ?>
                          <option value="<?php echo $readServers["id"]; ?>"><?php echo $readServers["name"]; ?></option>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <option><?php e__('No server found!') ?>!</option>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectParentID" class="col-sm-2 col-form-label"><?php e__('Parent Category') ?>:</label>
                  <div class="col-sm-10">
                    <div id="c-loading" style="display: none; margin-top: 7px"><?php e__('Loading') ?>...</div>
                    <div id="product-categories">
                      <?php
                        $firstServer = $db->query("SELECT * FROM Servers ORDER BY id ASC LIMIT 1");
                        $readFirstServer = $firstServer->fetch();

                        $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ?");
                        $productCategories->execute(array($readFirstServer["id"]));
                      ?>
                      <select id="selectParentID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="parentID">
                        <option value="0"><?php e__('Uncategorized') ?></option>
                        <?php if ($productCategories->rowCount() > 0): ?>
                          <?php foreach ($productCategories as $readProductCategories): ?>
                            <option value="<?php echo $readProductCategories["id"]; ?>"><?php echo $readProductCategories["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputPriority" class="col-sm-2 col-form-label"><?php e__('Priority') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputPriority" class="form-control" name="priority" placeholder="<?php e__('Enter the priority') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectMinecraftStatus" class="col-sm-2 col-form-label"><?php e__('Show in Game') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectMinecraftStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="minecraftStatus">
                      <option value="0"><?php e__('No') ?></option>
                      <option value="1"><?php e__('Yes') ?></option>
                    </select>
                  </div>
                </div>
                <div id="minecraftBlock" style="display: none;">
                  <div class="form-group row">
                    <label for="input-minecrafttitle" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Title') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="input-minecrafttitle" class="form-control" name="minecraftTitle" placeholder="<?php e__('If you leave it blank, the product name will be used. Color codes (&) are acceptable.') ?>.">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="input-minecraftdesc" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Description (lore)') ?>:</label>
                    <div class="col-sm-10">
                      <textarea id="input-minecraftdesc" class="form-control" name="minecraftDescription" placeholder="<?php e__('You can leave it blank. Color codes (&) are acceptable.') ?>."></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectMinecraftItem" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Icon') ?>:</label>
                    <div class="col-sm-10">
                      <div id="mi-loading" style="margin-top: 7px"><?php e__('Loading...') ?></div>
                      <div id="product-minecraftitems" style="display: none;" data-type="insert">
                        <select id="selectMinecraftItem" class="form-control" name="minecraftItem">
                          <option value=""><?php e__('Default') ?></option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="fileImage" class="col-sm-2 col-form-label"><?php e__('Image') ?>:</label>
                  <div class="col-sm-10">
                    <div data-toggle="dropimage" class="dropimage">
                      <div class="di-thumbnail">
                        <img src="" alt="<?php e__('Preview') ?>">
                      </div>
                      <div class="di-select">
                        <label for="fileImage"><?php e__('Select Image') ?></label>
                        <input type="file" id="fileImage" name="image" accept="image/*">
                      </div>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('insertProductCategories'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertProductCategories"><?php e__('Add') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'update' && get("id")): ?>
    <?php
      $productCategory = $db->prepare("SELECT PC.*, S.slug as serverSlug FROM ProductCategories PC INNER JOIN Servers S ON PC.serverID = S.id WHERE PC.id = ?");
      $productCategory->execute(array(get("id")));
      $readProductCategory = $productCategory->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Edit Category') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/categories"><?php e__('Category') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/categories"><?php e__('Edit Category') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($productCategory->rowCount() > 0) ? $readProductCategory["name"] : "Bulunamadı!"; ?></li>
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
          <?php if ($productCategory->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateProductCategories"])) {
                if (!$csrf->validate('updateProductCategories')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if (post("name") == null || post("serverID") == null || post("parentID") == null || post("minecraftStatus") == null || post("priority") == null) {
                  echo alertError(t__('Please fill all the fields!'));
                }
                else {
                  if ($_FILES["image"]["size"] != null) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                    $upload = new \Verot\Upload\Upload($_FILES["image"]);
                    $imageID = $readProductCategory["imageID"];
                    if ($upload->uploaded) {
                      $upload->allowed = array("image/*");
                      $upload->file_overwrite = true;
                      $upload->file_new_name_body = $imageID;
                      $upload->image_resize = true;
                      $upload->image_ratio_crop = true;
                      $upload->image_x = 360;
                      $upload->image_y = 360;
                      $upload->process(__ROOT__."/apps/main/public/assets/img/store/categories/");
                      if ($upload->processed) {
                        $updateProductCategories = $db->prepare("UPDATE ProductCategories SET imageType = ? WHERE id = ?");
                        $updateProductCategories->execute(array($upload->file_dst_name_ext, get("id")));
                      }
                      else {
                        echo alertError(t__('An error occupied while uploading an image: %error%', ['%error%' => $upload->error]));
                      }
                    }
                  }
                  $updateProductCategories = $db->prepare("UPDATE ProductCategories SET serverID = ?, parentID = ?, name = ?, slug = ?, priority = ?, minecraftStatus = ?, minecraftTitle = ?, minecraftDescription = ?, minecraftItem = ? WHERE id = ?");
                  $updateProductCategories->execute(array(post("serverID"), post("parentID"), post("name"), $slugify->slugify(post("name")), post("priority"), post("minecraftStatus"), post("minecraftTitle"), post("minecraftDescription"), post("minecraftItem"), get("id")));
                  echo alertSuccess(t__('Changes has been saved successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label"><?php e__('Category Name') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputName" class="form-control" name="name" placeholder="<?php e__('Enter the category name') ?>." value="<?php echo $readProductCategory["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectServerID" class="col-sm-2 col-form-label"><?php e__('Server') ?>:</label>
                    <div class="col-sm-10">
                      <?php
                        $servers = $db->query("SELECT * FROM Servers");
                      ?>
                      <select id="selectServerID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="serverID" <?php echo ($servers->rowCount() == 0) ? "disabled" : null; ?>>
                        <?php if ($servers->rowCount() > 0): ?>
                          <?php foreach ($servers as $readServers): ?>
                            <option value="<?php echo $readServers["id"]; ?>" <?php echo (($readProductCategory["serverID"] == $readServers["id"]) ? 'selected="selected"' : null); ?>><?php echo $readServers["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <option><?php e__('Server not found!') ?>!</option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectParentID" class="col-sm-2 col-form-label"><?php e__('Parent Category') ?>:</label>
                    <div class="col-sm-10">
                      <div id="c-loading" style="display: none; margin-top: 7px"><?php e__('Loading') ?>...</div>
                      <div id="product-categories">
                        <?php
                          $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE id != ? AND serverID = ?");
                          $productCategories->execute(array($readProductCategory["id"], $readProductCategory["serverID"]));
                        ?>
                        <select id="selectParentID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="parentID">
                          <option value="0"><?php e__('Uncategorized') ?></option>
                          <?php if ($productCategories->rowCount() > 0): ?>
                            <?php foreach ($productCategories as $readProductCategories): ?>
                              <option value="<?php echo $readProductCategories["id"]; ?>" <?php echo (($readProductCategory["parentID"] == $readProductCategories["id"]) ? 'selected="selected"' : null); ?>><?php echo $readProductCategories["name"]; ?></option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPriority" class="col-sm-2 col-form-label"><?php e__('Priority') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputPriority" class="form-control" name="priority" placeholder="<?php e__('Enter the priority') ?>." value="<?php echo $readProductCategory["priority"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectMinecraftStatus" class="col-sm-2 col-form-label"><?php e__('Show in GUI') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectMinecraftStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="minecraftStatus">
                        <option value="0" <?php echo ($readProductCategory["minecraftStatus"] == 0) ? 'selected="selected"' : null; ?>><?php e__('No') ?></option>
                        <option value="1" <?php echo ($readProductCategory["minecraftStatus"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Yes') ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="minecraftBlock" style="<?php echo ($readProductCategory["minecraftStatus"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <label for="input-minecrafttitle" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Title') ?>:</label>
                      <div class="col-sm-10">
                        <input type="text" id="input-minecrafttitle" class="form-control" name="minecraftTitle" placeholder="<?php e__('If you leave it blank, the product name will be used. Color codes (&) are acceptable.') ?>." value="<?php echo $readProductCategory["minecraftTitle"]; ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="input-minecraftdesc" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Description (lore)') ?>:</label>
                      <div class="col-sm-10">
                        <textarea id="input-minecraftdesc" class="form-control" name="minecraftDescription" placeholder="<?php e__('You can leave it blank. Color codes (&) are acceptable.') ?>."><?php echo $readProductCategory["minecraftDescription"]; ?></textarea>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectMinecraftItem" class="col-sm-2 col-form-label"><?php e__('Minecraft GUI Icon') ?>:</label>
                      <div class="col-sm-10">
                        <div id="mi-loading" style="margin-top: 7px"><?php e__('Loading...') ?></div>
                        <div id="product-minecraftitems" style="display: none;" data-type="update" data-selected="<?php echo $readProductCategory["minecraftItem"]; ?>">
                          <select id="selectMinecraftItem" class="form-control" name="minecraftItem">
                            <option value=""><?php e__('None') ?></option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="fileImage" class="col-sm-2 col-form-label"><?php e__('Image') ?>:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage active">
                        <div class="di-thumbnail">
                          <img src="/apps/main/public/assets/img/store/categories/<?php echo $readProductCategory["imageID"].'.'.$readProductCategory["imageType"]; ?>" alt="Ön İzleme">
                        </div>
                        <div class="di-select">
                          <label for="fileImage"><?php e__('Select Image') ?></label>
                          <input type="file" id="fileImage" name="image" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateProductCategories'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/store/categories/delete/<?php echo $readProductCategory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/store/<?php echo $readProductCategory["serverSlug"]; ?>/<?php echo $readProductCategory["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateProductCategories"><?php e__('Save Changes') ?></button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteProductCategory = $db->prepare("DELETE FROM ProductCategories WHERE id = ?");
      $deleteProductCategory->execute(array(get("id")));
      go("/dashboard/store/categories");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'coupon'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Coupons') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Coupons') ?></li>
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
          <?php $productCoupons = $db->query("SELECT * FROM ProductCoupons ORDER BY id DESC"); ?>
          <?php if ($productCoupons->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["productCouponID", "productCouponName", "productCouponDuration", "productCouponPiece", "productCouponPieceStock", "productCouponCreationDate"]'>
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <div class="row align-items-center">
                      <div class="col-auto pr-0">
                        <span class="fe fe-search text-muted"></span>
                      </div>
                      <div class="col">
                        <input type="search" class="form-control form-control-flush search" name="search" placeholder="<?php e__('Search') ?>">
                      </div>
                    </div>
                  </div>
                  <div class="col-auto">
                    <a class="btn btn-sm btn-white" href="/dashboard/store/coupons/create"><?php e__('Add Coupon') ?></a>
                  </div>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 40px;">
                          <a href="#" class="text-muted sort" data-sort="productCouponID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productCouponName">
                              <?php e__('Coupon Name') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productCouponDuration">
                              <?php e__('Time Left') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productCouponPiece">
                              <?php e__('Amount') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productCouponPieceStock">
                              <?php e__('Amount Left') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productCouponCreationDate">
                              <?php e__('Date') ?>
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($productCoupons as $readProductCoupons): ?>
                        <tr>
                          <td class="productCouponID text-center" style="width: 40px;">
                            <a href="/dashboard/store/coupons/edit/<?php echo $readProductCoupons["id"]; ?>">
                              #<?php echo $readProductCoupons["id"]; ?>
                            </a>
                          </td>
                          <td class="productCouponName">
                            <a href="/dashboard/store/coupons/edit/<?php echo $readProductCoupons["id"]; ?>">
                              <?php echo $readProductCoupons["name"]; ?>
                            </a>
                          </td>
                          <td class="productCouponDuration">
                            <?php echo (($readProductCoupons["expiryDate"] == '1000-01-01 00:00:00') ? t__('Lifetime') : getDuration($readProductCoupons["expiryDate"]).' '.e__('day')); ?>
                          </td>
                          <td class="productCouponPiece">
                            <?php echo (($readProductCoupons["piece"] == 0) ? t__('Unlimited') : $readProductCoupons["piece"].'x'); ?>
                          </td>
                          <td class="productCouponPieceStock">
                            <?php if ($readProductCoupons["piece"] == 0): ?>
                                <?php e__('Unlimited') ?>
                            <?php else: ?>
                              <?php
                                $productCouponsHistory = $db->prepare("SELECT * FROM ProductCouponsHistory WHERE couponID = ?");
                                $productCouponsHistory->execute(array($readProductCoupons["id"]));
                                echo (max($readProductCoupons["piece"]-$productCouponsHistory->rowCount(), 0).' adet');
                              ?>
                            <?php endif; ?>
                          </td>
                          <td class="productCouponCreationDate">
                            <?php echo convertTime($readProductCoupons["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/store/coupons/edit/<?php echo $readProductCoupons["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/store/coupons/delete/<?php echo $readProductCoupons["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'insert'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Add Coupon') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/coupons"><?php e__('Coupon') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Add') ?></li>
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
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["insertProductCoupons"])) {
              if (post("durationStatus") == 0) {
                $_POST["duration"] = '1000-01-01 00:00:00';
              }
              else {
                $_POST["duration"] = date("Y-m-d H:i:s", strtotime($_POST["duration"]));
              }
              if (post("pieceStatus") == 0) {
                $_POST["piece"] = 0;
              }
              if (post("productsStatus") == 0) {
                $_POST["products"] = 0;
              }
              if (!$csrf->validate('insertProductCoupons')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("name") == null || post("productsStatus") == null || post("products") == null || post("discount") == null || post("pieceStatus") == null || post("piece") == null || post("durationStatus") == null || post("duration") == null || post("minPayment") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else {
                $insertProductCoupons = $db->prepare("INSERT INTO ProductCoupons (name, products, discount, piece, minPayment, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $insertProductCoupons->execute(array(str_replace(" ", "", post("name")), post("products"), post("discount"), post("piece"), post("minPayment"), post("duration"), date("Y-m-d H:i:s")));
                echo alertSuccess(t__('Coupon has been added successfully!'));
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputName" class="col-sm-2 col-form-label"><?php e__('Copun Name') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputName" class="form-control" name="name" placeholder="<?php e__('Enter the coupon name') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectDurationStatus" class="col-sm-2 col-form-label"><?php e__('Duration') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectDurationStatus" class="form-control" name="durationStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0"><?php e__('Lifetime') ?></option>
                      <option value="1"><?php e__('Temporary') ?></option>
                    </select>
                  </div>
                </div>
                <div id="durationBlock" class="form-group row" style="display: none;">
                  <div class="col-sm-10 offset-sm-2">
                    <div class="input-group input-group-merge">
                      <input type="text" id="inputDuration" class="form-control form-control-prepended" name="duration" placeholder="<?php e__('Enter the expiration date of coupon') ?>." data-toggle="flatpickr" data-expirydate="true">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <span class="fe fe-clock"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectPieceStatus" class="col-sm-2 col-form-label"><?php e__('Amount') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectPieceStatus" class="form-control" name="pieceStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0"><?php e__('Unlimited') ?></option>
                      <option value="1"><?php e__('Limited') ?></option>
                    </select>
                  </div>
                </div>
                <div id="pieceBlock" class="form-group row" style="display: none;">
                  <div class="col-sm-10 offset-sm-2">
                    <div class="input-group input-group-merge">
                      <input type="number" id="inputPiece" class="form-control form-control-prepended" name="piece" placeholder="<?php e__('Enter the amount of coupon usage') ?>.">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <span class="fe fe-plus-circle"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputDiscount" class="col-sm-2 col-form-label"><?php e__('Discount') ?> (%):</label>
                  <div class="col-sm-10">
                    <div class="input-group input-group-merge">
                      <input type="number" id="inputDiscount" class="form-control form-control-prepended" name="discount" min="1" max="100" placeholder="<?php e__('Enter the percentage of discount') ?>.">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <span class="fe fe-percent"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputMinPayment" class="col-sm-2 col-form-label"><?php e__('Min Payment Amount') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputMinPayment" class="form-control" name="minPayment" placeholder="<?php e__('Enter how many credits the coupon will be valid for.') ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectProductsStatus" class="col-sm-2 col-form-label"><?php e__('Products') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectProductsStatus" class="form-control" name="productsStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0"><?php e__('All') ?></option>
                      <option value="1"><?php e__('Select Products') ?></option>
                    </select>
                  </div>
                </div>
                <div id="productsBlock" class="form-group row" style="display: none;">
                  <div class="col-sm-10 offset-sm-2">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <input type="text" class="form-control form-control-prepended" data-toggle="jstree-search" placeholder="<?php e__('Search Product') ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-search"></span>
                          </div>
                        </div>
                      </div>
                      <input type="hidden" data-toggle="jstree-value" name="products" value="">
                    </div>
                    <div data-toggle="jstree" json="/apps/dashboard/public/ajax/products.php"></div>
                  </div>
                </div>
                <?php echo $csrf->input('insertProductCoupons'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertProductCoupons"><?php e__('Add') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'update' && get("id")): ?>
    <?php
      $productCoupon = $db->prepare("SELECT * FROM ProductCoupons WHERE id = ?");
      $productCoupon->execute(array(get("id")));
      $readProductCoupon = $productCoupon->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Edit Coupon') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/coupons"><?php e__('Coupon') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/coupons"><?php e__('Edit') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($productCoupon->rowCount() > 0) ? $readProductCoupon["name"] : t__('Not found!'); ?></li>
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
          <?php if ($productCoupon->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateProductCoupons"])) {
                if (post("durationStatus") == 0) {
                  $_POST["duration"] = '1000-01-01 00:00:00';
                }
                else {
                  $_POST["duration"] = date("Y-m-d H:i:s", strtotime($_POST["duration"]));
                }
                if (post("pieceStatus") == 0) {
                  $_POST["piece"] = 0;
                }
                if (post("productsStatus") == 0) {
                  $_POST["products"] = 0;
                }
                if (!$csrf->validate('updateProductCoupons')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if (post("name") == null || post("productsStatus") == null || post("products") == null || post("discount") == null || post("pieceStatus") == null || post("piece") == null || post("durationStatus") == null || post("duration") == null || post("minPayment") == null) {
                  echo alertError(t__('Please fill all the fields!'));
                }
                else {
                  $updateProductCoupons = $db->prepare("UPDATE ProductCoupons SET name = ?, products = ?, discount = ?, expiryDate = ?, piece = ?, minPayment = ? WHERE id = ?");
                  $updateProductCoupons->execute(array(str_replace(" ", "", post("name")), post("products"), post("discount"), post("duration"), post("piece"), post("minPayment"), get("id")));
                  echo alertSuccess(t__('Changes has been saved successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label"><?php e__('Coupon Name') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputName" class="form-control" name="name" placeholder="<?php e__('Enter the coupon name') ?>." value="<?php echo $readProductCoupon["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectDurationStatus" class="col-sm-2 col-form-label"><?php e__('Duration') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectDurationStatus" class="form-control" name="durationStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readProductCoupon["expiryDate"] == '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>><?php e__('Lifetime') ?></option>
                        <option value="1" <?php echo ($readProductCoupon["expiryDate"] != '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>><?php e__('Temporary') ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="durationBlock" class="form-group row" style="<?php echo ($readProductCoupon["expiryDate"] == '1000-01-01 00:00:00') ? "display: none;" : "display: block;"; ?>">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="text" id="inputDuration" class="form-control form-control-prepended" name="duration" placeholder="<?php e__('Enter the expiration date of coupon') ?>." data-toggle="flatpickr" data-expirydate="true" value="<?php echo ($readProductCoupon["expiryDate"] != '1000-01-01 00:00:00') ? $readProductCoupon["expiryDate"] : null; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-clock"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectPieceStatus" class="col-sm-2 col-form-label"><?php e__('Amount') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectPieceStatus" class="form-control" name="pieceStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readProductCoupon["piece"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Unlimited') ?></option>
                        <option value="1" <?php echo ($readProductCoupon["piece"] != 0) ? 'selected="selected"' : null; ?>><?php e__('Limited') ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="pieceBlock" class="form-group row" style="<?php echo ($readProductCoupon["piece"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputPiece" class="form-control form-control-prepended" name="piece" placeholder="<?php e__('Enter the amount of coupon usage') ?>." value="<?php echo ($readProductCoupon["piece"] != 0) ? $readProductCoupon["piece"] : null; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-plus-circle"></span>
                          </div>
                        </div>
                      </div>
                      <?php if ($readProductCoupon["piece"] != 0): ?>
                        <?php
                          $productCouponsHistory = $db->prepare("SELECT * FROM ProductCouponsHistory WHERE couponID = ?");
                          $productCouponsHistory->execute(array($readProductCoupon["id"]));
                        ?>
                        <small class="form-text text-muted pt-2">
                          <strong><?php e__('Left') ?>:</strong> <?php echo (max($readProductCoupon["piece"]-$productCouponsHistory->rowCount().'x', 0)); ?>
                        </small>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputMinPayment" class="col-sm-2 col-form-label"><?php e__('Min Payment Amount') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputMinPayment" class="form-control" name="minPayment" placeholder="<?php e__('Enter how many credits the coupon will be valid for.') ?>" value="<?php echo $readProductCoupon["minPayment"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputDiscount" class="col-sm-2 col-form-label"><?php e__('Discount') ?> (%):</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputDiscount" class="form-control form-control-prepended" name="discount" min="1" max="100" placeholder="<?php e__('Enter the percentage of discount') ?>." value="<?php echo $readProductCoupon["discount"]; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-percent"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectProductsStatus" class="col-sm-2 col-form-label"><?php e__('Products') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectProductsStatus" class="form-control" name="productsStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readProductCoupon["products"] == 0) ? 'selected="selected"' : null; ?>><?php e__('All') ?></option>
                        <option value="1" <?php echo ($readProductCoupon["products"] != 0) ? 'selected="selected"' : null; ?>><?php e__('Select Product') ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="productsBlock" class="form-group row" style="<?php echo ($readProductCoupon["products"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="form-group">
                        <div class="input-group input-group-merge">
                          <input type="text" class="form-control form-control-prepended" data-toggle="jstree-search" placeholder="<?php e__('Search Product') ?>">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fe fe-search"></span>
                            </div>
                          </div>
                        </div>
                        <input type="hidden" data-toggle="jstree-value" name="products" value="<?php echo ($readProductCoupon["products"] != 0) ? $readProductCoupon["products"] : null; ?>">
                      </div>
                      <div data-toggle="jstree" json="/apps/dashboard/public/ajax/products.php"></div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateProductCoupons'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/store/coupons/delete/<?php echo $readProductCoupon["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateProductCoupons"><?php e__('Save Changes') ?></button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteProductCoupon = $db->prepare("DELETE FROM ProductCoupons WHERE id = ?");
      $deleteProductCoupon->execute(array(get("id")));
      go("/dashboard/store/coupons");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'credit'): ?>
  <?php if (get("action") == 'send'): ?>
    <?php
      if (get("id")) {
        $account = $db->prepare("SELECT * FROM Accounts WHERE id = ?");
        $account->execute(array(get("id")));
        $readAccount = $account->fetch();
      }
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Send Credit') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <?php if (get("id")): ?>
                        <li class="breadcrumb-item"><a href="/dashboard/store/credit/send"><?php e__('Send Credit') ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo ($account->rowCount() > 0) ? $readAccount["realname"] : t__('Not found!'); ?></li>
                      <?php else: ?>
                        <li class="breadcrumb-item active" aria-current="page"><?php e__('Send Credit') ?></li>
                      <?php endif; ?>
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
          <?php if (!get("id") || (get("id") && $account->rowCount() > 0)): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["insertCreditHistory"])) {
                if ((!get("id") && post("username") != null)) {
                  $account = $db->prepare("SELECT * FROM Accounts WHERE realname = ?");
                  $account->execute(array(post("username")));
                  $readAccount = $account->fetch();
                }
                if (!$csrf->validate('insertCreditHistory')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if ((!get("id") && post("username") == null) || post("type") == null || post("price") == null) {
                  echo alertError(t__('Please fill all the fields'));
                }
                else if (!get("id") && $account->rowCount() == 0) {
                  echo alertError(t__('User not found!'));
                }
                else {
                  if (post("type") == 3) {
                    $_POST["type"] = 2;
                  }
                  $insertCreditHistory = $db->prepare("INSERT INTO CreditHistory (accountID, paymentID, paymentAPI, paymentStatus, type, price, earnings, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                  $insertCreditHistory->execute(array($readAccount["id"], 0, post("paymentAPI"), 1, post("type"), post("price"), post("price")/$readSettings["creditMultiplier"], date("Y-m-d H:i:s")));
                  $updateAccounts = $db->prepare("UPDATE Accounts SET credit = credit + ? WHERE id = ?");
                  $updateAccounts->execute(array(post("price"), $readAccount["id"]));

                  if ($readSettings["webhookCreditURL"] != '0') {
                    require_once(__ROOT__."/apps/main/private/packages/class/webhook/webhook.php");
                    $search = array("%username%", "%credit%", "%money%");
                    $replace = array($readAccount["realname"], (int)post("price"), 0);
                    $webhookMessage = $readSettings["webhookCreditMessage"];
                    $webhookEmbed = $readSettings["webhookCreditEmbed"];
                    $postFields = (array(
                      'content'     => ($webhookMessage != '0') ? str_replace($search, $replace, $webhookMessage) : null,
                      'avatar_url'  => 'https://minotar.net/avatar/'.$readAccount["realname"].'/256.png',
                      'tts'         => false,
                      'embeds'      => array(
                        array(
                          'type'        => 'rich',
                          'title'       => $readSettings["webhookCreditTitle"],
                          'color'       => hexdec($readSettings["webhookCreditColor"]),
                          'description' => str_replace($search, $replace, $webhookEmbed),
                          'image'       => array(
                            'url' => ($readSettings["webhookCreditImage"] != '0') ? $readSettings["webhookCreditImage"] : null
                          ),
                          'footer'      =>
                          ($readSettings["webhookCreditAdStatus"] == 1) ? array(
                            'text'      => 'Powered by LeaderOS',
                            'icon_url'  => 'https://i.ibb.co/wNHKQ7B/leaderos-logo.png'
                          ) : array()
                        )
                      )
                    ));
                    $curl = new \LeaderOS\Http\Webhook($readSettings["webhookCreditURL"]);
                    $curl(json_encode($postFields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                  }

                  echo alertSuccess(t__('The credit has been sent successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <?php if (get("id")): ?>
                      <label for="staticUsername" class="col-sm-2 col-form-label"><?php e__('Username') ?>:</label>
                      <div class="col-sm-10">
                        <a id="staticUsername" class="form-control-plaintext" href="/dashboard/users/view/<?php echo $readAccount["id"]; ?>">
                          <?php echo $readAccount["realname"]; ?>
                        </a>
                      </div>
                    <?php else: ?>
                      <label for="inputUsername" class="col-sm-2 col-form-label"><?php e__('Username') ?>:</label>
                      <div class="col-sm-10">
                        <input type="text" id="inputUsername" class="form-control" name="username" placeholder="<?php e__('Enter the username to send credit') ?>.">
                      </div>
                    <?php endif; ?>
                  </div>
                  <div class="form-group row">
                    <label for="inputPrice" class="col-sm-2 col-form-label"><?php e__('Sending Amount') ?>:</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputPrice" class="form-control form-control-prepended" name="price" placeholder="<?php e__('Enter the amount of credit to send') ?>.">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fa fa-coins"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectAPIID" class="col-sm-2 col-form-label"><?php e__('Payment Gateway') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectAPIID" class="form-control" name="paymentAPI" data-toggle="select" data-minimum-results-for-search="-1">
                        <?php
                          $paymentSettings = $db->prepare("SELECT name, slug FROM PaymentSettings WHERE status = ? ORDER BY slug ASC");
                          $paymentSettings->execute(array(1));
                        ?>
                        <?php if ($paymentSettings->rowCount() > 0): ?>
                          <?php foreach ($paymentSettings as $readPaymentSettings): ?>
                            <option value="<?php echo $readPaymentSettings["slug"]; ?>"><?php echo $readPaymentSettings["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectType" class="col-sm-2 col-form-label"><?php e__('Payment Way') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectType" class="form-control" name="type" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="1"><?php e__('Mobile Payment') ?></option>
                        <option value="2"><?php e__('Credit Card Payment') ?></option>
                        <option value="3" disabled="disabled"><?php e__('EFT') ?></option>
                      </select>
                    </div>
                  </div>
                  <?php echo $csrf->input('insertCreditHistory'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <button type="submit" class="btn btn-rounded btn-success" name="insertCreditHistory"><?php e__('Send Credit') ?></button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('User not found!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'chest'): ?>
  <?php if (get("action") == 'send'): ?>
    <?php
      if (get("id")) {
        $account = $db->prepare("SELECT * FROM Accounts WHERE id = ?");
        $account->execute(array(get("id")));
        $readAccount = $account->fetch();
      }
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Send Chest Item') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <?php if (get("id")): ?>
                        <li class="breadcrumb-item"><a href="/dashboard/store/chest-item/send"><?php e__('Send Chest Item') ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo ($account->rowCount() > 0) ? $readAccount["realname"] : t__('Not found!'); ?></li>
                      <?php else: ?>
                        <li class="breadcrumb-item active" aria-current="page"><?php e__('Send Chest Item') ?></li>
                      <?php endif; ?>
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
          <?php if (!get("id") || (get("id") && $account->rowCount() > 0)): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["sendChestItem"])) {
                if ((!get("id") && post("username") != null)) {
                  $account = $db->prepare("SELECT * FROM Accounts WHERE realname = ?");
                  $account->execute(array(post("username")));
                  $readAccount = $account->fetch();
                }
                if (!$csrf->validate('sendChestItem')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if ((!get("id") && post("username") == null) || post("productID") == null || post("amount") == null) {
                  echo alertError(t__('Please fill all the fields!'));
                }
                else if (!get("id") && $account->rowCount() == 0) {
                  echo alertError(t__('User not found!'));
                }
                else {
                  for ($i = 0; $i < post("amount"); $i++) {
                    $insertChests = $db->prepare("INSERT INTO Chests (accountID, productID, status, creationDate) VALUES (?, ?, ?, ?)");
                    $insertChests->execute(array($readAccount["id"], post("productID"), 0, date("Y-m-d H:i:s")));
                  }
                  echo alertSuccess(t__('Product has been sent successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <?php if (get("id")): ?>
                      <label for="staticUsername" class="col-sm-2 col-form-label"><?php e__('Username') ?>:</label>
                      <div class="col-sm-10">
                        <a id="staticUsername" class="form-control-plaintext" href="/dashboard/users/view/<?php echo $readAccount["id"]; ?>">
                          <?php echo $readAccount["realname"]; ?>
                        </a>
                      </div>
                    <?php else: ?>
                      <label for="inputUsername" class="col-sm-2 col-form-label"><?php e__('Username') ?>:</label>
                      <div class="col-sm-10">
                        <input type="text" id="inputUsername" class="form-control" name="username" placeholder="<?php e__('Enter the username to Send Chest Item') ?>.">
                      </div>
                    <?php endif; ?>
                  </div>
                  <div class="form-group row">
                    <label for="selectServerID" class="col-sm-2 col-form-label"><?php e__('Server') ?>:</label>
                    <div class="col-sm-10">
                      <?php $servers = $db->query("SELECT * FROM Servers"); ?>
                      <select id="selectServerID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" <?php echo ($servers->rowCount() == 0) ? "disabled" : null; ?>>
                        <?php if ($servers->rowCount() > 0): ?>
                          <?php foreach ($servers as $readServers): ?>
                            <option value="<?php echo $readServers["id"]; ?>"><?php echo $readServers["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <option><?php e__('Server not found!') ?></option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectProductID" class="col-sm-2 col-form-label"><?php e__('Product') ?>:</label>
                    <div class="col-sm-10">
                      <div id="c-loading" style="display: none; margin-top: 7px"><?php e__('Loading') ?>...</div>
                      <div id="products">
                        <?php
                          $firstServer = $db->query("SELECT * FROM Servers ORDER BY id ASC LIMIT 1");
                          $readFirstServer = $firstServer->fetch();

                          $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ?");
                          $productCategories->execute(array($readFirstServer["id"]));
                        ?>
                        <select name="productID" id="selectProductID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1">
                          <?php foreach ($productCategories as $readCategories): ?>
                            <optgroup label="<?php echo $readCategories["name"]; ?>" data-select2-id="<?php echo $readCategories["id"]; ?>">
                              <?php
                                $products = $db->prepare("SELECT * FROM Products WHERE serverID = ? AND categoryID = ?");
                                $products->execute(array($readCategories["serverID"], $readCategories["id"]));
                              ?>
                              <?php foreach ($products as $readProducts): ?>
                                <option value="<?php echo $readProducts["id"]; ?>"><?php echo $readProducts["name"]; ?></option>
                              <?php endforeach; ?>
                            </optgroup>
                          <?php endforeach; ?>

                          <?php
                            $products = $db->prepare("SELECT * FROM Products WHERE serverID = ? AND categoryID = ?");
                            $products->execute(array($readFirstServer["id"], 0));
                          ?>
                          <?php if ($products->rowCount() > 0): ?>
                            <optgroup label="<?php e__('Other') ?>" data-select2-id="0">
                              <?php foreach ($products as $readProducts): ?>
                                <option value="<?php echo $readProducts["id"]; ?>"><?php echo $readProducts["name"]; ?></option>
                              <?php endforeach; ?>
                            </optgroup>
                          <?php endif; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputAmount" class="col-sm-2 col-form-label"><?php e__('Amount') ?>:</label>
                    <div class="col-sm-10">
                      <input type="number" id="inputAmount" class="form-control" name="amount" placeholder="<?php e__('Enter the quantity of items to be sent.') ?>" value="1">
                    </div>
                  </div>
                  <?php echo $csrf->input('sendChestItem'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <button type="submit" class="btn btn-rounded btn-success" name="sendChestItem"><?php e__('Send Chest Item') ?></button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('User not found!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'discount'): ?>
  <?php if (get("action") == 'update'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Bulk Discount') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Bulk Discount') ?></li>
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
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["updateDiscount"])) {
              if (post("storeDiscountStatus") == 0) {
                $_POST["storeDiscount"] = 0;
              }
              if (post("storeDiscountDurationStatus") == 0) {
                $_POST["storeDiscountDuration"] = '1000-01-01 00:00:00';
              }
              else {
                $_POST["storeDiscountDuration"] = date("Y-m-d H:i:s", strtotime($_POST["storeDiscountDuration"]));
              }
              if (post("storeDiscountProductStatus") == 0) {
                $_POST["storeDiscountProducts"] = 0;
              }
              if (!$csrf->validate('updateDiscount')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("storeDiscountStatus") == null || post("storeDiscount") == null || post("storeDiscountDurationStatus") == null || post("storeDiscountDuration") == null || post("storeDiscountProductStatus") == null || post("storeDiscountProducts") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else {
                $updateSettings = $db->prepare("UPDATE Settings SET storeDiscount = ?, storeDiscountExpiryDate = ?, storeDiscountProducts = ? WHERE id = ?");
                $updateSettings->execute(array(post("storeDiscount"), post("storeDiscountDuration"), post("storeDiscountProducts"), $readSettings["id"]));
                echo alertSuccess(t__('Changes has been saved successfully!'));
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="selectStoreDiscountStatus" class="col-sm-2 col-form-label"><?php e__('Discount Status') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectStoreDiscountStatus" class="form-control" name="storeDiscountStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["storeDiscount"] == 0) ? 'selected="selected"' : null; ?>><?php e__('Disable') ?></option>
                      <option value="1" <?php echo ($readSettings["storeDiscount"] != 0) ? 'selected="selected"' : null; ?>><?php e__('Active') ?></option>
                    </select>
                  </div>
                </div>
                <div id="storeDiscountBlock" style="<?php echo ($readSettings["storeDiscount"] == 0) ? "display: none;" : "display: block;"; ?>">
                  <div class="form-group row">
                    <label for="inputStoreDiscount" class="col-sm-2 col-form-label"><?php e__('Discount') ?> (%):</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputStoreDiscount" class="form-control form-control-prepended" name="storeDiscount" aria-describedby="storeDiscountHelp" min="1" max="100" placeholder="<?php e__('Enter the percentage of discount') ?>." value="<?php echo ($readSettings["storeDiscount"] > 0) ? $readSettings["storeDiscount"] : null; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-percent"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectStoreDiscountDurationStatus" class="col-sm-2 col-form-label"><?php e__('Duration') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectStoreDiscountDurationStatus" class="form-control" name="storeDiscountDurationStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readSettings["storeDiscountExpiryDate"] == '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>><?php e__('Lifetime') ?></option>
                        <option value="1" <?php echo ($readSettings["storeDiscountExpiryDate"] != '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>><?php e__('Temporary') ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="storeDiscountDurationBlock" class="form-group row" style="<?php echo ($readSettings["storeDiscountExpiryDate"] == '1000-01-01 00:00:00') ? "display: none;" : "display: block;"; ?>">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="text" id="inputStoreDiscountDuration" class="form-control form-control-prepended" name="storeDiscountDuration" placeholder="<?php e__('Enter the expiration date of discount') ?>." data-toggle="flatpickr" data-expirydate="true" value="<?php echo ($readSettings["storeDiscountExpiryDate"] != '1000-01-01 00:00:00') ? $readSettings["storeDiscountExpiryDate"] : null; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-clock"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectStoreDiscountProductsStatus" class="col-sm-2 col-form-label"><?php e__('Products') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectStoreDiscountProductsStatus" class="form-control" name="storeDiscountProductStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readSettings["storeDiscountProducts"] == 0) ? 'selected="selected"' : null; ?>><?php e__('All') ?></option>
                        <option value="1" <?php echo ($readSettings["storeDiscountProducts"] != 0) ? 'selected="selected"' : null; ?>><?php e__('Select Products') ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="storeDiscountProductsBlock" class="form-group row" style="<?php echo ($readSettings["storeDiscountProducts"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="form-group">
                        <div class="input-group input-group-merge">
                          <input type="text" class="form-control form-control-prepended" data-toggle="jstree-search" placeholder="<?php e__('Search') ?>">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fe fe-search"></span>
                            </div>
                          </div>
                        </div>
                        <input type="hidden" data-toggle="jstree-value" name="storeDiscountProducts" value="<?php echo ($readSettings["storeDiscountProducts"] != 0) ? $readSettings["storeDiscountProducts"] : null; ?>">
                      </div>
                      <div data-toggle="jstree" json="/apps/dashboard/public/ajax/products.php"></div>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('updateDiscount'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateDiscount"><?php e__('Save Changes') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
<?php elseif (get("target") == 'chest-history'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Chest Logs') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Chest Logs') ?></li>
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
          <?php
            if (isset($_GET["page"])) {
              if (!is_numeric($_GET["page"])) {
                $_GET["page"] = 1;
              }
              $page = intval(get("page"));
            }
            else {
              $page = 1;
            }

            $visiblePageCount = 5;
            $limit = 50;

            $chestsHistory = $db->query("SELECT CH.id FROM ChestsHistory CH INNER JOIN Accounts A ON CH.accountID = A.id INNER JOIN Chests C ON CH.chestID = C.id INNER JOIN Products P ON C.productID = P.id INNER JOIN Servers S ON P.serverID = S.id");
            $itemsCount = $chestsHistory->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $chestsHistory = $db->query("SELECT CH.*, A.realname, P.name as productName, S.name as serverName FROM ChestsHistory CH INNER JOIN Accounts A ON CH.accountID = A.id INNER JOIN Chests C ON CH.chestID = C.id INNER JOIN Products P ON C.productID = P.id INNER JOIN Servers S ON P.serverID = S.id ORDER BY CH.id DESC LIMIT $visibleItemsCount, $limit");

            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $chestsHistory = $db->prepare("SELECT CH.*, A.realname, P.name as productName, S.name as serverName FROM ChestsHistory CH INNER JOIN Accounts A ON CH.accountID = A.id INNER JOIN Chests C ON CH.chestID = C.id INNER JOIN Products P ON C.productID = P.id INNER JOIN Servers S ON P.serverID = S.id WHERE A.realname LIKE :search ORDER BY CH.id DESC");
                $chestsHistory->execute(array(
                  "search" => '%'.post("query").'%'
                ));
              }
            }
          ?>
          <?php if ($chestsHistory->rowCount() > 0): ?>
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <form action="" method="post" class="d-flex align-items-center w-100">
                    <div class="col">
                      <div class="row align-items-center">
                        <div class="col-auto pr-0">
                          <span class="fe fe-search text-muted"></span>
                        </div>
                        <div class="col">
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="<?php e__('Search (Username)') ?>" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success"><?php e__('Search') ?></button>
                    </div>
                  </form>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 40px;">#ID</th>
                        <th><?php e__('Username') ?></th>
                        <th><?php e__('Product') ?></th>
                        <th><?php e__('Server') ?></th>
                        <th class="text-center"><?php e__('Process') ?></th>
                        <th><?php e__('Date') ?></th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($chestsHistory as $readChestsHistory): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            #<?php echo $readChestsHistory["id"]; ?>
                          </td>
                          <td>
                            <a href="/dashboard/users/view/<?php echo $readChestsHistory["accountID"]; ?>">
                              <?php echo $readChestsHistory["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php echo $readChestsHistory["productName"]; ?>
                          </td>
                          <td>
                            <?php echo $readChestsHistory["serverName"]; ?>
                          </td>
                          <td class="text-center">
                            <?php if ($readChestsHistory["type"] == 1): ?>
                              <i class="fa fa-check" data-toggle="tooltip" data-placement="top" title="<?php e__('Delivery') ?>"></i>
                            <?php elseif ($readChestsHistory["type"] == 2): ?>
                              <i class="fa fa-gift" data-toggle="tooltip" data-placement="top" title="<?php e__('Gift (Sender)') ?>"></i>
                            <?php elseif ($readChestsHistory["type"] == 3): ?>
                              <i class="fa fa-gift" data-toggle="tooltip" data-placement="top" title="<?php e__('Gift (Receiver)') ?>"></i>
                            <?php else: ?>
                              <i class="fa fa-check"></i>
                            <?php endif; ?>
                          </td>
                          <td>
                            <?php echo convertTime($readChestsHistory["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/store/chest-logs/delete/<?php echo $readChestsHistory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <?php if (post("query") == false): ?>
              <nav class="pt-3 pb-5" aria-label="Page Navigation">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo ($page == 1) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/store/chest-logs/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/dashboard/store/chest-logs/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/store/chest-logs/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
                  </li>
                </ul>
              </nav>
            <?php endif; ?>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteChestsHistory = $db->prepare("DELETE FROM ChestsHistory WHERE id = ?");
      $deleteChestsHistory->execute(array(get("id")));
      go("/dashboard/store/chest-logs");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'coupon-history'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Coupon History') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Coupon History') ?></li>
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
          <?php
            if (isset($_GET["page"])) {
              if (!is_numeric($_GET["page"])) {
                $_GET["page"] = 1;
              }
              $page = intval(get("page"));
            }
            else {
              $page = 1;
            }
    
            $visiblePageCount = 5;
            $limit = 50;
    
            $productCouponsHistory = $db->query("SELECT PCH.id FROM ProductCouponsHistory PCH INNER JOIN Accounts A ON PCH.accountID = A.id INNER JOIN ProductCoupons PC ON PCH.couponID = PC.id");
            $itemsCount = $productCouponsHistory->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $productCouponsHistory = $db->query("SELECT PCH.*, A.realname, PC.name as couponName FROM ProductCouponsHistory PCH INNER JOIN Accounts A ON PCH.accountID = A.id INNER JOIN ProductCoupons PC ON PCH.couponID = PC.id ORDER BY PCH.id DESC LIMIT $visibleItemsCount, $limit");
    
            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $productCouponsHistory = $db->prepare("SELECT PCH.*, A.realname, PC.name as couponName FROM ProductCouponsHistory PCH INNER JOIN Accounts A ON PCH.accountID = A.id INNER JOIN ProductCoupons PC ON PCH.couponID = PC.id WHERE A.realname LIKE :search ORDER BY PCH.id DESC");
                $productCouponsHistory->execute(array(
                  "search" => '%'.post("query").'%'
                ));
              }
            }
          ?>
          <?php if ($productCouponsHistory->rowCount() > 0): ?>
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <form action="" method="post" class="d-flex align-items-center w-100">
                    <div class="col">
                      <div class="row align-items-center">
                        <div class="col-auto pr-0">
                          <span class="fe fe-search text-muted"></span>
                        </div>
                        <div class="col">
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="<?php e__('Search (Username)') ?>" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success"><?php e__('Search') ?></button>
                    </div>
                  </form>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 40px;">#ID</th>
                        <th><?php e__('Username') ?></th>
                        <th><?php e__('Coupon') ?></th>
                        <th><?php e__('Date') ?></th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($productCouponsHistory as $readProductCouponsHistory): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            #<?php echo $readProductCouponsHistory["id"]; ?>
                          </td>
                          <td>
                            <a href="/dashboard/users/view/<?php echo $readProductCouponsHistory["accountID"]; ?>">
                              <?php echo $readProductCouponsHistory["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php echo $readProductCouponsHistory["couponName"]; ?>
                          </td>
                          <td>
                            <?php echo convertTime($readProductCouponsHistory["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/store/coupon-logs/delete/<?php echo $readProductCouponsHistory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <?php if (post("query") == false): ?>
              <nav class="pt-3 pb-5" aria-label="Page Navigation">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo ($page == 1) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/store/coupon-logs/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/dashboard/store/coupon-logs/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/store/coupon-logs/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
                  </li>
                </ul>
              </nav>
            <?php endif; ?>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
        $deleteProductCouponsHistory = $db->prepare("DELETE FROM ProductCouponsHistory WHERE id = ?");
        $deleteProductCouponsHistory->execute(array(get("id")));
        go("/dashboard/store/coupon-logs");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'credit-purchase-history'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Credit Purchase History') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Credit Purchase History') ?></li>
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
          <?php
            if (isset($_GET["page"])) {
              if (!is_numeric($_GET["page"])) {
                $_GET["page"] = 1;
              }
              $page = intval(get("page"));
            }
            else {
              $page = 1;
            }

            $visiblePageCount = 5;
            $limit = 50;

            $creditHistory = $db->prepare("SELECT CH.id FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE CH.type IN (?, ?) AND CH.paymentStatus = ?");
            $creditHistory->execute(array(1, 2, 1));
            $itemsCount = $creditHistory->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $creditHistory = $db->prepare("SELECT CH.*, A.realname FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE CH.type IN (?, ?) AND CH.paymentStatus = ? ORDER BY CH.id DESC LIMIT $visibleItemsCount, $limit");
            $creditHistory->execute(array(1, 2, 1));

            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $creditHistory = $db->prepare("SELECT CH.*, A.realname FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE A.realname LIKE :search AND CH.type IN (:mobileType, :creditCardType) AND CH.paymentStatus = :paymentStatus ORDER BY CH.id DESC");
                $creditHistory->execute(array(
                  "search"          => '%'.post("query").'%',
                  "paymentStatus"   => 1,
                  "mobileType"      => 1,
                  "creditCardType"  => 2
                ));
              }
            }
          ?>
          <?php if ($creditHistory->rowCount() > 0): ?>
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <form action="" method="post" class="d-flex align-items-center w-100">
                    <div class="col">
                      <div class="row align-items-center">
                        <div class="col-auto pr-0">
                          <span class="fe fe-search text-muted"></span>
                        </div>
                        <div class="col">
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="<?php e__('Search (Username)') ?>" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success"><?php e__('Search') ?></button>
                    </div>
                  </form>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 40px;">#ID</th>
                        <th><?php e__('Username') ?></th>
                        <th class="text-center"><?php e__('Amount') ?></th>
                        <th class="text-center"><?php e__('Earning') ?></th>
                        <th class="text-center"><?php e__('Payment') ?></th>
                        <th class="text-center"><?php e__('Payment Gateway') ?></th>
                        <th><?php e__('Date') ?></th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($creditHistory as $readCreditHistory): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            #<?php echo $readCreditHistory["id"]; ?>
                          </td>
                          <td>
                            <a href="/dashboard/users/view/<?php echo $readCreditHistory["accountID"]; ?>">
                              <?php echo $readCreditHistory["realname"]; ?>
                            </a>
                          </td>
                          <td class="text-center">
                            <?php echo ($readCreditHistory["type"] == 3 || $readCreditHistory["type"] == 5) ? '<span class="text-danger">-'.$readCreditHistory["price"].'</span>' : '<span class="text-success">+'.$readCreditHistory["price"].'</span>'; ?>
                          </td>
                          <td class="text-center">
                            <?php echo $readCreditHistory["earnings"]; ?>
                          </td>
                          <td class="text-center">
                            <?php if ($readCreditHistory["type"] == 1): ?>
                              <i class="fa fa-mobile" data-toggle="tooltip" data-placement="top" title="<?php e__('Mobile Payment') ?>"></i>
                            <?php elseif ($readCreditHistory["type"] == 2): ?>
                              <i class="fa fa-credit-card" data-toggle="tooltip" data-placement="top" title="<?php e__('Credit Card Payment') ?>"></i>
                            <?php elseif ($readCreditHistory["type"] == 3): ?>
                              <i class="fa fa-paper-plane" data-toggle="tooltip" data-placement="top" title="<?php e__('Transfer (Sender)') ?>"></i>
                            <?php elseif ($readCreditHistory["type"] == 4): ?>
                              <i class="fa fa-paper-plane" data-toggle="tooltip" data-placement="top" title="<?php e__('Transfer (Receiver)') ?>"></i>
                            <?php elseif ($readCreditHistory["type"] == 5): ?>
                              <i class="fa fa-ticket-alt" data-toggle="tooltip" data-placement="top" title="<?php e__('Wheel of Fortune (Ticket)') ?>"></i>
                            <?php elseif ($readCreditHistory["type"] == 6): ?>
                              <i class="fa fa-ticket-alt" data-toggle="tooltip" data-placement="top" title="<?php e__('Wheel of Fortune (Earning)') ?>"></i>
                            <?php else: ?>
                              <i class="fa fa-paper-plane"></i>
                            <?php endif; ?>
                          </td>
                          <td class="text-center">
                            <?php
                              $paymentAPI = $db->prepare("SELECT name FROM PaymentSettings WHERE slug = ?");
                              $paymentAPI->execute(array($readCreditHistory["paymentAPI"]));
                              $readPaymentAPI = $paymentAPI->fetch();

                              if ($paymentAPI->rowCount() > 0) {
                                echo $readPaymentAPI["name"];
                              }
                              else {
                                 e__('Other');
                              }
                            ?>
                          </td>
                          <td>
                            <?php echo convertTime($readCreditHistory["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/store/credit-purchase-logs/delete/<?php echo $readCreditHistory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <?php if (post("query") == false): ?>
              <nav class="pt-3 pb-5" aria-label="Page Navigation">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo ($page == 1) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/store/credit-purchase-logs/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/dashboard/store/credit-purchase-logs/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/store/credit-purchase-logs/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
                  </li>
                </ul>
              </nav>
            <?php endif; ?>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteCreditHistory = $db->prepare("DELETE FROM CreditHistory WHERE id = ?");
      $deleteCreditHistory->execute(array(get("id")));
      go("/dashboard/store/credit-purchase-logs");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'credit-usage-history'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Credit Usage History') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Credit Usage History') ?></li>
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
          <?php
            if (isset($_GET["page"])) {
              if (!is_numeric($_GET["page"])) {
                $_GET["page"] = 1;
              }
              $page = intval(get("page"));
            }
            else {
              $page = 1;
            }

            $visiblePageCount = 5;
            $limit = 50;

            $creditHistory = $db->prepare("SELECT CH.id FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE CH.type != ? AND CH.type != ? AND CH.paymentStatus = ?");
            $creditHistory->execute(array(1, 2, 1));
            $itemsCount = $creditHistory->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $creditHistory = $db->prepare("SELECT CH.*, A.realname FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE CH.type != ? AND CH.type != ? AND CH.paymentStatus = ? ORDER BY CH.id DESC LIMIT $visibleItemsCount, $limit");
            $creditHistory->execute(array(1, 2, 1));

            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $creditHistory = $db->prepare("SELECT CH.*, A.realname FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE A.realname LIKE :search AND CH.type != :mobileType AND CH.type != :creditCardType AND CH.paymentStatus = :paymentStatus ORDER BY CH.id DESC");
                $creditHistory->execute(array(
                  "search"        => '%'.post("query").'%',
                  "paymentStatus" => 1,
                  "mobileType"      => 1,
                  "creditCardType"  => 2
                ));
              }
            }
          ?>
          <?php if ($creditHistory->rowCount() > 0): ?>
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <form action="" method="post" class="d-flex align-items-center w-100">
                    <div class="col">
                      <div class="row align-items-center">
                        <div class="col-auto pr-0">
                          <span class="fe fe-search text-muted"></span>
                        </div>
                        <div class="col">
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="<?php e__('Search (Username)') ?>" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success"><?php e__('Search') ?></button>
                    </div>
                  </form>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 40px;">#ID</th>
                        <th><?php e__('Username') ?></th>
                        <th class="text-center"><?php e__('Amount') ?></th>
                        <th class="text-center"><?php e__('Payment') ?></th>
                        <th><?php e__('Date') ?></th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($creditHistory as $readCreditHistory): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            #<?php echo $readCreditHistory["id"]; ?>
                          </td>
                          <td>
                            <a href="/dashboard/users/view/<?php echo $readCreditHistory["accountID"]; ?>">
                              <?php echo $readCreditHistory["realname"]; ?>
                            </a>
                          </td>
                          <td class="text-center">
                            <?php echo ($readCreditHistory["type"] == 3 || $readCreditHistory["type"] == 5) ? '<span class="text-danger">-'.$readCreditHistory["price"].'</span>' : '<span class="text-success">+'.$readCreditHistory["price"].'</span>'; ?>
                          </td>
                          <td class="text-center">
                            <?php if ($readCreditHistory["type"] == 1): ?>
                              <i class="fa fa-mobile" data-toggle="tooltip" data-placement="top" title="<?php e__('Mobile Payment') ?>"></i>
                            <?php elseif ($readCreditHistory["type"] == 2): ?>
                              <i class="fa fa-credit-card" data-toggle="tooltip" data-placement="top" title="<?php e__('Credit Card Payment') ?>"></i>
                            <?php elseif ($readCreditHistory["type"] == 3): ?>
                              <i class="fa fa-paper-plane" data-toggle="tooltip" data-placement="top" title="<?php e__('Transfer (Sender)') ?>"></i>
                            <?php elseif ($readCreditHistory["type"] == 4): ?>
                              <i class="fa fa-paper-plane" data-toggle="tooltip" data-placement="top" title="<?php e__('Transfer (Receiver)') ?>"></i>
                            <?php elseif ($readCreditHistory["type"] == 5): ?>
                              <i class="fa fa-ticket-alt" data-toggle="tooltip" data-placement="top" title="<?php e__('Wheel of Fortune (Ticket)') ?>"></i>
                            <?php elseif ($readCreditHistory["type"] == 6): ?>
                              <i class="fa fa-ticket-alt" data-toggle="tooltip" data-placement="top" title="<?php e__('Wheel of Fortune (Earning)') ?>"></i>
                            <?php else: ?>
                              <i class="fa fa-paper-plane"></i>
                            <?php endif; ?>
                          </td>
                          <td>
                            <?php echo convertTime($readCreditHistory["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/store/credit-usage-logs/delete/<?php echo $readCreditHistory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <?php if (post("query") == false): ?>
              <nav class="pt-3 pb-5" aria-label="Page Navigation">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo ($page == 1) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/store/credit-usage-logs/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/dashboard/store/credit-usage-logs/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/store/credit-usage-logs/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
                  </li>
                </ul>
              </nav>
            <?php endif; ?>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteCreditHistory = $db->prepare("DELETE FROM CreditHistory WHERE id = ?");
      $deleteCreditHistory->execute(array(get("id")));
      go("/dashboard/store/credit-usage-logs");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'store-history'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Store History') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Store History') ?></li>
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
          <?php
            if (isset($_GET["page"])) {
              if (!is_numeric($_GET["page"])) {
                $_GET["page"] = 1;
              }
              $page = intval(get("page"));
            }
            else {
              $page = 1;
            }
    
            $visiblePageCount = 5;
            $limit = 50;
    
            $storeHistory = $db->query("SELECT id FROM Orders");
            $itemsCount = $storeHistory->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $storeHistory = $db->query("SELECT O.*, A.realname FROM Orders O INNER JOIN Accounts A ON O.accountID = A.id ORDER BY O.id DESC LIMIT $visibleItemsCount, $limit");
    
            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $storeHistory = $db->prepare("SELECT O.*, A.realname FROM Orders O INNER JOIN Accounts A ON O.accountID = A.id WHERE A.realname LIKE :search ORDER BY O.id DESC");
                $storeHistory->execute(array(
                  "search" => '%'.post("query").'%'
                ));
              }
            }
          ?>
          <?php if ($storeHistory->rowCount() > 0): ?>
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <form action="" method="post" class="d-flex align-items-center w-100">
                    <div class="col">
                      <div class="row align-items-center">
                        <div class="col-auto pr-0">
                          <span class="fe fe-search text-muted"></span>
                        </div>
                        <div class="col">
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="<?php e__('Search (Username)') ?>" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success"><?php e__('Search') ?></button>
                    </div>
                  </form>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 40px;">#ID</th>
                        <th><?php e__('Username') ?></th>
                        <th><?php e__('Subtotal') ?></th>
                        <th><?php e__('Date') ?></th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($storeHistory as $readStoreHistory): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            #<?php echo $readStoreHistory["id"]; ?>
                          </td>
                          <td>
                            <a href="/dashboard/users/view/<?php echo $readStoreHistory["accountID"]; ?>">
                              <?php echo $readStoreHistory["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php e__('%credit% credit(s)', ['%credit%' => $readStoreHistory["subtotal"]]); ?>
                          </td>
                          <td>
                            <?php echo convertTime($readStoreHistory["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/dashboard/store/store-logs/view/<?php echo $readStoreHistory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/store/store-logs/delete/<?php echo $readStoreHistory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <?php if (post("query") == false): ?>
              <nav class="pt-3 pb-5" aria-label="Page Navigation">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo ($page == 1) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/store/store-logs/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/dashboard/store/store-logs/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/store/store-logs/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
                  </li>
                </ul>
              </nav>
            <?php endif; ?>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'get' && get("id")): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Order') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dasboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/products"><?php e__('Store') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/store/store-logs"><?php e__('Store History') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Order') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <?php
          $order = $db->prepare("SELECT * FROM Orders WHERE id = ?");
          $order->execute(array(get("id")));
          $readOrder = $order->fetch();
        ?>
        <?php if ($order->rowCount() > 0): ?>
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <?php e__('Order') ?> #<?php echo $readOrder["id"]; ?>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                    <tr>
                      <th><?php e__('Product') ?></th>
                      <th><?php e__('Quantity') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                      $orderProducts = $db->prepare("SELECT P.name, OP.quantity FROM OrderProducts OP INNER JOIN Products P ON OP.productID = P.id WHERE OP.orderID = ?");
                      $orderProducts->execute(array($readOrder["id"]));
                    ?>
                    <?php foreach ($orderProducts as $readOrderProducts): ?>
                      <tr>
                        <td><?php echo $readOrderProducts["name"]; ?></td>
                        <td><?php echo $readOrderProducts["quantity"]; ?></td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <?php e__('Order Summary') ?>
              </div>
              <div class="card-body">
                <?php if ($readOrder["coupon"] != null): ?>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="input-group">
                        <input type="text" class="form-control" name="coupon" disabled readonly value="<?php echo $readOrder["coupon"]; ?>">
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <div class="row pt-4">
                  <div class="col">
                    <span class="font-weight-bold"><?php e__('Discount') ?>:</span>
                  </div>
                  <div class="col-auto text-right">
                    <span class="text-danger">
                      -<?php e__('%credit% credit(s)', ['%credit%' => $readOrder["discount"]]); ?>
                    </span>
                  </div>
                </div>
                <div class="row pt-4">
                  <div class="col">
                    <span class="font-weight-bold"><?php e__('Subtotal') ?>:</span>
                  </div>
                  <div class="col-auto text-right">
                    <span class="text-success">
                      <?php e__('%credit% credit(s)', ['%credit%' => $readOrder["subtotal"]]); ?>
                    </span>
                  </div>
                </div>
                <div class="row pt-4">
                  <div class="col">
                    <span class="font-weight-bold"><?php e__('Date') ?>:</span>
                  </div>
                  <div class="col-auto text-right">
                    <?php echo convertTime($readOrder["creationDate"], 2, true); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="col-md-12">
            <?php echo alertError(t__('No data for this page!')); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteStoreHistory = $db->prepare("DELETE FROM StoreHistory WHERE id = ?");
      $deleteStoreHistory->execute(array(get("id")));
      go("/dashboard/store/store-logs");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
