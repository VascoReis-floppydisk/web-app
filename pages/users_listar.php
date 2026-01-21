<?php
require __DIR__ . "/../config.php";
require __DIR__ . "/../includes/auth.php";

if ($_SESSION['user']['perfil'] !== 'admin') {
    http_response_code(403);
    exit("Acesso negado.");
}

$res = mysqli_query($conexao, "
SELECT id, nome, email, perfil, created_at
FROM users
ORDER BY nome
");
?>

<h3>Utilizadores</h3>

<a href="index.php?bb=users_novo" class="btn btn-success mb-3">
+ Novo Utilizador
</a>

<div class="table-responsive">
<table class="table table-bordered">
<thead>
<tr>
<th>Nome</th>
<th>Email</th>
<th>Perfil</th>
<th>Criado em</th>
<th>Ações</th>
</tr>
</thead>

<tbody>
<?php while ($u = mysqli_fetch_assoc($res)): ?>
<tr>
<td><?= htmlspecialchars($u['nome']) ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td>
<span class="badge <?= $u['perfil'] === 'admin' ? 'badge-danger' : 'badge-secondary' ?>">
<?= $u['perfil'] ?>
</span>
</td>
<td><?= $u['created_at'] ?></td>
<td>
<a href="index.php?bb=users_editar&id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">
Editar
</a>

<?php if ($u['id'] != $_SESSION['user']['id']): ?>
<a href="index.php?bb=users_excluir&id=<?= $u['id'] ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Apagar utilizador?')">
Apagar
</a>
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
