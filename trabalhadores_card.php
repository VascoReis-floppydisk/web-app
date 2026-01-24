<?php
// =================================================================
// PASSO 1: INCLUIR FICHEIROS DE CONFIGURAÇÃO E FUNÇÕES
// =================================================================
require __DIR__ . "/config.php";
require __DIR__ . "/includes/auth.php"; // Para segurança, se necessário

// =================================================================
// PASSO 2: DEFINIR A FUNÇÃO 'e()' QUE ESTÁ A FALTAR
// =================================================================
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

// =================================================================
// PASSO 3: OBTER E VALIDAR O ID DO TRABALHADOR
// =================================================================
$trabalhador_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$trabalhador_id) {
    die("ID do trabalhador inválido ou não fornecido.");
}

// =================================================================
// PASSO 4: BUSCAR OS DADOS DO TRABALHADOR NA BASE DE DADOS
// =================================================================
$stmt = mysqli_prepare($conexao, "SELECT * FROM trabalhadores WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $trabalhador_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$trabalhador = mysqli_fetch_assoc($result);

if (!$trabalhador) {
    die("Trabalhador não encontrado.");
}

// =================================================================
// PASSO 5: CRIAR A VARIÁVEL '$fotoHtml' QUE ESTÁ A FALTAR
// =================================================================
$fotoHtml = '<div class="photo-placeholder">Sem foto</div>'; // Valor padrão
$fotoPath = __DIR__ . "/" . ($trabalhador['foto'] ?? '');

if (!empty($trabalhador['foto']) && file_exists($fotoPath)) {
    // Converte a imagem para base64 para embutir no HTML/PDF
    $imageData = base64_encode(file_get_contents($fotoPath));
    $imageType = mime_content_type($fotoPath);
    $fotoHtml = '<img src="data:' . $imageType . ';base64,' . $imageData . '">';
}

// =================================================================
// PASSO 6: INCLUIR O HTML DO CARTÃO (O SEU CÓDIGO ORIGINAL)
// =================================================================
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cartão do Trabalhador</title>
    <style>
        /* Estilos básicos para o cartão. Podem ser melhorados. */
        body { font-family: sans-serif; margin: 0; }
        .page { border: 1px solid #ccc; width: 800px; margin: 20px auto; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { display: flex; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header-left { width: 150px; height: 150px; border: 1px solid #eee; }
        .header-left img { width: 100%; height: 100%; object-fit: cover; }
        .header-right { flex-grow: 1; padding-left: 20px; }
        .title { font-size: 24px; font-weight: bold; }
        .subtitle { font-size: 16px; color: #555; }
        .section { margin-top: 20px; }
        .section-title { font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 20px; }
        .label { font-size: 12px; color: #777; }
        .value { font-size: 16px; }
        .footer { text-align: center; font-size: 12px; color: #999; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

<div class="page">

    <div class="header">
        <div class="header-left">
            <?= $fotoHtml ?>
        </div>

        <div class="header-right">
            <div class="title">Cartão do Trabalhador</div>
            <div class="subtitle">Documento interno • Recursos Humanos</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Dados Pessoais</div>

        <div class="grid">
            <div class="cell">
                <div class="label">Nome</div>
                <div class="value"><?= e($trabalhador['nome']) ?></div>
            </div>
            <div class="cell">
                <div class="label">Estado Civil</div>
                <div class="value"><?= e($trabalhador['estado_civil']) ?></div>
            </div>
            <div class="cell">
                <div class="label">Nº do Trabalhador</div>
                <div class="value"><?= e($trabalhador['numero_trabalhador']) ?></div>
            </div>
            <div class="cell">
                <div class="label">Género</div>
                <div class="value"><?= e($trabalhador['genero']) ?></div>
            </div>
            <div class="cell">
                <div class="label">Telefone</div>
                <div class="value"><?= e($trabalhador['telefone']) ?></div>
            </div>
            <div class="cell">
                <div class="label">Naturalidade</div>
                <div class="value"><?= e($trabalhador['naturalidade']) ?></div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Dados Administrativos</div>

        <div class="grid">
            <div class="cell">
                <div class="label">Data de Nascimento</div>
                <div class="value"><?= e($trabalhador['data_nascimento']) ?></div>
            </div>
            <div class="cell">
                <div class="label">Data de Admissão</div>
                <div class="value"><?= e($trabalhador['data_admissao']) ?></div>
            </div>
            <div class="cell">
                <div class="label">Data de Demissão</div>
                <div class="value"><?= e($trabalhador['data_demissao'] ?: '-') ?></div>
            </div>
        </div>
    </div>

    <div class="footer">
        Documento gerado em <?= date('d/m/Y') ?>
    </div>

</div>

</body>
</html>
