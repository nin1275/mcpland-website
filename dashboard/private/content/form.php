<?php
  if (!checkPerm($readAdmin, 'MANAGE_CUSTOM_FORMS')) {
    go('/dashboard/error/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  
  if (get("target") == 'form' && (get("action") == 'insert' || get("action") == 'update')) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/custom-form.js');
  }
?>
<?php if (get("target") == 'form'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("Custom Forms")?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard")?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Custom Forms")?></li>
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
          <?php $forms = $db->query("SELECT * FROM CustomForms ORDER BY id DESC"); ?>
          <?php if ($forms->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["formID", "formTitle", "formCreationDate"]'>
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <div class="row align-items-center">
                      <div class="col-auto pr-0">
                        <span class="fe fe-search text-muted"></span>
                      </div>
                      <div class="col">
                        <input type="search" class="form-control form-control-flush search" name="search" placeholder="<?php e__("Search")?>">
                      </div>
                    </div>
                  </div>
                  <div class="col-auto">
                    <a class="btn btn-sm btn-white" href="/dashboard/form/create"><?php e__("Add Custom Form")?></a>
                  </div>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                    <tr>
                      <th class="text-center" style="width: 40px;">
                        <a href="#" class="text-muted sort" data-sort="formID">
                          #<?php e__("ID")?>
                        </a>
                      </th>
                      <th>
                        <a href="#" class="text-muted sort" data-sort="formTitle">
                            <?php e__("Title")?>
                        </a>
                      </th>
                      <th>
                        <a href="#" class="text-muted sort" data-sort="formCreationDate">
                            <?php e__("Creation Date")?>
                        </a>
                      </th>
                      <th class="text-right">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    <?php foreach ($forms as $readForms): ?>
                      <tr>
                        <td class="formID text-center" style="width: 40px;">
                          <a href="/dashboard/form/edit/<?php echo $readForms["id"]; ?>">
                            #<?php echo $readForms["id"]; ?>
                          </a>
                        </td>
                        <td class="formTitle">
                          <a href="/dashboard/form/edit/<?php echo $readForms["id"]; ?>">
                            <?php echo $readForms["title"]; ?>
                          </a>
                        </td>
                        <td class="formCreationDate">
                          <?php echo convertTime($readForms["creationDate"], 2, true); ?>
                        </td>
                        <td class="text-right">
                          <a class="btn btn-sm btn-rounded-circle btn-success" href="/dashboard/form/edit/<?php echo $readForms["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Edit")?>">
                            <i class="fe fe-edit-2"></i>
                          </a>
                          <a class="btn btn-sm btn-rounded-circle btn-primary" href="/form/<?php echo $readForms["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__("View")?>">
                            <i class="fe fe-eye"></i>
                          </a>
                          <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/dashboard/form/delete/<?php echo $readForms["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete")?>">
                            <i class="fe fe-trash-2"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
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
                  <h2 class="header-title"><?php e__("Add Custom Form")?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard")?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/form"><?php e__("Custom Forms")?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Add Custom Form")?></li>
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
            if (isset($_POST["insertForms"])) {
              if (!$csrf->validate('insertForms')) {
                echo alertError(t__('A system error happened!'));
              }
              else if (post("title") == null || post("description") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else {
                $insertForms = $db->prepare("INSERT INTO CustomForms (title, slug, description, creationDate) VALUES (?, ?, ?, ?)");
                $insertForms->execute(array(post("title"), $slugify->slugify(post("title")), filteredContent($_POST["description"]), date("Y-m-d H:i:s")));
                
                $formID = $db->lastInsertId();
                foreach ($_POST["formQuestion"] as $key => $value) {
                  if ($_POST["formQuestion"][$key] == '') continue;
                  $_POST["formQuestion"][$key] = strip_tags($_POST["formQuestion"][$key]);
                  $_POST["formQuestionType"][$key] = strip_tags($_POST["formQuestionType"][$key]);
                  $_POST["formQuestionVariables"][$key] = ($_POST["formQuestionVariables"][$key] != null) ? strip_tags($_POST["formQuestionVariables"][$key]) : '-';
                  $insertFormQuestions = $db->prepare("INSERT INTO CustomFormQuestions (formID, question, type, variables) VALUES (?, ?, ?, ?)");
                  $insertFormQuestions->execute(array($formID, $_POST["formQuestion"][$key], $_POST["formQuestionType"][$key], $_POST["formQuestionVariables"][$key]));
                }
                
                echo alertSuccess(t__('Custom Form has been added successfully!'));
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputTitle" class="col-sm-2 col-form-label"><?php e__("Title")?>:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="<?php e__("Enter the title")?>.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaContent" class="col-sm-2 col-form-label"><?php e__("Description")?>:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaContent" class="form-control" name="description" placeholder="<?php e__("Enter the description")?>."></textarea>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-sm-12">
                    <span><?php e__("Questions")?>:</span>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-12">
                    <div class="table-responsive">
                      <table id="tableitems" class="table table-sm table-nowrap array-table">
                        <thead>
                        <tr>
                          <th><?php e__("Questions")?></th>
                          <th><?php e__("Type")?></th>
                          <th><?php e__("Variables")?></th>
                          <th class="text-center pt-0 pb-0 align-middle">
                            <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                              <i class="fe fe-plus"></i>
                            </button>
                          </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="d-none">
                          <td>
                            <textarea rows="2" class="form-control" name="formQuestion[]" placeholder="<?php e__("Enter question")?>"></textarea>
                          </td>
                          <td>
                            <select class="form-control" name="formQuestionType[]">
                              <option value="1"><?php e__("Text")?></option>
                              <option value="2"><?php e__("Large Text")?></option>
                              <option value="3"><?php e__("Select")?></option>
                              <option value="4"><?php e__("Multiple-Select")?></option>
                            </select>
                          </td>
                          <td class="variableData">
                            <div class="selectData" style="display: none;">
                              <input type="text" class="form-control" name="formQuestionVariables[]"  placeholder="<?php e__("You can split the variables with comma's")?>.">
                            </div>
                            <div class="textData" style="margin: .5rem 0;">
                              <span>-</span>
                              <input type="hidden" name="formQuestionVariables[]" value="-" disabled>
                            </div>
                          </td>
                          <td class="text-center align-middle">
                            <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                              <i class="fe fe-trash-2"></i>
                            </button>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <textarea rows="2" type="text" class="form-control" name="formQuestion[]" placeholder="<?php e__("Enter question")?>"></textarea>
                          </td>
                          <td>
                            <select class="form-control" name="formQuestionType[]">
                              <option value="1"><?php e__("Text")?></option>
                              <option value="2"><?php e__("Large Text")?></option>
                              <option value="3"><?php e__("Select")?></option>
                              <option value="4"><?php e__("Multiple-Select")?></option>
                            </select>
                          </td>
                          <td class="variableData">
                            <div class="selectData" style="display: none;">
                              <input type="text" class="form-control" name="formQuestionVariables[]" placeholder="<?php e__("You can split the variables with comma's")?>.">
                            </div>
                            <div class="textData" style="margin: .5rem 0;">
                              <span>-</span>
                              <input type="hidden" name="formQuestionVariables[]" value="-" disabled>
                            </div>
                          </td>
                          <td class="text-center align-middle">
                            <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                              <i class="fe fe-trash-2"></i>
                            </button>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('insertForms'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertForms"><?php e__("Add")?></button>
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
    $form = $db->prepare("SELECT * FROM CustomForms WHERE id = ?");
    $form->execute(array(get("id")));
    $readForm = $form->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("Edit Custom Form")?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard")?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/form"><?php e__("Custom Forms")?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/form"><?php e__("Edit Custom Form")?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($form->rowCount() > 0) ? substr($readForm["title"], 0, 30): "Bulunamadı!"; ?></li>
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
          <?php if ($form->rowCount() > 0): ?>
            <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["updateForms"])) {
              if (!$csrf->validate('updateForms')) {
                echo alertError(t__('A system error happened!'));
              }
              else if (post("title") == null || post("description") == null) {
                echo alertError(t__('Please fill all the fields!'));
              }
              else {
                $updateForms = $db->prepare("UPDATE CustomForms SET title = ?, slug = ?, description = ? WHERE id = ?");
                $updateForms->execute(array(post("title"), $slugify->slugify(post("title")), filteredContent($_POST["description"]), get("id")));
                
                $formID = $readForm["id"];
                $disableOldQuestions = $db->prepare("UPDATE CustomFormQuestions SET isEnabled = ? WHERE formID = ?");
                $disableOldQuestions->execute(array(0, $formID));
                foreach ($_POST["formQuestion"] as $key => $value) {
                  if ($_POST["formQuestion"][$key] == '') continue;
                  $_POST["formQuestion"][$key] = strip_tags($_POST["formQuestion"][$key]);
                  $_POST["formQuestionType"][$key] = strip_tags($_POST["formQuestionType"][$key]);
                  $_POST["formQuestionVariables"][$key] = ($_POST["formQuestionVariables"][$key] != null) ? strip_tags($_POST["formQuestionVariables"][$key]) : '-';
                  $insertFormQuestions = $db->prepare("INSERT INTO CustomFormQuestions (formID, question, type, variables) VALUES (?, ?, ?, ?)");
                  $insertFormQuestions->execute(array($formID, $_POST["formQuestion"][$key], $_POST["formQuestionType"][$key], $_POST["formQuestionVariables"][$key]));
                }
                
                echo alertSuccess(t__('Changes has been saved successfully!'));
              }
            }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputTitle" class="col-sm-2 col-form-label"><?php e__("Title")?>:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="<?php e__("Enter the title")?>." value="<?php echo $readForm["title"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="textareaContent" class="col-sm-2 col-form-label"><?php e__("Description")?>:</label>
                    <div class="col-sm-10">
                      <textarea id="textareaContent" class="form-control" name="description" placeholder="<?php e__("Enter the description")?>."><?php echo $readForm["description"]; ?></textarea>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-sm-12">
                      <span><?php e__("Questions")?>:</span>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-12">
                      <div class="table-responsive">
                        <table id="tableitems" class="table table-sm table-nowrap array-table">
                          <thead>
                          <tr>
                            <th><?php e__("Question")?></th>
                            <th><?php e__("Type")?></th>
                            <th><?php e__("Variables")?></th>
                            <th class="text-center pt-0 pb-0 align-middle">
                              <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                                <i class="fe fe-plus"></i>
                              </button>
                            </th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr class="d-none">
                            <td>
                              <textarea rows="2" class="form-control" name="formQuestion[]" placeholder="<?php e__("Enter the question")?>"></textarea>
                            </td>
                            <td>
                              <select class="form-control" name="formQuestionType[]">
                                <option value="1"><?php e__("Text")?></option>
                                <option value="2"><?php e__("Large Text")?></option>
                                <option value="3"><?php e__("Select")?></option>
                                <option value="4"><?php e__("Multiple-Select")?></option>
                              </select>
                            </td>
                            <td class="variableData">
                              <div class="selectData" style="display: none;">
                                <input type="text" class="form-control" name="formQuestionVariables[]"  placeholder="<?php e__("You can split the variables with comma's")?>.">
                              </div>
                              <div class="textData" style="margin: .5rem 0;">
                                <span>-</span>
                                <input type="hidden" name="formQuestionVariables[]" value="-" disabled>
                              </div>
                            </td>
                            <td class="text-center align-middle">
                              <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                <i class="fe fe-trash-2"></i>
                              </button>
                            </td>
                          </tr>
                          <?php
                            $questions = $db->prepare("SELECT * FROM CustomFormQuestions WHERE formId = ? AND isEnabled = ?");
                            $questions->execute(array($readForm["id"], 1));
                          ?>
                          <?php foreach($questions as $readQuestion): ?>
                            <tr>
                              <td>
                                <textarea rows="2" type="text" class="form-control" name="formQuestion[]" placeholder="<?php e__("Enter the question")?>"><?php echo $readQuestion["question"] ?></textarea>
                              </td>
                              <td>
                                <select class="form-control" name="formQuestionType[]">
                                  <option value="1" <?php echo ($readQuestion["type"] == 1) ? "selected" : null; ?>>Text</option>
                                  <option value="2" <?php echo ($readQuestion["type"] == 2) ? "selected" : null; ?>>Uzun Text</option>
                                  <option value="3" <?php echo ($readQuestion["type"] == 3) ? "selected" : null; ?>>Select</option>
                                  <option value="4" <?php echo ($readQuestion["type"] == 4) ? "selected" : null; ?>>Multi-Select</option>
                                </select>
                              </td>
                              <td class="variableData">
                                <div class="selectData" style="display: <?php echo ($readQuestion["type"] == 1 || $readQuestion["type"] == 2) ? "none" : "block" ?>;">
                                  <input type="text" class="form-control" name="formQuestionVariables[]" placeholder="<?php e__("You can split the variables with comma's")?>." value="<?php echo ($readQuestion["variables"] != '-') ? $readQuestion["variables"] : null; ?>">
                                </div>
                                <div class="textData" style="display: <?php echo ($readQuestion["type"] == 1 || $readQuestion["type"] == 2) ? "block" : "none" ?>; margin: .5rem 0;">
                                  <span>-</span>
                                  <input type="hidden" name="formQuestionVariables[]" value="-" disabled>
                                </div>
                              </td>
                              <td class="text-center align-middle">
                                <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                  <i class="fe fe-trash-2"></i>
                                </button>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateForms'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/dashboard/form/delete/<?php echo $readForm["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("Delete")?>">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/form/<?php echo $readForm["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="<?php e__("View")?>">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateForms"><?php e__("Save Changes")?></button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
    $deleteForm = $db->prepare("DELETE FROM CustomForms WHERE id = ?");
    $deleteForm->execute(array(get("id")));
    go("/dashboard/form");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'answers'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("Form Answers")?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard")?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php e__("Form Answers")?></li>
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
            if (isset($_GET["page"])) {
              if (!is_numeric($_GET["page"])) {
                $_GET["page"] = 1;
              }
              $page = intval(get("page"));
            }
            else {
              $page = 1;
            }
            
            $visiblePageCount = 5;
            $limit = 50;
            
            if (get("status") != null) {
              $forms = $db->prepare("SELECT id FROM Forms WHERE status = ?");
              $forms->execute(array(get("status")));
            } else {
              $forms = $db->query("SELECT id FROM Forms");
            }
            $itemsCount = $forms->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            if (get("status") != null) {
              $forms = $db->prepare("SELECT AP.*, A.realname, AF.title FROM Forms AP INNER JOIN Accounts A ON A.id = AP.accountID INNER JOIN CustomForms AF ON AF.id = AP.formID WHERE AP.status = ? ORDER BY AP.id DESC LIMIT $visibleItemsCount, $limit");
              $forms->execute(array(get("status")));
            } else {
              $forms = $db->query("SELECT AP.*, A.realname, AF.title FROM Forms AP INNER JOIN Accounts A ON A.id = AP.accountID INNER JOIN CustomForms AF ON AF.id = AP.formID ORDER BY AP.id DESC LIMIT $visibleItemsCount, $limit");
            }
            
            
            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $forms = $db->prepare("SELECT AP.*, A.realname, AF.title FROM Forms AP INNER JOIN Accounts A ON A.id = AP.accountID INNER JOIN CustomForms AF ON AF.id = AP.formID WHERE A.realname LIKE :search OR AF.title LIKE :search ORDER BY AP.id DESC");
                $forms->execute(array(
                  "search" => '%'.post("query").'%'
                ));
              }
            }
          ?>
          <?php if ($forms->rowCount() > 0): ?>
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <form action="" method="post" class="d-flex align-items-center w-100">
                    <div class="col">
                      <div class="row align-items-center">
                        <div class="col-auto pr-0">
                          <span class="fe fe-search text-muted"></span>
                        </div>
                        <div class="col">
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="<?php e__("Search (Username, Form Title)")?>" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success"><?php e__("Search")?></button>
                    </div>
                  </form>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                    <tr>
                      <th class="text-center" style="width: 40px;">#<?php e__("ID")?></th>
                      <th><?php e__("Username")?></th>
                      <th><?php e__("Form")?></th>
                      <th><?php e__("Date")?></th>
                      <th class="text-right">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    <?php foreach ($forms as $readForms): ?>
                      <tr>
                        <td class="text-center" style="width: 40px;">
                          #<?php echo $readForms["id"]; ?>
                        </td>
                        <td>
                          <a href="/dashboard/users/view/<?php echo $readForms["accountID"]; ?>">
                            <?php echo $readForms["realname"]; ?>
                          </a>
                        </td>
                        <td>
                          <?php echo $readForms["title"]; ?>
                        </td>
                        <td>
                          <?php echo convertTime($readForms["creationDate"], 2, true); ?>
                        </td>
                        <td class="text-right">
                          <a class="btn btn-sm btn-rounded-circle btn-primary" href="/dashboard/form-answers/view/<?php echo $readForms["id"]; ?>" data-toggle="tooltip" data-placement="top" title="<?php e__("View")?>">
                            <i class="fe fe-eye"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            
            <?php if (post("query") == false): ?>
              <nav class="pt-3 pb-5" aria-label="Page Navigation">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo ($page == 1) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/form-answers/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/dashboard/form-answers/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/dashboard/form-answers/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
                  </li>
                </ul>
              </nav>
            <?php endif; ?>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'view' && get("id")): ?>
    <?php
    $form = $db->prepare("SELECT AP.*, A.realname, AF.title FROM Forms AP INNER JOIN Accounts A ON A.id = AP.accountID INNER JOIN CustomForms AF ON AP.formID = AF.id WHERE AP.id = ?");
    $form->execute(array(get("id")));
    $readForm = $form->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php e__("View Form Answer")?></h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/dashboard"><?php e__("Dashboard")?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/form-answers"><?php e__("Form Answers")?></a></li>
                      <li class="breadcrumb-item"><a href="/dashboard/form-answers"><?php e__("View Form Answer")?></a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($form->rowCount() > 0) ? $readForm["realname"] : t__('Not found!'); ?></li>
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
          <?php if ($form->rowCount() > 0): ?>
            <div class="row">
              <div class="col-md-4">
                <div class="card">
                  <div class="card-header">
                      <?php e__("Username")?>
                  </div>
                  <div class="card-body">
                    <?php echo $readForm["realname"]; ?>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card">
                  <div class="card-header">
                    Form
                  </div>
                  <div class="card-body">
                    <?php echo $readForm["title"]; ?>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card">
                  <div class="card-header">
                      <?php e__("Date")?>
                  </div>
                  <div class="card-body">
                    <?php echo convertTime($readForm["creationDate"], 2, true); ?>
                  </div>
                </div>
              </div>
            </div>
            
            <?php
            $answers = $db->prepare("SELECT GROUP_CONCAT(AA.answer) as answer, AFQ.question FROM FormAnswers AA INNER JOIN CustomFormQuestions AFQ ON AFQ.id = AA.questionID WHERE AA.applicationID = ? GROUP BY AFQ.id");
            $answers->execute(array($readForm["id"]));
            ?>
            <?php foreach ($answers as $readAnswer): ?>
              <div class="card">
                <div class="card-header">
                  <?php echo $readAnswer["question"] ?>
                </div>
                <div class="card-body">
                  <?php echo $readAnswer["answer"]; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <?php echo alertError(t__('No data for this page!')); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
    $deleteForm = $db->prepare("DELETE FROM Forms WHERE id = ?");
    $deleteForm->execute(array(get("id")));
    go("/dashboard/form-answers");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>