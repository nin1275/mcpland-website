<?php include __ROOT__.'/apps/dashboard/private/layouts/translation_js.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.5.1/flatpickr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.5.1/l10n/tr.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/1.5.0/list.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.7.3/feather.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.9.1/js/froala_editor.pkgd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.9.1/js/languages/tr.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.43.0/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.43.0/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.7/jstree.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/js/bootstrap-colorpicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/js/bootstrap-iconpicker.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.10.0/js/md5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-lazyload/16.1.0/lazyload.min.js"></script>
<script src="/apps/dashboard/public/assets/plugins/dropimage/js/dropimage.min.js"></script>
<script src="/apps/dashboard/public/assets/plugins/tableitems/js/tableitems.min.js"></script>
<script src="/apps/dashboard/public/assets/plugins/nestable2/js/nestable2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

<?php
  if (isset($extraResourcesJS)) {
    $extraResourcesJS->getResources();
  }
?>
<script type="text/javascript">
  var $onlineAPI = <?php echo $readSettings["onlineAPI"]; ?>;
</script>
<script src="/apps/dashboard/public/assets/js/main.min.js?v=<?php echo BUILD_NUMBER; ?>"></script>
<script src="/apps/dashboard/public/assets/js/edit.min.js?v=<?php echo BUILD_NUMBER; ?>"></script>

<?php if ($readSettings["oneSignalAppID"] != '0' && $readSettings["oneSignalAPIKey"] != '0'): ?>
  <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
  <script>
    var OneSignal = window.OneSignal || [];
    OneSignal.push(function() {
      OneSignal.init({
        appId: "<?php echo $readSettings["oneSignalAppID"]; ?>",
        promptOptions: {
        	actionMessage: "<?php e__('Do you want notifications from your site to be sent to your browser?'); ?>",
        	acceptButtonText: "<?php e__('Confirm') ?>",
        	cancelButtonText: "<?php e__('No, thanks!') ?>"
        },
    		welcomeNotification: {
    			"title": "<?php e__('Notifications'); ?>",
    			"message": "<?php e__('Notifications activated successfully!'); ?>",
    			"url": "/dashboard"
    		}
      });
  	  OneSignal.on('subscriptionChange', function (isSubscribed) {
        OneSignal.getUserId(function(userID) {
          $.ajax({
            type: "POST",
            url: "/apps/dashboard/public/ajax/onesignal.php",
            data: {id: userID}
          });
        });
  	  });
      OneSignal.showSlidedownPrompt();
    });
  </script>
<?php endif; ?>

<?php if (checkPerm($readAdmin, 'MANAGE_NOTIFICATIONS')): ?>
  <script type="text/javascript">
    $(window).on("load", function() {
      var notifications = <?php echo isset($notificationCount) ? $notificationCount : 0; ?>;
      if (notifications != 0) {
        if (notifications > 99) {
          notifications = '99+';
        }
        var defaultTitle = $("title").text();
        $("title").text("(" + notifications + ") " + defaultTitle);
      }
    });
  </script>
<?php endif; ?>
