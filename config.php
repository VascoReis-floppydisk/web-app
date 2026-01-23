<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$servidor = "localhost";
$baseDados = "escola";
$user = "root";
$pass = "";
$conexao = mysqli_connect($servidor, $user, $pass, $baseDados);
if (!$conexao) {
die("Conexão Falhada: " . mysqli_connect_error());
}
mysqli_set_charset($conexao, "utf8mb4");
