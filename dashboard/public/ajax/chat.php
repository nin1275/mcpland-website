<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/dashboard/private/config/settings.php");
?>
<?php if (get("action") == 'send'): ?>
  <?php
    if (post("message") != null) {
      $insertChatHistory = $db->prepare("INSERT INTO ChatHistory (accountID, message, creationDate) VALUES(?, ?, ?)");
      $insertChatHistory->execute(array($readAdmin["id"], post("message"), date("Y-m-d H:i:s")));
      echo showEmoji(urlContent(hashtag(hashtag(post("message"), "@", "/dashboard/users/view"), "#", "/tags")));
    }
  ?>
<?php elseif (get("action") == 'update'): ?>
  <?php $chatHistory = $db->query("SELECT * FROM (SELECT CH.*, A.realname FROM ChatHistory CH INNER JOIN Accounts A ON CH.accountID = A.id ORDER BY CH.id DESC LIMIT 100) tmp ORDER BY tmp.id ASC"); ?>
  <?php if ($chatHistory->rowCount() > 0): ?>
    <?php foreach ($chatHistory as $readChatHistory): ?>
      <div class="media mb-3">
        <img class="rounded-circle mt-1 mr-2" src="https://minotar.net/avatar/<?php echo $readChatHistory["realname"]; ?>/20.png" alt="Yetkili">
        <div class="media-body">
          <div class="row">
            <div class="col">
              <a href="/dashboard/users/view/<?php echo $readChatHistory["realname"]; ?>">
                <?php
                  echo '<strong>'.$readChatHistory["realname"].'</strong> ';
                ?>
              </a>
            </div>
            <div class="col-auto small">
              <?php
                if (date("Y-m-d") == date("Y-m-d", strtotime($readChatHistory["creationDate"]))) {
                  $creationDate = explode(" ", convertTime($readChatHistory["creationDate"], 1, true));
                  echo $creationDate[1];
                }
                else {
                  echo convertTime($readChatHistory["creationDate"], 1, true);
                }
              ?>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <?php echo showEmoji(urlContent(hashtag(hashtag($readChatHistory["message"], "@", "/dashboard/users/view"), "#", "/tags"))); ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <?php die(t__('No data found!')); ?>
  <?php endif; ?>
<?php else: ?>
  <?php die(false); ?>
<?php endif; ?>
