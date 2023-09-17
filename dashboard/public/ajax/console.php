<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/dashboard/private/config/settings.php");
  if (!checkPerm($readAdmin, 'MANAGE_SERVERS')) {
    go('/dashboard/error/001');
  }
?>
<?php if (get("action") == 'send' && get("id")): ?>
  <?php
    if (post("command") != null) {
      $command = ltrim(post("command"), '/');
      $server = $db->prepare("SELECT * FROM Servers WHERE id = ?");
      $server->execute(array(get("id")));
      $readServer = $server->fetch();

      $consoleIP = $readServer["ip"];
      $consoleID = $readServer["consoleID"];
      $consolePort = $readServer["consolePort"];
      $consolePassword = $readServer["consolePassword"];
      $consoleTimeout = 3;

      if ($readServer["consoleID"] == 1) {
        require_once(__ROOT__."/apps/dashboard/private/packages/class/websend/websend.php");
        $console = new Websend($consoleIP, $consolePort);
        $console->password = $consolePassword;
      }
      else if ($readServer["consoleID"] == 2) {
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
        $console->sendCommand($command);
        $insertConsoleHistory = $db->prepare("INSERT INTO ConsoleHistory (accountID, command, serverID, creationDate) VALUES(?, ?, ?, ?)");
        $insertConsoleHistory->execute(array($readAdmin["id"], $command, get("id"), date("Y-m-d H:i:s")));
        $console->disconnect();
        die(true);
      }
      else {
        die(false);
      }
    }
  ?>
<?php elseif (get("action") == 'update' && get("id")): ?>
  <?php
    $consoleHistory = $db->prepare("SELECT CH.*, A.realname FROM ConsoleHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE CH.serverID = ? ORDER BY CH.id ASC LIMIT 100");
    $consoleHistory->execute(array(get("id")));
  ?>
  <?php if ($consoleHistory->rowCount() > 0): ?>
    <?php foreach ($consoleHistory as $readConsoleHistory): ?>
      <div class="media mb-3">
        <img class="rounded-circle mt-1 mr-2" src="https://minotar.net/avatar/<?php echo $readConsoleHistory["realname"]; ?>/20.png" alt="Yetkili">
        <div class="media-body">
          <div class="row">
            <div class="col">
              <a href="/dashboard/users/view/<?php echo $readConsoleHistory["realname"]; ?>">
                <?php
                  echo '<strong>'.$readConsoleHistory["realname"].'</strong> ';
                ?>
              </a>
            </div>
            <div class="col-auto small">
              <?php
                if (date("Y-m-d") == date("Y-m-d", strtotime($readConsoleHistory["creationDate"]))) {
                  $creationDate = explode(" ", convertTime($readConsoleHistory["creationDate"], 1, true));
                  echo $creationDate[1];
                }
                else {
                  echo convertTime($readConsoleHistory["creationDate"], 1, true);
                }
              ?>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <?php echo $readConsoleHistory["command"]; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <?php die(false); ?>
  <?php endif; ?>
<?php else: ?>
  <?php die(false); ?>
<?php endif; ?>
