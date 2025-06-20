<?php
// login/login_verificar.php

define('APP_RUNNING', true);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../clases/Sesion.class.php';

Sesion::iniciar();

if (empty($_POST['ci']) || empty($_POST['clave'])) {
    echo "⚠️ CI y clave requeridos. <a href='login.php'>Volver</a>";
    exit;
}

$ci = trim($_POST['ci']);
$clave = $_POST['clave'];

$sql = "SELECT * FROM usuarios WHERE ci = ? AND estado = 'activo' LIMIT 1";
$stmt = $db_con->prepare($sql);
$stmt->bind_param("s", $ci);
$stmt->execute();
$result = $stmt->get_result();

if ($usuario = $result->fetch_assoc()) {
    if (password_verify($clave, $usuario['clave'])) {
        // Convertimos a minúsculas el rol para evitar problemas
        $rol = strtolower($usuario['rol']);

        // Guardar sesión
        Sesion::set('usuario_id', $usuario['id']);
        Sesion::set('ci', $usuario['ci']);
        Sesion::set('nombre', $usuario['nombre']);
        Sesion::set('rol', $rol);
        Sesion::set('foto', $usuario['foto'] ?? 'user.png');

        // Si es admin, cargar servicios asociados (opcional)
        if ($rol === 'admin') {
            $sql_serv = "SELECT s.id, s.nombre_serv
                         FROM servicios s
                         JOIN usuario_servicio us ON s.id = us.servicio_id
                         WHERE us.usuario_id = ?";
            $stmt_serv = $db_con->prepare($sql_serv);
            $stmt_serv->bind_param("i", $usuario['id']);
            $stmt_serv->execute();
            $result_serv = $stmt_serv->get_result();

            $servicios = [];
            while ($row = $result_serv->fetch_assoc()) {
                $servicios[] = $row;
            }

            Sesion::set('servicios', $servicios);
        }

        // Redirigir a dashboard
        header("Location: ../admin/index.php");
        exit;
    } else {
        echo "❌ Clave incorrecta. <a href='login.php'>Volver</a>";
    }
} else {
    echo "❌ Usuario no encontrado o inactivo. <a href='login.php'>Volver</a>";
}
