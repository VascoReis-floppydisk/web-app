<?php
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";

/* =========================
 *   SESSION & PERMISSIONS
 *   ========================= */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isAdmin = (($_SESSION['user']['perfil'] ?? '') === 'admin');

/* =========================
 *   QUERY (ALIGNED WITH DB)
 *   ========================= */
$sql = "
SELECT
id,
nome,
numero_trabalhador,
telefone,
residencia,
estado_civil,
genero,
data_nascimento,
data_admissao AS admissao,
data_demissao AS demissao,
naturalidade,
foto
FROM trabalhadores
ORDER BY nome
";

$result = mysqli_query($conexao, $sql);
if (!$result) {
    die('Erro SQL: ' . mysqli_error($conexao));
}
?>

<h3 class="mb-4">Trabalhadores</h3>

<!-- ADD BUTTON (ADMIN ONLY) -->
<?php if ($isAdmin): ?>
<div class="mb-3">
<a href="index.php?bb=trabalhadores_novo" class="btn btn-success">
+ Novo Trabalhador
</a>
</div>
<?php endif; ?>

<div class="table-responsive">
<table class="table table-bordered table-striped align-middle">
<thead class="table-light">
<tr>
<th>Foto</th>
<th>Nome</th>
<th>Nº</th>
<th>Telefone</th>
<th>Residência</th>
<th>Estado Civil</th>
<th>Género</th>
<th>Naturalidade</th>
<th>Admissão</th>
<th>Demissão</th>
<th>Ações</th>
</tr>
</thead>

<tbody>
<?php if (mysqli_num_rows($result) === 0): ?>
<tr>
<td colspan="11" class="text-center">
Nenhum trabalhador encontrado.
</td>
</tr>
<?php endif; ?>

<?php while ($t = mysqli_fetch_assoc($result)): ?>
<tr>

<!-- FOTO -->
<td style="width:90px">
<?php
$foto = $t['foto'] ?? '';
if ($foto !== '' && file_exists($foto)):
    ?>
    <img src="<?= htmlspecialchars($foto) ?>"
    alt="Foto"
    style="width:70px;height:90px;object-fit:cover;border-radius:4px">
    <?php else: ?>
    <span class="text-muted">—</span>
    <?php endif; ?>
    </td>

    <!-- INFO -->
    <td><?= htmlspecialchars($t['nome']) ?></td>
    <td><?= htmlspecialchars($t['numero_trabalhador']) ?></td>
    <td><?= htmlspecialchars($t['telefone']) ?></td>
    <td><?= htmlspecialchars($t['residencia']) ?></td>
    <td><?= htmlspecialchars($t['estado_civil']) ?></td>
    <td><?= htmlspecialchars($t['genero']) ?></td>
    <td><?= htmlspecialchars($t['naturalidade']) ?></td>
    <td><?= htmlspecialchars($t['admissao'] ?? '-') ?></td>
    <td><?= htmlspecialchars($t['demissao'] ?? '-') ?></td>

    <!-- ACTIONS -->
    <td style="white-space:nowrap">

    <!-- PDF CARD (ADMIN ONLY) -->
    <?php if ($isAdmin): ?>
    <a href="trabalhadores_card.php?id=<?= $t['id'] ?>"
    class="btn btn-sm btn-info mb-1">
    Cartão PDF
    </a>
    <?php endif; ?>

    <!-- EDIT / DELETE (ADMIN ONLY) -->
    <?php if ($isAdmin): ?>
    <a href="index.php?bb=trabalhadores_editar&id=<?= $t['id'] ?>"
    class="btn btn-sm btn-warning mb-1">
    Editar
    </a>

    <a href="index.php?bb=trabalhadores_excluir&id=<?= $t['id'] ?>"
    class="btn btn-sm btn-danger mb-1"
    onclick="return confirm('Deseja apagar este trabalhador?')">
    Apagar
    </a>
    <?php else: ?>
    <span class="text-muted">—</span>
    <?php endif; ?>

    </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
    </table>
    </div>



