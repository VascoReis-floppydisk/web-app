<?php
require __DIR__ . "/config.php";
require __DIR__ . "/includes/auth.php";

$bb = $_GET['bb'] ?? 'home';

include __DIR__ . "/includes/header.php";
include __DIR__ . "/includes/menu.php";
?>

<div class="main-panel">
<div class="content-wrapper">

<?php
switch ($bb) {

    case 'home':
        include __DIR__ . "/pages/inicio.php";
        break;

        /* ===== RESIDENTES ===== */
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

            /* ===== TRABALHADORES ===== */
            case 'trabalhadores_listar':
                include __DIR__ . "/pages/trabalhadores_listar.php";
                break;

            case 'trabalhadores_novo':
                include __DIR__ . "/pages/trabalhadores_novo.php";
                break;

            case 'trabalhadores_editar':
                include __DIR__ . "/pages/trabalhadores_editar.php";
                break;

            case 'trabalhadores_excluir':
                include __DIR__ . "/pages/trabalhadores_excluir.php";
                break;

            default:
                include __DIR__ . "/pages/404.php";
                break;
}
?>

</div>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
