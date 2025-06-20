<?php
// clases/Sesion.class.php

class Sesion
{
    /**
     * Inicia la sesión y verifica inactividad
     */
    public static function iniciar($timeoutMinutos = 20)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Control de inactividad
        if (isset($_SESSION['ultimo_acceso'])) {
            $inactivo = time() - $_SESSION['ultimo_acceso'];
            if ($inactivo > ($timeoutMinutos * 60)) {
                // Tiempo de inactividad superado
                session_unset();
                session_destroy();
                header("Location: ../login/timeout.php");
                exit;
            }
        }

        // Registrar nuevo acceso
        $_SESSION['ultimo_acceso'] = time();
    }

    /**
     * Cierra sesión completamente y redirige
     */
    public static function cerrarSesionYRedirigir($ruta = '../login/login.php')
    {
        self::iniciar(); // Asegura sesión iniciada antes de destruir

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        header("Location: $ruta");
        exit();
    }

    public static function set($clave, $valor)
    {
        $_SESSION[$clave] = $valor;
    }

    public static function get($clave)
    {
        return $_SESSION[$clave] ?? null;
    }

    public static function existe($clave)
    {
        return isset($_SESSION[$clave]);
    }

    public static function eliminar($clave)
    {
        unset($_SESSION[$clave]);
    }

    /**
     * Redirige al login si no hay sesión o rol no permitido
     */
    public static function redirigirSiNoLogueado($roles_permitidos = [])
    {
        self::iniciar(); // ya incluye timeout

        if (!isset($_SESSION['usuario_id'])) {
            //header("Location: ../login/login.php?timeout=1");
			header("Location: ../login/timeout.php");
            exit;
        }

        // Aceptar string o array
        if (!is_array($roles_permitidos)) {
            $roles_permitidos = [$roles_permitidos];
        }

        $rol_actual = $_SESSION['rol'] ?? '';

        if (!empty($roles_permitidos) && !in_array($rol_actual, $roles_permitidos)) {
            die("⛔ Acceso denegado para el rol: <strong>$rol_actual</strong>");
        }
    }
}
