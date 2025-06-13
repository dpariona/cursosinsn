<?php
//admin/admin_encuesta.php
require_once '../templates/header_admin.php'; // Ya incluye config y control de sesión
require_once '../templates/sidebar_admin.php';

// Borrar encuesta si se envió ?eliminar=ID
if (isset($_GET['eliminar']) && $_SESSION['rol'] === 'admin') {
    $id = intval($_GET['eliminar']);

    // Validar que la encuesta pertenece a un curso que el usuario puede gestionar
    $usuario_id = $_SESSION['usuario_id'];
    $valid = $db_con->query("
        SELECT e.id
        FROM encuestas e
        JOIN cursos c ON e.id_curso = c.id
        JOIN usuario_servicio us ON c.servicio_id = us.servicio_id
        WHERE e.id = $id AND us.usuario_id = $usuario_id
    ");

    if ($valid && $valid->num_rows > 0) {
        $preguntas = $db_con->query("SELECT id FROM encuesta_preguntas WHERE id_encuesta=$id");
        while ($p = $preguntas->fetch_assoc()) {
            $id_pregunta = $p['id'];
            $db_con->query("DELETE FROM encuesta_opciones_respuesta WHERE id_pregunta=$id_pregunta");
            $db_con->query("DELETE FROM encuesta_preguntas WHERE id=$id_pregunta");
        }
        $db_con->query("DELETE FROM encuestas WHERE id=$id");
        echo "<div class='alert alert-success'>Encuesta eliminada correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>No tienes permiso para eliminar esta encuesta.</div>";
    }
}

// Consultar encuestas según rol
$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

if ($rol === 'superadmin') {
    // Ver todas las encuestas
    $sql = "SELECT e.id, e.titulo_encuesta, e.fecha_creacion, c.titulo_curso
            FROM encuestas e
            JOIN cursos c ON e.id_curso = c.id
            ORDER BY e.fecha_creacion DESC";
} else {
    // Ver solo las encuestas de cursos que pertenecen a servicios del usuario
    $sql = "SELECT e.id, e.titulo_encuesta, e.fecha_creacion, c.titulo_curso
            FROM encuestas e
            JOIN cursos c ON e.id_curso = c.id
            JOIN usuario_servicio us ON c.servicio_id = us.servicio_id
            WHERE us.usuario_id = $usuario_id
            ORDER BY e.fecha_creacion DESC";
}

$encuestas = $db_con->query($sql);
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="./">Inicio</a></li>
                <li class="breadcrumb-item active">Encuestas</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <div class="row">
            <div class="card shadow-lg">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">📋 Encuestas</h4>
                    <a href="<?= URL_BASE ?>admin/encuesta_nueva.php" class="btn btn-info text-white">➕ Nueva Encuesta</a>
                </div>
                <div class="card-body">
                    <?php if ($encuestas->num_rows > 0): ?>
                        <table class="table datatable table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="4%">#</th>
                                    <th>Título de Encuesta</th>
                                    <th>Curso</th>
                                    <th width="10%">Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $n = 1; while ($e = $encuestas->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $n++ ?></td>
                                        <td><?= htmlspecialchars($e['titulo_encuesta']) ?></td>
                                        <td><?= htmlspecialchars($e['titulo_curso']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($e['fecha_creacion'])) ?></td>
                                        <td>
                                            <a href="encuesta_editar.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-primary">✏️Editar</a>
                                            <?php if ($rol === 'admin'): ?>
                                                <a href="?eliminar=<?= $e['id'] ?>" class="btn btn-sm btn-outline-danger disabled" onclick="return confirm('¿Eliminar esta encuesta?')">🗑️Borrar</a>
                                            <?php endif; ?>
                                            <a href="encuesta_estadistica.php?encuesta_id=<?= $e['id'] ?>" class="btn btn-primary btn-sm">📝Estad</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info">No hay encuestas disponibles.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../templates/footer_admin.php'; ?>
