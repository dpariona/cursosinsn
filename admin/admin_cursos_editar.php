<?php
require_once '../templates/header_admin.php'; // Ya incluye config y control de sesión
require_once '../templates/sidebar_admin.php'; 

$rol = Sesion::get('rol');
$usuario_id = Sesion::get('usuario_id');

$curso_id = $_GET['id'] ?? null;
if (!$curso_id) {
    echo "<div class='alert alert-danger'>ID de curso inválido.</div>";
    exit;
}

// Obtener datos del curso
$sql = "SELECT c.*, s.nombre_serv 
        FROM cursos c 
        LEFT JOIN servicios s ON c.servicio_id = s.id
        WHERE c.id = ?";
$stmt = $db_con->prepare($sql);
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();

if (!$curso) {
    echo "<div class='alert alert-danger'>Curso no encontrado.</div>";
    exit;
}

if ($rol === 'admin') {
    $servicios = Sesion::get('servicios');
    $ids = array_column($servicios, 'id');
    if (!in_array($curso['servicio_id'], $ids)) {
        echo "<div class='alert alert-warning'>No tienes permiso para editar este curso.</div>";
        exit;
    }
} elseif ($rol !== 'superadmin') {
    echo "<div class='alert alert-danger'>Acceso no autorizado.</div>";
    exit;
}
?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
<div class="container mt-4">
  <h2>Editar Curso</h2>
  <form method="post" action="admin_cursos_guardar.php">
    <input type="hidden" name="id" value="<?= $curso['id'] ?>">

    <div class="form-group">
      <label for="titulo">Título:</label>
      <input type="text" class="form-control" name="titulo" id="titulo"
             value="<?= htmlspecialchars($curso['titulo_curso']) ?>" required>
    </div>

    <div class="form-group">
      <label>Servicio:</label>
      <input type="text" class="form-control" value="<?= htmlspecialchars($curso['nombre_serv']) ?>" disabled>
    </div>

    <button type="submit" class="btn btn-success">Guardar Cambios</button>
    <a href="admin_cursos.php" class="btn btn-secondary">Volver</a>
  </form>
</div>

  </main>