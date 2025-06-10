<?php
//templates/header_admin.php

define('APP_RUNNING', true); // Para permitir la inclusión de config.php
require_once __DIR__ . '/../config/config.php';

Sesion::redirigirSiNoLogueado('admin'); // o 'estudiante' si es para estudiantes

// Iniciar sesión y obtener variables
$sesion = new sesion();
$ci = $sesion->get("ci");
$nombre = $sesion->get("nombre") ?? 'Usuario';
$foto = $sesion->get("foto") ?? 'user.png';

// Ruta de la foto
$ruta_foto = "../assets/uploads/usuarios/" . $foto;
if (!file_exists($ruta_foto) || empty($foto)) {
    $ruta_foto = "../assets/uploads/usuarios/user.png";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Curso Virtual - INSN</title>

  <link href="../assets/img/favicon.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <link href="https://fonts.googleapis.com/css?family=Open+Sans|Nunito|Poppins" rel="stylesheet">

  <!-- Vendor CSS -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Main CSS -->
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="dashboard.php" class="logo d-flex align-items-center">
        <img src="https://www.insn.gob.pe/sites/default/files/logo-INSN-2022.png" alt="Logo">
        <span class="d-none d-lg-block">Cursos</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?php echo $ruta_foto; ?>" alt="Profile" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($nombre); ?></span>
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header text-center">
              <img src="<?php echo $ruta_foto; ?>" class="rounded-circle mb-2" width="60" height="60" style="object-fit: cover;">
              <h6 class="mb-0"><?php echo htmlspecialchars($nombre); ?></h6>
              <small class="text-success">● Conectado</small>
            </li>
            <li><hr class="dropdown-divider"></li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="perfil.php">
                <i class="bi bi-person"></i>
                <span>Mi perfil</span>
              </a>
            </li>

            <li><hr class="dropdown-divider"></li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="../login/logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </header><!-- End Header -->
