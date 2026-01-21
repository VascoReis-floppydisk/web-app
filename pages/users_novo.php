<?php
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";

if ($_SESSION['user']['perfil'] !== 'admin') {
    exit("Acesso negado.");
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome   = trim($_POST['nome']);
    $email  = trim($_POST['email']);
    $perfil = $_POST['perfil'];
    $senha  = $_POST['senha'];

    if ($nome === '' || $email === '' || $senha === '') {
        $erro = "Todos os campos são obrigatórios.";
    } else {
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conexao,
                               "INSERT INTO users (nome, email, pass_hash, perfil)
        VALUES (?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "ssss", $nome, $email, $hash, $perfil);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?bb=users_listar");
            exit;
        } else {
            $erro = "Erro ao criar utilizador.";
        }
    }
}
?>

<h3>Novo Utilizador</h3>

<?php if ($erro): ?>
<div class="alert alert-danger"><?= $erro ?></div>
<?php endif; ?>

<form method="post">
<input class="form-control mb-2" name="nome" placeholder="Nome">
<input class="form-control mb-2" name="email" type="email" placeholder="Email">
<input class="form-control mb-2" name="senha" type="password" placeholder="Senha">

<select class="form-control mb-3" name="perfil">
<option value="user">User</option>
<option value="admin">Admin</option>
</select>

<button class="btn btn-success">Guardar</button>
<a href="index.php?bb=users_listar" class="btn btn-secondary">Cancelar</a>
</form>
