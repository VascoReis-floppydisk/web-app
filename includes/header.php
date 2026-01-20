<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$nomeUser = $_SESSION['user']['nome'] ?? 'Utilizador';
$bb = $_GET['bb'] ?? 'home';

// Dynamic page title
$titulo = 'Dashboard';
switch ($bb) {
  case 'residentes_listar': $titulo = 'Residentes'; break;
  case 'residentes_novo':   $titulo = 'Novo Residente'; break;
  case 'residentes_editar': $titulo = 'Editar Residente'; break;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title><?= htmlspecialchars($titulo) ?> | Gestão de Residentes</title>

<link rel="stylesheet" href="vendors/typicons.font/font/typicons.css">
<link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
<link rel="stylesheet" href="css/vertical-layout-light/style.css">
<link rel="shortcut icon" href="images/favicon.png" />
</head>
<body>
<div class="container-scroller">

<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
<div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
<a class="navbar-brand brand-logo" href="index.php?bb=home"><img src="images/logo.svg" alt="logo"/></a>
<a class="navbar-brand brand-logo-mini" href="index.php?bb=home"><img src="images/logo-mini.svg" alt="logo"/></a>
<button class="navbar-toggler navbar-toggler align-self-center d-none d-lg-flex" type="button" data-toggle="minimize">
<span class="typcn typcn-th-menu"></span>
</button>
</div>

<div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
<ul class="navbar-nav navbar-nav-right">

<!-- User profile dropdown -->
<li class="nav-item nav-profile dropdown">
<a class="nav-link dropdown-toggle pl-0 pr-0" href="#" data-toggle="dropdown" id="profileDropdown">
<i class="typcn typcn-user-outline mr-0"></i>
<span class="nav-profile-name">Olá, <?= htmlspecialchars($nomeUser) ?></span>
</a>
<div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
<a class="dropdown-item" href="logout.php">
<i class="typcn typcn-power text-primary"></i>
Sair
</a>
</div>
</li>

</ul>

<button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
<span class="typcn typcn-th-menu"></span>
</button>
</div>
</nav>

<div class="container-fluid page-body-wrapper">
