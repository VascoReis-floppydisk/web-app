<?php
require __DIR__ . "/config.php";
require __DIR__ . "/includes/auth.php";
include __DIR__ . "/includes/header.php";
include __DIR__ . "/includes/menu.php";
$bb = $_GET['bb'] ?? 'home';

switch ($bb) {

    case 'home':
        include __DIR__ . "/pages/inicio.php";
        break;

    case 'residentes_listar':
        include __DIR__ . "/pages/residentes_listar.php";
        break;

    case 'residentes_novo':
        include __DIR__ . "/pages/residentes_novo.php";
        break;

    case 'residentes_editar':
        include __DIR__ . "/pages/residentes_editar.php";
        break;

    case 'residentes_excluir':
        include __DIR__ . "/pages/residentes_excluir.php";
        break;

    default:
        include __DIR__ . "/pages/404.php";
        break;
}
include __DIR__ . "/includes/footer.php";
