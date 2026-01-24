<?php
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";

$isAdmin = (($_SESSION['user']['perfil'] ?? '') === 'admin');

/* ================= QUERY ================= */
$sql = "
SELECT
id,
nome,
numero_trabalhador,
telefone,
residencia,
estado_civil,
genero,
data_admissao AS admissao,
data_demissao AS demissao,
naturalidade,
foto
FROM trabalhadores
ORDER BY nome
";

$result = mysqli_query($conexao, $sql);

if (!$result) {
    die("Erro SQL: " . mysqli_error($conexao));
}
?>

<h3 class="mb-4">Trabalhadores</h3>

<?php if ($isAdmin): ?>
<div class="mb-3">
<a href="index.php?bb=trabalhadores_novo" class="btn btn-success">
+ Novo Trabalhador
</a>
</div>
<?php endif; ?>

<div class="row g-4">

<?php if (mysqli_num_rows($result) === 0): ?>
<div class="col-12 text-center text-muted">
Nenhum trabalhador encontrado.
</div>
<?php endif; ?>

<?php while ($t = mysqli_fetch_assoc($result)): ?>
<?php
$foto = $t['foto'] ?? '';
$fotoPath = __DIR__ . "/../" . $foto;
?>

<div class="col-12 col-md-6 col-lg-4">
<div class="card shadow-sm h-100 border-0">

<!-- FOTO -->
<?php if ($foto && file_exists($fotoPath)): ?>
<img src="<?= htmlspecialchars($foto) ?>"
class="card-img-top"
style="height:260px; object-fit:cover;">
<?php else: ?>
<div class="d-flex align-items-center justify-content-center bg-light text-muted"
style="height:260px;">
Sem foto
</div>
<?php endif; ?>

<!-- BODY -->
<div class="card-body">
<h5 class="card-title mb-2"><?= htmlspecialchars($t['nome']) ?></h5>

<p class="mb-1"><strong>Nº:</strong> <?= htmlspecialchars($t['numero_trabalhador']) ?></p>
<p class="mb-1"><strong>Telefone:</strong> <?= htmlspecialchars($t['telefone']) ?></p>
<p class="mb-1 text-truncate"><strong>Residência:</strong> <?= htmlspecialchars($t['residencia']) ?></p>
<p class="mb-1"><strong>Estado Civil:</strong> <?= htmlspecialchars($t['estado_civil']) ?></p>
<p class="mb-1"><strong>Género:</strong> <?= htmlspecialchars($t['genero']) ?></p>
<p class="mb-1 text-truncate"><strong>Naturalidade:</strong> <?= htmlspecialchars($t['naturalidade']) ?></p>
<p class="mb-1"><strong>Admissão:</strong> <?= htmlspecialchars($t['admissao'] ?? '-') ?></p>
<p class="mb-0"><strong>Demissão:</strong> <?= htmlspecialchars($t['demissao'] ?? '-') ?></p>
</div>

<!-- ACTIONS -->
<div class="card-footer bg-white border-0 pt-0">
<?php if ($isAdmin): ?>

<a href="trabalhadores_card.php?id=<?= $t['id'] ?>"
class="btn btn-sm btn-info w-100 mb-1">
Cartão PDF
</a>

<a href="index.php?bb=trabalhadores_documentos&id=<?= $t['id'] ?>"
class="btn btn-sm btn-secondary w-100 mb-1">
Documentos
</a>




<a href="index.php?bb=trabalhadores_editar&id=<?= $t['id'] ?>"
class="btn btn-sm btn-warning w-100 mb-1">
Editar
</a>

<a href="index.php?bb=trabalhadores_excluir&id=<?= $t['id'] ?>"
class="btn btn-sm btn-danger w-100"
onclick="return confirm('Deseja apagar este trabalhador?')">
Apagar
</a>

<?php else: ?>
<div class="text-center text-muted small">Acesso restrito</div>
<?php endif; ?>
</div>

</div>
</div>

<?php endwhile; ?>

</div>
