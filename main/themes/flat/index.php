<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
  <?php require_once(themePath(true)."/private/layouts/head.php"); ?>
</head>
<body style="<?php echo (($readSettings["preloaderStatus"] == 1) ? 'overflow: hidden;' : 'overflow: auto;'); ?>">
<?php require_once(themePath(true)."/private/layouts/header.php"); ?>
<main class="main" role="main">
  <?php include $routeFile; ?>
</main>
<?php require_once(themePath(true)."/private/layouts/footer.php"); ?>
<?php require_once(themePath(true)."/private/layouts/scripts.php"); ?>
</body>
</html>
<?php ob_end_flush(); ?>
