<?php
  define('DB_HOST', 'localhost');
  define('DB_PORT', '3306');
  define('DB_USERNAME', 'username');
  define('DB_PASSWORD', 'password');
  define('DB_NAME', 'database');

  try {
    $db = new PDO("mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME.";charset=utf8", DB_USERNAME, DB_PASSWORD);
  }
  catch (PDOException $e) {
    die("<strong>MySQL connection error:</strong> ".utf8_encode($e->getMessage()));
  }
?>