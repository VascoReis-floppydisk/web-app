<?php
$bb = $_GET['bb'] ?? 'home';

function active($route, $current) {
  return $route === $current ? 'active' : '';
}

function active_group($prefix, $current) {
  return str_starts_with($current, $prefix) ? 'active' : '';
}
?>

<nav class="sidebar sidebar-offcanvas" id="sidebar">
<ul class="nav">

<!-- DASHBOARD -->
<li class="nav-item">
<a class="nav-link <?= active('home', $bb) ?>"
href="index.php?bb=home">
<i class="typcn typcn-home-outline menu-icon"></i>
<span class="menu-title">Dashboard</span>
</a>
</li>

<!-- RESIDENTES -->
<li class="nav-item">
<a class="nav-link <?= active_group('residentes', $bb) ?>"
href="index.php?bb=residentes_listar">
<i class="typcn typcn-group-outline menu-icon"></i>
<span class="menu-title">Residentes</span>
</a>
</li>

<!-- TRABALHADORES -->
<li class="nav-item">
<a class="nav-link <?= active_group('trabalhadores', $bb) ?>"
href="index.php?bb=trabalhadores_listar">
<i class="typcn typcn-user menu-icon"></i>
<span class="menu-title">Trabalhadores</span>
</a>
</li>

</ul>
</nav>
