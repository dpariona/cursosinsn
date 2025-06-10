<?php
// login/sesion.class.php

class Sesion
{
    public static function iniciar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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
