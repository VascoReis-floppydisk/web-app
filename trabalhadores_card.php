<?php
require __DIR__ . "/config.php";
require __DIR__ . "/includes/auth.php";

/* ADMIN ONLY */
if ($_SESSION['user']['perfil'] !== 'admin') {
    die("Acesso negado.");
}

/* DOMPDF */
require __DIR__ . "/lib/dompdf/autoload.inc.php";
use Dompdf\Dompdf;

/* VALIDATE ID */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}

$id = (int) $_GET['id'];

/* QUERY */
$stmt = mysqli_prepare(
    $conexao,
    "SELECT
    nome,
    numero_trabalhador,
    telefone,
    residencia,
    estado_civil,
    genero,
    data_nascimento,
    data_admissao,
    data_demissao,
    naturalidade,
    foto
    FROM trabalhadores
    WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$t = mysqli_fetch_assoc($result);

if (!$t) {
    die("Trabalhador não encontrado.");
}

/* IMAGE → BASE64 (DOMPDF SAFE) */
$fotoHtml = '<div class="photo empty"></div>';
if (!empty($t['foto']) && file_exists($t['foto'])) {
    $imgData = base64_encode(file_get_contents($t['foto']));
    $fotoHtml = '<img src="data:image/jpeg;base64,' . $imgData . '" class="photo">';
}

/* HTML */
$html = '
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">

<style>
@page {
size: A4;
margin: 30px;
}

html, body {
margin: 0;
padding: 5;
font-family: DejaVu Sans, sans-serif;
font-size: 14px;
}

.page {
width: auto;
height: auto;              /* ← KEY FIX */
min-height: auto;          /* ← SAFE */
border: 3px solid #000;
box-sizing: border-box;
padding: 25px;
}

.header {
display: table;
width: 100%;
margin-bottom: 20px;
}

.header-left {
display: table-cell;
width: 140px;
vertical-align: top;
}

.header-right {
display: table-cell;
vertical-align: top;
padding-left: 20px;
}

.photo {
width: 120px;
height: 150px;
border: 1px solid #000;
object-fit: cover;
}

.photo.empty {
background: #f2f2f2;
}

p {
margin: 6px 0;
}

.section {
margin-top: 15px;
}
</style>

</head>
<body>

<div class="page">

<div class="header">
<div class="header-left">
' . $fotoHtml . '
</div>

<div class="header-right">
<p><strong>Nome:</strong> ' . htmlspecialchars($t['nome']) . '</p>
<p><strong>Nº Trabalhador:</strong> ' . htmlspecialchars($t['numero_trabalhador']) . '</p>
<p><strong>Telefone:</strong> ' . htmlspecialchars($t['telefone']) . '</p>
<p><strong>Naturalidade:</strong> ' . htmlspecialchars($t['naturalidade']) . '</p>
</div>
</div>

<div class="section">
<p><strong>Residência:</strong> ' . htmlspecialchars($t['residencia']) . '</p>
<p><strong>Estado Civil:</strong> ' . htmlspecialchars($t['estado_civil']) . '</p>
<p><strong>Género:</strong> ' . htmlspecialchars($t['genero']) . '</p>
<p><strong>Data de Nascimento:</strong> ' . $t['data_nascimento'] . '</p>
<p><strong>Data de Admissão:</strong> ' . $t['data_admissao'] . '</p>
<p><strong>Data de Demissão:</strong> ' . ($t['data_demissao'] ?: '-') . '</p>
</div>

</div>

</body>
</html>
';

/* DOMPDF */
$dompdf = new Dompdf([
    'isRemoteEnabled' => true
]);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

/* FORCE DOWNLOAD */
$dompdf->stream(
    "cartao_trabalhador_" . $id . ".pdf",
    ["Attachment" => true]
);
