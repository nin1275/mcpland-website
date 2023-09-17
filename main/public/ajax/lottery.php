<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  if (moduleIsDisabled('lottery')) die("error");
  if (isset($_SESSION["login"])) {
  	if (get("action") == 'play' && get("id")) {
      $lottery = $db->prepare("SELECT * FROM Lotteries WHERE id = ?");
      $lottery->execute(array(get("id")));
      $readLottery = $lottery->fetch();
      if ($lottery->rowCount() > 0) {
        // Ücretsiz oyun ise veya oyuncunun parası yetiyorsa
        if ($readLottery["price"] == 0 || $readAccount["credit"] >= $readLottery["price"]) {
          $lotteryHistory = $db->prepare("SELECT LH.creationDate, L.duration FROM LotteryHistory LH INNER JOIN LotteryAwards LA ON LA.id = LH.lotteryAwardID INNER JOIN Lotteries L ON LA.lotteryID = L.id WHERE L.id = ? AND LH.accountID = ? AND L.price = ? ORDER BY LH.id DESC LIMIT 1");
          $lotteryHistory->execute(array($readLottery["id"], $readAccount["id"], 0));
          $readLotteryHistory = $lotteryHistory->fetch();
          $expiryDate = date("Y-m-d H:i:s", (strtotime($readLotteryHistory["creationDate"]) + ($readLotteryHistory["duration"] * 3600)));
          // Oyuncu daha önce ücretsiz oynamamışsa veya oynadıysa da oynama limiti kalktıysa
          if ($lotteryHistory->rowCount() == 0 || ($lotteryHistory->rowCount() > 0 && date("Y-m-d H:i:s") > $expiryDate)) {
            $lotteryAwardID = 0;
            $awardType = 1;
            $award = 0;
            $random = mt_rand(0, 100);
            $total = 0;
            $lotteryAwards = $db->prepare("SELECT * FROM LotteryAwards WHERE lotteryID = ? ORDER BY chance DESC");
            $lotteryAwards->execute(array($readLottery["id"]));
            foreach ($lotteryAwards as $readLotteryAwards) {
              $total += $readLotteryAwards["chance"];
              if ($total > $random) {
                $lotteryAwardID = $readLotteryAwards["id"];
                $lotteryAwardTitle = $readLotteryAwards["title"];
                $awardType = $readLotteryAwards["awardType"];
                $award = $readLotteryAwards["award"];
                break;
              }
            }

            $insertCreditHistory = $db->prepare("INSERT INTO CreditHistory (accountID, paymentID, paymentStatus, type, price, earnings, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?)");

            // Oyun ücretli ise kredisini kes
            if ($readLottery["price"] > 0) {
              $updateAccount =$db->prepare("UPDATE Accounts SET credit = credit - ? WHERE id = ?");
              $updateAccount->execute(array($readLottery["price"], $readAccount["id"]));

              $insertCreditHistory->execute(array($readAccount["id"], 0, 1, 5, $readLottery["price"], 0, date("Y-m-d H:i:s")));
            }

            // Kredi Kazanırsa kredisini ver
            if ($awardType == 1) {
              $updateAccount =$db->prepare("UPDATE Accounts SET credit = credit + ? WHERE id = ?");
              $updateAccount->execute(array($award, $readAccount["id"]));
              $insertCreditHistory->execute(array($readAccount["id"], 0, 1, 6, $award, 0, date("Y-m-d H:i:s")));
            }

            // Ürün Kazanırsa ürünü sandığa ekle
            if ($awardType == 2) {
              $insertChests = $db->prepare("INSERT INTO Chests (accountID, productID, status, creationDate) VALUES (?, ?, ?, ?)");
              $insertChests->execute(array($readAccount["id"], $award, 0, date("Y-m-d H:i:s")));
            }

            // Oyun geçmişine ekle
            $insertLotteryHistory = $db->prepare("INSERT INTO LotteryHistory (accountID, lotteryAwardID, creationDate) VALUES (?, ?, ?)");
            $insertLotteryHistory->execute(array($readAccount["id"], $lotteryAwardID, date("Y-m-d H:i:s")));

            // Discord Webhook Gönder (Pas gelmediyse)
            if ($awardType != 3) {
              if ($readSettings["webhookLotteryURL"] != '0') {
                require_once(__ROOT__."/apps/main/private/packages/class/webhook/webhook.php");
                $search = array("%username%", "%lottery%", "%award%");
                $replace = array($readAccount["realname"], $readLottery["title"], $lotteryAwardTitle);
                $webhookMessage = $readSettings["webhookLotteryMessage"];
                $webhookEmbed = $readSettings["webhookLotteryEmbed"];
                $postFields = (array(
                  'content'     => ($webhookMessage != '0') ? str_replace($search, $replace, $webhookMessage) : null,
                  'avatar_url'  => 'https://minotar.net/avatar/'.$readAccount["realname"].'/256.png',
                  'tts'         => false,
                  'embeds'      => array(
                    array(
                      'type'        => 'rich',
                      'title'       => $readSettings["webhookLotteryTitle"],
                      'color'       => hexdec($readSettings["webhookLotteryColor"]),
                      'description' => str_replace($search, $replace, $webhookEmbed),
                      'image'       => array(
                        'url' => ($readSettings["webhookLotteryImage"] != '0') ? $readSettings["webhookLotteryImage"] : null
                      ),
                      'footer'      =>
                      ($readSettings["webhookLotteryAdStatus"] == 1) ? array(
                        'text'      => 'Powered by LeaderOS',
                        'icon_url'  => 'https://i.ibb.co/b1XB16h/ledaeros-png-64.png'
                      ) : array()
                    )
                  )
                ));
                $curl = new \LeaderOS\Http\Webhook($readSettings["webhookLotteryURL"]);
                $curl(json_encode($postFields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
              }
            }

            die(json_encode(array(
              'data' => $lotteryAwardID
            )));
          }
          else {
            die(json_encode(array(
              'data'      => 'error_duration',
              'variable'  =>  date("d.m.Y H:i", (strtotime($readLotteryHistory["creationDate"]) + ($readLotteryHistory["duration"] * 3600)))
            )));
          }
        }
        else {
          die(json_encode(array(
            'data' => 'error_credit'
          )));
        }
      }
      else {
        die(json_encode(array(
          'data' => 'error'
        )));
      }
  	}
  	else if (get("action") == 'credit') {
  		die($readAccount["credit"]);
  	}
  	else {
      die(json_encode(array(
        'data' => 'error'
      )));
  	}
  }
  else {
    die(json_encode(array(
      'data' => 'error_login'
    )));
  }
?>
