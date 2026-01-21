<?php
require __DIR__ . "/config.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$erro = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $stmt = mysqli_prepare($conexao, "SELECT * FROM users WHERE email=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $u = mysqli_fetch_assoc($res);

    if (!$u || !password_verify($senha, $u['pass_hash'])) {
        $erro = "Credenciais inválidas.";
    } else {
        $_SESSION['user'] = [
            'id' => (int)$u['id'],
            'nome' => $u['nome'],
            'email' => $u['email'],
            'perfil' => $u['perfil'],
        ];
        header("Location: index.php?bb=home");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Login - Gestão de Alunos</title>

<link rel="stylesheet" href="vendors/typicons.font/font/typicons.css">
<link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
<link rel="stylesheet" href="css/vertical-layout-light/style.css">
<link rel="shortcut icon" href="images/favicon.png">
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

<h4>Entrar</h4>
<h6 class="font-weight-light">Use as credenciais fornecidas.</h6>

<?php if ($erro): ?>
<div class="alert alert-danger">
<?= htmlspecialchars($erro) ?>
</div>
<?php endif; ?>

<form class="pt-3" method="post">

<div class="form-group">
<input
type="email"
class="form-control form-control-lg"
name="email"
placeholder="Email"
value="<?= htmlspecialchars($email) ?>"
required
>
</div>

<div class="form-group">
<input
type="password"
class="form-control form-control-lg"
name="senha"
placeholder="Palavra-passe"
required
>
</div>

<div class="mt-3">
<button
class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
type="submit">
ENTRAR
</button>
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

