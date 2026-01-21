<?php
function is_admin(): bool {
    return isset($_SESSION['user']) && $_SESSION['user']['perfil'] === 'admin';
}
