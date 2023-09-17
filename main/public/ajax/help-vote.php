<?php
  
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  
  if (moduleIsDisabled('help')) die("error");
  
  function cookie($parameter) {
    if (isset($_COOKIE[$parameter])) {
      return strip_tags(trim(addslashes($_COOKIE[$parameter])));
    }
    else {
      return false;
    }
  }
  
  if (isset($_GET["id"]) && isset($_GET["vote"])) {
    $helpArticle = $db->prepare("SELECT id FROM HelpArticles WHERE id = ?");
    $helpArticle->execute(array(get("id")));
    $readHelpArticle = $helpArticle->fetch();
    if ($helpArticle->rowCount() > 0) {
      $helpArticleVotes = isset($_COOKIE["helpArticleVotes"]) ? explode(",", cookie("helpArticleVotes")) : array();
      if (!isset($_COOKIE["helpArticleVotes"]) || !in_array($readHelpArticle["id"], $helpArticleVotes)) {
        if (!isset($_COOKIE["helpArticleVotes"])) {
          $votes = $readHelpArticle["id"];
        }
        else {
          $votes = cookie("helpArticleVotes").",".$readHelpArticle["id"];
        }
        createCookie("helpArticleVotes", $votes, 365, $sslStatus);
        if (get("vote") == 0) {
          $updateHelpArticle = $db->prepare("UPDATE HelpArticles SET dislikesCount = dislikesCount+1 WHERE id = ?");
          $updateHelpArticle->execute(array($readHelpArticle["id"]));
        }
        if (get("vote") == 1) {
          $updateHelpArticle = $db->prepare("UPDATE HelpArticles SET likesCount = likesCount+1 WHERE id = ?");
          $updateHelpArticle->execute(array($readHelpArticle["id"]));
        }
        die("success");
      }
      else {
        die("error");
      }
    }
    else {
      die("error");
    }
  }
  else {
    die("error");
  }

?>