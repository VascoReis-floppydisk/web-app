<?php
$id = (int)($_GET['id'] ?? 0);

/* =========================
 B US*CAR EQUIPA
 ========================= */
$equipa = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT * FROM equipas WHERE id = $id"));
if (!$equipa) {
    echo '<div class="alert alert-danger">Equipa não encontrada.</div>';
    return;
}

/* =========================
 A DI*CIONAR TRABALHADORES
 ========================= */
if (!empty($_POST['trabalhadores'])) {
    $stmt = mysqli_prepare($conexao, "INSERT IGNORE INTO equipa_trabalhador (trabalhador_id, equipa_id) VALUES (?, ?)");

    foreach ($_POST['trabalhadores'] as $trab_id) {
        $trab_id = (int)$trab_id;
        mysqli_stmt_bind_param($stmt, "ii", $trab_id, $id);
        mysqli_stmt_execute($stmt);
    }

    echo '<div class="alert alert-success">Trabalhadores adicionados à equipa!</div>';
}

/* =========================
 R EM*OVER TRABALHADOR
 ========================= */
if (isset($_GET['remover'])) {
    $trab = (int)$_GET['remover'];
    mysqli_query($conexao, "DELETE FROM equipa_trabalhador WHERE trabalhador_id = $trab AND equipa_id = $id");

    echo "<script>window.location='index.php?bb=equipa_ver&id=$id';</script>";
    exit;
}

/* =========================
 L IS*TAS
 ========================= */

// Trabalhadores já na equipa
$naEquipa = mysqli_query($conexao, "
SELECT t.id, t.nome
FROM trabalhadores t
INNER JOIN equipa_trabalhador et ON et.trabalhador_id = t.id
WHERE et.equipa_id = $id
ORDER BY t.nome
");

// Trabalhadores fora da equipa
$foraEquipa = mysqli_query($conexao, "
SELECT id, nome
FROM trabalhadores
WHERE id NOT IN (
    SELECT trabalhador_id FROM equipa_trabalhador WHERE equipa_id = $id
)
ORDER BY nome
");
?>

<div class="row">

<!-- ESQUERDA - MEMBROS -->
<div class="col-md-6 grid-margin stretch-card">
<div class="card">
<div class="card-body">
<h4 class="card-title">Equipa: <?= htmlspecialchars($equipa['nome']) ?></h4>

<h6 class="mt-4">Trabalhadores na Equipa</h6>

<?php if (mysqli_num_rows($naEquipa) == 0): ?>
<p class="text-muted">Nenhum trabalhador nesta equipa.</p>
<?php else: ?>
<ul class="list-group">
<?php while ($t = mysqli_fetch_assoc($naEquipa)): ?>
<li class="list-group-item d-flex justify-content-between align-items-center">
<?= htmlspecialchars($t['nome']) ?>
<a href="index.php?bb=equipa_ver&id=<?= $id ?>&remover=<?= $t['id'] ?>"
class="btn btn-sm btn-outline-danger">
Remover
</a>
</li>
<?php endwhile; ?>
</ul>
<?php endif; ?>

</div>
</div>
</div>

<!-- DIREITA - ADICIONAR -->
<div class="col-md-6 grid-margin stretch-card">
<div class="card">
<div class="card-body">
<h4 class="card-title">Adicionar Trabalhadores</h4>

<form method="POST">
<div class="mb-3">
<select name="trabalhadores[]" class="form-select" multiple size="12">
<?php if (mysqli_num_rows($foraEquipa) == 0): ?>
<option disabled>Todos os trabalhadores já estão nesta equipa</option>
<?php else: ?>
<?php while ($t = mysqli_fetch_assoc($foraEquipa)): ?>
<option value="<?= $t['id'] ?>">
<?= htmlspecialchars($t['nome']) ?>
</option>
<?php endwhile; ?>
<?php endif; ?>
</select>
</div>

<button class="btn btn-primary">Adicionar à Equipa</button>
</form>

</div>
</div>
</div>

</div>
