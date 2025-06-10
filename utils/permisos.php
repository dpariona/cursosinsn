<?php
// utils/permisos.php
require_once __DIR__ . '/../clases/Sesion.class.php';

function tieneAccesoACurso(int $curso_id, mysqli $db_con): bool {
    Sesion::iniciar();
    $rol = Sesion::get('rol');

    // Superadmin tiene acceso total
    if ($rol === 'superadmin') {
        return true;
    }

    // Admin debe tener servicios
    if ($rol === 'admin') {
        $servicios = Sesion::get('servicios');
        if (!$servicios || empty($servicios)) {
            return false;
        }

        $ids_servicios = array_column($servicios, 'id');
        $placeholders = implode(',', array_fill(0, count($ids_servicios), '?'));

        $sql = "SELECT COUNT(*) 
                FROM cursos 
                WHERE id = ? AND servicio_id IN ($placeholders)";

        $stmt = $db_con->prepare($sql);
        $types = 'i' . str_repeat('i', count($ids_servicios));
        $params = array_merge([$curso_id], $ids_servicios);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count > 0;
    }

    return false;
}
