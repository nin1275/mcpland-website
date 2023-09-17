<section class="section page-section m-0">
  <div class="play-section">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h1 class="h4 text-white mb-4"><?php e__('Play on %server%', ['%server%' => $serverName]) ?></h1>
      <ol>
        <li><?php e__('In Minecraft, go to <span>Multiplayer</span>, then <span>Add Server</span>') ?></li>
        <li><?php e__('Enter <span>%ip%</span> into the server address box and click <span>Done</span>', ['%ip%' => $serverIP]) ?></li>
      </ol>
      <div class="d-flex flex-column justify-content-center align-items-center mt-5">
        <div class="d-flex flex-column w-75">
          <span class="play-iptitle"><?php e__('Server Address') ?></span>
          <div class="play-ipbox">
            <span class="play-ip"><?php echo $serverIP; ?></span>
          </div>
          <button class="play-btn" data-toggle="copyip" data-clipboard-action="copy" data-clipboard-text="<?php echo $serverIP; ?>"><?php e__('Copy to clipboard') ?></button>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
