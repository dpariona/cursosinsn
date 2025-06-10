<?php
session_start();

require_once '../clases/Sesion.class.php';
Sesion::cerrarSesionYRedirigir('./');

// Limpia todas las variables de sesión
$_SESSION = array();

// Si quieres destruir la cookie de sesión también (recomendado)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruye la sesión
session_destroy();

// Redirige al login (ajusta la ruta si es necesario)
header("Location: ../login/");
exit();
?>
