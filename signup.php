<?php
require __DIR__ . "/config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$erro = '';
$nome = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || $email === '' || $senha === '') {
        $erro = "Todos os campos são obrigatórios.";
    } else {

        /* Check if email already exists */
        $stmt = mysqli_prepare($conexao, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $erro = "Este email já está registado.";
        } else {

            $hash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($conexao,
                                   "INSERT INTO users (nome, email, pass_hash, perfil)
            VALUES (?, ?, ?, 'user')"
            );

            mysqli_stmt_bind_param($stmt, "sss", $nome, $email, $hash);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: login.php");
                exit;
            } else {
                $erro = "Erro ao criar conta.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Registar | Gestão de Residentes</title>

<link rel="stylesheet" href="vendors/typicons.font/font/typicons.css">
<link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
<link rel="stylesheet" href="css/vertical-layout-light/style.css">
<link rel="shortcut icon" href="images/favicon.png" />
</head>

<body>
<div class="container-scroller">
<div class="container-fluid page-body-wrapper full-page-wrapper">
<div class="content-wrapper d-flex align-items-center auth px-0">
<div class="row w-100 mx-0">
<div class="col-lg-4 mx-auto">

<div class="auth-form-light text-left py-5 px-4 px-sm-5">

<div class="brand-logo">
<img src="images/logo.svg" alt="logo">
</div>

<h4>Criar Conta</h4>
<h6 class="font-weight-light">Registe-se como utilizador.</h6>

<?php if ($erro): ?>
<div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<form class="pt-3" method="post">

<div class="form-group">
<input type="text"
name="nome"
class="form-control form-control-lg"
placeholder="Nome"
value="<?= htmlspecialchars($nome) ?>">
</div>

<div class="form-group">
<input type="email"
name="email"
class="form-control form-control-lg"
placeholder="Email"
value="<?= htmlspecialchars($email) ?>">
</div>

<div class="form-group">
<input type="password"
name="senha"
class="form-control form-control-lg"
placeholder="Palavra-passe">
</div>

<div class="mt-3">
<button class="btn btn-block btn-primary btn-lg font-weight-medium">
CRIAR CONTA
</button>
</div>

<div class="text-center mt-4 font-weight-light">
Já tem conta?
<a href="login.php" class="text-primary">Entrar</a>
</div>

</form>
</div>
</div>
</div>
</div>
</div>
</div>

<script src="vendors/js/vendor.bundle.base.js"></script>
<script src="js/template.js"></script>
</body>
</html>
<div class="text-center mt-4 font-weight-light">
Não tem conta?
<a href="signup.php" class="text-primary">Criar conta</a>
</div>
