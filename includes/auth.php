<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Le pasamos la ruta para que sepa dónde está el index (Ej: "../index.php" o "index.php")
function redirigirSiNoLogueado($rutaAlIndex = "index.php") {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: " . $rutaAlIndex . "?error=debes_iniciar_sesion");
        exit();
    }
}

// Recibe el rol explícito del usuario y la ruta de escape
function restringirAccesoA($rolDelUsuario, array $rolesPermitidos, $rutaAlIndex = "index.php") {
    if (!in_array($rolDelUsuario, $rolesPermitidos)) {
        header("Location: " . $rutaAlIndex . "?error=acceso_denegado_admin");
        exit();
    }
}
?>