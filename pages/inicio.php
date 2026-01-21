<?php
require __DIR__ . "/../includes/auth.php";
require __DIR__ . "/../includes/permissions.php";
?>

<h3>Dashboard</h3>
<p>Bem-vindo ao sistema de Gestão de Residentes.</p>

<div class="row">

<!-- RESIDENTES CARD -->
<div class="col-md-6 grid-margin stretch-card">
<div class="card">
<div class="card-body">

<h4 class="card-title">Residentes</h4>
<p class="card-description">
Consultar os residentes do sistema.
</p>

<a class="btn btn-primary"
href="index.php?bb=residentes_listar">
Ver Residentes
</a>

<?php if (is_admin()): ?>
<a class="btn btn-success ml-2"
href="index.php?bb=residentes_novo">
Novo Residente
</a>
<?php endif; ?>

</div>
</div>
</div>

<!-- TRABALHADORES CARD -->
<div class="col-md-6 grid-margin stretch-card">
<div class="card">
<div class="card-body">

<h4 class="card-title">Trabalhadores</h4>
<p class="card-description">
Consultar os trabalhadores do sistema.
</p>

<a class="btn btn-primary"
href="index.php?bb=trabalhadores_listar">
Ver Trabalhadores
</a>

<?php if (is_admin()): ?>
<a class="btn btn-success ml-2"
href="index.php?bb=trabalhadores_novo">
Novo Trabalhador
</a>
<?php endif; ?>

</div>
</div>
</div>

</div>

