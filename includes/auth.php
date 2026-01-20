<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$script = basename($_SERVER['SCRIPT_NAME']);
if ($script === 'login.php' || $script === 'logout.php') {
  return; // não bloquear login/logout
}

if (empty($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
