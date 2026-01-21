<?php
require __DIR__ . "/../includes/auth.php";

$perfil = $_SESSION['user']['perfil'] ?? 'user';
?>

<h3 class="mb-3">Dashboard</h3>
<p class="mb-4 text-muted">Bem-vindo ao sistema de Gestão de Residentes.</p>

<div class="row">

<!-- RESIDENTES -->
<div class="col-md-6 grid-margin stretch-card">
<div class="card h-100 border-primary">
<div class="card-body d-flex flex-column">
<h4 class="card-title text-primary">Residentes</h4>

<a class="btn btn-outline-primary mb-2"
href="index.php?bb=residentes_listar">
Ver Residentes
</a>

<?php if ($perfil === 'admin'): ?>
<a class="btn btn-primary mt-auto"
href="index.php?bb=residentes_novo">
Novo Residente
</a>
<?php endif; ?>

</div>
</div>
</div>

<!-- TRABALHADORES (ADMIN ONLY) -->
<?php if ($perfil === 'admin'): ?>
<div class="col-md-6 grid-margin stretch-card">
<div class="card h-100 border-info">
<div class="card-body d-flex flex-column">
<h4 class="card-title text-info">Trabalhadores</h4>

<a class="btn btn-outline-info mb-2"
href="index.php?bb=trabalhadores_listar">
Ver Trabalhadores
</a>

<a class="btn btn-info mt-auto"
href="index.php?bb=trabalhadores_novo">
Novo Trabalhador
</a>

</div>
</div>
</div>
<?php endif; ?>

</div>
