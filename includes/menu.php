<?php
$rota = $_GET['bb'] ?? 'home';
$userPerfil = $_SESSION['user']['perfil'] ?? 'user';

function active($r) {
  global $rota;
  return $rota === $r ? 'active' : '';
}

function isAdmin() {
  global $userPerfil;
  return $userPerfil === 'admin';
}
?>

<nav class="sidebar sidebar-offcanvas" id="sidebar">
<ul class="nav">


<!-- DASHBOARD -->
<li class="nav-item <?= active('home') ?>">
<a class="nav-link" href="index.php?bb=home">
<i class="typcn typcn-home-outline menu-icon"></i>
<span class="menu-title">Dashboard</span>
</a>
</li>

<!-- RESIDENTES (ALL USERS) -->
<li class="nav-item <?= strpos($rota, 'residentes') === 0 ? 'active' : '' ?>">
<a class="nav-link" href="index.php?bb=residentes_listar">
<i class="typcn typcn-group-outline menu-icon"></i>
<span class="menu-title">Residentes</span>
</a>
</li>

<!-- TRABALHADORES (ADMIN ONLY) -->
<?php if (isAdmin()): ?>
<li class="nav-item <?= strpos($rota, 'trabalhadores') === 0 ? 'active' : '' ?>">
<a class="nav-link" href="index.php?bb=trabalhadores_listar">
<i class="typcn typcn-user-outline menu-icon"></i>
<span class="menu-title">Trabalhadores</span>
</a>
</li>
<?php endif; ?>

<?php if ($_SESSION['user']['perfil'] === 'admin'): ?>
<li class="nav-item">
<a class="nav-link" href="index.php?bb=users_listar">
<i class="typcn typcn-user-add-outline menu-icon"></i>
<span class="menu-title">Utilizadores</span>
</a>
</li>
<?php endif; ?>

<!-- LOGOUT -->
<li class="nav-item">
<a class="nav-link" href="logout.php">
<i class="typcn typcn-power-outline menu-icon"></i>
<span class="menu-title">Sair</span>
</a>
</li>

</ul>
</nav>
