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

<!-- EXTRAS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.4.0/dist/select2-bootstrap4.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.3/tiny-slider.min.css">

<!-- FONTS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- THEMES -->
<link rel="stylesheet" type="text/css" href="<?php echo themePath(); ?>/public/assets/css/theme.min.css?v=<?php echo BUILD_NUMBER; ?>">
<link rel="stylesheet" type="text/css" href="<?php echo themePath(); ?>/public/assets/css/extra.css?v=<?php echo BUILD_NUMBER; ?>">

<?php if (get("route") == 'lottery'): ?>
	<link rel="stylesheet" type="text/css" href="<?php echo themePath(); ?>/public/assets/css/plugins/superwheel/superwheel.min.css">
	<style type="text/css">
		.superWheel .sWheel-inner {
			background-image: url(/apps/main/public/assets/img/extras/lottery-bg.png);
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

<!-- THEME SETTINGS -->
<script>
  var defaultTheme = "<?php echo themeSettings("defaultMode") ?>"; // light, dark
</script>
<?php
  $themeSettings = [
    'color' => themeSettings("color"), // blue, orange, red, green, purple, pink
    'colors' => [
      'blue' => [
        'primary' => '#3b82f6',
        'primary-darker' => '#2563eb',
        'secondary' => '#60a5fa',
        'tertiary' => '#eff6ff',
      ],
      'orange' => [
        'primary' => '#ff9400',
        'primary-darker' => '#ff7100',
        'secondary' => '#ffb15c',
        'tertiary' => '#fff3e7',
      ],
      'red' => [
        'primary' => '#f43f5e',
        'primary-darker' => '#e11d48',
        'secondary' => '#fb7185',
        'tertiary' => '#fff1f2',
      ],
      'green' => [
        'primary' => '#10b981',
        'primary-darker' => '#059669',
        'secondary' => '#34d399',
        'tertiary' => '#ecfdf5',
      ],
      'purple' => [
        'primary' => '#8b5cf6',
        'primary-darker' => '#7c3aed',
        'secondary' => '#a78bfa',
        'tertiary' => '#f5f3ff',
      ],
      'pink' => [
        'primary' => '#ec4899',
        'primary-darker' => '#db2777',
        'secondary' => '#f472b6',
        'tertiary' => '#fdf2f8',
      ],
    ]
  ];
  function getThemeColor($r) {
    global $themeSettings;
    echo $themeSettings['colors'][$themeSettings['color']][$r];
  }
?>
<!-- LIGHT -->
<style>
  .btn-primary {
    background-color: <?php getThemeColor('primary') ?> !important;
    border-color: <?php getThemeColor('primary') ?> !important;
    color: #fff !important;
  }
  .btn-primary:hover {
    color: #fff !important;
    background-color: <?php getThemeColor('primary-darker') ?> !important;
    border-color: <?php getThemeColor('primary-darker') ?> !important;
  }
  .btn-translucent-primary {
    background-color: <?php getThemeColor('tertiary') ?> !important;
    color: <?php getThemeColor('primary') ?> !important;
  }
  .btn-translucent-primary:hover {
    color: #fff !important;
    background-color: <?php getThemeColor('primary') ?> !important;
  }
  .form-group {
    margin-bottom: 1rem;
  }
  .list-group-flush>.list-group-item:last-child {
    border-bottom-left-radius: 15px;
    border-bottom-right-radius: 15px;
  }
  .dropdown-item:first-child {
    border-top-left-radius: 15px !important;
    border-top-right-radius: 15px !important;;
  }
  .dropdown-item:last-child {
    border-bottom-left-radius: 15px !important;;
    border-bottom-right-radius: 15px !important;;
  }
  a {
    color: #000;
  }
  a:hover {
    color: <?php getThemeColor('primary-darker') ?>;
  }
  .navbar-expand-lg .navbar-nav .nav-item.active>.nav-link:not(.disabled),
  .navbar-expand-lg .navbar-nav .nav-item:hover>.nav-link:not(.disabled),
  .navbar-expand-lg .navbar-nav .nav-item:focus>.nav-link:not(.disabled),
  .navbar-light .navbar-nav .nav-link:hover, .navbar-light .navbar-nav .nav-link:focus {
    color: <?php getThemeColor('primary') ?> !important;
    background-color: <?php getThemeColor('tertiary') ?> !important;
  }
  .signin-form .signin-form-card .nav-link-style:hover {
    color: <?php getThemeColor('primary') ?> !important;
  }
  .list-group-item.active {
    color: #fff !important;
    background-color: <?php getThemeColor('primary') ?> !important;
  }
  .linked-list .list-group-item a:hover {
    background-color: <?php getThemeColor('tertiary') ?> !important;
    color: <?php getThemeColor('primary') ?> !important;
  }
  .list-group-item.active a, .list-group-item.active a:hover {
    color: #fff !important;
  }
  .widget-link:hover {
    color: <?php getThemeColor('primary') ?> !important;
  }
  .select2-results__option[aria-selected=true] {
    background-color: <?php getThemeColor('primary') ?> !important;
    color: #fff !important;
  }
  .select2-results__option--highlighted {
    background-color: <?php getThemeColor('tertiary') ?> !important;
    color: <?php getThemeColor('primary') ?> !important;
  }
  
  .dropdown-item:hover, .dropdown-item.active {
    background-color: <?php getThemeColor('tertiary') ?> !important;
    color: <?php getThemeColor('primary') ?> !important;
  }

  .widget-light .widget-title {
    color: <?php getThemeColor('secondary') ?> !important;
  }
  .text-primary {
    color: <?php getThemeColor('primary') ?> !important;
  }
  .bg-primary {
    background-color: <?php getThemeColor('primary') ?> !important;
  }
  .store-nav .nav-link.active, .store-nav .nav-link:hover {
    background-color: <?php getThemeColor('primary') ?> !important;
  }
  a:hover, .breadcrumb-item>a:hover, .nav-link-style:hover {
    color: <?php getThemeColor('primary') ?> !important;
  }
  .btn-danger:hover,
  .btn-success:hover,
  .btn-primary:hover,
  .btn-info:hover,
  .btn-warning:hover {
    color: #fff !important;
  }
</style>

<!-- DARK -->
<style>
  body.dark {
    color: rgba(255, 255, 255, 0.7) !important;
    background-color: #171a21 !important;
  }
  .dark .text-body {
    color: rgba(255, 255, 255, 0.7) !important;
  }
  .dark a {
    color: #fff;
  }
  .dark a:hover {
    color: <?php getThemeColor('primary') ?>;
  }
  .dark .btn-light {
    background-color: #111111 !important;
    color: #ffffff !important;
    border-color: transparent !important;
    transition: background-color 0.2s ease-in-out;
  }
  .dark .btn-light:hover {
    background-color: #171a21 !important;
  }
  .dark .btn-secondary {
    background-color: #171a21 !important;
    color: #ffffff !important;
    border-color: transparent !important;
    transition: background-color 0.2s ease-in-out;
  }
  .dark .btn-secondary:hover {
    background-color: #2a2e37 !important;
  }
  .dark .nav-link-style, .dark .text-muted {
    color: rgba(255, 255, 255, 0.7) !important;
  }
  .dark .form-check-input {
    border-color: rgba(62, 62, 62, 0.7) !important;
    background-color: #171a21 !important;
  }
  .dark .form-check-label {
    cursor: pointer;
    color: rgba(255, 255, 255, 0.7) !important;
  }
  .dark .header {
    background-color: #111111 !important;
  }
  .dark .header .nav-link, .dark .header .nav-link-style {
    color: #fff !important;
  }
  .dark .navbar-expand-lg .navbar-nav .nav-item.active>.nav-link:not(.disabled),
  .dark .navbar-expand-lg .navbar-nav .nav-item:hover>.nav-link:not(.disabled),
  .dark .navbar-expand-lg .navbar-nav .nav-item:focus>.nav-link:not(.disabled),
  .dark .navbar-light .navbar-nav .nav-link:hover, .navbar-light .navbar-nav .nav-link:focus {
    color: #ffffff !important;
    background-color: <?php getThemeColor('primary') ?> !important;
  }
  .dark .header .offcanvas {
    background-color: #111111 !important;
  }
  .dark .offcanvas-collapse .offcanvas-body .navbar-nav .nav-item {
    border-color: rgba(255, 255, 255, 0.12) !important;
  }
  .dark .offcanvas-collapse .offcanvas-body .navbar-nav .dropdown-menu .dropdown-item {
    color: #fff !important;
  }
  .dark .btn-close {
    background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/.625em auto no-repeat;
  }
  .dark .footer, .dark footer .bg-dark {
    background-color: #111111 !important;
  }
  .dark .btn-play span {
    color: #fff !important;;
    background-color: #171a21 !important;;
  }
  .dark h1, .dark .h1, .dark h2, .dark .h2, .dark h3, .dark .h3, .dark h4, .dark .h4, .dark h5, .dark .h5, .dark h6, .dark .h6 {
    color: #fff !important;;
  }
  .dark .breadcrumb-item>a {
    color: #fff !important;;
  }
  .dark .card-hover:hover,
  .dark .card-active,
  .dark .card-hover.border-0::before,
  .dark .card-active.border-0::before {
    border-color: rgba(62, 62, 62, 0.7) !important;
  }
  .dark .card-product .price {
    color: #fff !important;
  }
  .dark .card {
    color: rgba(255, 255, 255, 0.7) !important;
    background-color: #111111 !important;
  }
  .dark .card-header {
    border-color: rgba(255, 255, 255, 0.12);
  }
  .dark .border-top, .dark .border-bottom {
    border-color: rgba(255, 255, 255, 0.12) !important;
  }
  .dark .signin-form .signin-form-card {
    background-color: #111111 !important;
  }
  .dark .signin-form .signin-form-card .nav-link-style {
    color: rgba(255, 255, 255, 0.7) !important;
  }
  .dark .signin-form .signin-form-card .nav-link-style:hover {
    color: <?php getThemeColor('primary') ?> !important;
  }
  .dark .section-help-cta {
    background-color: #101519 !important;
  }
  .dark .faq-card {
    background-color: #111111 !important;
    border-color: transparent !important;
  }
  .dark .list-group-item {
    background-color: #111111 !important;
  }
  .dark .list-group-item a {
    color: #fff !important;
  }
  .dark .list-group-item.active {
    background-color: <?php getThemeColor('primary') ?> !important;
  }
  .dark .list-group-item.active a {
    color: #fff !important;
  }
  .dark .form-control {
    background-color: #171a21;
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.12);
  }
  .dark .form-label, .dark .col-form-label {
    color: rgba(255, 255, 255, 0.7) !important;
  }
  .dark .form-control-plaintext {
    color: #fff !important;
  }
  .dark .select2-selection {
    background-color: #171a21 !important;
    color: #fff !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
  }
  .dark .select2-results__option {
    background-color: #171a21 !important;
    color: #fff !important;
  }
  .dark .select2-results__option[aria-selected=true] {
    background-color: <?php getThemeColor('primary') ?> !important;
    color: #fff !important;
  }
  .dark .select2-results__option--highlighted {
    background-color: #ffe1b9 !important;
    color: <?php getThemeColor('primary') ?> !important;
  }
  .dark .select2-dropdown {
    border-color: rgb(53 49 49) !important;
  }
  .dark #searchBox .input-group-prepend .input-group-text {
    border-radius: 0;
    height: 100%;
    background-color: #171a21 !important;
  }
  .dark tr {
    color: rgba(255, 255, 255, 0.7) !important;
  }
  .dark #leaderboards tr.active {
    background-color: <?php getThemeColor('primary') ?> !important;
    color: #fff !important;
  }
  .dark .table>:not(caption)>*>* {
    border-color: rgba(255, 255, 255, 0.12) !important;
  }
  .dark .search-cancel {
    background-color: #171a21;
    color: #fff;
    height: 100%;
  }
  .dark .bg-dark-2 {
    background-color: #101519 !important;
  }
  .dark .store-nav .nav-link.active, .dark .store-nav .nav-link:hover {
    color: #fff !important;
  }
  .dark .broadcast {
    background-color: #111111;
  }
  .dark .page-item.active .page-link {
    color: #fff !important;
    background-color: #111111 !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
  }
  .dark .dropdown-menu {
    background-color: #171a21 !important;
    color: #fff !important;
  }
  .dark .dropdown-item:hover, .dark .dropdown-item.active {
    background-color: <?php getThemeColor('primary') ?> !important;
    color: #fff !important;
  }
  .dark .dropdown-divider {
    border-color: rgba(255, 255, 255, 0.12) !important;
  }
  .dark .nav-tabs .nav-item.show .nav-link,
  .dark .nav-tabs .nav-link.active {
    color: #fff !important;
    border-color: #fff !important;
  }
  .dark .nav-tabs .nav-link {
    color: rgba(255, 255, 255, 0.7) !important;
  }
  .dark .nav-link-style:hover {
    color: #fff !important;
  }
  .dark .profile-card {
    background-color: #111111 !important;
  }
  .dark .profile-card .bg-secondary {
    background-color: #000 !important;
  }
  .dark .btn-profile {
    color: #fff !important;
    background-color: <?php getThemeColor('primary') ?> !important;
    border-color: transparent !important;
  }
  .dark .btn-profile:hover {
    border-color: transparent !important;
  }
  .dark .modal-content {
    color: rgba(255, 255, 255, 0.7) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    background-color: #000 !important;
  }
  .dark .modal-header {
    color: #fff !important;
  }
  .dark .title.background:before {
    border-color: rgba(255, 255, 255, 0.2) !important;
  }
  .dark .title span {
    color: #fff !important;
    background-color: #000 !important;
  }
  .dark .modal-header, .dark .modal-footer {
    border-color: rgba(255, 255, 255, 0.12) !important;
  }
  .dark .swal2-popup {
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    background-color: #000 !important;
  }
  .dark #swal2-content {
    color: rgba(255, 255, 255, 0.7) !important;
  }
  .dark .was-validated .form-control:valid,
  .dark .was-validated .form-control.is-valid,
  .dark .was-validated .form-select:valid,
  .dark .was-validated .form-select.is-valid {
    border-color: #16c995 !important;
  }
  .dark .btn-tag {
    color: #fff !important;
    background-color: #171a21 !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
  }
  .dark .meta-link {
    color: rgba(255, 255, 255, 0.85) !important;
  }
  .dark .meta-link:hover {
    color: #fff !important;
  }
  
  .dark .vip-table {
    background-color: #111111 !important;
  }
  .dark .table-header {
    color: #fff !important;
  }
  .dark .ck-editor__main a {
    color: #007bff !important;
  }
  .dark .ck-editor__main a:active,
  .dark .ck-editor__main a:hover,
  .dark .ck-editor__main a:focus{
    color: #0056b3 !important;
  }

  .dark pre {
    color: #ffffff !important;
    border: 1px solid #293d54;
    padding: 10px 20px;
    border-radius: 10px;
  }

  .shopping-cart-count-circle {
    background-color: <?php getThemeColor('primary-darker') ?>;
  }
</style>


<!-- CUSTOM CSS -->
<style type="text/css">
  <?php echo $readTheme["customCSS"]; ?>
</style>