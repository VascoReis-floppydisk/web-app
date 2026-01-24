<?php
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";
require __DIR__ . "/../includes/permissions.php";

/* =========================
 *   ADMIN ONLY
 * ========================= */
if (!is_admin()) {
    die("Acesso negado.");
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $numero_trabalhador = trim($_POST['numero_trabalhador']);
    $nome               = trim($_POST['nome']);
    $telefone           = trim($_POST['telefone']);
    $residencia         = trim($_POST['residencia']);
    $estado_civil       = $_POST['estado_civil'];
    $genero             = $_POST['genero'];
    $data_nascimento    = $_POST['data_nascimento'] ?: null;
    $data_admissao      = $_POST['data_admissao'] ?: null;
    $data_demissao      = $_POST['data_demissao'] ?: null;
    $naturalidade       = trim($_POST['naturalidade']);

    /* =========================
     *       FOTO UPLOAD (FIXED)
     *    ========================= */
    $foto = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {

        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $erro = "Erro no upload da imagem.";
        } else {

            $uploadDir = __DIR__ . "/../uploads/trabalhadores/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg','jpeg','png','webp'];

            if (!in_array($ext, $permitidas)) {
                $erro = "Formato inválido. Use JPG, PNG ou WEBP.";
            } else {

                $novoNome = uniqid("trab_", true) . "." . $ext;
                $destino = $uploadDir . $novoNome;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
                    // Path saved to DB (used by cards & PDF)
                    $foto = "uploads/trabalhadores/" . $novoNome;
                } else {
                    $erro = "Falha ao guardar a imagem no servidor.";
                }
            }
        }
    }

    /* =========================
     *       VALIDATION
     *    ========================= */
    if ($numero_trabalhador === '' || $nome === '') {
        $erro = "Número do trabalhador e Nome são obrigatórios.";
    }

    /* =========================
     *       INSERT
     *    ========================= */
    if ($erro === '') {
        
        mysqli_query($conexao, "SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");

        $stmt = mysqli_prepare($conexao, "
        INSERT INTO trabalhadores (
            numero_trabalhador,
            nome,
            telefone,
            residencia,
            estado_civil,
            genero,
            data_nascimento,
            data_admissao,
            data_demissao,
            naturalidade,
            foto
        )
        VALUES (?,?,?,?,?,?,?,?,?,?,?)
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "sssssssssss",
            $numero_trabalhador,
            $nome,
            $telefone,
            $residencia,
            $estado_civil,
            $genero,
            $data_nascimento,
            $data_admissao,
            $data_demissao,
            $naturalidade,
            $foto
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?bb=trabalhadores_listar");
            exit;
        } else {
            $erro = "Erro ao criar trabalhador: " . mysqli_error($conexao);
        }
    }
}
?>

<h3>Novo Trabalhador</h3>

<?php if ($erro): ?>
<div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">

<div class="row">

<div class="col-md-4 mb-3">
<label>Nº Trabalhador *</label>
<input type="text" name="numero_trabalhador" class="form-control" required>
</div>

<div class="col-md-8 mb-3">
<label>Nome *</label>
<input type="text" name="nome" class="form-control" required>
</div>

<div class="col-md-4 mb-3">
<label>Telefone</label>
<input type="text" name="telefone" class="form-control">
</div>

<div class="col-md-8 mb-3">
<label>Residência</label>
<input type="text" name="residencia" class="form-control">
</div>

<div class="col-md-4 mb-3">
<label>Estado Civil</label>
<select name="estado_civil" class="form-control">
<option value="Solteiro">Solteiro</option>
<option value="Casado">Casado</option>
<option value="Divorciado">Divorciado</option>
<option value="Viúvo">Viúvo</option>
</select>
</div>

<div class="col-md-4 mb-3">
<label>Género</label>
<select name="genero" class="form-control">
<option value="Masculino">Masculino</option>
<option value="Feminino">Feminino</option>
<option value="Outro">Outro</option>
</select>
</div>

<div class="col-md-4 mb-3">
<label>Naturalidade</label>
<input type="text" name="naturalidade" class="form-control">
</div>

<div class="col-md-4 mb-3">
<label>Data de Nascimento</label>
<input type="date" name="data_nascimento" class="form-control">
</div>

<div class="col-md-4 mb-3">
<label>Data de Admissão</label>
<input type="date" name="data_admissao" class="form-control">
</div>

<div class="col-md-4 mb-3">
<label>Data de Demissão</label>
<input type="date" name="data_demissao" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Foto</label>
<input type="file" name="foto" class="form-control" accept="image/*">
</div>

</div>

<button class="btn btn-success">Guardar</button>
<a href="index.php?bb=trabalhadores_listar" class="btn btn-secondary">Cancelar</a>

</form>
