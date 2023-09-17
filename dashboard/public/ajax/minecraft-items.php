<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/dashboard/private/config/settings.php");
?>
<?php
  $data = file_get_contents('./items.json');
  $data = json_decode($data, true);
  $selectedItem = get("selectedMinecraftItem");
?>
<option value="default"><?php e__('Default') ?></option>
<?php foreach ($data as $readData): ?>
  <option value="<?php echo $readData["item"]; ?>" icon="<?php echo $readData["item"] ?>" <?php echo ($selectedItem == $readData["item"]) ? 'selected="selected"' : null; ?>>
    <?php echo $readData["name"]; ?>
  </option>
<?php endforeach; ?>