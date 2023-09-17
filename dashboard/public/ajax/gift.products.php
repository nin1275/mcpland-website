<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/dashboard/private/config/settings.php");
  if (!checkPerm($readAdmin, 'MANAGE_STORE') && !checkPerm($readAdmin, 'MANAGE_GIFTS')) {
    go('/dashboard/error/001');
  }
?>
<?php if (post("serverID") != null): ?>
  <?php
    $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ?");
    $productCategories->execute(array(post("serverID")));
  ?>
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
    $products->execute(array(post("serverID"), 0));
  ?>
  <?php if ($products->rowCount() > 0): ?>
    <optgroup label="<?php e__('Other') ?>" data-select2-id="0">
      <?php foreach ($products as $readProducts): ?>
        <option value="<?php echo $readProducts["id"]; ?>"><?php echo $readProducts["name"]; ?></option>
      <?php endforeach; ?>
    </optgroup>
  <?php endif; ?>
<?php endif; ?>
