<?php
require_once '../templates/header_admin.php'; // Ya incluye config y control de sesión
require_once '../templates/sidebar_admin.php';
//require_once '../utils/permisos.php';

Sesion::iniciar();

$rol = Sesion::get('rol');
$usuario_id = Sesion::get('usuario_id');

// Verificar acceso al curso si viene por GET
$curso_id = isset($_GET['curso_id']) ? (int) $_GET['curso_id'] : 0;
if ($curso_id && !tieneAccesoACurso($curso_id, $db_con)) {
    exit("⛔ Acceso denegado al curso.");
}

// Mostrar todos los cursos según rol
if ($rol === 'superadmin') {
    $sql = "SELECT c.*, s.nombre_serv 
            FROM cursos c 
            LEFT JOIN servicios s ON c.servicio_id = s.id";
    $stmt = $db_con->prepare($sql);
} elseif ($rol === 'admin') {
    $servicios = Sesion::get('servicios');
    if (empty($servicios)) {
        echo "<div class='alert alert-warning'>No tiene servicios asignados.</div>";
        exit;
    }

    $ids = array_column($servicios, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $sql = "SELECT c.*, s.nombre_serv 
            FROM cursos c 
            LEFT JOIN servicios s ON c.servicio_id = s.id
            WHERE c.servicio_id IN ($placeholders)";
    $stmt = $db_con->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
} else {
    exit("⛔ Rol no autorizado.");
}

$stmt->execute();
$resultado = $stmt->get_result();

?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Cursos</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Inicio</a></li>
        <li class="breadcrumb-item active">Cursos</li>
      </ol>
    </nav>
  </div>

  <section class="section dashboard">
    <div class="row">
      <div class="card shadow-lg">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">📋 Cursos</h4>
          <a href="admin_curso_nuevo.php" class="btn btn-info text-white">➕ Nuevo Curso</a>
        </div>
        <div class="card-body">
          <table class="table datatable table-bordered table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Servicio</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($curso = $resultado->fetch_assoc()) : ?>
              <tr>
                <td><?= $curso['id'] ?></td>
                <td><?= htmlspecialchars($curso['titulo_curso']) ?></td>
                <td><?= htmlspecialchars($curso['nombre_serv']) ?></td>
                <td>
                  <a href="admin_curso_editar.php?curso_id=<?= $curso['id'] ?>" class="btn btn-sm btn-primary">✏️ Editar</a>
                  <a href="admin_curso_ver.php?curso_id=<?= $curso['id'] ?>" class="btn btn-sm btn-info">👁️ Ver</a>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include '../templates/footer_admin.php'; ?>
