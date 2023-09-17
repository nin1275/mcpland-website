<?php include themePath(true).'/private/layouts/translation_js.php'; ?>
<script>
  var themePath = '<?php echo themePath(); ?>';
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-lazyload/16.1.0/lazyload.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/tr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/smooth-scroll/16.1.3/smooth-scroll.polyfills.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.3/min/tiny-slider.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.3/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/parallax/3.1.0/parallax.min.js"></script>
<?php if (themeSettings("announcementBar") == 1) : ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.Marquee/1.5.0/jquery.marquee.min.js"></script>
<?php endif; ?>
<?php
  if (isset($extraResourcesJS)) {
    $extraResourcesJS->getResources();
  }
?>
<script type="text/javascript">
  var $onlineAPI = <?php echo $readSettings["onlineAPI"]; ?>;
  var $preloaderStatus = '<?php echo (($readSettings["preloaderStatus"] == 1) ? 'true' : 'false'); ?>';
</script>
<script src="<?php echo themePath(); ?>/public/assets/js/main.min.js?v=<?php echo BUILD_NUMBER; ?>"></script>
<script src="<?php echo themePath(); ?>/public/assets/js/theme.min.js?v=<?php echo BUILD_NUMBER; ?>"></script>

<?php if ($readSettings["analyticsUA"] != '0'): ?>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $readSettings["analyticsUA"]; ?>"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag("js", new Date());

    gtag("config", "<?php echo $readSettings["analyticsUA"]; ?>");
  </script>
<?php endif; ?>

<?php if ($readSettings["tawktoID"] != '0'): ?>
  <!--Start of Tawk.to Script-->
  <script type="text/javascript">
    var Tawk_API = Tawk_API || {};
    var Tawk_LoadStart = new Date();
    (function(){
      var s1 = document.createElement("script");
      var s0 = document.getElementsByTagName("script")[0];
      s1.async = true;
      s1.src = 'https://embed.tawk.to/<?php echo $readSettings["tawktoID"]; ?>/default';
      s1.charset = 'UTF-8';
      s1.setAttribute('crossorigin','*');
      s0.parentNode.insertBefore(s1,s0);
    })();
  </script>
  <!--End of Tawk.to Script-->

  <!-- Disable ScrollUp button -->
  <style type="text/css">
    #scrollUp {
      display: none !important;
    }
  </style>
<?php endif; ?>

<script>
  var theme = localStorage.getItem('theme') || defaultTheme;
  
  if (theme === 'dark') {
    document.body.classList.add('dark');
    $("#changeMode").find("i").attr("class", "shi shi-sun");
  }
  else {
    document.body.classList.remove('dark');
  }
  
  $("#changeMode").on("click", function() {
    theme = (theme === 'light') ? 'dark' : 'light';
    localStorage.setItem('theme', theme);
    $(this).find("i").attr("class", theme === "light" ? "shi shi-moon" : "shi shi-sun");
    if (theme === 'dark') {
      document.body.classList.add('dark');
    } else {
      document.body.classList.remove('dark');
    }
  });
</script>