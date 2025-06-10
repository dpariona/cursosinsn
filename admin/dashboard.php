<?php
include '../templates/header_admin.php';
include '../templates/sidebar_admin.php';


session_start();

// Tiempo máximo de inactividad en segundos (ej. 15 minutos = 900)
$tiempo_inactivo = 900;

// Verifica si hay registro de la última actividad
if (isset($_SESSION['ultimo_acceso'])) {
    $tiempo_actual = time();
    $tiempo_transcurrido = $tiempo_actual - $_SESSION['ultimo_acceso'];

    if ($tiempo_transcurrido > $tiempo_inactivo) {
        // Se supera el tiempo de inactividad → destruir sesión
        session_unset();
        session_destroy();
        header("Location: ../login.php?timeout=1"); // puedes mostrar mensaje
        exit();
    }
}

// Actualiza el tiempo de último acceso
$_SESSION['ultimo_acceso'] = time();


?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">


		</div>
    </section>

  </main><!-- End #main -->

 <?php
include '../templates/footer_admin.php';

?>