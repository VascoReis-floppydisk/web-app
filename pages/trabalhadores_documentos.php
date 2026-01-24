<?php
// Requisitos básicos e verificação de segurança
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";

// Apenas administradores podem aceder a esta página
if (($_SESSION['user']['perfil'] ?? '') !== 'admin') {
    die("Acesso negado. Esta área é restrita a administradores.");
}

// Obter o ID do trabalhador a partir do URL e garantir que é um número
$trabalhador_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$trabalhador_id) {
    die("ID do trabalhador inválido ou não fornecido.");
}

// Buscar o nome do trabalhador para exibir no título
$sql_nome = "SELECT nome FROM trabalhadores WHERE id = ?";
$stmt = mysqli_prepare($conexao, $sql_nome);
mysqli_stmt_bind_param($stmt, "i", $trabalhador_id);
mysqli_stmt_execute($stmt);
$result_nome = mysqli_stmt_get_result($stmt);
$trabalhador = mysqli_fetch_assoc($result_nome);
$nome_trabalhador = $trabalhador ? htmlspecialchars($trabalhador['nome']) : 'Trabalhador não encontrado';

?>

<!-- Título da Página -->
<h3 class="mb-4">Documentos de: <?= $nome_trabalhador ?></h3>
<p class="text-muted">Selecione uma categoria de documento para gerir. Cada categoria abrirá numa nova aba.</p>

<!-- Lista de Opções de Documentos (COM TARGET BLANK) -->
<div class="list-group">
    <a href="index.php?bb=documento_upload&id=<?= $trabalhador_id ?>&categoria=contratos" class="list-group-item list-group-item-action" target="_blank">
        <i class="typcn typcn-document-text me-2"></i>
        Contratos
    </a>
    <a href="index.php?bb=documento_upload&id=<?= $trabalhador_id ?>&categoria=identificacao" class="list-group-item list-group-item-action" target="_blank">
        <i class="typcn typcn-vcard me-2"></i>
        Documento de Identificação
    </a>
    <a href="index.php?bb=documento_upload&id=<?= $trabalhador_id ?>&categoria=ferias" class="list-group-item list-group-item-action" target="_blank">
        <i class="typcn typcn-plane-outline me-2"></i>
        Férias
    </a>
    <a href="index.php?bb=documento_upload&id=<?= $trabalhador_id ?>&categoria=declaracoes" class="list-group-item list-group-item-action" target="_blank">
        <i class="typcn typcn-document-add me-2"></i>
        Declarações
    </a>
    <a href="index.php?bb=documento_upload&id=<?= $trabalhador_id ?>&categoria=seguros" class="list-group-item list-group-item-action" target="_blank">
        <i class="typcn typcn-shield-outline me-2"></i>
        Seguros
    </a>
    <a href="index.php?bb=documento_upload&id=<?= $trabalhador_id ?>&categoria=diversos" class="list-group-item list-group-item-action" target="_blank">
        <i class="typcn typcn-folder-open me-2"></i>
        Diversos
    </a>
</div>

<!-- Botão para voltar à lista de trabalhadores -->
<div class="mt-4">
    <a href="index.php?bb=trabalhadores_listar" class="btn btn-secondary">
        &laquo; Voltar para a lista de trabalhadores
    </a>
</div>
