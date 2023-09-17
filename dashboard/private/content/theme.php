<?php
  if (!checkPerm($readAdmin, 'MANAGE_THEME')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  if (get("target") == 'header' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/theme.header.js');
  }
  if (get("target") == 'color' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/theme.color.js');
  }
  $theme = $db->query("SELECT * FROM Theme ORDER BY id DESC LIMIT 1");
  $readTheme = $theme->fetch();
?>
<?php if (get("target") == 'themes'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <style>
      .preview {
        align-items: center;
        width: 100%;
        display: flex;
        justify-content: center;
      }
      .preview .boxImg {
        background: #000;
        width: 100%;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        min-height: 400px;
        transition: ease-in 1s;
      }
      .preview .boxImg {
        background-size: cover !important;
      }
      .preview .boxImg:hover {
        background-position: bottom !important;
      }
      .sl {
        position: relative;
        overflow: hidden;
        background-color: #eef0f2;
      }
      #darkTheme .sl {
        background-color: #12263f;
      }
      .sl-animation-shimmer::after {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        content: "";
        -webkit-transform: translateX(-100%);
        transform: translateX(-100%);
        background-image: -webkit-gradient(linear, left top, right top, color-stop(0, rgba(255, 255, 255, 0)), color-stop(20%, rgba(255, 255, 255, 0.2)), color-stop(60%, rgba(255, 255, 255, 0.5)), to(rgba(255, 255, 255, 0)));
        background-image: linear-gradient(90deg, rgba(255, 255, 255, 0) 0, rgba(255, 255, 255, 0.2) 20%, rgba(255, 255, 255, 0.5) 60%, rgba(255, 255, 255, 0));
        -webkit-animation: shimmer 1s infinite;
        animation: shimmer 1s infinite;
      }
      
      #darkTheme .sl-animation-shimmer::after {
        background-image: -webkit-gradient(linear, left top, right top, color-stop(0, rgba(35, 64, 100, 0)), color-stop(20%, rgba(35, 64, 100, 0.2)), color-stop(60%, rgba(35, 64, 100, 0.5)), to(rgba(35, 64, 100, 0)));
        background-image: linear-gradient(90deg, rgba(35, 64, 100, 0) 0, rgba(35, 64, 100, 0.2) 20%, rgba(35, 64, 100, 0.5) 60%, rgba(35, 64, 100, 0));
      }

      @-webkit-keyframes shimmer {
        100% {
          -webkit-transform: translateX(100%);
          transform: translateX(100%);
        }
      }

      @keyframes shimmer {
        100% {
          -webkit-transform: translateX(100%);
          transform: translateX(100%);
        }
      }

      .sl-rounded-md {
        border-radius: 0.375rem !important;
      }

      .sl-image {
        display: block;
        width: 100%;
        height: 400px;
      }

      .sl-title {
        display: block;
        width: 150px;
        height: 22px;
      }

      .sl-button {
        display: block;
        width: 100%;
        height: 40px;
        border-radius: .375rem;
      }
    </style>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col d-flex align-items-center">
                  <h2 class="header-title d-inline-flex"><?php e__('Themes') ?></h2>
                  <a class="d-inline-flex btn btn-sm btn-primary ml-3" href="/dashboard/theme/add-theme"><?php e__('Add Theme') ?></a>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Themes') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php
        $availableThemes = [];
        $themes = scandir(__ROOT__.'/apps/main/themes');
        foreach ($themes as $theme) {
          if ($theme != '.' && $theme != '..' && is_dir(__ROOT__.'/apps/main/themes/'.$theme)) {
            if ($theme != themeName()) $availableThemes[] = $theme;
          }
        }
        $availableThemesJS = "";
        foreach ($availableThemes as $theme) {
          $availableThemesJS .= "'".$theme."',";
        }
      ?>
      <div class="row" x-cloak x-data="{themes: [], availableThemes: [<?php echo $availableThemesJS; ?>], isLoading: true}" x-init="fetch('https://api2.leaderos.net/themes.php').then(response => response.json()).then(response => { themes = response; isLoading = false; console.log(response); })">
        <div class="col-sm-6 col-lg-4 mb-4 order-0">
          <div class="card h-100">
            <div class="preview">
              <div class="boxImg" style="background: url(<?php echo themeJson('thumbnail'); ?>);"></div>
            </div>
            <div class="card-body pt-4 pb-0">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="card-text mb-0"><?php echo themeJson('name'); ?></h2>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <a href="/dashboard/theme/settings" class="btn btn-white d-block w-100 mt-4"><?php e__('Settings') ?></a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php foreach ($availableThemes as $theme): ?>
          <?php $themeSettings = json_decode(file_get_contents(__ROOT__.'/apps/main/themes/'.$theme."/theme.json"), true) ?>
          <div class="col-sm-6 col-lg-4 mb-4 order-1">
            <div class="card h-100">
              <div class="preview">
                <div class="boxImg" style="background: url(<?php echo $themeSettings["thumbnail"]; ?>);"></div>
              </div>
              <div class="card-body pt-4 pb-0">
                <div class="row align-items-center">
                  <div class="col">
                    <h2 class="card-text mb-0"><?php echo $themeSettings["name"]; ?></h2>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <a href="/dashboard/theme/update-theme/<?php echo $theme; ?>" class="btn btn-primary d-block w-100 mt-2 mt-md-4"><?php e__('Activate') ?></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php for ($i = 0; $i < (3 - (count($availableThemes)+1) % 3); $i++): ?>
          <div class="col-sm-6 col-lg-4 mb-4 order-2" x-show="isLoading">
            <div class="card h-100">
              <div class="sl sl-image sl-animation-shimmer"></div>
              <div class="card-body pt-4 pb-0">
                <div class="row align-items-center">
                  <div class="col">
                    <div class="sl sl-title sl-rounded-md sl-animation-shimmer"></div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 pr-2">
                    <div class="sl sl-button w-100 sl-animation-shimmer mt-4"></div>
                  </div>
                  <div class="col-md-6 pl-2">
                    <div class="sl sl-button w-100 sl-animation-shimmer mt-4"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endfor; ?>
        <template x-for="theme in themes" :key="theme.id">
          <div class="col-sm-6 col-lg-4 mb-4 order-2" x-show="(theme.slug == '<?php echo themeName() ?>' || availableThemes.includes(theme.slug)) ? false : true">
            <div class="card h-100">
              <div class="preview">
                <div class="boxImg" :style="{background: 'url(' + theme.thumbnail + ')'}"></div>
              </div>
              <div class="card-body pt-4 pb-0">
                <div class="row align-items-center">
                  <div class="col">
                    <h2 class="card-text mb-0" x-text="theme.name"></h2>
                  </div>
                  <div class="col-auto" style="font-size: 17px;">
                    <span x-text="theme.price == 0 ? 'Free' : '£' + theme.price"></span>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 pr-2">
                    <a x-bind:href="theme.demo" target="_blank" class="btn btn-white d-block w-100 mt-4">Demo</a>
                  </div>
                  <div class="col-md-6 pl-2">
                    <a x-bind:href="'https://www.leaderos.net/checkout/themes/' + theme.slug" target="_blank" class="btn btn-success d-block w-100 mt-2 mt-md-4"><?php e__('Buy') ?></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
        <?php $themes = []; ?>
        <?php foreach ($themes as $theme): ?>
          <?php $isAvailable = file_exists(__ROOT__.'/apps/main/themes/'.$theme["slug"].'/theme.json'); ?>
          <div class="col-sm-6 col-lg-4 mb-4 <?php echo $theme["slug"] == themeName() ? "order-0" : (($isAvailable) ? "order-1" : "order-2") ?>">
            <div class="card h-100">
              <div class="preview">
                <div class="boxImg" style="background: url(<?php echo $theme["thumbnail"]; ?>);"></div>
              </div>
              <div class="card-body pt-4 pb-0">
                <div class="row align-items-center">
                  <div class="col">
                    <h2 class="card-text mb-0"><?php echo $theme["name"]; ?></h2>
                  </div>
                  <?php if ($theme["slug"] != themeName() && !$isAvailable): ?>
                    <div class="col-auto" style="font-size: 17px;">
                      £<?php echo $theme["price"]; ?>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="row">
                  <?php if ($theme["slug"] == themeName()): ?>
                    <div class="col-md-12">
                      <a href="/dashboard/theme/settings" class="btn btn-white d-block w-100 mt-4"><?php e__('Settings') ?></a>
                    </div>
                  <?php else: ?>
                    <?php if ($isAvailable): ?>
                      <div class="col-md-12">
                        <a href="/dashboard/theme/update-theme/<?php echo $theme["slug"]; ?>" class="btn btn-primary d-block w-100 mt-2 mt-md-4"><?php e__('Activate') ?></a>
                      </div>
                    <?php else: ?>
                      <div class="col-md-6 pr-2">
                        <a href="<?php echo $theme["demo"]; ?>" target="_blank" class="btn btn-white d-block w-100 mt-4">Demo</a>
                      </div>
                      <div class="col-md-6 pl-2">
                        <a href="https://www.leaderos.net/checkout/themes/<?php echo $theme["slug"]; ?>" target="_blank" class="btn btn-success d-block w-100 mt-2 mt-md-4"><?php e__('Buy') ?></a>
                      </div>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php elseif (get("action") == 'insert'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Add Theme') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/theme/themes"><?php e__('Themes') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Add Theme') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["addTheme"])) {
              if (!$csrf->validate('addTheme')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else {
                require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                $upload = new \Verot\Upload\Upload($_FILES["themeFile"]);
                $imageID = md5(uniqid(rand(0, 9999)));
                if ($upload->uploaded) {
                  $upload->allowed = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
                  $upload->file_auto_rename = false;
                  $upload->file_overwrite = true;
                  $upload->process(__ROOT__."/apps/main/private/");
                  if ($upload->processed) {
                    $themeZIPFile = __ROOT__."/apps/main/private/".$upload->file_src_name;
                    $zip = new ZipArchive;
                    if ($zip->open($themeZIPFile) === TRUE) {
                      $zip->extractTo(__ROOT__);
                      $zip->close();
                      unlink($themeZIPFile);
                    }
                    createLog($readAdmin["id"], "THEME_SETTINGS_UPDATED");
                    go("/dashboard/theme/themes");
                  }
                  else {
                    echo alertError(t__('An error occupied while uploading an image: %error%', ['%error%' => $upload->error]));
                  }
                }
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post" enctype="multipart/form-data">
                <div class="input-group mb-4">
                  <div class="custom-file">
                    <input type="file" name="themeFile" class="custom-file-input" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01" accept=".zip">
                    <label class="custom-file-label" for="inputGroupFile01"><?php e__('Choose .zip file') ?></label>
                  </div>
                </div>
                <?php echo $csrf->input('addTheme'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="addTheme"><?php e__('Upload') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'update' && get("id")): ?>
    <?php
      $updateSettings = $db->prepare("UPDATE Settings SET themeName = ?, updatedAt = ? WHERE id = ?");
      $updateSettings->execute([get("id"), time(), $readSettings["id"]]);
      
      go("/dashboard/theme/themes");
    ?>
  <?php else: ?>
    <?php go("/404"); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'settings'): ?>
  <?php if (get("action") == 'update'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Settings') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Theme') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Settings') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php
            $themeJsonFile = themePath(true).'/theme.json';
            
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["updateThemeSettings"])) {
              if (!$csrf->validate('updateThemeSettings')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else {
                require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                if (file_exists($themeJsonFile)) {
                  $themeJson = json_decode(file_get_contents($themeJsonFile), true);
                  foreach ($_POST as $key => $value) {
                    if (substr($key, 0, 9) == 'settings_') {
                      $themeJson["settings"][substr($key, 9)]["value"] = $value;
                    }
                  }
                  foreach ($_FILES as $key => $value) {
                    if (substr($key, 0, 9) == 'settings_') {
                      if ($themeJson["settings"][substr($key, 9)]["settings"]["type"] == "image") {
                        if ($_FILES[$key]["size"] != null) {
                          $image = pathinfo($themeJson["settings"][substr($key, 9)]["value"]);
                          $upload = new \Verot\Upload\Upload($_FILES[$key]);
                          if ($upload->uploaded) {
                            $upload->allowed = array("image/*");
                            $upload->file_overwrite = true;
                            $upload->file_new_name_body = $image["filename"];
                            $upload->image_convert = "png";
                            $upload->process(__ROOT__.$image["dirname"]);
                            $updateTheme = $db->prepare("UPDATE Theme SET updatedAt = ? WHERE id = ?");
                            $updateTheme->execute(array(time(), $readTheme["id"]));
                            if (!$upload->processed) {
                              echo alertError(t__('An error occupied while uploading a header logo: %error%', ['%error%' => $upload->error]));
                            }
                          }
                        }
                      }
                    }
                  }
                  file_put_contents($themeJsonFile, json_encode($themeJson, JSON_PRETTY_PRINT));
                  
                  echo alertSuccess(t__('Changes has been saved successfully!'));
                  createLog($readAdmin["id"], "THEME_SETTINGS_UPDATED");
                }
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post" enctype="multipart/form-data">
                <?php if (file_exists($themeJsonFile)): ?>
                  <?php $themeJson = json_decode(file_get_contents($themeJsonFile), true); ?>
                  <?php foreach ($themeJson["settings"] as $key => $themeSettings): ?>
                    <div class="form-group row">
                      <label for="input-<?php echo $key; ?>" class="col-sm-2 col-form-label"><?php echo $themeSettings["settings"]["title"] ?>:</label>
                      <div class="col-sm-10">
                        <?php if ($themeSettings["settings"]["type"] == "select"): ?>
                          <select id="input-<?php echo $key; ?>" class="form-control" name="settings_<?php echo $key; ?>" data-toggle="select" data-minimum-results-for-search="-1">
                            <?php foreach ($themeSettings["settings"]["values"] as $value): ?>
                              <option value="<?php echo $value["value"]; ?>" <?php echo ($themeSettings["value"] == $value["value"]) ? 'selected="selected"' : null; ?>><?php echo $value["text"] ?></option>
                            <?php endforeach; ?>
                          </select>
                        <?php endif; ?>
                        
                        <?php if ($themeSettings["settings"]["type"] == "input"): ?>
                          <input type="text" id="input-<?php echo $key; ?>" class="form-control" name="settings_<?php echo $key; ?>" value="<?php echo $themeSettings["value"]; ?>">
                        <?php endif; ?>
  
                        <?php if ($themeSettings["settings"]["type"] == "checkbox"): ?>
                          <div class="custom-control custom-switch d-flex align-items-center">
                            <input type="hidden" name="settings_<?php echo $key; ?>" value="<?php echo $themeSettings["value"]; ?>">
                            <input type="checkbox" id="checkbox-<?php echo $key; ?>" class="custom-control-input" <?php echo $themeSettings["value"] == 1 ? 'checked' : null; ?>>
                            <label for="checkbox-<?php echo $key; ?>" class="custom-control-label"></label>
                          </div>
                        <?php endif; ?>
  
                        <?php if ($themeSettings["settings"]["type"] == "image"): ?>
                          <div data-toggle="dropimage" class="dropimage active">
                            <div class="di-thumbnail">
                              <img src="<?php echo $themeSettings["value"]."?cache=".$readTheme["updatedAt"];  ?>" alt="Ön İzleme">
                            </div>
                            <div class="di-select">
                              <label for="file-<?php echo $key; ?>"><?php e__('Select Image') ?></label>
                              <input type="file" id="file-<?php echo $key; ?>" name="settings_<?php echo $key; ?>" accept="image/*">
                            </div>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
                <?php echo $csrf->input('updateThemeSettings'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateThemeSettings"><?php e__('Save Changes') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'header'): ?>
  <?php if (get("action") == 'update'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('Header') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Theme') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('Header') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="card">
            <div class="card-header">
                <?php e__('Header Element Add/Edit') ?>
            </div>
            <div class="card-body">
              <form id="formNestable" class="form-horizontal">
                <div class="form-group">
                  <label for="selectPageList"><?php e__('Page Type') ?>:</label>
                  <select id="selectPageTypeList" class="form-control" name="pageTypeList" data-toggle="select" data-minimum-results-for-search="-1">
                    <option value="custom"><?php e__('Custom Page') ?></option>
                    <option value="home"><?php e__('Home') ?></option>
                    <option value="store"><?php e__('Store') ?></option>
                    <option value="games"><?php e__('Games') ?></option>
                    <option value="lottery"><?php e__('Wheel of Fortune') ?></option>
                    <option value="credit"><?php e__('Credit') ?></option>
                    <option value="credit-buy"><?php e__('Buy Credits') ?></option>
                    <option value="credit-send"><?php e__('Send Credit') ?></option>
                    <option value="leaderboards"><?php e__('Rank') ?></option>
                    <option value="support"><?php e__('Support') ?></option>
                    <option value="chest"><?php e__('Storage') ?></option>
                    <option value="download"><?php e__('Download') ?></option>
                    <option value="help"><?php e__('Help Center') ?></option>
                    <option value="bazaar"><?php e__('Bazaar') ?></option>
                    <option value="gaming-night"><?php e__('Gaming Night') ?></option>
                    <option value="forum"><?php e__('Forum') ?></option>
                    <option value="rules"><?php e__('Rules') ?></option>
                  </select>
                  <input type="hidden" name="pagetype" value="custom">
                </div>
                <div class="form-group">
                  <label for="inputTitle"><?php e__('Title') ?>:</label>
                  <div class="input-group">
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="<?php e__('For Exm: Custom Page') ?>">
                    <div class="input-group-append">
                      <button type="button" class="btn btn-success" data-toggle="iconpicker"></button>
                    </div>
                  </div>
                  <input type="hidden" id="inputIcon" name="icon">
                </div>
                <div class="form-group">
                  <label for="inputURL"><?php e__('URL (Link)') ?>:</label>
                  <input type="text" class="form-control" id="inputURL" name="url" placeholder="For Exm: /custom-page">
                </div>
                <div class="form-group">
                  <label for="selectTab"><?php e__('Tab') ?>:</label>
                  <select id="selectTabStatus" class="form-control" name="tabstatus" data-toggle="select" data-minimum-results-for-search="-1">
                    <option value="0"><?php e__('Same Tab') ?></option>
                    <option value="1"><?php e__('New Tab') ?></option>
                  </select>
                </div>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="button" class="btn btn-rounded btn-danger" data-action="cancel" style="display: none;"><?php e__('Cancel') ?></button>
                    <button type="button" class="btn btn-rounded btn-success" data-action="update" style="display: none;"><?php e__('Update') ?></button>
                    <button type="button" class="btn btn-rounded btn-success" data-action="insert" style="display: inline-block;"><?php e__('Add') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="col-md-9">
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["updateHeader"])) {
              if (!$csrf->validate('updateHeader')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else if (post("json") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else {
                $updateTheme = $db->prepare("UPDATE Theme SET header = ? WHERE id = ?");
                $updateTheme->execute(array($_POST["json"], $readTheme["id"]));
                echo alertSuccess(t__('Changes has been saved successfully!'));
                createLog($readAdmin["id"], "THEME_HEADER_UPDATED");
                echo goDelay("/dashboard/theme/header", 2);
              }
            }
          ?>
          <div class="card">
            <div class="card-header">
                <?php e__('Header Elements') ?>
            </div>
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group">
                  <div class="dd" data-toggle="nestable"></div>
                  <input type="hidden" name="json" value='<?php echo htmlentities($readTheme["header"], ENT_QUOTES, 'UTF-8'); ?>'>
                </div>
                <?php echo $csrf->input('updateHeader'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateHeader"><?php e__('Save Changes') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'css'): ?>
  <?php if (get("action") == 'update'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__('CSS') ?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Dashboard') ?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__('Theme') ?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__('CSS') ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["updateCustomCSS"])) {
              if (!$csrf->validate('updateCustomCSS')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else {
                $updateTheme = $db->prepare("UPDATE Theme SET customCSS = ? WHERE id = ?");
                $updateTheme->execute(array(post("customCSS"), $readTheme["id"]));
                echo alertSuccess(t__('Changes has been saved successfully!'));
                createLog($readAdmin["id"], "THEME_CSS_UPDATED");
                echo goDelay("/dashboard/theme/css", 2);
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div  class="form-group row">
                  <div class="col-sm-12">
                    <p><?php e__('You can type your own css codes right here!') ?></p>
                    <textarea id="textareaCustomCSS" class="form-control" data-toggle="codeEditor" name="customCSS"><?php echo $readTheme["customCSS"]; ?></textarea>
                  </div>
                </div>
                <?php echo $csrf->input('updateCustomCSS'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateCustomCSS"><?php e__('Save Changes') ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
