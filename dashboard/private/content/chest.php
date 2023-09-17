<?php
  if (!checkPerm($readAdmin, 'MANAGE_ACCOUNTS')) {
    go('/dashboard/error/001');
  }
?>
<?php if (get("target") == 'chest'): ?>
  <?php if (get("action") == 'delete' && get("chestID") && get("accountID")): ?>
    <?php
      $deleteChest = $db->prepare("DELETE FROM Chests WHERE id = ?");
      $deleteChest->execute(array(get("chestID")));
      go("/dashboard/users/view/".get("accountID"));
    ?>
  <?php else: ?>
    <?php go("/404"); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go("/404"); ?>
<?php endif; ?>
