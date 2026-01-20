<?php
$result = mysqli_query($conexao, "SELECT * FROM residentes ORDER BY nome");
?>

<h4>Residentes</h4>

<a href="index.php?bb=residentes_novo" class="btn btn-primary mb-3">
Novo Residente
</a>

<table class="table table-bordered">
<thead>
<tr>
<th>Nome</th>
<th>Email</th>
<th>Quarto</th>
<th>Telefone</th>
<th>Ações</th>
</tr>
</thead>
<tbody>
<?php while ($r = mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= htmlspecialchars($r['nome']) ?></td>
<td><?= htmlspecialchars($r['email']) ?></td>
<td><?= htmlspecialchars($r['quarto']) ?></td>
<td><?= htmlspecialchars($r['telefone']) ?></td>
<td>
<a class="btn btn-sm btn-warning"
href="index.php?bb=residentes_editar&id=<?= $r['id'] ?>">
Editar
</a>
<a class="btn btn-sm btn-danger"
onclick="return confirm('Tem certeza?')"
href="index.php?bb=residentes_excluir&id=<?= $r['id'] ?>">
Excluir
</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
