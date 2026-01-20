<?php
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";

$result = mysqli_query(
    $conexao,
    "SELECT * FROM trabalhadores ORDER BY id DESC"
);
?>

<h3>Trabalhadores</h3>

<a href="index.php?bb=trabalhadores_novo" class="btn btn-primary mb-3">
Novo Trabalhador
</a>

<table class="table table-bordered">
<tr>
<th>Foto</th>
<th>Nome</th>
<th>Sexo</th>
<th>Ações</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>

    <td>
    <?php if (!empty($row['foto'])) { ?>
        <img src="<?= htmlspecialchars($row['foto']) ?>"
        width="60" height="60"
        style="border-radius:50%; object-fit:cover;">
        <?php } else { ?>
            <img src="images/avatar-default.png"
            width="60" height="60"
            style="border-radius:50%; object-fit:cover;">
            <?php } ?>
            </td>

            <td><?= htmlspecialchars($row['nome']) ?></td>
            <td><?= htmlspecialchars($row['sexo']) ?></td>

            <td>
            <a class="btn btn-sm btn-warning"
            href="index.php?bb=trabalhadores_editar&id=<?= $row['id'] ?>">
            Editar
            </a>

            <a class="btn btn-sm btn-danger"
            onclick="return confirm('Apagar trabalhador?')"
            href="index.php?bb=trabalhadores_excluir&id=<?= $row['id'] ?>">
            Apagar
            </a>
            </td>

            </tr>
            <?php } ?>
            </table>

