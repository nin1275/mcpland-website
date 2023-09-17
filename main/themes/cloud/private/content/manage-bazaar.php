<?php
  if (!isset($_SESSION["login"])) {
    go("/login");
  }
  if (moduleIsDisabled('bazaar')) {
    go("/404");
  }
  require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
?>
<section class="section credit-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><?php e__('Home') ?></a></li>
            <?php if (get("action") == 'getAll'): ?>
              <li class="breadcrumb-item active" aria-current="page"><?php e__('Bazaar Storage') ?></li>
            <?php elseif (get("action") == 'sell'): ?>
              <li class="breadcrumb-item"><a href="/manage-bazaar"><?php e__('Bazaar Storage') ?></a></li>
              <li class="breadcrumb-item active" aria-current="page"><?php e__('Sell') ?></li>
            <?php elseif (get("action") == 'help'): ?>
              <li class="breadcrumb-item"><a href="/bazaar"><?php e__('Bazaar') ?></a></li>
              <li class="breadcrumb-item active" aria-current="page"><?php e__('Help') ?></li>
            <?php else: ?>
              <li class="breadcrumb-item active" aria-current="page"><?php e__('Error!') ?></li>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
      <?php if (get("action") == 'getAll'): ?>
        <div class="col-md-8">
          <?php if ($readSettings["bazaarCommission"] > 0): ?>
            <?php echo alertWarning(t__('You will be charged %commission%% commission per sale!', ['%commission%' => $readSettings["bazaarCommission"]])); ?>
          <?php endif; ?>
          <?php
            $items = $db->prepare("SELECT BI.*, S.name as serverName FROM BazaarItems BI INNER JOIN Servers S ON S.id = BI.serverID WHERE BI.owner = ? ORDER BY BI.id DESC");
            $items->execute(array($readAccount["id"]));
          ?>
          <?php if ($items->rowCount() > 0): ?>
            <div class="card">
              <div class="card-header">
                <div class="row">
                  <div class="col"><?php e__('Bazaar Storage') ?></div>
                  <div class="col-auto">
                    <a href="/help-bazaar" class="text-white"><?php e__('Help') ?></a>
                  </div>
                </div>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                    <tr>
                      <th class="text-center" style="width: 40px;">#ID</th>
                      <th><?php e__('Item') ?></th>
                      <th><?php e__('Server') ?></th>
                      <th><?php e__('Price') ?></th>
                      <th><?php e__('Status') ?></th>
                      <th><?php e__('Date') ?></th>
                      <th class="text-right">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $readItems): ?>
                      <tr>
                        <td class="text-center" style="width: 40px;">
                          #<?php echo $readItems["id"]; ?>
                        </td>
                        <td>
                          <?php echo $readItems["name"]; ?>
                        </td>
                        <td>
                          <?php echo $readItems["serverName"]; ?>
                        </td>
                        <td>
                          <?php echo $readItems["price"] == 0 ? "-" : $readItems["price"]; ?>
                        </td>
                        <td>
                          <?php if ($readItems["sold"] == 0): ?>
                            <?php if ($readItems["price"] == 0): ?>
                              <span class="badge badge-pill badge-warning"><?php e__('In Storage') ?></span>
                            <?php else: ?>
                              <span class="badge badge-pill badge-success"><?php e__('On Sale') ?></span>
                            <?php endif; ?>
                          <?php else: ?>
                            <span class="badge badge-pill badge-danger"><?php e__('Sold') ?></span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php echo convertTime($readItems["creationDate"], 2, true); ?>
                        </td>
                        <td class="text-right">
                          <a class="btn btn-success btn-circle" href="/manage-bazaar/<?php echo $readItems["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__('Edit') ?>">
                            <i class="fa fa-pen"></i>
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
            <?php echo alertError(t__('No items found in your storage!')); ?>
          <?php endif; ?>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-12">
              <?php
                $bazaarHistory = $db->prepare("SELECT BH.*, BI.name as itemName, BI.price as itemPrice FROM BazaarHistory BH INNER JOIN BazaarItems BI ON BH.itemID = BI.id WHERE BH.accountID = ? ORDER BY BH.id DESC LIMIT 5");
                $bazaarHistory->execute(array($readAccount["id"]));
              ?>
              <?php if ($bazaarHistory->rowCount() > 0): ?>
                <div class="card">
                  <div class="card-header">
                    <div class="row">
                      <div class="col">
                        <span><?php e__('Bazaar History') ?></span>
                      </div>
                      <div class="col-auto">
                        <a class="text-white" href="/profile"><?php e__('View All') ?></a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <table class="table table-hover">
                        <thead>
                        <tr>
                          <th><?php e__('Item') ?></th>
                          <th class="text-center"><?php e__('Type') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($bazaarHistory as $readBazaarHistory): ?>
                          <tr>
                            <td><?php echo $readBazaarHistory["itemName"]; ?></td>
                            <td class="text-center">
                              <?php if ($readBazaarHistory["type"] == 0): ?>
                                <span class="text-danger" data-toggle="tooltip" data-placement="top" title="<?php e__('Purchase') ?>">-<?php echo $readBazaarHistory["itemPrice"] ?> <i class="fa fa-coins"></i></span>
                              <?php elseif ($readBazaarHistory["type"] == 1): ?>
                                <span class="text-success" data-toggle="tooltip" data-placement="top" title="<?php e__('Sell') ?>">+<?php echo $readBazaarHistory["itemPrice"] ?> <i class="fa fa-coins"></i></span>
                              <?php else: ?>
                                <i class="fa fa-check"></i>
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              <?php else: ?>
                <?php echo alertError(t__('History not found!')); ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php elseif (get("action") == 'sell' && get("id")): ?>
        <?php
          $item = $db->prepare("SELECT BI.*, S.name as serverName FROM BazaarItems BI INNER JOIN Servers S ON S.id = BI.serverID WHERE BI.owner = ? AND BI.id = ?");
          $item->execute(array($readAccount["id"], get("id")));
          $readItem = $item->fetch();
        ?>
        <?php if ($item->rowCount() > 0): ?>
          <div class="col-md-4">
            <div class="card">
              <div class="text-center py-3 bg-light">
                <img width="64px" src="/apps/main/public/assets/img/items/<?php echo strtolower($readItem["itemID"]).".png"; ?>" />
              </div>
              <div class="card-body">
                <div class="form-group">
                  <strong><?php e__('Item') ?>:</strong>
                  <div>
                    <input type="text" class="form-control-plaintext" value="<?php echo $readItem["name"]; ?>" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <strong><?php e__('Amount') ?>:</strong>
                  <div>
                    <?php echo $readItem["amount"]; ?>
                  </div>
                </div>
                <div class="form-group">
                  <strong><?php e__('Durability') ?>:</strong>
                  <div>
                    <?php echo ($readItem["durability"] > $readItem["maxDurability"] ? $readItem["maxDurability"] : $readItem["durability"])."/".$readItem["maxDurability"]; ?>
                  </div>
                </div>
                <div class="form-group">
                  <strong><?php e__('Server') ?>:</strong>
                  <div>
                    <?php echo $readItem["serverName"]; ?>
                  </div>
                </div>
                <?php if ($readItem["lore"] != null && $readItem["lore"] != ""): ?>
                  <div class="form-group">
                    <strong><?php e__('Lore') ?>:</strong>
                    <div>
                      <?php echo str_replace("\n", "<br>", $readItem["lore"]); ?>
                    </div>
                  </div>
                <?php endif; ?>
                <?php if ($readItem["enchantments"] != null && $readItem["enchantments"] != ""): ?>
                  <div class="form-group">
                    <strong><?php e__('Enchantments') ?>:</strong>
                    <div>
                      <?php
                        $enchantments = $readItem["enchantments"];
                        $enchantments = explode(",", $enchantments);
                        foreach ($enchantments as $enchantment) {
                          $enchantment = explode(":", $enchantment);
                          echo "* Lvl. ".$enchantment[1]." - ".$enchantment[0]."<br>";
                        }
                      ?>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["sellItem"]) || isset($_POST["removeItem"])) {
              if (!$csrf->validate('sellItem')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("price") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else if (post("price") <= 0) {
                echo alertError(t__('Enter a valid price!'));
              }
              else if ($readItem["sold"] == 1) {
                echo alertError(t__('You cannot edit an item on sale!'));
              }
              else {
                if (isset($_POST["sellItem"])) {
                  $oldPrice = $readItem["price"];
                  $updateItem = $db->prepare("UPDATE BazaarItems SET price = ?, description = ? WHERE owner = ? AND id = ?");
                  $updateItem->execute(array(post("price"), post("description"), $readAccount["id"], $readItem["id"]));
                  if ($oldPrice == 0) {
                    echo alertSuccess(t__('Item has been listed for sale successfully!'));
                  }
                  else {
                    echo alertSuccess(t__('Your item has been successfully updated!'));
                  }
                }
                else {
                  $updateItem = $db->prepare("UPDATE BazaarItems SET price = ? WHERE id = ?");
                  $updateItem->execute(array(0, $readItem["id"]));
          
                  echo alertSuccess(t__('The item has been removed from the bazaar. You can get the item back with the /webbazaar command.'));
                }
              }
            }
            ?>
            <div class="card">
              <div class="card-header">
                <?php e__('Sell') ?>
              </div>
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputProduct" class="col-sm-2 col-form-label"><?php e__('Item') ?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputProduct" class="form-control-plaintext" value="<?php echo $readItem["name"]; ?>" readonly>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPrice" class="col-sm-2 col-form-label"><?php e__('Price') ?>:</label>
                    <div class="col-sm-10">
                      <input type="number" name="price" id="inputPrice" class="form-control" value="<?php echo $readItem["price"] == 0 ? null : $readItem["price"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputDesc" class="col-sm-2 col-form-label"><?php e__('Description') ?>:</label>
                    <div class="col-sm-10">
                      <textarea name="description" id="inputDesc" class="form-control"><?php echo $readItem["description"]; ?></textarea>
                    </div>
                  </div>
                  <?php echo $csrf->input('sellItem'); ?>
                  <?php if ($readItem["sold"] == 0): ?>
                    <div class="clearfix">
                      <div class="float-right">
                        <?php if ($readItem["price"] > 0): ?>
                          <button type="submit" class="btn btn-rounded btn-danger" name="removeItem" onclick="return confirm('<?php e__('Are you sure you want to remove this item from the bazaar?') ?>')">
                            <?php e__('Remove from Bazaar') ?>
                          </button>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-rounded btn-success" name="sellItem">
                          <?php echo $readItem["price"] == 0 ? t__('Sell') : t__('Update') ?>
                        </button>
                      </div>
                    </div>
                  <?php endif; ?>
                </form>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="col-md-12">
            <?php echo alertError(t__('Not data were found!')); ?>
          </div>
        <?php endif; ?>
      <?php elseif (get("action") == 'help'): ?>
        <div class="col-md-12">
          <div class="card">
            <div class="card-header"><?php e__('Bazaar Help') ?></div>
            <div class="card-body">
              <h5><?php e__('How to make a sale?') ?></h5>
              <p><?php e__('Place the item you would like to sell in to your bazaar storage by using /webbazaar command. After that you can view and put the items for sale from your bazaar storage in the website.') ?></p>
              <hr>
              <h5><?php e__('How to use purchased items?') ?></h5>
              <p><?php e__('Your purchased items will be available in your bazaar storage. You can access your bazaar storage in-game by using /webbazaaar command.') ?></p>
              <hr>
              <h5><?php e__('How to cancel sales and return items to your bazaar storage?') ?></h5>
              <p><?php e__('You can remove items for sale by clicking "Remove from Bazaar" in the product editing page. You can access your bazaar storage and returned items in-game by using /webbazaar command.') ?></p>
            </div>
          </div>
        </div>
      <?php else: ?>
        <?php go("/404"); ?>
      <?php endif; ?>
    </div>
  </div>
</section>
