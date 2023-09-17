<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/dashboard/private/config/settings.php");
  if (!checkPerm($readAdmin, 'MANAGE_STORE')) {
    go('/dashboard/error/001');
  }
?>
<?php if (post("serverID") != null): ?>
  <?php
    $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ?");
    $productCategories->execute(array(post("serverID")));
  ?>
  <option value="0"><?php e__('Uncategorized'); ?></option>
  <?php foreach ($productCategories as $readProductCategories): ?>
    <option value="<?php echo $readProductCategories["id"]; ?>"><?php echo $readProductCategories["name"]; ?></option>
  <?php endforeach; ?>
<?php endif; ?>
