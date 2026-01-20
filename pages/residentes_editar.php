<?php
$id = intval($_GET['id'] ?? 0);
$erro = '';

$res = mysqli_query($conexao, "SELECT * FROM residentes WHERE id=$id");
$residente = mysqli_fetch_assoc($res);

if (!$residente) {
    die("Residente não encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $data_nascimento = $_POST['data_nascimento'];
    $quarto = trim($_POST['quarto']);
    $telefone = trim($_POST['telefone']);

    if ($nome === '' || $email === '') {
        $erro = "Nome e Email são obrigatórios.";
    } else {
        $stmt = mysqli_prepare(
            $conexao,
            "UPDATE residentes
            SET nome=?, email=?, data_nascimento=?, quarto=?, telefone=?
            WHERE id=?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sssssi",
            $nome,
            $email,
            $data_nascimento,
            $quarto,
            $telefone,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?bb=residentes_listar");
            exit;
        } else {
            $erro = "Erro ao atualizar.";
        }
    }
}
?>

<h4>Editar Residente</h4>

<?php if ($erro): ?>
<div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<form method="post">
<div class="form-group">
<label>Nome *</label>
<input class="form-control" name="nome"
value="<?= htmlspecialchars($residente['nome']) ?>">
</div>

<div class="form-group">
<label>Email *</label>
<input class="form-control" name="email"
value="<?= htmlspecialchars($residente['email']) ?>">
</div>

<div class="form-group">
<label>Data de Nascimento</label>
<input type="date" class="form-control" name="data_nascimento"
value="<?= htmlspecialchars($residente['data_nascimento']) ?>">
</div>

<div class="form-group">
<label>Quarto</label>
<input class="form-control" name="quarto"
value="<?= htmlspecialchars($residente['quarto']) ?>">
</div>

<div class="form-group">
<label>Telefone</label>
<input class="form-control" name="telefone"
value="<?= htmlspecialchars($residente['telefone']) ?>">
</div>

<button class="btn btn-success">Guardar</button>
<a class="btn btn-light" href="index.php?bb=residentes_listar">Cancelar</a>
</form>
