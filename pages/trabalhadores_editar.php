<?php
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";

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
$stmt = mysqli_prepare(
    $conexao,
    "SELECT * FROM trabalhadores WHERE id = ?"
);
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
    $sexo = $_POST['sexo'] ?? '';
    $localizacao = trim($_POST['localizacao'] ?? '');
    $estado_civil = $_POST['estado_civil'] ?? 'Solteiro';
    $numero = trim($_POST['numero'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');

    if ($nome === '' || $sexo === '') {
        $erro = "Nome e Sexo são obrigatórios.";
    } else {

        /* =========================
         *           FOTO (KEEP OLD BY DEFAULT)
         *        ========================= */
        $foto = $trab['foto'];

        if (!empty($_FILES['foto']['name'])) {

            $pasta = "uploads/trabalhadores/";

            if (!is_dir($pasta)) {
                mkdir($pasta, 0777, true);
            }

            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $permitidas)) {
                $erro = "Formato de imagem inválido.";
            } else {

                // Remove old photo
                if (!empty($foto) && file_exists($foto)) {
                    unlink($foto);
                }

                $nomeFoto = uniqid() . "." . $ext;
                move_uploaded_file(
                    $_FILES['foto']['tmp_name'],
                    $pasta . $nomeFoto
                );

                $foto = $pasta . $nomeFoto;
            }
        }

        if ($erro === '') {
            $stmt = mysqli_prepare(
                $conexao,
                "UPDATE trabalhadores
                SET nome=?, foto=?, sexo=?, localizacao=?, estado_civil=?, numero=?, endereco=?
                WHERE id=?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssssssi",
                $nome,
                $foto,
                $sexo,
                $localizacao,
                $estado_civil,
                $numero,
                $endereco,
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
}
?>

<h3>Editar Trabalhador</h3>

<?php if ($erro): ?>
<div class="alert alert-danger">
<?= htmlspecialchars($erro) ?>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">

<div class="form-group">
<label>Nome</label>
<input type="text" name="nome" class="form-control"
value="<?= htmlspecialchars($trab['nome']) ?>" required>
</div>

<div class="form-group">
<label>Foto</label><br>

<?php if (!empty($trab['foto'])): ?>
<img src="<?= htmlspecialchars($trab['foto']) ?>"
style="max-width:150px; border-radius:10px; margin-bottom:10px;">
<?php endif; ?>

<input type="file" name="foto" class="form-control">
</div>

<div class="form-group">
<label>Sexo</label>
<select name="sexo" class="form-control" required>
<option value="Masculino" <?= $trab['sexo']=='Masculino'?'selected':'' ?>>Masculino</option>
<option value="Feminino" <?= $trab['sexo']=='Feminino'?'selected':'' ?>>Feminino</option>
<option value="Outro" <?= $trab['sexo']=='Outro'?'selected':'' ?>>Outro</option>
</select>
</div>

<div class="form-group">
<label>Localização</label>
<input type="text" name="localizacao" class="form-control"
value="<?= htmlspecialchars($trab['localizacao']) ?>">
</div>

<div class="form-group">
<label>Estado Civil</label>
<select name="estado_civil" class="form-control">
<?php
$estados = ['Solteiro','Casado','Divorciado','Viúvo'];
foreach ($estados as $e):
    ?>
    <option value="<?= $e ?>" <?= $trab['estado_civil']==$e?'selected':'' ?>>
    <?= $e ?>
    </option>
    <?php endforeach; ?>
    </select>
    </div>

    <div class="form-group">
    <label>Número</label>
    <input type="text" name="numero" class="form-control"
    value="<?= htmlspecialchars($trab['numero']) ?>">
    </div>

    <div class="form-group">
    <label>Endereço</label>
    <input type="text" name="endereco" class="form-control"
    value="<?= htmlspecialchars($trab['endereco']) ?>">
    </div>

    <button class="btn btn-success">Atualizar</button>
    <a href="index.php?bb=trabalhadores_listar" class="btn btn-secondary">Cancelar</a>

    </form>

