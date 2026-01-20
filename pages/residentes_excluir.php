<?php
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    echo "<div class='alert alert-danger'>ID inválido.</div>";
    return;
}

/* DELETE after confirmation */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmtDel = mysqli_prepare($conexao, "DELETE FROM residentes WHERE id=?");
    mysqli_stmt_bind_param($stmtDel, "i", $id);
    mysqli_stmt_execute($stmtDel);
    mysqli_stmt_close($stmtDel);

    header("Location: index.php?bb=residentes_listar");
    exit;
}

/* FETCH resident for confirmation */
$stmt = mysqli_prepare(
    $conexao,
    "SELECT id, nome, email FROM residentes WHERE id=?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$residente = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$residente) {
    echo "<div class='alert alert-danger'>Residente não encontrado.</div>";
    return;
}
?>

<div class="row">
<div class="col-md-8 grid-margin stretch-card">
<div class="card">
<div class="card-body">

<h4 class="card-title">Apagar Residente</h4>

<div class="alert alert-warning">
Tem a certeza que quer apagar:
<strong><?= htmlspecialchars($residente['nome']) ?></strong>
(<?= htmlspecialchars($residente['email']) ?>)?
</div>

<form method="post">
<button class="btn btn-danger mr-2" type="submit">
Confirmar
</button>
<a class="btn btn-light" href="index.php?bb=residentes_listar">
Cancelar
</a>
</form>

</div>
</div>
</div>
</div>
