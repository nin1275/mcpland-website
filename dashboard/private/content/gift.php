<?php
  if (!checkPerm($readAdmin, 'MANAGE_GIFTS')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  if (get("target") == 'gift' && (get("action") == 'insert' || get("action") == 'update')) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/gift.products.js');
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/gift.type.js');
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.coupon.js');
  }
?>
<?php if (get("target") == 'gift'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("Gifts") ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard") ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Gifts") ?></li>
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
          <?php $productGifts = $db->query("SELECT * FROM ProductGifts ORDER BY id DESC"); ?>
          <?php if ($productGifts->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["productGiftID", "productGiftName", "productGiftProduct", "productGiftDuration", "productGiftPiece", "productGiftPieceStock", "productGiftCreationDate"]'>
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <div class="row align-items-center">
                      <div class="col-auto pr-0">
                        <span class="fe fe-search text-muted"></span>
                      </div>
                      <div class="col">
                        <input type="search" class="form-control form-control-flush search" name="search" placeholder="<?php e__("Search") ?>">
                      </div>
                    </div>
                  </div>
                  <div class="col-auto">
                    <a class="btn btn-sm btn-white" href="/dashboard/gifts/create"><?php e__("Add Gift") ?></a>
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
                          <a href="#" class="text-muted sort" data-sort="productGiftID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productGiftName">
                              <?php e__("Coupon Name") ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productGiftProduct">
                              <?php e__("Product") ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productGiftDuration">
                              <?php e__("Duration") ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productGiftPiece">
                              <?php e__("Amount") ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productGiftPieceStock">
                              <?php e__("Amount Left") ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productGiftCreationDate">
                              <?php e__("Date") ?>
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($productGifts as $readProductGifts): ?>
                        <tr>
                          <td class="productGiftID text-center" style="width: 40px;">
                            <a href="/dashboard/gifts/edit/<?php echo $readProductGifts["id"]; ?>">
                              #<?php echo $readProductGifts["id"]; ?>
                            </a>
                          </td>
                          <td class="productGiftName">
                            <a href="/dashboard/gifts/edit/<?php echo $readProductGifts["id"]; ?>">
                              <?php echo $readProductGifts["name"]; ?>
                            </a>
                          </td>
                          <td class="productGiftProduct">
                            <?php if ($readProductGifts["giftType"] == 1): ?>
                              <?php
                                $product = $db->prepare("SELECT name FROM Products WHERE id = ?");
                                $product->execute(array($readProductGifts["gift"]));
                                $readProduct = $product->fetch();
                                echo $readProduct["name"];
                              ?>
                            <?php else: ?>
                              <?php echo $readProductGifts["gift"]; ?> <?php t__("credit") ?>
                            <?php endif; ?>
                          </td>
                          <td class="productGiftDuration">
                            <?php echo (($readProductGifts["expiryDate"] == '1000-01-01 00:00:00') ? t__('Lifetime') : getDuration($readProductGifts["expiryDate"]).' '.t__('day')); ?>
                          </td>
                          <td class="productGiftPiece">
                            <?php echo (($readProductGifts["piece"] == 0) ? t__('Unlimited') : $readProductGifts["piece"].'x'); ?>
                          </td>
                          <td class="productGiftPieceStock">
                            <?php if ($readProductGifts["piece"] == 0): ?>
                                <?php e__("Unlimited") ?>
                            <?php else: ?>
                              <?php
                                $productGiftsHistory = $db->prepare("SELECT * FROM ProductGiftsHistory WHERE giftID = ?");
                                $productGiftsHistory->execute(array($readProductGifts["id"]));
                                echo (max($readProductGifts["piece"]-$productGiftsHistory->rowCount(), 0).'x');
                              ?>
                            <?php endif; ?>
                          </td>
                          <td class="productGiftCreationDate">
                            <?php echo convertTime($readProductGifts["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/gifts/edit/<?php echo $readProductGifts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Edit") ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/gifts/delete/<?php echo $readProductGifts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete") ?>">
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
                  <h2 class="header-title"><?php e__("Add Gift") ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard") ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/gifts"><?php e__("Gift") ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Add") ?></li>
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
            if (isset($_POST["insertProductGifts"])) {
              $checkGiftName = $db->prepare("SELECT id FROM ProductGifts WHERE name = ?");
              $checkGiftName->execute(array($slugify->slugify(str_replace(" ", "", post("name")))));
              if (post("durationStatus") == 0) {
                $_POST["duration"] = '1000-01-01 00:00:00';
              }
              else {
                $_POST["duration"] = date("Y-m-d H:i:s", strtotime($_POST["duration"]));
              }
              if (post("pieceStatus") == 0) {
                $_POST["piece"] = 0;
              }
              if (!$csrf->validate('insertProductGifts')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("name") == null || post("giftType") == null || post("gift") == null || post("pieceStatus") == null || post("piece") == null || post("durationStatus") == null || post("duration") == null) {
                echo alertError(t__('Please fill all the fields.'));
              }
              else if ($checkGiftName->rowCount() > 0) {
                echo alertError(t__('This coupon has already been created with this name!'));
              }
              else {
                $insertProductGifts = $db->prepare("INSERT INTO ProductGifts (name, giftType, gift, piece, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?, ?)");
                $insertProductGifts->execute(array($slugify->slugify(str_replace(" ", "", post("name"))), post("giftType"), post("gift"), post("piece"), post("duration"), date("Y-m-d H:i:s")));
                echo alertSuccess(t__('Gift has been added successfully!'));
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputName" class="col-sm-2 col-form-label"><?php e__("Coupon Name") ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputName" class="form-control" name="name" placeholder="<?php e__("Enter the coupon name") ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectGiftType" class="col-sm-2 col-form-label"><?php e__("Gift Type") ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectGiftType" class="form-control" name="giftType" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1"><?php e__("Product") ?></option>
                      <option value="2"><?php e__("Credit") ?></option>
                    </select>
                  </div>
                </div>
                <div id="productBlock">
                  <div class="form-group row">
                    <label for="selectServerID" class="col-sm-2 col-form-label"><?php e__("Server") ?>:</label>
                    <div class="col-sm-10">
                      <?php $servers = $db->query("SELECT * FROM Servers"); ?>
                      <select id="selectServerID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" <?php echo ($servers->rowCount() == 0) ? "disabled" : null; ?>>
                        <?php if ($servers->rowCount() > 0): ?>
                          <?php foreach ($servers as $readServers): ?>
                            <option value="<?php echo $readServers["id"]; ?>"><?php echo $readServers["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <option><?php e__("Server not found") ?>!</option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectProductID" class="col-sm-2 col-form-label"><?php e__("Product") ?>:</label>
                    <div class="col-sm-10">
                      <div id="c-loading" style="display: none; margin-top: 7px"><?php e__("Loading") ?>...</div>
                      <div id="products">
                        <?php
                          $firstServer = $db->query("SELECT * FROM Servers ORDER BY id ASC LIMIT 1");
                          $readFirstServer = $firstServer->fetch();

                          $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ?");
                          $productCategories->execute(array($readFirstServer["id"]));
                        ?>
                        <select id="selectProductID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="gift">
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
                            <optgroup label="<?php e__("Other") ?>" data-select2-id="0">
                              <?php foreach ($products as $readProducts): ?>
                                <option value="<?php echo $readProducts["id"]; ?>"><?php echo $readProducts["name"]; ?></option>
                              <?php endforeach; ?>
                            </optgroup>
                          <?php endif; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="creditBlock" style="display: none;">
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputCredit" class="form-control form-control-prepended" name="gift" placeholder="<?php e__("Enter the amount of credit to gift") ?>." disabled>
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fa fa-coins"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectDurationStatus" class="col-sm-2 col-form-label"><?php e__("Duration") ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectDurationStatus" class="form-control" name="durationStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0"><?php e__("Lifetime") ?></option>
                      <option value="1"><?php e__("Temporary") ?></option>
                    </select>
                  </div>
                </div>
                <div id="durationBlock" class="form-group row" style="display: none;">
                  <div class="col-sm-10 offset-sm-2">
                    <div class="input-group input-group-merge">
                      <input type="text" id="inputDuration" class="form-control form-control-prepended" name="duration" placeholder="<?php e__("Choose an expiration date") ?>." data-toggle="flatpickr" data-expirydate="true">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <span class="fe fe-clock"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectPieceStatus" class="col-sm-2 col-form-label"><?php e__("Amount") ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectPieceStatus" class="form-control" name="pieceStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0"><?php e__("Unlimited") ?></option>
                      <option value="1"><?php e__("Limited") ?></option>
                    </select>
                  </div>
                </div>
                <div id="pieceBlock" class="form-group row" style="display: none;">
                  <div class="col-sm-10 offset-sm-2">
                    <div class="input-group input-group-merge">
                      <input type="number" id="inputPiece" class="form-control form-control-prepended" name="piece" placeholder="<?php e__("Enter the amount of use") ?>.">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <span class="fe fe-plus-circle"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('insertProductGifts'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertProductGifts"><?php e__("Add") ?></button>
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
      $productGift = $db->prepare("SELECT * FROM ProductGifts WHERE id = ?");
      $productGift->execute(array(get("id")));
      $readProductGift = $productGift->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("Edit Gift") ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard") ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/gifts"><?php e__("Gift") ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/gifts"><?php e__("Edit") ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($productGift->rowCount() > 0) ? $readProductGift["name"] : t__('Not found!'); ?></li>
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
          <?php if ($productGift->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateProductGifts"])) {
                $checkGiftName = $db->prepare("SELECT id FROM ProductGifts WHERE name = ?");
                $checkGiftName->execute(array($slugify->slugify(str_replace(" ", "", post("name")))));
                if (post("durationStatus") == 0) {
                  $_POST["duration"] = '1000-01-01 00:00:00';
                }
                else {
                  $_POST["duration"] = date("Y-m-d H:i:s", strtotime($_POST["duration"]));
                }
                if (post("pieceStatus") == 0) {
                  $_POST["piece"] = 0;
                }
                if (!$csrf->validate('updateProductGifts')) {
                  echo alertError(t__('Something went wrong! Please try again later.'));
                }
                else if (post("name") == null || post("giftType") == null || post("gift") == null || post("pieceStatus") == null || post("piece") == null || post("durationStatus") == null || post("duration") == null) {
                  echo alertError(t__('Please fill all the fields'));
                }
                else if ($readProductGift["name"] != $slugify->slugify(str_replace(" ", "", post("name"))) && $checkGiftName->rowCount() > 0) {
                  echo alertError(t__("This coupon has already been created with this name!"));
                }
                else {
                  $updateProductGifts = $db->prepare("UPDATE ProductGifts SET name = ?, giftType = ?, gift = ?, expiryDate = ?, piece = ? WHERE id = ?");
                  $updateProductGifts->execute(array($slugify->slugify(str_replace(" ", "", post("name"))), post("giftType"), post("gift"), post("duration"), post("piece"), get("id")));
                  echo alertSuccess(t__('Changes has been saved successfully!'));
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label"><?php e__("Coupon Name") ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputName" class="form-control" name="name" placeholder="<?php e__("Enter the name of coupon") ?>." value="<?php echo $readProductGift["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectGiftType" class="col-sm-2 col-form-label"><?php e__("Gift Type") ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectGiftType" class="form-control" name="giftType" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="1" <?php echo ($readProductGift["giftType"] == 1) ? 'selected="selected"' : null; ?>><?php e__("Product") ?></option>
                        <option value="2" <?php echo ($readProductGift["giftType"] == 2) ? 'selected="selected"' : null; ?>><?php e__("Credit") ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="productBlock" style="<?php echo ($readProductGift["giftType"] == 1) ? "display: block;" : "display: none;"; ?>">
                    <?php
                      if ($readProductGift["giftType"] == 1) {
                        $giftProduct = $db->prepare("SELECT S.id as serverID FROM Products P INNER JOIN Servers S ON P.serverID = S.id WHERE P.id = ?");
                        $giftProduct->execute(array($readProductGift["gift"]));
                        $readGiftProduct = $giftProduct->fetch();
                      }
                      else {
                        $giftProduct = $db->query("SELECT id as serverID FROM Servers ORDER BY id ASC LIMIT 1");
                        $readGiftProduct = $giftProduct->fetch();
                      }
                    ?>
                    <div class="form-group row">
                      <label for="selectServerID" class="col-sm-2 col-form-label"><?php e__("Server") ?>:</label>
                      <div class="col-sm-10">
                        <?php $servers = $db->query("SELECT * FROM Servers"); ?>
                        <select id="selectServerID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1">
                          <?php if ($servers->rowCount() > 0): ?>
                            <?php foreach ($servers as $readServers): ?>
                              <option value="<?php echo $readServers["id"]; ?>" <?php echo (($readGiftProduct["serverID"] == $readServers["id"]) ? 'selected="selected"' : null); ?>><?php echo $readServers["name"]; ?></option>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <option><?php e__("Server not found") ?>!</option>
                          <?php endif; ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectProductID" class="col-sm-2 col-form-label"><?php e__("Product") ?>:</label>
                      <div class="col-sm-10">
                        <div id="c-loading" style="display: none; margin-top: 7px"><?php e__("Loading") ?>...</div>
                        <div id="products">
                          <?php
                            $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ?");
                            $productCategories->execute(array($readGiftProduct["serverID"]));
                          ?>
                          <select id="selectProductID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="gift" <?php echo ($readProductGift["giftType"] != 1) ? 'disabled' : null; ?>>
                            <?php foreach ($productCategories as $readCategories): ?>
                              <optgroup label="<?php echo $readCategories["name"]; ?>" data-select2-id="<?php echo $readCategories["id"]; ?>">
                                <?php
                                  $products = $db->prepare("SELECT * FROM Products WHERE serverID = ? AND categoryID = ?");
                                  $products->execute(array($readCategories["serverID"], $readCategories["id"]));
                                ?>
                                <?php foreach ($products as $readProducts): ?>
                                  <option value="<?php echo $readProducts["id"]; ?>" <?php echo (($readProductGift["gift"] == $readProducts["id"]) ? 'selected="selected"' : null); ?>><?php echo $readProducts["name"]; ?></option>
                                <?php endforeach; ?>
                              </optgroup>
                            <?php endforeach; ?>

                            <?php
                              $products = $db->prepare("SELECT * FROM Products WHERE serverID = ? AND categoryID = ?");
                              $products->execute(array($readProductGift["serverID"], 0));
                            ?>
                            <?php if ($products->rowCount() > 0): ?>
                              <optgroup label="Other" data-select2-id="0">
                                <?php foreach ($products as $readProducts): ?>
                                  <option value="<?php echo $readProducts["id"]; ?>" <?php echo (($readProductGift["gift"] == $readProducts["id"]) ? 'selected="selected"' : null); ?>><?php echo $readProducts["name"]; ?></option>
                                <?php endforeach; ?>
                              </optgroup>
                            <?php endif; ?>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div id="creditBlock" style="<?php echo ($readProductGift["giftType"] == 2) ? "display: block;" : "display: none;"; ?>">
                    <div class="form-group row">
                      <div class="col-sm-10 offset-sm-2">
                        <div class="input-group input-group-merge">
                          <input type="number" id="inputCredit" class="form-control form-control-prepended" name="gift" placeholder="<?php e__("Enter the amount of credit to gift") ?>." value="<?php echo ($readProductGift["giftType"] == 2) ? $readProductGift["gift"] : null; ?>" <?php echo ($readProductGift["giftType"] != 2) ? 'disabled' : null; ?>>
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fa fa-coins"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectDurationStatus" class="col-sm-2 col-form-label"><?php e__("Duration") ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectDurationStatus" class="form-control" name="durationStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readProductGift["expiryDate"] == '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>><?php e__("Lifetime") ?></option>
                        <option value="1" <?php echo ($readProductGift["expiryDate"] != '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>><?php e__("Temporary") ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="durationBlock" class="form-group row" style="<?php echo ($readProductGift["expiryDate"] == '1000-01-01 00:00:00') ? "display: none;" : "display: block;"; ?>">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="text" id="inputDuration" class="form-control form-control-prepended" name="duration" placeholder="<?php e__("Choose an expiration date") ?>." data-toggle="flatpickr" data-expirydate="true" value="<?php echo ($readProductGift["expiryDate"] != '1000-01-01 00:00:00') ? $readProductGift["expiryDate"] : null; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-clock"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectPieceStatus" class="col-sm-2 col-form-label"><?php e__("Amount") ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectPieceStatus" class="form-control" name="pieceStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readProductGift["piece"] == 0) ? 'selected="selected"' : null; ?>><?php e__("Unlimited") ?></option>
                        <option value="1" <?php echo ($readProductGift["piece"] != 0) ? 'selected="selected"' : null; ?>><?php e__("Limited") ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="pieceBlock" class="form-group row" style="<?php echo ($readProductGift["piece"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputPiece" class="form-control form-control-prepended" name="piece" placeholder="<?php e__("Enter the amount of use") ?>." value="<?php echo ($readProductGift["piece"] != 0) ? $readProductGift["piece"] : null; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-plus-circle"></span>
                          </div>
                        </div>
                      </div>
                      <?php if ($readProductGift["piece"] != 0): ?>
                        <?php
                          $productGiftsHistory = $db->prepare("SELECT * FROM ProductGiftsHistory WHERE giftID = ?");
                          $productGiftsHistory->execute(array($readProductGift["id"]));
                        ?>
                        <small class="form-text text-muted pt-2">
                          <strong><?php e__("Left") ?>:</strong> <?php echo (max($readProductGift["piece"]-$productGiftsHistory->rowCount().'x', 0)); ?>
                        </small>
                      <?php endif; ?>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateProductGifts'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/gifts/delete/<?php echo $readProductGift["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete") ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateProductGifts"><?php e__("Save Changes") ?></button>
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
      $deleteProductGift = $db->prepare("DELETE FROM ProductGifts WHERE id = ?");
      $deleteProductGift->execute(array(get("id")));
      go("/dashboard/gifts");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'gift-history'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("Gift History") ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard") ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/gifts"><?php e__("Gifts") ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Gift History") ?></li>
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

            $productGiftsHistory = $db->query("SELECT PGH.id FROM ProductGiftsHistory PGH INNER JOIN Accounts A ON PGH.accountID = A.id INNER JOIN ProductGifts PG ON PGH.giftID = PG.id");
            $itemsCount = $productGiftsHistory->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $productGiftsHistory = $db->query("SELECT PGH.*, A.realname, PG.name as giftTitle FROM ProductGiftsHistory PGH INNER JOIN Accounts A ON PGH.accountID = A.id INNER JOIN ProductGifts PG ON PGH.giftID = PG.id ORDER BY PGH.id DESC LIMIT $visibleItemsCount, $limit");

            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $productGiftsHistory = $db->prepare("SELECT PGH.*, A.realname, PG.name as giftTitle FROM ProductGiftsHistory PGH INNER JOIN Accounts A ON PGH.accountID = A.id INNER JOIN ProductGifts PG ON PGH.giftID = PG.id WHERE A.realname LIKE :search ORDER BY PGH.id DESC");
                $productGiftsHistory->execute(array(
                  "search" => '%'.post("query").'%'
                ));
              }
            }
          ?>
          <?php if ($productGiftsHistory->rowCount() > 0): ?>
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
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="<?php e__("Search (Username)") ?>" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success"><?php e__("Search") ?></button>
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
                        <th><?php e__("Username") ?></th>
                        <th><?php e__("Coupon") ?></th>
                        <th><?php e__("Date") ?></th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($productGiftsHistory as $readProductGiftsHistory): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            #<?php echo $readProductGiftsHistory["id"]; ?>
                          </td>
                          <td>
                            <a href="/dashboard/users/view/<?php echo $readProductGiftsHistory["accountID"]; ?>">
                              <?php echo $readProductGiftsHistory["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php
                              echo $readProductGiftsHistory["giftTitle"];
                            ?>
                          </td>
                          <td>
                            <?php echo convertTime($readProductGiftsHistory["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/gifts/logs/delete/<?php echo $readProductGiftsHistory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete") ?>">
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
                    <a class="page-link" href="/dashboard/gifts/logs/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/dashboard/gifts/logs/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/gifts/logs/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
                  </li>
                </ul>
              </nav>
            <?php endif; ?>
          <?php else: ?>
            <?php echo alertError(t__("No data for this page!")); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
        $deleteProductGiftsHistory = $db->prepare("DELETE FROM ProductGiftsHistory WHERE id = ?");
        $deleteProductGiftsHistory->execute(array(get("id")));
        go("/dashboard/gifts/logs");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
