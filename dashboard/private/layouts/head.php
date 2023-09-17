<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="author" content="Fırat KAYA">
<link rel="shortcut icon" type="image/x-icon" href="/apps/main/public/assets/img/extras/favicon.png?cache=<?php echo $readSettings["updatedAt"] ?>">

<title><?php echo $serverName." - ".t__('Dashboard'); ?></title>

<!-- EXTRAS -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.5.1/flatpickr.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.9.1/css/froala_editor.pkgd.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.9.1/css/froala_style.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.9.1/css/themes/dark.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.43.0/codemirror.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.43.0/theme/material.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.7/themes/default/style.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.7/themes/default-dark/style.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/css/bootstrap-iconpicker.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.min.css">
<link rel="stylesheet" type="text/css" href="/apps/dashboard/public/assets/plugins/dropimage/css/dropimage.min.css">

<!-- FONTS -->
<link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
<link rel="stylesheet" type="text/css" href="/apps/dashboard/public/assets/fonts/feather/feather.min.css">

<!-- MAIN -->
<link rel="stylesheet" type="text/css" href="/apps/dashboard/public/assets/css/light.main.min.css?v=<?php echo BUILD_NUMBER; ?>" id="stylesheetLight" disabled>
<link rel="stylesheet" type="text/css" href="/apps/dashboard/public/assets/css/dark.main.min.css?v=<?php echo BUILD_NUMBER; ?>" id="stylesheetDark">
<link rel="stylesheet" type="text/css" href="/apps/dashboard/public/assets/css/edit.min.css?v=<?php echo BUILD_NUMBER; ?>">
<link rel="stylesheet" type="text/css" href="/apps/dashboard/public/assets/css/responsive.min.css?v=<?php echo BUILD_NUMBER; ?>">

<style type="text/css">
	body {
		display: none;
	}
</style>

<?php if (get("route") == "theme" && get("target") == "themes"): ?>
  <script src="//unpkg.com/alpinejs" defer></script>
<?php endif; ?>
