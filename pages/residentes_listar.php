<?php
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";
require __DIR__ . "/../includes/permissions.php";

/* =========================
 *   FILTERS
 * ========================= */
$nome  = trim($_GET['nome'] ?? '');
$idade = trim($_GET['idade'] ?? '');

/* =========================
 *   QUERY
 * ========================= */
$sql = "
SELECT
id,
nome,
email,
quarto,
telefone,
TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) AS idade
FROM residentes
WHERE 1
";

$params = [];
$types  = "";

/* Filter by name */
if ($nome !== '') {
    $sql .= " AND nome LIKE ? ";
    $params[] = "%$nome%";
    $types .= "s";
}

/* Filter by age */
if ($idade !== '' && is_numeric($idade)) {
    $sql .= " AND TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) = ? ";
    $params[] = (int)$idade;
    $types .= "i";
}

$sql .= " ORDER BY nome";

/* =========================
 *   EXECUTE
 * ========================= */
$stmt = mysqli_prepare($conexao, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<h3>Residentes</h3>

<!-- ADD BUTTON (ADMIN ONLY) -->
<?php if (is_admin()): ?>
<div class="mb-3">
<a href="index.php?bb=residentes_novo" class="btn btn-success">
+ Novo Residente
</a>
</div>
<?php endif; ?>

<!-- FILTER FORM -->
<form method="get" class="mb-4">
<input type="hidden" name="bb" value="residentes_listar">

<div class="row">
<div class="col-md-4">
<input type="text"
name="nome"
class="form-control"
placeholder="Filtrar por nome"
value="<?= htmlspecialchars($nome) ?>">
</div>

<div class="col-md-3">
<input type="number"
name="idade"
class="form-control"
placeholder="Filtrar por idade"
value="<?= htmlspecialchars($idade) ?>">
</div>

<div class="col-md-3">
<button class="btn btn-primary">Filtrar</button>
<a href="index.php?bb=residentes_listar" class="btn btn-secondary">
Limpar
</a>
</div>
</div>
</form>

<!-- TABLE -->
<div class="table-responsive">
<table class="table table-bordered table-striped">
<thead>
<tr>
<th>Nome</th>
<th>Email</th>
<th>Idade</th>
<th>Quarto</th>
<th>Telefone</th>
<?php if (is_admin()): ?>
<th>Ações</th>
<?php endif; ?>
</tr>
</thead>

<tbody>
<?php if (mysqli_num_rows($result) === 0): ?>
<tr>
<td colspan="<?= is_admin() ? 6 : 5 ?>" class="text-center">
Nenhum residente encontrado.
</td>
</tr>
<?php endif; ?>

<?php while ($r = mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= htmlspecialchars($r['nome']) ?></td>
<td><?= htmlspecialchars($r['email']) ?></td>
<td><?= $r['idade'] ?> anos</td>
<td><?= htmlspecialchars($r['quarto']) ?></td>
<td><?= htmlspecialchars($r['telefone']) ?></td>

<?php if (is_admin()): ?>
<td>
<a href="index.php?bb=residentes_editar&id=<?= $r['id'] ?>"
class="btn btn-sm btn-warning">
Editar
</a>

<a href="index.php?bb=residentes_excluir&id=<?= $r['id'] ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Deseja apagar este residente?')">
Apagar
</a>
</td>
<?php endif; ?>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
