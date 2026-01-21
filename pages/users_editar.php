<?php
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";

if ($_SESSION['user']['perfil'] !== 'admin') {
    exit("Acesso negado.");
}

$id = (int)($_GET['id'] ?? 0);

$res = mysqli_query($conexao, "SELECT * FROM users WHERE id=$id");
$u = mysqli_fetch_assoc($res);

if (!$u) exit("Utilizador não encontrado.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $perfil = $_POST['perfil'];

    mysqli_query($conexao,
                 "UPDATE users SET perfil='$perfil' WHERE id=$id"
    );

    header("Location: index.php?bb=users_listar");
    exit;
}
?>

<h3>Editar Utilizador</h3>

<p><strong><?= $u['nome'] ?></strong> (<?= $u['email'] ?>)</p>

<form method="post">
<select name="perfil" class="form-control mb-3">
<option value="user" <?= $u['perfil']=='user'?'selected':'' ?>>User</option>
<option value="admin" <?= $u['perfil']=='admin'?'selected':'' ?>>Admin</option>
</select>

<button class="btn btn-primary">Guardar</button>
<a href="index.php?bb=users_listar" class="btn btn-secondary">Cancelar</a>
</form>
