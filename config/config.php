<?php
// config/config.php
if (!defined('APP_RUNNING')) {
    die("Acceso no permitido");
}

require_once __DIR__ . '/db_con.php';
require_once __DIR__ . '/../clases/Sesion.class.php';

// Iniciar sesión segura si aún no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
Sesion::iniciar();
