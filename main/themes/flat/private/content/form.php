<?php
  if (!isset($_SESSION["login"])) {
    go("/login");
  }
?>
<section class="section support-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><?php e__('Home') ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php e__('Form') ?></li>
          </ol>
        </nav>
      </div>
      <div class="col-md-12">
        <?php if (get("action") == 'view' && isset($_GET["form"])): ?>
          <?php
          $customForm = $db->prepare("SELECT * FROM CustomForms WHERE slug = ? AND isEnabled = ?");
          $customForm->execute(array(get("form"), 1));
          $readCustomForm = $customForm->fetch();
          ?>
          <?php if ($customForm->rowCount() > 0): ?>
            <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["apply"])) {
              if (!$csrf->validate('apply')) {
                echo alertError(t__('Something went wrong! Please try again later.'));
              }
              else {
                $questionsForChecking = $db->prepare("SELECT * FROM CustomFormQuestions WHERE formId = ? AND isEnabled = ? ORDER BY id ASC");
                $questionsForChecking->execute(array($readCustomForm["id"], 1));
                $questionsForChecking = $questionsForChecking->fetchAll();
  
                $insertForm = $db->prepare("INSERT INTO Forms (accountID, formID, creationDate) VALUES (?, ?, ?)");
                $insertForm->execute(array($readAccount["id"], $readCustomForm["id"], date("Y-m-d H:i:s")));
                $formID = $db->lastInsertId();
  
                foreach ($questionsForChecking as $readQuestionForChecking) {
                  if ($readQuestionForChecking["type"] == 4) {
                    foreach ($_POST["field-".$readQuestionForChecking["id"]] as $key => $value) {
                      $field = htmlspecialchars(trim(strip_tags($_POST["field-".$readQuestionForChecking["id"]][$key])));
                      $insertAnswer = $db->prepare("INSERT INTO FormAnswers (applicationId, questionId, answer) VALUES (?, ?, ?)");
                      $insertAnswer->execute(array($formID, $readQuestionForChecking["id"], $field));
                    }
                  }
                  else {
                    $field = post("field-".$readQuestionForChecking["id"]);
                    $insertAnswer = $db->prepare("INSERT INTO FormAnswers (applicationId, questionId, answer) VALUES (?, ?, ?)");
                    $insertAnswer->execute(array($formID, $readQuestionForChecking["id"], $field));
                  }
                }
  
                echo alertSuccess(t__('The form has been successfully answered!'));
                echo goDelay("/form/view/$formID", 2);
              }
            }
            ?>
            <div class="card">
              <div class="card-header">
                <h4><?php echo $readCustomForm["title"]; ?></h4>
                <div>
                  <?php echo $readCustomForm["description"]; ?>
                </div>
              </div>
              <div class="card-body">
                <form action="" method="post">
                  <?php
                    $questions = $db->prepare("SELECT * FROM CustomFormQuestions WHERE formId = ? AND isEnabled = ? ORDER BY id ASC");
                    $questions->execute(array($readCustomForm["id"], 1));
                  ?>
                  <?php foreach ($questions as $readQuestion): ?>
                    <div class="form-group">
                      <label for="input-<?php echo $readQuestion["id"]; ?>">
                        <?php echo $readQuestion["question"]; ?>
                      </label>
                      <?php if ($readQuestion["type"] == 1): ?>
                        <input type="text" id="input-<?php echo $readQuestion["id"]; ?>" class="form-control" name="field-<?php echo $readQuestion["id"]; ?>" required>
                      <?php endif; ?>
                      <?php if ($readQuestion["type"] == 2): ?>
                        <textarea id="input-<?php echo $readQuestion["id"]; ?>" class="form-control" name="field-<?php echo $readQuestion["id"]; ?>" rows="3" required></textarea>
                      <?php endif; ?>
                      <?php if ($readQuestion["type"] == 3 || $readQuestion["type"] == 4): ?>
                        <select id="input-<?php echo $readQuestion["id"]; ?>" class="form-control" name="field-<?php echo $readQuestion["id"].($readQuestion["type"] == 4 ? "[]" : null); ?>" data-toggle="select2" required <?php echo ($readQuestion["type"] == 4) ? 'multiple="multiple"' : null ?>>
                          <?php $variables = explode(",", $readQuestion["variables"]); ?>
                          <?php foreach ($variables as $variable): ?>
                            <?php $variable = trim($variable); ?>
                            <?php if ($variable != ''): ?>
                              <option value="<?php echo $variable; ?>"><?php echo $variable; ?></option>
                            <?php endif; ?>
                          <?php endforeach; ?>
                        </select>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                  <?php echo $csrf->input('apply'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <button type="submit" class="btn btn-success btn-rounded" name="apply">
                        <?php e__('Send') ?>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('Form not found!')); ?>
          <?php endif; ?>
        <?php elseif (get("action") == 'get' && isset($_GET["id"])): ?>
          <?php
          $form = $db->prepare("SELECT AP.*, A.realname, AF.title FROM Forms AP INNER JOIN Accounts A ON A.id = AP.accountID INNER JOIN CustomForms AF ON AP.formID = AF.id WHERE AP.id = ? AND AP.accountID = ?");
          $form->execute(array(get("id"), $readAccount["id"]));
          $readForm = $form->fetch();
          ?>
          <?php if ($form->rowCount() > 0): ?>
            <div class="card">
              <div class="card-header">
                <div class="row">
                  <div class="col">
                    <?php echo substr($readForm["title"], 0, 50); ?>
                  </div>
                </div>
              </div>
              <div class="card-body pb-0">
                <?php
                  $answers = $db->prepare("SELECT GROUP_CONCAT(AA.answer) as answer, AFQ.question FROM FormAnswers AA INNER JOIN CustomFormQuestions AFQ ON AFQ.id = AA.questionID WHERE AA.applicationID = ? GROUP BY AFQ.id");
                  $answers->execute(array($readForm["id"]));
                ?>
                <?php if ($answers->rowCount() > 0): ?>
                  <?php foreach ($answers as $readAnswer): ?>
                    <div class="message">
                      <div class="message-content">
                        <div class="message-header">
                          <div class="message-username" style="font-weight: 500">
                            <?php echo $readAnswer["question"]; ?>
                          </div>
                        </div>
                        <div class="message-body">
                          <p>
                            <?php echo $readAnswer["answer"]; ?>
                          </p>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError(t__('Form not found!')); ?>
          <?php endif; ?>
        <?php else: ?>
          <?php go('/404'); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>