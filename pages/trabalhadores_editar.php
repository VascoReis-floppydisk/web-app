<?php
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";
require __DIR__ . "/../includes/permissions.php";

if (!is_admin()) {
    die("Acesso negado.");
}

/* ================= VALIDATE ID ================= */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php?bb=trabalhadores_listar");
    exit;
}

$erro = '';

/* ================= FETCH TRABALHADOR ================= */
$stmt = mysqli_prepare($conexao, "SELECT * FROM trabalhadores WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$trab = mysqli_fetch_assoc($res);

if (!$trab) {
    die("Trabalhador não encontrado.");
}

/* ================= HANDLE FORM ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $numero_trabalhador = trim($_POST['numero_trabalhador']);
    $nome               = trim($_POST['nome']);
    $telefone           = trim($_POST['telefone']);
    $residencia         = trim($_POST['residencia']);
    $estado_civil       = $_POST['estado_civil'];
    $sexo               = $_POST['sexo'];
    $naturalidade       = trim($_POST['naturalidade']);
    $endereco           = trim($_POST['endereco']);
    $data_nascimento    = $_POST['data_nascimento'] ?: null;
    $data_admissao      = $_POST['data_admissao'] ?: null;
    $data_demissao      = $_POST['data_demissao'] ?: null;

    if ($numero_trabalhador === '' || $nome === '') {
        $erro = "Número do trabalhador e Nome são obrigatórios.";
    }

    /* FOTO */
    $foto = $trab['foto'];

    if (!$erro && !empty($_FILES['foto']['name'])) {

        $uploadDir = __DIR__ . "/../uploads/trabalhadores/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!is_writable($uploadDir)) {
            $erro = "A pasta de uploads não tem permissão de escrita.";
        }

        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg','jpeg','png','webp'];

        if (!in_array($ext, $permitidas)) {
            $erro = "Formato de imagem inválido.";
        } else {

            // Delete old photo
            if (!empty($foto) && file_exists(__DIR__ . "/../" . $foto)) {
                unlink(__DIR__ . "/../" . $foto);
            }

            $novoNome = uniqid("trab_") . "." . $ext;
            $destino = $uploadDir . $novoNome;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
                $foto = "uploads/trabalhadores/" . $novoNome; // path saved in DB
            } else {
                $erro = "Erro ao salvar a imagem.";
            }
        }
    }

    if ($erro === '') {
        $stmt = mysqli_prepare($conexao, "
        UPDATE trabalhadores SET
        numero_trabalhador=?,
        nome=?,
        telefone=?,
        residencia=?,
        estado_civil=?,
        sexo=?,
        naturalidade=?,
        endereco=?,
        data_nascimento=?,
        data_admissao=?,
        data_demissao=?,
        foto=?
        WHERE id=?
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssssssi",
            $numero_trabalhador,
            $nome,
            $telefone,
            $residencia,
            $estado_civil,
            $sexo,
            $naturalidade,
            $endereco,
            $data_nascimento,
            $data_admissao,
            $data_demissao,
            $foto,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?bb=trabalhadores_listar");
            exit;
        } else {
            $erro = "Erro ao atualizar trabalhador.";
        }
    }
}

function e($v){ return htmlspecialchars($v ?? ''); }
?>

<h3>Editar Trabalhador</h3>

<?php if ($erro): ?>
<div class="alert alert-danger"><?= e($erro) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">

<div class="row">

<div class="col-md-4 mb-3">
<label>Nº Trabalhador *</label>
<input type="text" name="numero_trabalhador" class="form-control"
value="<?= e($trab['numero_trabalhador']) ?>" required>
</div>

<div class="col-md-8 mb-3">
<label>Nome *</label>
<input type="text" name="nome" class="form-control"
value="<?= e($trab['nome']) ?>" required>
</div>

<div class="col-md-4 mb-3">
<label>Telefone</label>
<input type="text" name="telefone" class="form-control"
value="<?= e($trab['telefone']) ?>">
</div>

<div class="col-md-8 mb-3">
<label>Residência</label>
<input type="text" name="residencia" class="form-control"
value="<?= e($trab['residencia']) ?>">
</div>

<div class="col-md-4 mb-3">
<label>Estado Civil</label>
<select name="estado_civil" class="form-control">
<?php foreach (['Solteiro','Casado','Divorciado','Viúvo'] as $e): ?>
<option value="<?= $e ?>" <?= $trab['estado_civil']==$e?'selected':'' ?>><?= $e ?></option>
<?php endforeach; ?>
</select>
</div>

<div class="col-md-4 mb-3">
<label>Sexo</label>
<select name="sexo" class="form-control">
<?php foreach (['Masculino','Feminino','Outro'] as $s): ?>
<option value="<?= $s ?>" <?= $trab['sexo']==$s?'selected':'' ?>><?= $s ?></option>
<?php endforeach; ?>
</select>
</div>

<div class="col-md-4 mb-3">
<label>Naturalidade</label>
<input type="text" name="naturalidade" class="form-control"
value="<?= e($trab['naturalidade']) ?>">
</div>

<div class="col-md-4 mb-3">
<label>Data de Nascimento</label>
<input type="date" name="data_nascimento" class="form-control"
value="<?= e($trab['data_nascimento']) ?>">
</div>

<div class="col-md-4 mb-3">
<label>Data de Admissão</label>
<input type="date" name="data_admissao" class="form-control"
value="<?= e($trab['data_admissao']) ?>">
</div>

<div class="col-md-4 mb-3">
<label>Data de Demissão</label>
<input type="date" name="data_demissao" class="form-control"
value="<?= e($trab['data_demissao']) ?>">
</div>

<div class="col-md-8 mb-3">
<label>Endereço</label>
<input type="text" name="endereco" class="form-control"
value="<?= e($trab['endereco']) ?>">
</div>

<div class="col-md-4 mb-3">
<label>Foto</label>
<input type="file" name="foto" class="form-control" accept="image/*">
</div>

</div>

<button class="btn btn-success">Atualizar</button>
<a href="index.php?bb=trabalhadores_listar" class="btn btn-secondary">Cancelar</a>

</form>

