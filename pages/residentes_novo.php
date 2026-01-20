<?php
$erro = '';
$nome = '';
$email = '';
$data_nascimento = '';
$quarto = '';
$telefone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $data_nascimento = trim($_POST['data_nascimento'] ?? '');
    $quarto = trim($_POST['quarto'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    if ($nome === '' || $email === '') {
        $erro = "Nome e Email são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "O email não tem um formato válido.";
    } else {
        $stmt = mysqli_prepare(
            $conexao,
            "INSERT INTO residentes
            (nome, email, data_nascimento, quarto, telefone)
        VALUES (?,?,?,?,?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sssss",
            $nome,
            $email,
            $data_nascimento,
            $quarto,
            $telefone
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?bb=residentes_listar");
            exit;
        } else {
            $erro = "Erro ao inserir.";
        }
    }
}
?>

<h4>Novo Residente</h4>

<?php if ($erro): ?>
<div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<form method="post">
<div class="form-group">
<label>Nome *</label>
<input class="form-control" name="nome" value="<?= htmlspecialchars($nome) ?>">
</div>

<div class="form-group">
<label>Email *</label>
<input class="form-control" name="email" value="<?= htmlspecialchars($email) ?>">
</div>

<div class="form-group">
<label>Data de Nascimento</label>
<input type="date" class="form-control" name="data_nascimento"
value="<?= htmlspecialchars($data_nascimento) ?>">
</div>

<div class="form-group">
<label>Quarto</label>
<input class="form-control" name="quarto" value="<?= htmlspecialchars($quarto) ?>">
</div>

<div class="form-group">
<label>Telefone</label>
<input class="form-control" name="telefone" value="<?= htmlspecialchars($telefone) ?>">
</div>

<button class="btn btn-success">Guardar</button>
<a class="btn btn-light" href="index.php?bb=residentes_listar">Cancelar</a>
</form>
