<?php
require __DIR__ . "/../includes/auth.php";

if ($_SESSION['user']['perfil'] !== 'admin') {
    die("Acesso negado.");
}


require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = $_POST['nome'] ?? '';
    $sexo = $_POST['sexo'] ?? '';
    $localizacao = $_POST['localizacao'] ?? null;
    $estado_civil = $_POST['estado_civil'] ?? 'Solteiro';
    $numero = $_POST['numero'] ?? null;
    $endereco = $_POST['endereco'] ?? null;

    // FOTO
    $foto = null;

    if (!empty($_FILES['foto']['name'])) {

        $pasta = "uploads/trabalhadores/";

        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $permitidas)) {

            $nomeFoto = uniqid() . "." . $ext;
            move_uploaded_file(
                $_FILES['foto']['tmp_name'],
                $pasta . $nomeFoto
            );

            $foto = $pasta . $nomeFoto;
        }
    }

    $stmt = mysqli_prepare(
        $conexao,
        "INSERT INTO trabalhadores
        (nome, foto, sexo, localizacao, estado_civil, numero, endereco)
    VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sssssss",
        $nome,
        $foto,
        $sexo,
        $localizacao,
        $estado_civil,
        $numero,
        $endereco
    );

    mysqli_stmt_execute($stmt);

    header("Location: index.php?bb=trabalhadores_listar");
    exit;
}
?>

<h3>Novo Trabalhador</h3>

<form method="post" enctype="multipart/form-data">

<div class="form-group">
<label>Nome</label>
<input type="text" name="nome" class="form-control" required>
</div>

<div class="form-group">
<label>Foto</label>
<input type="file" name="foto" class="form-control">
</div>

<div class="form-group">
<label>Sexo</label>
<select name="sexo" class="form-control" required>
<option value="Masculino">Masculino</option>
<option value="Feminino">Feminino</option>
<option value="Outro">Outro</option>
</select>
</div>

<button class="btn btn-success">Guardar</button>

</form>
