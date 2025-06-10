<?php
// utils/acceso_curso.php o donde lo prefieras
function tieneAccesoACurso($curso_id, $db_con): bool {
    $usuario_id = Sesion::get('usuario_id');
    $rol = Sesion::get('rol');

    // Superadmin tiene acceso a todo
    if ($rol === 'superadmin') {
        return true;
    }

    // Verificar si el admin tiene acceso al curso por su servicio
    $sql = "SELECT 1
            FROM cursos c
            JOIN usuario_servicio us ON c.servicio_id = us.servicio_id
            WHERE c.id = ? AND us.usuario_id = ?
            LIMIT 1";
    $stmt = $db_con->prepare($sql);
    $stmt->bind_param("ii", $curso_id, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}
