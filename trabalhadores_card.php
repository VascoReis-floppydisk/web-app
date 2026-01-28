<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . "/lib/dompdf/autoload.inc.php";
require __DIR__ . "/config.php";
require __DIR__ . "/includes/auth.php";

use Dompdf\Dompdf;
use Dompdf\Options;

/* ================= FUNÇÃO ESCAPE ================= */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/* ================= VALIDAR ID ================= */
$trabalhador_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$trabalhador_id) {
    die("ID inválido.");
}

/* ================= BUSCAR DADOS ================= */
$stmt = mysqli_prepare($conexao, "SELECT * FROM trabalhadores WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $trabalhador_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$trabalhador = mysqli_fetch_assoc($result);

if (!$trabalhador) {
    die("Trabalhador não encontrado.");
}

/* ================= FOTO BASE64 ================= */
$fotoHtml = '<div style="width:140px;height:140px;background:#eee;text-align:center;line-height:140px;border-radius:10px;">Sem Foto</div>';

if (!empty($trabalhador['foto'])) {
    $relativePath = ltrim($trabalhador['foto'], '/');
    $fotoPath = __DIR__ . '/' . $relativePath;

    if (file_exists($fotoPath)) {
        $imageData = base64_encode(file_get_contents($fotoPath));
        $imageType = mime_content_type($fotoPath);
        $fotoHtml = '<img src="data:' . $imageType . ';base64,' . $imageData . '" style="width:140px;height:140px;object-fit:cover;border-radius:10px;">';
    }
}

/* ================= FUNÇÃO DATA SEGURA ================= */
function data_br($data) {
    return !empty($data) ? date('d/m/Y', strtotime($data)) : '-';
}

/* ================= CAPTURAR HTML ================= */
ob_start();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: DejaVu Sans, sans-serif; color:#2c3e50; }

.page { width: 100%; border: 1px solid #ddd; }

.header { background: #667eea; color: white; padding: 20px; }

.header-table { width: 100%; }

.header-title { font-size: 22px; font-weight: bold; }

.section { padding: 20px; }

.section-title {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 10px;
    border-bottom: 2px solid #667eea;
    padding-bottom: 5px;
}

.table { width: 100%; border-collapse: collapse; }

.table td {
    padding: 8px;
    border-bottom: 1px solid #eee;
    font-size: 12px;
}

.label { font-weight: bold; color: #555; width: 40%; }

.footer {
    font-size: 10px;
    text-align: center;
    padding: 10px;
    background: #f2f2f2;
    margin-top: 20px;
}
</style>
</head>
<body>

<div class="page">

<!-- HEADER -->
<div class="header">
<table class="header-table">
<tr>
<td width="160"><?= $fotoHtml ?></td>
<td>
<div class="header-title"><?= e($trabalhador['nome']) ?></div>
<div>Cartão de Identificação do Trabalhador</div>
<br>
<strong>Nº Trabalhador:</strong> <?= e($trabalhador['numero_trabalhador']) ?><br>
<strong>Admissão:</strong> <?= data_br($trabalhador['data_admissao']) ?>
</td>
</tr>
</table>
</div>

<!-- DADOS PESSOAIS -->
<div class="section">
<div class="section-title">Dados Pessoais</div>
<table class="table">
<tr><td class="label">Nome</td><td><?= e($trabalhador['nome']) ?></td></tr>
<tr><td class="label">Sexo</td><td><?= e($trabalhador['sexo'] ?? '-') ?></td></tr>
<tr><td class="label">Nascimento</td><td><?= data_br($trabalhador['data_nascimento'] ?? null) ?></td></tr>
<tr><td class="label">Naturalidade</td><td><?= e($trabalhador['naturalidade'] ?? '-') ?></td></tr>
<tr><td class="label">Estado Civil</td><td><?= e($trabalhador['estado_civil'] ?? '-') ?></td></tr>
<tr><td class="label">Telefone</td><td><?= e($trabalhador['telefone'] ?? '-') ?></td></tr>
</table>
</div>

<!-- DADOS ADMINISTRATIVOS -->
<div class="section">
<div class="section-title">Dados Administrativos</div>
<table class="table">
<tr><td class="label">Data de Admissão</td><td><?= data_br($trabalhador['data_admissao']) ?></td></tr>
<tr><td class="label">Data de Demissão</td><td><?= !empty($trabalhador['data_demissao']) ? data_br($trabalhador['data_demissao']) : 'Ativo' ?></td></tr>
<tr><td class="label">Nº Trabalhador</td><td><?= e($trabalhador['numero_trabalhador']) ?></td></tr>
</table>
</div>

<div class="footer">
Documento Confidencial • Recursos Humanos • Gerado em <?= date('d/m/Y H:i') ?>
</div>

</div>

</body>
</html>

<?php
$html = ob_get_clean();

/* ================= GERAR PDF ================= */
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

/* ================= DOWNLOAD ================= */
$filename = "cartao_" . preg_replace('/\s+/', '_', $trabalhador['nome']) . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit;

