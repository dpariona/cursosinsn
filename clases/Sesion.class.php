<?php
//clases/Sesion.class.php

class Sesion
{
    public static function iniciar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Control de inactividad
        $tiempo_inactivo = 900; // 15 minutos
        if (isset($_SESSION['ultimo_acceso'])) {
            $tiempo_actual = time();
            $tiempo_transcurrido = $tiempo_actual - $_SESSION['ultimo_acceso'];
            if ($tiempo_transcurrido > $tiempo_inactivo) {
                session_unset();
                session_destroy();
                header("Location: /login.php?timeout=1");
                exit();
            }
        }
        $_SESSION['ultimo_acceso'] = time();
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

    public static function destruir()
    {
        session_unset();
        session_destroy();
    }

    public static function cerrarSesionYRedirigir($url)
    {
        self::destruir();
        header("Location: $url");
        exit;
    }

    public static function redirigirSiNoLogueado($rolEsperado = null)
    {
        self::iniciar();
        if (!self::existe('ci')) {
            header("Location: ../login/login.php");
            exit;
        }

        if ($rolEsperado && self::get('rol') !== $rolEsperado) {
            self::cerrarSesionYRedirigir('../login/login.php');
        }
    }
}
