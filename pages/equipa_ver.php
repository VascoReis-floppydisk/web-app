<?php
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    echo '<div class="alert alert-danger">Equipa inválida.</div>';
    return;
}

/* =========================
 *   BUSCAR EQUIPA (SAFE)
 * ========================= */
$stmt = mysqli_prepare($conexao, "SELECT * FROM equipas WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$equipa = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$equipa) {
    echo '<div class="alert alert-danger">Equipa não encontrada.</div>';
    return;
}

/* =========================
 *   ADICIONAR TRABALHADORES
 * ========================= */
if (!empty($_POST['trabalhadores']) && is_array($_POST['trabalhadores'])) {

    $stmt = mysqli_prepare($conexao, "INSERT IGNORE INTO equipa_trabalhador (trabalhador_id, equipa_id) VALUES (?, ?)");

    foreach ($_POST['trabalhadores'] as $trab_id) {
        $trab_id = (int)$trab_id;
        mysqli_stmt_bind_param($stmt, "ii", $trab_id, $id);
        mysqli_stmt_execute($stmt);
    }

    echo '<div class="alert alert-success">Trabalhadores adicionados à equipa!</div>';
}

/* =========================
 *   REMOVER TRABALHADOR
 * ========================= */
if (isset($_GET['remover'])) {
    $trab = (int)$_GET['remover'];

    $stmt = mysqli_prepare($conexao, "DELETE FROM equipa_trabalhador WHERE trabalhador_id = ? AND equipa_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $trab, $id);
    mysqli_stmt_execute($stmt);

    header("Location: index.php?bb=equipa_ver&id=$id");
    exit;
}

/* =========================
 *   LISTAS
 * ========================= */

// Trabalhadores na equipa
$stmt = mysqli_prepare($conexao, "
SELECT t.id, t.nome, t.numero_trabalhador, t.telefone, t.foto
FROM trabalhadores t
INNER JOIN equipa_trabalhador et ON et.trabalhador_id = t.id
WHERE et.equipa_id = ?
ORDER BY t.nome
");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$naEquipa = mysqli_stmt_get_result($stmt);

// Trabalhadores fora da equipa
$stmt = mysqli_prepare($conexao, "
SELECT id, nome
FROM trabalhadores
WHERE id NOT IN (
    SELECT trabalhador_id FROM equipa_trabalhador WHERE equipa_id = ?
)
ORDER BY nome
");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$foraEquipa = mysqli_stmt_get_result($stmt);
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
<div class="row g-3 mt-2">

<?php while ($t = mysqli_fetch_assoc($naEquipa)): ?>

<?php
$foto = null;
if (!empty($t['foto'])) {
    $fotoPath = __DIR__ . "/../" . ltrim($t['foto'], '/');
    if (file_exists($fotoPath)) {
        $foto = $t['foto'];
    }
}
?>

<div class="col-12">
<div class="card shadow-sm border-0 h-100">
<div class="card-body d-flex align-items-center gap-3">

<!-- FOTO -->
<?php if ($foto): ?>
<img src="<?= htmlspecialchars($foto) ?>"
style="width:70px;height:70px;object-fit:cover;border-radius:10px;">
<?php else: ?>
<div class="bg-light text-muted d-flex align-items-center justify-content-center"
style="width:70px;height:70px;border-radius:10px;font-size:12px;">
Sem Foto
</div>
<?php endif; ?>

<!-- INFO -->
<div class="flex-grow-1">
<h6 class="mb-1"><?= htmlspecialchars($t['nome']) ?></h6>
<div class="small text-muted">
Nº: <?= htmlspecialchars($t['numero_trabalhador'] ?? '-') ?><br>
Tel: <?= htmlspecialchars($t['telefone'] ?? '-') ?>
</div>
</div>

<!-- ACTION -->
<a href="index.php?bb=equipa_ver&id=<?= $id ?>&remover=<?= $t['id'] ?>"
class="btn btn-sm btn-outline-danger"
onclick="return confirm('Remover trabalhador da equipa?')">
Remover
</a>

</div>
</div>
</div>

<?php endwhile; ?>
</div>
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
