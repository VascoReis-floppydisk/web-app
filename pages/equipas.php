<?php
/* =============================
 C RI*AR EQUIPA
 ============================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nome'])) {
    $nome = trim($_POST['nome']);

    $stmt = mysqli_prepare($conexao, "INSERT INTO equipas (nome) VALUES (?)");
    mysqli_stmt_bind_param($stmt, "s", $nome);

    if (!mysqli_stmt_execute($stmt)) {
        echo '<div class="alert alert-danger">Essa equipa já existe ou ocorreu um erro.</div>';
    } else {
        echo '<div class="alert alert-success">Equipa criada com sucesso!</div>';
    }
}

/* =============================
 A PA*GAR EQUIPA
 ============================= */
if (isset($_GET['apagar'])) {
    $id = (int)$_GET['apagar'];
    mysqli_query($conexao, "DELETE FROM equipas WHERE id = $id");

    echo "<script>window.location='index.php?bb=equipas';</script>";
    exit;
}

/* =============================
 L IS*TAR EQUIPAS
 ============================= */
$equipas = mysqli_query($conexao, "SELECT * FROM equipas ORDER BY nome");
?>

<div class="row">

<!-- COLUNA ESQUERDA - ADICIONAR EQUIPA -->
<div class="col-md-5 grid-margin stretch-card">
<div class="card">
<div class="card-body">
<h4 class="card-title">Adicionar Nova Equipa</h4>

<form method="POST">
<div class="input-group">
<input type="text" name="nome" class="form-control" placeholder="Nome da equipa" required>
<button class="btn btn-primary">Adicionar</button>
</div>
</form>

</div>
</div>
</div>

<!-- COLUNA DIREITA - LISTA DE EQUIPAS -->
<div class="col-md-7 grid-margin stretch-card">
<div class="card">
<div class="card-body">
<h4 class="card-title">Equipas Existentes</h4>

<div class="table-responsive">
<table class="table table-striped">
<thead>
<tr>
<th>Nome</th>
<th width="180">Ações</th>
</tr>
</thead>
<tbody>
<?php while ($e = mysqli_fetch_assoc($equipas)): ?>
<tr>
<td><?= htmlspecialchars($e['nome']) ?></td>
<td>
<a href="index.php?bb=equipa_ver&id=<?= $e['id'] ?>"
class="btn btn-sm btn-info">
Ver
</a>

<a href="index.php?bb=equipas&apagar=<?= $e['id'] ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Apagar esta equipa?')">
Apagar
</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

</div>
</div>
</div>

</div>
