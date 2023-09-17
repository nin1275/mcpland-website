<?php
	define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
	require_once(__ROOT__."/apps/dashboard/private/config/settings.php");
	require_once(__ROOT__."/apps/dashboard/private/packages/class/froalaeditor/lib/FroalaEditor.php");

	if (get("target") == 'image') {
		if (get("action") == 'upload') {
			try {
			  $response = FroalaEditor_Image::upload('/apps/main/public/assets/img/uploads/');
				$response->link = (isset($response->link)) ? ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"].$response->link) : $response->link;
			  echo stripslashes(json_encode($response));
			}
			catch (Exception $e) {
			  echo $e->getMessage();
			}
		}
		else {
			die(false);
		}
	}
	else {
		die(false);
	}
?>
