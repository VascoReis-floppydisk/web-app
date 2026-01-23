<?php
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";

/* ADMIN ONLY */
if ($_SESSION['user']['perfil'] !== 'admin') {
    exit("Acesso negado.");
}

/* =========================
 *   VALIDATE ID
 * ========================= */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php?bb=trabalhadores_listar");
    exit;
}

$erro = '';

/* =========================
 *   FETCH TRABALHADOR
 * ========================= */
$stmt = mysqli_prepare($conexao, "SELECT * FROM trabalhadores WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$trab = mysqli_fetch_assoc($res);

if (!$trab) {
    die("Trabalhador não encontrado.");
}

/* =========================
 *   HANDLE FORM SUBMIT
 * ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $numero_trabalhador = trim($_POST['numero_trabalhador'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $residencia = trim($_POST['residencia'] ?? '');
    $estado_civil = $_POST['estado_civil'] ?? 'Solteiro';
    $genero = $_POST['genero'] ?? '';
    $naturalidade = trim($_POST['naturalidade'] ?? '');
    $data_nascimento = $_POST['data_nascimento'] ?: null;
    $data_admissao = $_POST['data_admissao'] ?: null;
    $data_demissao = $_POST['data_demissao'] ?: null;

    if ($nome === '' || $genero === '') {
        $erro = "Nome e Género são obrigatórios.";
    }

    /* FOTO */
    $foto = $trab['foto'];
    if (!$erro && !empty($_FILES['foto']['name'])) {

        $pasta = "uploads/trabalhadores/";
        if (!is_dir($pasta)) mkdir($pasta, 0777, true);

        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $permitidas)) {
            $erro = "Formato de imagem inválido.";
        } else {
            if (!empty($foto) && file_exists($foto)) unlink($foto);

            $nomeFoto = uniqid() . "." . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], $pasta . $nomeFoto);
            $foto = $pasta . $nomeFoto;
        }
    }

    if (!$erro) {
        $stmt = mysqli_prepare($conexao, "
        UPDATE trabalhadores SET
        nome=?,
        numero_trabalhador=?,
        telefone=?,
        residencia=?,
        estado_civil=?,
        genero=?,
        naturalidade=?,
        data_nascimento=?,
        data_admissao=?,
        data_demissao=?,
        foto=?
        WHERE id=?
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "sssssssssssi",
            $nome,
            $numero_trabalhador,
            $telefone,
            $residencia,
            $estado_civil,
            $genero,
            $naturalidade,
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
?>

<h3>Editar Trabalhador</h3>

<?php if ($erro): ?>
<div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="card p-4">

<div class="mb-3">
<label>Nome</label>
<input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($trab['nome']) ?>" required>
</div>

<div class="mb-3">
<label>Foto</label><br>
<?php if (!empty($trab['foto'])): ?>
<img src="<?= htmlspecialchars($trab['foto']) ?>" style="max-width:150px;border-radius:10px;margin-bottom:10px;">
<?php endif; ?>
<input type="file" name="foto" class="form-control">
</div>

<div class="row">
<div class="col-md-6 mb-3">
<label>Nº Trabalhador</label>
<input type="text" name="numero_trabalhador" class="form-control" value="<?= htmlspecialchars($trab['numero_trabalhador']) ?>">
</div>

<div class="col-md-6 mb-3">
<label>Telefone</label>
<input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($trab['telefone']) ?>">
</div>
</div>

<div class="mb-3">
<label>Residência</label>
<input type="text" name="residencia" class="form-control" value="<?= htmlspecialchars($trab['residencia']) ?>">
</div>

<div class="row">
<div class="col-md-4 mb-3">
<label>Estado Civil</label>
<select name="estado_civil" class="form-control">
<?php foreach (['Solteiro','Casado','Divorciado','Viúvo'] as $e): ?>
<option value="<?= $e ?>" <?= $trab['estado_civil']==$e?'selected':'' ?>><?= $e ?></option>
<?php endforeach; ?>
</select>
</div>

<div class="col-md-4 mb-3">
<label>Género</label>
<select name="genero" class="form-control" required>
<option value="Masculino" <?= $trab['genero']=='Masculino'?'selected':'' ?>>Masculino</option>
<option value="Feminino" <?= $trab['genero']=='Feminino'?'selected':'' ?>>Feminino</option>
<option value="Outro" <?= $trab['genero']=='Outro'?'selected':'' ?>>Outro</option>
</select>
</div>

<div class="col-md-4 mb-3">
<label>Naturalidade</label>
<input type="text" name="naturalidade" class="form-control" value="<?= htmlspecialchars($trab['naturalidade']) ?>">
</div>
</div>

<div class="row">
<div class="col-md-4 mb-3">
<label>Data de Nascimento</label>
<input type="date" name="data_nascimento" class="form-control" value="<?= $trab['data_nascimento'] ?>">
</div>

<div class="col-md-4 mb-3">
<label>Data de Admissão</label>
<input type="date" name="data_admissao" class="form-control" value="<?= $trab['data_admissao'] ?>">
</div>

<div class="col-md-4 mb-3">
<label>Data de Demissão</label>
<input type="date" name="data_demissao" class="form-control" value="<?= $trab['data_demissao'] ?>">
</div>
</div>

<button class="btn btn-success">Atualizar</button>
<a href="index.php?bb=trabalhadores_listar" class="btn btn-secondary">Cancelar</a>

</form>
