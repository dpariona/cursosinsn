<?php
// login/logout.php
require_once '../clases/Sesion.class.php';
Sesion::cerrarSesionYRedirigir('login.php?logout=1');


// anterior sin Sesion.class.php
/*
require_once '../clases/Sesion.class.php';

Sesion::iniciar();

// Limpiar todas las variables de sesión
$_SESSION = [];

// Destruir cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión completamente
session_destroy();

// Redirigir al login (ajusta si está en otra carpeta)
header("Location: login.php?logout=1");
exit();
*/
