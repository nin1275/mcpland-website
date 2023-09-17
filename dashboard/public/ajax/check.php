<?php
	define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
	require_once(__ROOT__."/apps/dashboard/private/config/settings.php");
	if (isset($_GET["action"]) && isset($_GET["category"])) {
		if (get("action") == "connect") {
			if (get("category") == "console" && checkPerm($readAdmin, 'MANAGE_SERVERS')) {
				if (post("serverIP") != null && post("consoleID") != null && post("consolePort") != null && post("consolePassword") != null) {
					$consoleIP = post("serverIP");
					$consoleID = post("consoleID");
					$consolePort = post("consolePort");
					$consolePassword = post("consolePassword");
					$consoleTimeout = 3;

					if ($consoleID == 1) {
						require_once(__ROOT__."/apps/dashboard/private/packages/class/websend/websend.php");
						$console = new Websend($consoleIP, $consolePort);
						$console->password = $consolePassword;
					}
					else if ($consoleID == 2) {
						require_once(__ROOT__."/apps/dashboard/private/packages/class/rcon/rcon.php");
						$console = new Rcon($consoleIP, $consolePort, $consolePassword, $consoleTimeout);
					}
					else if ($consoleID == 3) {
						require_once(__ROOT__."/apps/dashboard/private/packages/class/websender/websender.php");
						$console = new Websender($consoleIP, $consolePassword, $consolePort);
					}
					else {
						require_once(__ROOT__."/apps/dashboard/private/packages/class/websend/websend.php");
						$console = new Websend($consoleIP, $consolePort);
						$console->password = $consolePassword;
					}
					if (@$console->connect()) {
						$console->disconnect();
						die(true);
					}
					else {
						die(false);
					}
				}
				else {
					die(false);
				}
			}
			else if (get("category") == "mysql" && checkPerm($readAdmin, 'MANAGE_LEADERBOARDS')) {
				if (post("mysqlServer") != null && post("mysqlPort") != null && post("mysqlUsername") != null  && post("mysqlPassword") != null && post("mysqlDatabase") != null) {
					$connectStatus = true;
					try {
						$dbConnectionTest = new PDO("mysql:host=".post("mysqlServer")."; port=".post("mysqlPort")."; dbname=".post("mysqlDatabase")."; charset=utf8", post("mysqlUsername"), post("mysqlPassword"));
					}
					catch (PDOException $e) {
						$connectStatus = false;
					}
					if ($connectStatus == false) {
						die(false);
					}
					else {
						die(true);
					}
					$dbConnectionTest = null;
				}
				else {
					die(false);
				}
			}
			else if (get("category") == "smtp" && checkPerm($readAdmin, 'MANAGE_SETTINGS')) {
				if (post("smtpServer") != null && post("smtpPort") != null && post("smtpSecure") != null && post("smtpUsername") != null  && post("smtpPassword") != null) {
					require_once(__ROOT__."/apps/main/private/packages/class/phpmailer/exception.php");
					require_once(__ROOT__."/apps/main/private/packages/class/phpmailer/phpmailer.php");
					require_once(__ROOT__."/apps/main/private/packages/class/phpmailer/smtp.php");
					$phpMailer = new \PHPMailer\PHPMailer\PHPMailer(true);
					try {
						$phpMailer->IsSMTP();
						$phpMailer->setLanguage('tr', __ROOT__.'/apps/main/private/packages/class/phpmailer/lang/');
						$phpMailer->SMTPAuth = true;
						$phpMailer->Host = post("smtpServer");
						$phpMailer->Port = post("smtpPort");
						$phpMailer->SMTPSecure = ((post("smtpSecure") == 1) ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : ((post("smtpSecure") == 2) ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS));
						$phpMailer->Username = post("smtpUsername");
						$phpMailer->Password = post("smtpPassword");
						$phpMailer->SetFrom($phpMailer->Username, $readSettings["serverName"]);
						$phpMailer->AddAddress($readAdmin["email"], $readAdmin["realname"]);
						$phpMailer->isHTML(true);
						$phpMailer->CharSet = 'UTF-8';
						$phpMailer->Subject = "Test Maili";
						$phpMailer->Body = "Bu bir SMTP bağlantı kontrol testidir.";
						$phpMailer->send();
						die('true');
					} catch (\PHPMailer\PHPMailer\Exception $e) {
						die($e->errorMessage());
					}
				}
				else {
					die(false);
				}
			}
			else {
				die(false);
			}
		}
		else if (get("action") == "webhook" && checkPerm($readAdmin, 'MANAGE_SETTINGS')) {
			require_once(__ROOT__."/apps/main/private/packages/class/webhook/webhook.php");
			$websiteURL = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"]);
			if (get("category") == 'credit') {
				$search = array("%username%", "%credit%", "%money%");
				$credit = floor(($readSettings["bonusCredit"] != 0) ? (10*(($readSettings["bonusCredit"]+100)/100)) : 10);
				$replace = array($readAdmin["realname"], $credit, 10);
			}
			else if (get("category") == 'store') {
				$search = array("%username%", "%server%", "%product%");
				$replace = array($readAdmin["realname"], 'Test', 'Test');
			}
			else if (get("category") == 'support') {
				$search = array("%username%", "%panelurl%");
				$replace = array($readAdmin["realname"], "$websiteURL/dashboard/support/view/1");
			}
			else if (get("category") == 'news') {
				$search = array("%username%", "%panelurl%", "%posturl%");
				$replace = array($readAdmin["realname"], "$websiteURL/dashboard/blog/comments/edit/1", "$websiteURL/posts/1/test");
			}
			else if (get("category") == 'lottery') {
				$search = array("%username%", "%lottery%", "%award%");
				$replace = array($readAdmin["realname"], "Çarkıfelek (5 TL)", "Elmas Kılıç");
			}
			else if (get("category") == 'application') {
				$search = array("%username%", "%form%", "%panelurl%");
				$replace = array($readAdmin["realname"], "Test Application", "$websiteURL/dashboard/applications/view/1");
			}
			else {
				die(false);
			}
			$webhookMessage = strip_tags($_POST["webhookMessage"]);
			$webhookEmbed = strip_tags($_POST["webhookEmbed"]);
			$postFields = (array(
				'content'     => str_replace($search, $replace, $webhookMessage),
				'avatar_url'  => 'https://minotar.net/avatar/'.$readAdmin["realname"].'/256.png',
				'tts'         => false,
				'embeds'      => array(
					array(
						'type'        => 'rich',
						'title'       => post("webhookTitle"),
						'color'       => hexdec(post("webhookColor")),
						'description' => str_replace($search, $replace, $webhookEmbed),
						'image'       => array(
							'url' => (post("webhookImage") != '0') ? post("webhookImage") : null
						),
						'footer'      =>
						(post("webhookAdStatus") == 1) ? array(
							'text'      => 'Powered by LeaderOS',
							'icon_url'  => 'https://i.ibb.co/wNHKQ7B/leaderos-logo.png'
						) : array()
					)
				)
			));
			$curl = new \LeaderOS\Http\Webhook(post("webhookURL"));
			$curl(json_encode($postFields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
			die(true);
		}
		else {
			die(false);
		}
	}
	else {
		die(false);
	}
?>
