<?php
$servidor = "localhost";
$baseDados = "escola";
$user = "root";
$pass = "";
$conexao = mysqli_connect($servidor, $user, $pass, $baseDados);
if (!$conexao) {
die("Conexão Falhada: " . mysqli_connect_error());
}
mysqli_set_charset($conexao, "utf8mb4");