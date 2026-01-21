<?php
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";

if ($_SESSION['user']['perfil'] !== 'admin') {
    exit("Acesso negado.");
}

$id = (int)($_GET['id'] ?? 0);

if ($id && $id != $_SESSION['user']['id']) {
    mysqli_query($conexao, "DELETE FROM users WHERE id=$id");
}

header("Location: index.php?bb=users_listar");
exit;
