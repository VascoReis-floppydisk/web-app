<?php
$rota = $_GET['bb'] ?? 'home';

function is_active($r, $current) {
  return $r === $current ? 'active' : '';
}

function is_open($arr, $current) {
  return in_array($current, $arr, true) ? 'show' : '';
}

$residentesRoutes = [
  'residentes_listar',
'residentes_novo',
'residentes_editar'
];
?>

<nav class="sidebar sidebar-offcanvas" id="sidebar">
<ul class="nav">

<!-- Dashboard -->
<li class="nav-item <?= is_active('home',$rota) ?>">
<a class="nav-link" href="index.php?bb=home">
<i class="typcn typcn-device-desktop menu-icon"></i>
<span class="menu-title">Dashboard</span>
</a>
</li>

<!-- Residentes -->
<li class="nav-item <?= in_array($rota,$residentesRoutes,true) ? 'active' : '' ?>">
<a class="nav-link" data-toggle="collapse" href="#residentesMenu"
aria-expanded="false" aria-controls="residentesMenu">
<i class="typcn typcn-group-outline menu-icon"></i>
<span class="menu-title">Residentes</span>
<i class="typcn typcn-chevron-right menu-arrow"></i>
</a>

<div class="collapse <?= is_open($residentesRoutes,$rota) ?>" id="residentesMenu">
<ul class="nav flex-column sub-menu">
<li class="nav-item">
<a class="nav-link <?= is_active('residentes_listar',$rota) ?>"
href="index.php?bb=residentes_listar">
Listar
</a>
</li>
<li class="nav-item">
<a class="nav-link <?= is_active('residentes_novo',$rota) ?>"
href="index.php?bb=residentes_novo">
Novo Residente
</a>
</li>
</ul>
</div>
</li>

<!-- Logout -->
<li class="nav-item">
<a class="nav-link" href="logout.php">
<i class="typcn typcn-power-outline menu-icon"></i>
<span class="menu-title">Sair</span>
</a>
</li>

</ul>
</nav>

<div class="main-panel">
<div class="content-wrapper">
