<?php
if ($_SESSION['user']['perfil'] !== 'admin') {
    die("Acesso negado.");
}
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";
require __DIR__ . "/../includes/permissions.php";

/* =========================
 *   QUERY
 * ========================= */
$sql = "
SELECT
id,
foto,
numero_trabalhador,
nome,
telefone,
residencia,
estado_civil,
genero,
DATE_FORMAT(data_nascimento,'%d/%m/%Y') AS nascimento,
DATE_FORMAT(data_admissao,'%d/%m/%Y') AS admissao,
DATE_FORMAT(data_demissao,'%d/%m/%Y') AS demissao,
naturalidade
FROM trabalhadores
ORDER BY nome
";

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
<table class="table table-bordered table-striped align-middle">
<thead>
<tr>
<th>Foto</th>
<th>Nº</th>
<th>Nome</th>
<th>Telefone</th>
<th>Residência</th>
<th>Estado Civil</th>
<th>Género</th>
<th>Nascimento</th>
<th>Admissão</th>
<th>Demissão</th>
<th>Naturalidade</th>
<?php if (is_admin()): ?>
<th>Ações</th>
<?php endif; ?>
</tr>
</thead>

<tbody>
<?php if (mysqli_num_rows($result) === 0): ?>
<tr>
<td colspan="<?= is_admin() ? 12 : 11 ?>" class="text-center">
Nenhum trabalhador encontrado.
</td>
</tr>
<?php endif; ?>

<?php while ($t = mysqli_fetch_assoc($result)): ?>
<tr>

<!-- FOTO -->
<td class="text-center">
<?php if (!empty($t['foto']) && file_exists($t['foto'])): ?>
<img src="<?= htmlspecialchars($t['foto']) ?>"
alt="Foto"
style="width:50px;height:50px;object-fit:cover;border-radius:50%;">
<?php else: ?>
<img src="images/avatar.png"
alt="Sem foto"
style="width:50px;height:50px;object-fit:cover;border-radius:50%;">
<?php endif; ?>
</td>

<td><?= htmlspecialchars($t['numero_trabalhador']) ?></td>
<td><?= htmlspecialchars($t['nome']) ?></td>
<td><?= htmlspecialchars($t['telefone']) ?></td>
<td><?= htmlspecialchars($t['residencia']) ?></td>
<td><?= htmlspecialchars($t['estado_civil']) ?></td>
<td><?= htmlspecialchars($t['genero']) ?></td>
<td><?= $t['nascimento'] ?: '-' ?></td>
<td><?= $t['admissao'] ?: '-' ?></td>
<td><?= $t['demissao'] ?: '-' ?></td>
<td><?= htmlspecialchars($t['naturalidade']) ?></td>

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


