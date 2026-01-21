<?php
require __DIR__ . "/../includes/auth.php";

if ($_SESSION['user']['perfil'] !== 'admin') {
    die("Acesso negado.");
}

require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?bb=trabalhadores_listar");
    exit;
}

$id = (int) $_GET['id'];

$stmt = mysqli_prepare(
    $conexao,
    "DELETE FROM trabalhadores WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

header("Location: index.php?bb=trabalhadores_listar");
exit;
