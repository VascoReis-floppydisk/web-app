<?php
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";
require __DIR__ . "/../includes/permissions.php";

/* =========================
 *   QUERY
 * ========================= */
$sql = "SELECT id, nome, sexo, localizacao, numero FROM trabalhadores ORDER BY nome";
$result = mysqli_query($conexao, $sql);
?>

<h3>Trabalhadores</h3>

<!-- ADD BUTTON (ADMIN ONLY) -->
<?php if (is_admin()): ?>
<div class="mb-3">
<a href="index.php?bb=trabalhadores_novo" class="btn btn-success">
+ Novo Trabalhador
</a>
</div>
<?php endif; ?>

<!-- TABLE -->
<div class="table-responsive">
<table class="table table-bordered table-striped">
<thead>
<tr>
<th>Nome</th>
<th>Sexo</th>
<th>Localização</th>
<th>Telefone</th>
<?php if (is_admin()): ?>
<th>Ações</th>
<?php endif; ?>
</tr>
</thead>

<tbody>
<?php if (mysqli_num_rows($result) === 0): ?>
<tr>
<td colspan="<?= is_admin() ? 5 : 4 ?>" class="text-center">
Nenhum trabalhador encontrado.
</td>
</tr>
<?php endif; ?>

<?php while ($t = mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= htmlspecialchars($t['nome']) ?></td>
<td><?= htmlspecialchars($t['sexo']) ?></td>
<td><?= htmlspecialchars($t['localizacao']) ?></td>
<td><?= htmlspecialchars($t['numero']) ?></td>

<?php if (is_admin()): ?>
<td>
<a href="index.php?bb=trabalhadores_editar&id=<?= $t['id'] ?>"
class="btn btn-sm btn-warning">
Editar
</a>

<a href="index.php?bb=trabalhadores_excluir&id=<?= $t['id'] ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Deseja apagar este trabalhador?')">
Apagar
</a>
</td>
<?php endif; ?>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
