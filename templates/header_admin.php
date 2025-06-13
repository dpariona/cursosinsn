<?php
// templates/header_admin.php

define('APP_RUNNING', true); // ← Esto permite cargar config.php
require_once __DIR__ . '/../config/config.php'; // ← Ruta correcta desde templates/

Sesion::redirigirSiNoLogueado('admin'); // Ya puedes usar la clase

$sesion = new Sesion();
$ci = $sesion->get("ci");
$nombre = $sesion->get("nombre") ?? 'Usuario';
$foto = $sesion->get("foto") ?? 'user.png';

// Ruta de la foto
$ruta_foto = URL_BASE . "assets/uploads/usuarios/" . $foto;
if (!file_exists(BASE_PATH . "assets/uploads/usuarios/" . $foto) || empty($foto)) {
    $ruta_foto = URL_BASE . "assets/uploads/usuarios/user.png";
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Curso Virtual - INSN</title>

  <link href="<?= URL_BASE ?>assets/img/favicon.png" rel="icon">
  <link href="<?= URL_BASE ?>assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <link href="https://fonts.googleapis.com/css?family=Open+Sans|Nunito|Poppins" rel="stylesheet">

  <!-- Vendor CSS -->
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/vendor/bootstrap-icons/bootstrap-icons.css">
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/vendor/boxicons/css/boxicons.min.css">
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/vendor/quill/quill.snow.css">
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/vendor/quill/quill.bubble.css">
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/vendor/remixicon/remixicon.css">
  <link rel="stylesheet" href="<?= URL_BASE ?>assets/vendor/simple-datatables/style.css">

  <!-- Main CSS -->  
 
 <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/style.css">

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
