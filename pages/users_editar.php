<?php
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";

/* ADMIN ONLY */
if ($_SESSION['user']['perfil'] !== 'admin') {
    exit("Acesso negado.");
}

/* VALIDATE ID */
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    exit("ID inválido.");
}

/* GET USER */
$stmt = mysqli_prepare($conexao, "SELECT id, nome, email, perfil FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$u = mysqli_fetch_assoc($result);

if (!$u) {
    exit("Utilizador não encontrado.");
}

/* UPDATE USER */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $perfil = $_POST['perfil'] === 'admin' ? 'admin' : 'user';

    $stmt = mysqli_prepare($conexao, "UPDATE users SET perfil = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $perfil, $id);
    mysqli_stmt_execute($stmt);

    header("Location: index.php?bb=users_listar");
    exit;
}
?>

<h3>Editar Utilizador</h3>

<div class="card p-3 mb-3">
<p class="mb-1"><strong>Nome:</strong> <?= htmlspecialchars($u['nome']) ?></p>
<p class="mb-0"><strong>Email:</strong> <?= htmlspecialchars($u['email']) ?></p>
</div>

<form method="post" class="card p-3" style="max-width:400px">
<label class="form-label">Perfil</label>
<select name="perfil" class="form-control mb-3">
<option value="user" <?= $u['perfil']=='user'?'selected':'' ?>>User</option>
<option value="admin" <?= $u['perfil']=='admin'?'selected':'' ?>>Admin</option>
</select>

<button class="btn btn-primary">Guardar</button>
<a href="index.php?bb=users_listar" class="btn btn-secondary">Cancelar</a>
</form>
