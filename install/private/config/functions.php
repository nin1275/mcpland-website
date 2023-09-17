<?php
  session_start();
  ob_start();
  //date_default_timezone_set("Europe/Istanbul");

  function go($url) {
    header("Location: $url");
    exit();
  }

  function post($par, $st = true) {
    if (isset($_POST[$par])) {
      if ($st) {
        return htmlspecialchars(addslashes(trim(strip_tags($_POST[$par]))));
      }
      else {
        return addslashes(trim(strip_tags($_POST[$par])));
      }
    }
    else {
      return false;
    }
  }

  function get($par) {
    if (isset($_GET[$par])) {
      return strip_tags(trim(addslashes($_GET[$par])));
    }
    else {
      return false;
    }
  }

  function sqlPost($par) {
    if (isset($_POST[$par])) {
      return strip_tags(trim($_POST[$par]));
    }
    else {
      return false;
    }
  }

  function phpFileEscape($text = null) {
    return addcslashes($text, '\'\\');
  }

  function createDuration($duration = 0) {
    return date("Y-m-d H:i:s", (strtotime(date("Y-m-d H:i:s")) + ($duration * 86400)));
  }

  function generateSalt($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
  }

  function createSHA256($password){
    $salt = generateSalt(16);
  	$hash = '$SHA$'.$salt.'$'.hash('sha256', hash('sha256', $password).$salt);
  	return $hash;
  }

  function checkSHA256($password, $realPassword){
  	$parts = explode('$', $realPassword);
  	$salt = $parts[2];
  	$hash = hash('sha256', hash('sha256', $password).$salt);
  	$hash = '$SHA$'.$salt.'$'.$hash;
  	return (($hash == $realPassword) ? true : false);
  }

  function checkUsername($username) {
    return preg_match("/[^a-zA-Z0-9_]/", $username);
  }

  function checkEmail($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $mailDomainWhitelist  = array(
        "yandex.com",
        "gmail.com",
        "hotmail.com",
        "hotmail.com.tr",
        "outlook.com",
        "outlook.com.tr",
        "aol.com",
        "icloud.com",
        "yahoo.com",
        "live.com",
        "mynet.com"
      );
      $mailExplode          = explode("@", $email);
      $mailDomain           = strtolower($mailExplode[1]);
      if (in_array($mailDomain, $mailDomainWhitelist)) {
        return false;
      }
      else {
        return true;
      }
    }
    else {
      return true;
    }
  }

  function checkBadPassword($password) {
    $badPasswordList = array(
      '1234',
      '12345',
      '123456',
      '1234567',
      '12345678',
      '123456789',
      '1234567890',
      'abc123',
      'xyz123',
      'qwerty',
      'qwerty123',
      'sifre',
      'sifre0',
      'sifre123',
      'password',
      'password0'
    );
    return in_array($password, $badPasswordList);
  }

  function getIP() {
    if (getenv("HTTP_CLIENT_IP")) {
      $ip = getenv("HTTP_CLIENT_IP");
    }
    else if (getenv("HTTP_X_FORWARDED_FOR")) {
      $ip = getenv("HTTP_X_FORWARDED_FOR");
      if (strstr($ip, ",")) {
        $tmp = explode (",", $ip);
        $ip = trim($tmp[0]);
      }
    }
    else {
      $ip = getenv("REMOTE_ADDR");
    }
    return $ip;
  }
?>
