$id = intval($_GET['id'] ?? 0);
$erro = '';

$res = mysqli_query($conexao, "SELECT * FROM trabalhadores WHERE id=$id");
$trab = mysqli_fetch_assoc($res);

if (!$trab) die("Trabalhador não encontrado.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $sexo = $_POST['sexo'];
    $localizacao = trim($_POST['localizacao']);
    $estado_civil = $_POST['estado_civil'];
    $numero = trim($_POST['numero']);
    $endereco = trim($_POST['endereco']);

    $foto = $trab['foto'];
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
        $foto = $target_dir . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
    }

    if ($nome === '' || $sexo === '') {
        $erro = "Nome e Sexo são obrigatórios.";
    } else {
        $stmt = mysqli_prepare($conexao,
                               "UPDATE trabalhadores SET nome=?, foto=?, sexo=?, localizacao=?, estado_civil=?, numero=?, endereco=? WHERE id=?"
        );
        mysqli_stmt_bind_param($stmt, "sssssssi", $nome, $foto, $sexo, $localizacao, $estado_civil, $numero, $endereco, $id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?bb=trabalhadores_listar");
            exit;
        } else {
            $erro = "Erro ao atualizar.";
        }
    }
}
