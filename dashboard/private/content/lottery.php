<?php
  if (!checkPerm($readAdmin, 'MANAGE_LOTTERY')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');

  if (get("target") == 'lottery' && (get("action") == 'insert' || get("action") == 'update')) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/lottery.js');
  }
?>
<?php if (get("target") == 'lottery'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Wheel of Fortune') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Wheel of Fortunes') ?></li>
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
          <?php $lotteries = $db->query("SELECT * FROM Lotteries ORDER BY id DESC"); ?>
          <?php if ($lotteries->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["lotteryID", "lotteryTitle", "lotteryPrice", "lotteryDuration"]'>
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
                    <a class="btn btn-sm btn-white" href="/dashboard/fortune-wheel/create"><?php e__('Add Wheel of Fortune') ?></a>
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
                          <a href="#" class="text-muted sort" data-sort="lotteryID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="lotteryTitle">
                              <?php e__('Title') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="lotteryPrice">
                              <?php e__('Game Price') ?>
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="lotteryDuration">
                              <?php e__('Spin Again Duration') ?>
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($lotteries as $readLotteries): ?>
                        <tr>
                          <td class="lotteryID text-center" style="width: 40px;">
                            <a href="/dashboard/fortune-wheel/edit/<?php echo $readLotteries["id"]; ?>">
                              #<?php echo $readLotteries["id"]; ?>
                            </a>
                          </td>
                          <td class="lotteryTitle">
                            <a href="/dashboard/fortune-wheel/edit/<?php echo $readLotteries["id"]; ?>">
                              <?php echo $readLotteries["title"]; ?>
                            </a>
                          </td>
                          <td class="lotteryPrice">
                            <?php echo ($readLotteries["price"] != 0) ? $readLotteries["price"].' '. e__('credit') : t__('Free'); ?>
                          </td>
                          <td class="lotteryDuration">
                            <?php echo ($readLotteries["duration"] != 0) ? $readLotteries["duration"].' ' . e__('hour') : '-'; ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/fortune-wheel/edit/<?php echo $readLotteries["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/fortune-wheel/<?php echo $readLotteries["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/fortune-wheel/delete/<?php echo $readLotteries["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
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
            <?php echo alertError(t__("No data for this page!")); ?>
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
                  <h2 class="header-title"><?php e__('Add Wheel of Fortune') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/fortune-wheel"><?php e__('Wheel of Fortunes') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Add Wheel of Fortune') ?></li>
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
            if (isset($_POST["insertLotteries"])) {
              $checkLotteryTitle = $db->prepare("SELECT id FROM Lotteries WHERE slug = ?");
              $checkLotteryTitle->execute(array($slugify->slugify(post("title"))));
              if (post("priceStatus") == 0) {
                $_POST["price"] = 0;
              }
              else {
                $_POST["duration"] = 0;
              }
              if (!$csrf->validate('insertLotteries')) {
                echo alertError(t__("Something went wrong! Please try again later."));
              }
              else if (post("title") == null || post("price") == null || post("duration") == null || empty(array_filter($_POST["lotteryTitle"])) || empty(array_filter($_POST["lotteryChance"])) || empty(array_filter($_POST["lotteryColor"])) || empty(array_filter($_POST["lotteryAwardType"])) || empty(array_filter($_POST["lotteryAward"]))) {
                echo alertError(t__("Please fill all the fields!"));
              }
              else if ($checkLotteryTitle->rowCount() > 0) {
                echo alertError(t__("You cant create one more wheel of fortune with same name!"));
              }
              else {
                $percent = 0;
                foreach ($_POST["lotteryChance"] as $value) {
                  $percent += $value;
                }
                if ($percent == 100) {
                  $insertLotteries = $db->prepare("INSERT INTO Lotteries (title, slug, price, duration) VALUES (?, ?, ?, ?)");
                  $insertLotteries->execute(array(post("title"), $slugify->slugify(post("title")), post("price"), post("duration")));
                  $lotteryID = $db->lastInsertId();
                  foreach ($_POST["lotteryChance"] as $key => $value) {
                    $_POST["lotteryTitle"][$key] = strip_tags($_POST["lotteryTitle"][$key]);
                    $_POST["lotteryChance"][$key] = strip_tags($_POST["lotteryChance"][$key]);
                    $_POST["lotteryColor"][$key] = strip_tags($_POST["lotteryColor"][$key]);
                    $_POST["lotteryAwardType"][$key] = strip_tags($_POST["lotteryAwardType"][$key]);
                    $_POST["lotteryAward"][$key] = ($_POST["lotteryAward"][$key] != null) ? strip_tags($_POST["lotteryAward"][$key]) : '0';
                    $insertLotteryAwards = $db->prepare("INSERT INTO LotteryAwards (lotteryID, title, chance, color, awardType, award) VALUES (?, ?, ?, ?, ?, ?)");
                    $insertLotteryAwards->execute(array($lotteryID, $_POST["lotteryTitle"][$key], $_POST["lotteryChance"][$key], $_POST["lotteryColor"][$key], $_POST["lotteryAwardType"][$key], $_POST["lotteryAward"][$key]));
                  }
                  echo alertSuccess(t__("Wheel of Fortune has been added successfully!"));
                }
                else {
                  echo alertError(t__('The sum of the averages must be <strong>100</strong>!'));
                }
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputTitle" class="col-sm-2 col-form-label"><?php e__('Title') ?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="<?php e__('Enter the wheel of fortune title') ?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectPriceStatus" class="col-sm-2 col-form-label"><?php e__('Game Price') ?>:</label>
                  <div class="col-sm-10">
                    <select id="selectPriceStatus" class="form-control" name="priceStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0"><?php e__('Free') ?></option>
                      <option value="1"><?php e__('Paid') ?></option>
                    </select>
                  </div>
                </div>
                <div id="priceOptions" style="display: none;">
                  <div class="form-group row">
                    <label for="inputPrice" class="col-sm-2 col-form-label"><?php e__('Game Price') ?>:</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputPrice" class="form-control form-control-prepended" name="price" placeholder="<?php e__('Enter the price of game') ?>.">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fa fa-coins"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="durationOptions">
                  <div class="form-group row">
                    <label for="inputDuration" class="col-sm-2 col-form-label"><?php e__('Redial Spin Price (Hour)') ?>:</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputDuration" class="form-control form-control-prepended" name="duration" placeholder="<?php e__('Enter the cooldown of free-spin. (Hour)') ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-clock"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-sm-12">
                    <span><?php e__('Wheel of Fortune Settings') ?>:</span>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-12">
                    <div class="table-responsive">
                      <table id="tableitems" class="table table-sm table-nowrap array-table">
                        <thead>
                          <tr>
                            <th><?php e__('Title') ?></th>
                            <th><?php e__('Winning Rate') ?> (%)</th>
                            <th><?php e__('Background Color') ?></th>
                            <th><?php e__('Award Type') ?></th>
                            <th><?php e__('Award') ?></th>
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
                              <input type="text" class="form-control" name="lotteryTitle[]" placeholder="<?php e__('Enter the title of award') ?>.">
                            </td>
                            <td>
                              <input type="text" class="form-control" name="lotteryChance[]" placeholder="<?php e__('Enter the winning rate as percentage') ?>.">
                            </td>
                            <td>
                              <input type="text" class="form-control" name="lotteryColor[]" placeholder="<?php e__('Enter the color (For Exm: #00000)') ?>">
                            </td>
                            <td>
                              <select class="form-control" name="lotteryAwardType[]">
                                <option value="1"><?php e__('Credit') ?></option>
                                <option value="2"><?php e__('Product') ?></option>
                                <option value="3"><?php e__('Pass') ?></option>
                              </select>
                            </td>
                            <td class="variableData">
                              <div class="creditData">
                                <input type="text" class="form-control" name="lotteryAward[]" placeholder="<?php e__('Enter the amount of credit') ?>.">
                              </div>
                              <div class="productData" style="display: none;">
                                <select class="form-control" name="lotteryAward[]" disabled>
                                  <?php $servers = $db->query("SELECT id, name FROM Servers ORDER BY id DESC"); ?>
                                  <?php foreach ($servers as $readServers): ?>
                                    <optgroup label="<?php echo $readServers["name"]; ?>">
                                      <?php
                                        $productCategories = $db->prepare("SELECT id, name FROM ProductCategories WHERE serverID = ?");
                                        $productCategories->execute(array($readServers["id"]));
                                      ?>
                                      <?php foreach ($productCategories as $readProductCategories): ?>
                                        <optgroup label="&nbsp;&nbsp;<?php echo $readProductCategories["name"]; ?>">
                                          <?php
                                            $products = $db->prepare("SELECT id, name FROM Products WHERE serverID = ? AND categoryID = ?");
                                            $products->execute(array($readServers["id"], $readProductCategories["id"]));
                                          ?>
                                          <?php foreach ($products as $readProducts): ?>
                                            <option value="<?php echo $readProducts["id"]; ?>">&nbsp;<?php echo $readProducts["name"]; ?></option>
                                          <?php endforeach; ?>
                                        </optgroup>
                                      <?php endforeach; ?>
                                      <?php
                                        $uncategorizedProducts = $db->prepare("SELECT id, name FROM Products WHERE serverID = ? AND categoryID = ?");
                                        $uncategorizedProducts->execute(array($readServers["id"], 0));
                                      ?>
                                      <?php if ($uncategorizedProducts->rowCount() > 0): ?>
                                        <optgroup label="&nbsp;&nbsp;<?php e__('Other') ?>">
                                          <?php foreach ($uncategorizedProducts as $readUncategorizedProducts): ?>
                                            <option value="<?php echo $readUncategorizedProducts["id"]; ?>"><?php echo $readUncategorizedProducts["name"]; ?></option>
                                          <?php endforeach; ?>
                                        </optgroup>
                                      <?php endif; ?>
                                    </optgroup>
                                  <?php endforeach; ?>
                                </select>
                              </div>
                              <div class="pas" style="display: none; margin: .5rem 0;">
                                <span><?php e__('The player wont be awarded') ?>.</span>
                                <input type="hidden" name="lotteryAward[]" value="0" disabled>
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
                  </div>
                </div>
                <?php echo $csrf->input('insertLotteries'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertLotteries"><?php e__('Add') ?></button>
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
      $lottery = $db->prepare("SELECT * FROM Lotteries WHERE id = ?");
      $lottery->execute(array(get("id")));
      $readLottery = $lottery->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Edit Wheel of Fortune') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/fortune-wheel"><?php e__('Wheel of Fortunes') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/fortune-wheel"><?php e__('Edit Wheel of Fortunes') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($lottery->rowCount() > 0) ? substr($readLottery["title"], 0, 30): t__('Not found!'); ?></li>
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
          <?php if ($lottery->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateLotteries"])) {
                $checkLotteryTitle = $db->prepare("SELECT id FROM Lotteries WHERE slug = ?");
                $checkLotteryTitle->execute(array($slugify->slugify(post("title"))));
                if (post("priceStatus") == 0) {
                  $_POST["price"] = 0;
                }
                else {
                  $_POST["duration"] = 0;
                }
                if (!$csrf->validate('updateLotteries')) {
                  echo alertError(t__("Something went wrong! Please try again later."));
                }
                else if (post("title") == null || post("price") == null || post("duration") == null || empty(array_filter($_POST["lotteryTitle"])) || empty(array_filter($_POST["lotteryChance"])) || empty(array_filter($_POST["lotteryColor"])) || empty(array_filter($_POST["lotteryAwardType"])) || empty(array_filter($_POST["lotteryAward"]))) {
                  echo alertError(t__('Please fill all the fields!'));
                }
                else if ($readLottery["title"] != post("title") && $checkLotteryTitle->rowCount() > 0) {
                  echo alertError(t__("You cant create one more wheel of fortune with same name!"));
                }
                else {
                  $percent = 0;
                  foreach ($_POST["lotteryChance"] as $value) {
                    $percent += $value;
                  }
                  if ($percent == 100) {
                    $updateLotteries = $db->prepare("UPDATE Lotteries SET title = ?, slug = ?, price = ?, duration = ? WHERE id = ?");
                    $updateLotteries->execute(array(post("title"), $slugify->slugify(post("title")), post("price"), post("duration"), get("id")));
                    $deleteLotteryAwards = $db->prepare("DELETE FROM LotteryAwards WHERE lotteryID = ?");
                    $deleteLotteryAwards->execute(array($readLottery["id"]));
                    foreach ($_POST["lotteryChance"] as $key => $value) {
                      $_POST["lotteryTitle"][$key] = strip_tags($_POST["lotteryTitle"][$key]);
                      $_POST["lotteryChance"][$key] = strip_tags($_POST["lotteryChance"][$key]);
                      $_POST["lotteryColor"][$key] = strip_tags($_POST["lotteryColor"][$key]);
                      $_POST["lotteryAwardType"][$key] = strip_tags($_POST["lotteryAwardType"][$key]);
                      $_POST["lotteryAward"][$key] = ($_POST["lotteryAward"][$key] != null) ? strip_tags($_POST["lotteryAward"][$key]) : '0';
                      $insertLotteryAwards = $db->prepare("INSERT INTO LotteryAwards (lotteryID, title, chance, color, awardType, award) VALUES (?, ?, ?, ?, ?, ?)");
                      $insertLotteryAwards->execute(array($readLottery["id"], $_POST["lotteryTitle"][$key], $_POST["lotteryChance"][$key], $_POST["lotteryColor"][$key], $_POST["lotteryAwardType"][$key], $_POST["lotteryAward"][$key]));
                    }
                    echo alertSuccess(t__('Changes has been saved successfully!'));
                  }
                  else {
                    echo alertError(t__('The sum of the averages must be <strong>100</strong>!'));
                  }
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputTitle" class="col-sm-2 col-form-label"><?php e__('Title') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="<?php e__('Enter the title of wheel of fortune') ?>." value="<?php echo $readLottery["title"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectPriceStatus" class="col-sm-2 col-form-label"><?php e__('Game Price') ?>:</label>
                    <div class="col-sm-10">
                      <select id="selectPriceStatus" class="form-control" name="priceStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readLottery["price"] == '0') ? 'selected="selected"' : null; ?>><?php e__('Free') ?></option>
                        <option value="1" <?php echo ($readLottery["price"] != '0') ? 'selected="selected"' : null; ?>><?php e__('Paid') ?></option>
                      </select>
                    </div>
                  </div>
                  <div id="priceOptions" style="<?php echo ($readLottery["price"] == '0') ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <label for="inputPrice" class="col-sm-2 col-form-label"><?php e__('Game Price') ?>:</label>
                      <div class="col-sm-10">
                        <div class="input-group input-group-merge">
                          <input type="number" id="inputPrice" class="form-control form-control-prepended" name="price" placeholder="<?php e__('Enter the amount of credit to play game') ?>." value="<?php echo ($readLottery["price"] != '0') ? $readLottery["price"] : null; ?>">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fa fa-coins"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div id="durationOptions" style="<?php echo ($readLottery["price"] == '0') ? "display: block;" : "display: none;"; ?>">
                    <div class="form-group row">
                      <label for="inputDuration" class="col-sm-2 col-form-label"><?php e__('Redial Spin Price (Hour)') ?>:</label>
                      <div class="col-sm-10">
                        <div class="input-group input-group-merge">
                          <input type="number" id="inputDuration" class="form-control form-control-prepended" name="duration" placeholder="<?php e__('Enter the cooldown of free-spin. (Hour)') ?>" value="<?php echo ($readLottery["price"] == '0') ? $readLottery["duration"] : null; ?>">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fe fe-clock"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-sm-12">
                      <span><?php e__('Wheel of Fortune Settings') ?>:</span>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-12">
                      <div class="table-responsive">
                        <table id="tableitems" class="table table-sm table-nowrap array-table">
                          <thead>
                            <tr>
                                <th><?php e__('Title') ?></th>
                                <th><?php e__('Winning Rate') ?> (%)</th>
                                <th><?php e__('Background Color') ?></th>
                                <th><?php e__('Award Type') ?></th>
                                <th><?php e__('Award') ?></th>
                              <th class="text-center pt-0 pb-0 align-middle">
                                <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                                  <i class="fe fe-plus"></i>
                                </button>
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              $lotteryAwards = $db->prepare("SELECT * FROM LotteryAwards WHERE lotteryID = ?");
                              $lotteryAwards->execute(array($readLottery["id"]));
                            ?>
                            <?php if ($lotteryAwards->rowCount() > 0): ?>
                              <?php foreach ($lotteryAwards as $readLotteryAwards): ?>
                                <tr>
                                  <td>
                                    <input type="text" class="form-control" name="lotteryTitle[]" placeholder="<?php e__('Enter the title of award') ?>." value="<?php echo $readLotteryAwards["title"]; ?>">
                                  </td>
                                  <td>
                                    <input type="text" class="form-control" name="lotteryChance[]" placeholder="<?php e__('Enter the winning rate as percentage') ?>." value="<?php echo $readLotteryAwards["chance"]; ?>">
                                  </td>
                                  <td>
                                    <input type="text" class="form-control" name="lotteryColor[]" placeholder="<?php e__('Enter the color (For Exm: #00000)') ?>" value="<?php echo $readLotteryAwards["color"]; ?>">
                                  </td>
                                  <td>
                                    <select class="form-control" name="lotteryAwardType[]">
                                      <option value="1" <?php echo ($readLotteryAwards["awardType"] == 1) ? 'selected="selected"' : null; ?>><?php e__('Credit') ?></option>
                                      <option value="2" <?php echo ($readLotteryAwards["awardType"] == 2) ? 'selected="selected"' : null; ?>><?php e__('Product') ?>></option>
                                      <option value="3" <?php echo ($readLotteryAwards["awardType"] == 3) ? 'selected="selected"' : null; ?>><?php e__('Pass') ?></option>
                                    </select>
                                  </td>
                                  <td class="variableData">
                                    <div class="creditData" style="<?php echo ($readLotteryAwards["awardType"] == 1) ? "display: block;" : "display: none;"; ?>">
                                      <input type="text" class="form-control" name="lotteryAward[]" placeholder="<?php e__('Enter the amount of credit') ?>." value="<?php echo ($readLotteryAwards["awardType"] == 1) ? $readLotteryAwards["award"] : null; ?>" <?php echo ($readLotteryAwards["awardType"] == 1) ? null : "disabled"; ?>>
                                    </div>
                                    <div class="productData" style="<?php echo ($readLotteryAwards["awardType"] == 2) ? "display: block;" : "display: none;"; ?>">
                                      <select class="form-control" name="lotteryAward[]" <?php echo ($readLotteryAwards["awardType"] == 2) ? null : "disabled"; ?>>
                                        <?php $servers = $db->query("SELECT id, name FROM Servers ORDER BY id DESC"); ?>
                                        <?php foreach ($servers as $readServers): ?>
                                          <optgroup label="<?php echo $readServers["name"]; ?>">
                                            <?php
                                              $productCategories = $db->prepare("SELECT id, name FROM ProductCategories WHERE serverID = ?");
                                              $productCategories->execute(array($readServers["id"]));
                                            ?>
                                            <?php foreach ($productCategories as $readProductCategories): ?>
                                              <optgroup label="&nbsp;&nbsp;<?php echo $readProductCategories["name"]; ?>">
                                                <?php
                                                  $products = $db->prepare("SELECT id, name FROM Products WHERE serverID = ? AND categoryID = ?");
                                                  $products->execute(array($readServers["id"], $readProductCategories["id"]));
                                                ?>
                                                <?php foreach ($products as $readProducts): ?>
                                                  <option value="<?php echo $readProducts["id"]; ?>" <?php echo ($readLotteryAwards["awardType"] == 2 && $readLotteryAwards["award"] == $readProducts["id"]) ? 'selected="selected"' : null; ?>>&nbsp;<?php echo $readProducts["name"]; ?></option>
                                                <?php endforeach; ?>
                                              </optgroup>
                                            <?php endforeach; ?>
                                            <?php
                                              $uncategorizedProducts = $db->prepare("SELECT id, name FROM Products WHERE serverID = ? AND categoryID = ?");
                                              $uncategorizedProducts->execute(array($readServers["id"], 0));
                                            ?>
                                            <?php if ($uncategorizedProducts->rowCount() > 0): ?>
                                              <optgroup label="&nbsp;&nbsp;<?php e__('Other') ?>">
                                                <?php foreach ($uncategorizedProducts as $readUncategorizedProducts): ?>
                                                  <option value="<?php echo $readUncategorizedProducts["id"]; ?>" <?php echo ($readLotteryAwards["awardType"] == 2 && $readLotteryAwards["award"] == $readProducts["id"]) ? 'selected="selected"' : null; ?>><?php echo $readUncategorizedProducts["name"]; ?></option>
                                                <?php endforeach; ?>
                                              </optgroup>
                                            <?php endif; ?>
                                          </optgroup>
                                        <?php endforeach; ?>
                                      </select>
                                    </div>
                                    <div class="pas" style="<?php echo ($readLotteryAwards["awardType"] == 3) ? "display: block;" : "display: none;"; ?> margin: .5rem 0;">
                                      <span><?php e__('The player wont be awarded') ?>.</span>
                                      <input type="hidden" name="lotteryAward[]" value="0" <?php echo ($readLotteryAwards["awardType"] == 3) ? null : "disabled"; ?>>
                                    </div>
                                  </td>
                                  <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                      <i class="fe fe-trash-2"></i>
                                    </button>
                                  </td>
                                </tr>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="lotteryTitle[]" placeholder="<?php e__('Enter the title of award') ?>.">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="lotteryChance[]" placeholder="<?php e__('Enter the winning rate as percentage') ?>.">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="lotteryColor[]" placeholder="<?php e__('Enter the color (For Exm: #00000)') ?>">
                                </td>
                                <td>
                                  <select class="form-control" name="lotteryAwardType[]">
                                    <option value="1"><?php e__('Credit') ?></option>
                                    <option value="2"><?php e__('Product') ?></option>
                                    <option value="3"><?php e__('Pass') ?></option>
                                  </select>
                                </td>
                                <td class="variableData">
                                  <div class="creditData">
                                    <input type="text" class="form-control" name="lotteryAward[]" placeholder="<?php e__('Enter the amount of credit') ?>.">
                                  </div>
                                  <div class="productData" style="display: none;">
                                    <select class="form-control" name="lotteryAward[]" disabled>
                                      <?php $servers = $db->query("SELECT id, name FROM Servers ORDER BY id DESC"); ?>
                                      <?php foreach ($servers as $readServers): ?>
                                        <optgroup label="<?php echo $readServers["name"]; ?>">
                                          <?php
                                            $productCategories = $db->prepare("SELECT id, name FROM ProductCategories WHERE serverID = ?");
                                            $productCategories->execute(array($readServers["id"]));
                                          ?>
                                          <?php foreach ($productCategories as $readProductCategories): ?>
                                            <optgroup label="&nbsp;&nbsp;<?php echo $readProductCategories["name"]; ?>">
                                              <?php
                                                $products = $db->prepare("SELECT id, name FROM Products WHERE serverID = ? AND categoryID = ?");
                                                $products->execute(array($readServers["id"], $readProductCategories["id"]));
                                              ?>
                                              <?php foreach ($products as $readProducts): ?>
                                                <option value="<?php echo $readProducts["id"]; ?>">&nbsp;<?php echo $readProducts["name"]; ?></option>
                                              <?php endforeach; ?>
                                            </optgroup>
                                          <?php endforeach; ?>
                                          <?php
                                            $uncategorizedProducts = $db->prepare("SELECT id, name FROM Products WHERE serverID = ? AND categoryID = ?");
                                            $uncategorizedProducts->execute(array($readServers["id"], 0));
                                          ?>
                                          <?php if ($uncategorizedProducts->rowCount() > 0): ?>
                                            <optgroup label="&nbsp;&nbsp;<?php e__('Other') ?>">
                                              <?php foreach ($uncategorizedProducts as $readUncategorizedProducts): ?>
                                                <option value="<?php echo $readUncategorizedProducts["id"]; ?>"><?php echo $readUncategorizedProducts["name"]; ?></option>
                                              <?php endforeach; ?>
                                            </optgroup>
                                          <?php endif; ?>
                                        </optgroup>
                                      <?php endforeach; ?>
                                    </select>
                                  </div>
                                  <div class="pas" style="display: none; margin: .5rem 0;">
                                    <span><?php e__('The player wont be awarded') ?></span>
                                    <input type="hidden" name="lotteryAward[]" value="0" disabled>
                                  </div>
                                </td>
                                <td class="text-center align-middle">
                                  <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                    <i class="fe fe-trash-2"></i>
                                  </button>
                                </td>
                              </tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateLotteries'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/fortune-wheel/delete/<?php echo $readLottery["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/fortune-wheel/<?php echo $readLottery["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__('View') ?>">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateLotteries"><?php e__('Save Changes') ?></button>
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
      $deleteLottery = $db->prepare("DELETE FROM Lotteries WHERE id = ?");
      $deleteLottery->execute(array(get("id")));
      $deleteLotteryAwards = $db->prepare("DELETE FROM LotteryAwards WHERE lotteryID = ?");
      $deleteLotteryAwards->execute(array(get("id")));
      go("/dashboard/fortune-wheel");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'lottery-history'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Wheel of Fortune History') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/fortune-wheel"><?php e__('Wheel of Fortunes') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Wheel of Fortune History') ?></li>
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

            $lotteryHistory = $db->query("SELECT LH.id FROM LotteryHistory LH INNER JOIN LotteryAwards LA ON LH.lotteryAwardID = LA.id INNER JOIN Lotteries L ON LA.lotteryID = L.id INNER JOIN Accounts A ON LH.accountID = A.id");
            $itemsCount = $lotteryHistory->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $lotteryHistory = $db->query("SELECT LH.*, L.title as lotteryTitle, LA.title as awardTitle, LA.awardType, LA.award, A.realname FROM LotteryHistory LH INNER JOIN LotteryAwards LA ON LH.lotteryAwardID = LA.id INNER JOIN Lotteries L ON LA.lotteryID = L.id INNER JOIN Accounts A ON LH.accountID = A.id ORDER BY LH.id DESC LIMIT $visibleItemsCount, $limit");

            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $lotteryHistory = $db->prepare("SELECT LH.*, L.title as lotteryTitle, LA.title as awardTitle, LA.awardType, LA.award, A.realname FROM LotteryHistory LH INNER JOIN LotteryAwards LA ON LH.lotteryAwardID = LA.id INNER JOIN Lotteries L ON LA.lotteryID = L.id INNER JOIN Accounts A ON LH.accountID = A.id WHERE A.realname LIKE :search ORDER BY LH.id DESC");
                $lotteryHistory->execute(array(
                  "search" => '%'.post("query").'%'
                ));
              }
            }
          ?>
          <?php if ($lotteryHistory->rowCount() > 0): ?>
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
                        <th><?php e__('Wheel of Fortune') ?></th>
                        <th><?php e__('Award') ?></th>
                        <th><?php e__('Date') ?></th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($lotteryHistory as $readLotteryHistory): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            #<?php echo $readLotteryHistory["id"]; ?>
                          </td>
                          <td>
                            <a href="/dashboard/users/view/<?php echo $readLotteryHistory["accountID"]; ?>">
                              <?php echo $readLotteryHistory["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php echo $readLotteryHistory["lotteryTitle"]; ?>
                          </td>
                          <td>
                            <?php echo $readLotteryHistory["awardTitle"]; ?>
                          </td>
                          <td>
                            <?php echo convertTime($readLotteryHistory["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/fortune-wheel/logs/delete/<?php echo $readLotteryHistory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Delete') ?>">
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
                    <a class="page-link" href="/dashboard/fortune-wheel/logs/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/dashboard/fortune-wheel/logs/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/fortune-wheel/logs/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
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
      $deleteLotteryHistory = $db->prepare("DELETE FROM LotteryHistory WHERE id = ?");
      $deleteLotteryHistory->execute(array(get("id")));
      go("/dashboard/fortune-wheel/logs");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
