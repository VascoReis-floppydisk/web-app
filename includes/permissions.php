<?php
function is_admin() {
    return isset($_SESSION['user']) && $_SESSION['user']['perfil'] === 'admin';
}

function require_admin() {
    if (!is_admin()) {
        http_response_code(403);
        die("Acesso negado.");
    }
}
