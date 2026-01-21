<?php
/* =========================
 *   BOOTSTRAP
 * ========================= */
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";

/* =========================
 *   SESSION SAFETY
 * ========================= */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================
 *   ADMIN ONLY
 * ========================= */
if (!isset($_SESSION['user']) || $_SESSION['user']['perfil'] !== 'admin') {
    http_response_code(403);
    ?>
    <h3>Acesso negado</h3>
    <p>Não tem permissões para apagar trabalhadores.</p>
    <?php
    exit;
}

/* =========================
 *   VALIDATE ID
 * ========================= */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?bb=trabalhadores_listar");
    exit;
}

$id = (int) $_GET['id'];

/* =========================
 *   DELETE
 * ========================= */
$stmt = mysqli_prepare(
    $conexao,
    "DELETE FROM trabalhadores WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

/* =========================
 *   REDIRECT
 * ========================= */
header("Location: index.php?bb=trabalhadores_listar");
exit;
