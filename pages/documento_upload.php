<?php
// Requisitos básicos e verificação de segurança
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";

// Apenas administradores podem aceder
if (($_SESSION['user']['perfil'] ?? '') !== 'admin') {
    die("Acesso negado.");
}

// 1. Obter os dados do URL de forma segura
$trabalhador_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$categoria = filter_input(INPUT_GET, 'categoria', FILTER_SANITIZE_SPECIAL_CHARS);

// Lista de categorias permitidas para segurança
$categorias_validas = ['contratos', 'identificacao', 'ferias', 'declaracoes', 'seguros', 'diversos'];

if (!$trabalhador_id || !$categoria || !in_array($categoria, $categorias_validas)) {
    die("Erro: Parâmetros inválidos.");
}

// 2. Buscar o nome do trabalhador para exibir no título
$sql_nome = "SELECT nome FROM trabalhadores WHERE id = ?";
$stmt = mysqli_prepare($conexao, $sql_nome);
mysqli_stmt_bind_param($stmt, "i", $trabalhador_id);
mysqli_stmt_execute($stmt);
$result_nome = mysqli_stmt_get_result($stmt);
$trabalhador = mysqli_fetch_assoc($result_nome);
$nome_trabalhador = $trabalhador ? htmlspecialchars($trabalhador['nome']) : 'Desconhecido';

// Formata o nome da categoria para exibição (ex: "identificacao" -> "Identificação")
$titulo_categoria = ucfirst(str_replace('_', ' ', $categoria));

// --- LÓGICA DE UPLOAD (será adicionada depois) ---
// --- LÓGICA DE UPLOAD ---
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Validar o ficheiro enviado
    if (isset($_FILES['documento_pdf']) && $_FILES['documento_pdf']['error'] === UPLOAD_ERR_OK) {
        
        $file = $_FILES['documento_pdf'];
        $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);

        // 2. Verificar se é mesmo um PDF
        $file_type = mime_content_type($file['tmp_name']);
        if ($file_type === 'application/pdf') {

            // 3. Criar um nome de ficheiro único e seguro
            $nome_original = basename($file['name']);
            $extensao = pathinfo($nome_original, PATHINFO_EXTENSION);
            $nome_seguro = uniqid('doc_', true) . '.' . $extensao;
            
            // 4. Definir o caminho para guardar o ficheiro
            // Vamos criar uma pasta "documentos" dentro da pasta "uploads"
            $pasta_destino = __DIR__ . '/../uploads/documentos/';
            if (!is_dir($pasta_destino)) {
                mkdir($pasta_destino, 0755, true); // Cria a pasta se não existir
            }
            $caminho_completo = $pasta_destino . $nome_seguro;

            // 5. Mover o ficheiro para o destino final
            if (move_uploaded_file($file['tmp_name'], $caminho_completo)) {
                
                // 6. Guardar a informação na base de dados
                $caminho_db = 'uploads/documentos/' . $nome_seguro; // Caminho relativo para o DB
                
                $sql_insert = "INSERT INTO documentos (trabalhador_id, categoria, descricao, caminho_ficheiro, nome_ficheiro) VALUES (?, ?, ?, ?, ?)";
                $stmt_insert = mysqli_prepare($conexao, $sql_insert);
                mysqli_stmt_bind_param($stmt_insert, "issss", $trabalhador_id, $categoria, $descricao, $caminho_db, $nome_original);
                
                if (mysqli_stmt_execute($stmt_insert)) {
                    $mensagem = '<div class="alert alert-success">Documento anexado com sucesso!</div>';
                } else {
                    $mensagem = '<div class="alert alert-danger">Erro ao guardar a informação na base de dados.</div>';
                    unlink($caminho_completo); // Apaga o ficheiro se o registo no DB falhar
                }

            } else {
                $mensagem = '<div class="alert alert-danger">Erro ao mover o ficheiro para o destino.</div>';
            }
        } else {
            $mensagem = '<div class="alert alert-danger">Erro: O ficheiro enviado não é um PDF válido.</div>';
        }
    } else {
        $mensagem = '<div class="alert alert-danger">Erro no upload do ficheiro. Tente novamente.</div>';
    }
}

?>

<!-- Título da Página -->
<h3 class="mb-2">Anexar Documento</h3>
<p class="text-muted">
    Trabalhador: <strong><?= $nome_trabalhador ?></strong>  

    Categoria: <strong><?= $titulo_categoria ?></strong>
</p>

<hr>

<!-- Mensagem de feedback (sucesso ou erro) -->
<?php if ($mensagem): ?>
    <?= $mensagem ?>
<?php endif; ?>

<!-- Formulário de Upload -->
<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="documento_pdf" class="form-label">Selecione o ficheiro PDF</label>
        <input class="form-control" type="file" id="documento_pdf" name="documento_pdf" accept=".pdf" required>
        <div class="form-text">Apenas ficheiros no formato .pdf são permitidos.</div>
    </div>

    <div class="mb-3">
        <label for="descricao" class="form-label">Descrição (Opcional)</label>
        <input type="text" class="form-control" id="descricao" name="descricao" placeholder="Ex: Contrato de trabalho inicial">
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="typcn typcn-upload-outline me-2"></i>
        Anexar Documento
    </button>
    
    <a href="index.php?bb=trabalhadores_documentos&id=<?= $trabalhador_id ?>" class="btn btn-secondary">
        Cancelar
    </a>
</form>

<hr>

<?php
// --- LÓGICA PARA BUSCAR DOCUMENTOS EXISTENTES ---
$sql_docs = "
    SELECT id, nome_ficheiro, caminho_ficheiro, descricao, data_upload
    FROM documentos
    WHERE trabalhador_id = ? AND categoria = ?
    ORDER BY data_upload DESC
";
$stmt_docs = mysqli_prepare($conexao, $sql_docs);
mysqli_stmt_bind_param($stmt_docs, "is", $trabalhador_id, $categoria);
mysqli_stmt_execute($stmt_docs);
$result_docs = mysqli_stmt_get_result($stmt_docs);
?>

<!-- Área para listar documentos já existentes -->
<div class="mt-5">
    <h5 class="mb-3">Documentos Anexados nesta Categoria</h5>

    <?php if (mysqli_num_rows($result_docs) > 0): ?>
        <ul class="list-group">
            <?php while ($doc = mysqli_fetch_assoc($result_docs)): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <a href="<?= htmlspecialchars($doc['caminho_ficheiro']) ?>" target="_blank" class="text-decoration-none">
                            <i class="typcn typcn-document-text me-2"></i>
                            <?= htmlspecialchars($doc['nome_ficheiro']) ?>
                        </a>
                        <?php if ($doc['descricao']): ?>
                            <small class="d-block text-muted mt-1"><?= htmlspecialchars($doc['descricao']) ?></small>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted">
                        <?= date('d/m/Y H:i', strtotime($doc['data_upload'])) ?>
                    </small>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <div class="text-muted">
            Nenhum documento anexado nesta categoria ainda.
        </div>
    <?php endif; ?>
</div>

