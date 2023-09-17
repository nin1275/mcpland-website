<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/dashboard/private/config/settings.php");
  
  if (!checkPerm($readAdmin, 'MANAGE_STORE')) {
    go('/dashboard/error/001');
  }
?>
<?php if (post("serverID") != null): ?>
  <?php
    $products = $db->prepare("SELECT * FROM Products WHERE serverID = ?");
    $products->execute(array(post("serverID")));
    $categories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ?");
    $categories->execute(array(post("serverID")));
    $vips = '';
    $categoriesHtml = '<option value="0">'.(t__('None')).'</option>';
  ?>
  <?php foreach ($products as $readProducts): ?>
    <?php $vips .= '<option value="'.$readProducts["id"].'">'.$readProducts["name"].'</option>'; ?>
  <?php endforeach; ?>
  <?php foreach ($categories as $readCategories): ?>
    <?php $categoriesHtml .= '<option value="'.$readCategories["id"].'">'.$readCategories["name"].'</option>'; ?>
  <?php endforeach; ?>
  <?php
    echo json_encode(array(
      'vips' => $vips,
      'categories' => $categoriesHtml,
    ));
  ?>
<?php endif; ?>