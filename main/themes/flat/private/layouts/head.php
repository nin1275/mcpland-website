<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="author" content="Fırat KAYA">
<link rel="shortcut icon" type="image/x-icon" href="/apps/main/public/assets/img/extras/favicon.png?cache=<?php echo $readSettings["updatedAt"] ?>">

<?php
  $seoPages = $db->prepare("SELECT * FROM SeoPages WHERE page = ?");
  $seoPages->execute(array(get("route")));
  $readSeoPages = $seoPages->fetch();
  
  $image = null;
  $description = $readSettings["siteDescription"];
  
  if ($seoPages->rowCount() > 0) {
    if ($readSeoPages["title"] != "") {
      $siteTitle = str_replace(
        [
          '%serverName%',
          '%title%'
        ],
        [
          $serverName,
          $readSettings["siteSlogan"]
        ],
        $readSeoPages["title"]
      );
    }
    if ($readSeoPages["description"] != "" || $readSeoPages["description"] != null) {
      $description = $readSeoPages["description"];
    }
    if ($readSeoPages["image"] != "" || $readSeoPages["image"] != null) {
      $image = $readSeoPages["image"];
    }
  }
?>

<title><?php echo $siteTitle; ?></title>

<?php $siteURL = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"]); ?>
<meta name="description" content="<?php echo $description; ?>" />
<meta name="keywords" content="<?php echo $readSettings["siteTags"]; ?>">
<link rel="canonical" href="<?php echo $siteURL; ?>" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?php echo $siteTitle; ?>" />
<meta property="og:description" content="<?php echo $description; ?>" />
<meta property="og:url" content="<?php echo $siteURL; ?>" />
<meta property="og:site_name" content="<?php echo $serverName; ?>" />
<?php if ($image != null): ?>
  <meta property="og:image" content="<?php echo $image; ?>" />
<?php endif; ?>

<!-- MAIN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">

<!-- EXTRAS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.4.0/dist/select2-bootstrap4.min.css">

<!-- FONTS -->
<link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">

<!-- CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo themePath(); ?>/public/assets/css/main.min.css?v=<?php echo BUILD_NUMBER; ?>">
<link rel="stylesheet" type="text/css" href="<?php echo themePath(); ?>/public/assets/css/responsive.min.css?v=<?php echo BUILD_NUMBER; ?>">

<?php if (get("route") == 'lottery'): ?>
	<link rel="stylesheet" type="text/css" href="<?php echo themePath(); ?>/public/assets/css/plugins/superwheel/superwheel.min.css">
	<style type="text/css">
		.superWheel .sWheel-inner {
			background-image: url(/apps/main/public/assets/img/extras/lottery-bg.png?cache=<?php echo $readSettings["updatedAt"] ?>);
			background-repeat: no-repeat;
			background-position: center;
			background-size: 120px;
		}
	</style>
<?php endif; ?>

<style type="text/css">
  .credit-icon::before {
    content: ' ';
    display: inline-block;
    width: 1rem;
    height: 1rem;
    background-image: url(/apps/main/public/assets/img/extras/credit.png);
    background-repeat: no-repeat;
    background-position: center;
    background-size: 1rem;
  }
</style>

<!-- CUSTOM CSS -->
<style type="text/css">
	<?php echo $readTheme["customCSS"]; ?>
</style>
