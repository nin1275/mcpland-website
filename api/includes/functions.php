<?php
  function get($parameter) {
    if (isset($_GET[$parameter])) {
      return strip_tags(trim(addslashes($_GET[$parameter])));
    }
    else {
      return false;
    }
  }
?>
